<?php

declare(strict_types=1);

namespace Poker\Commands;

use Poker\Calculators\Board;
use Poker\Calculators\CalculatorInterface;
use Poker\Calculators\Card;
use Poker\Calculators\Combinations;
use Poker\Calculators\EquityCollection;
use Poker\Calculators\Hand;
use Poker\Calculators\HandCollection;
use Poker\Calculators\Holdem\Odds as HoldemOddsCalculator;
use Poker\Exceptions\InvalidArgumentException;
use Poker\Exceptions\InvalidBoardException;
use Poker\Exceptions\InvalidHandException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HoldemOdds
 * @package Poker\Commands
 */
class Odds extends Command
{
    private const ARGUMENT_HANDS = 'hands';

    private const OPTION_BOARD = 'board';
    private const OPTION_ITERATIONS = 'iterations';
    private const OPTION_EXHAUSTIVE = 'exhaustive';
    private const OPTION_POSSIBILITIES = 'possibilities';

    private const ITERATIONS_DEFAULT = 100000;

    private const COLOR_CARDS = [
        's' => '#333333',
        'h' => '#9c0909',
        'd' => '#09299c',
        'c' => '#078f34',
    ];

    private const SYMBOL_CARDS = [
        's' => '♠',
        'h' => '♥',
        'd' => '♦',
        'c' => '♣',
    ];

    /**
     * @var string
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected static $defaultName = 'odds';

    /**
     * @var string
     * @psalm-suppress NonInvariantDocblockPropertyType
     */
    protected static $defaultDescription = 'Calculating poker odds';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument(
            self::ARGUMENT_HANDS,
            InputArgument::IS_ARRAY,
            'Players cards'
        );

        $this->addOption(
            self::OPTION_BOARD,
            'b',
            InputOption::VALUE_OPTIONAL,
            'Board cards'
        );

        $this->addOption(
            self::OPTION_ITERATIONS,
            'i',
            InputOption::VALUE_OPTIONAL,
            'Number of preflop simulations to run, default: ' . self::ITERATIONS_DEFAULT,
            self::ITERATIONS_DEFAULT
        );

        $this->addOption(
            self::OPTION_EXHAUSTIVE,
            'e',
            InputOption::VALUE_NEGATABLE,
            'Run all preflop simulations',
            false
        );

        $this->addOption(
            self::OPTION_POSSIBILITIES,
            'p',
            InputOption::VALUE_NEGATABLE,
            'Show individual hand possibilities',
            false
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws InvalidArgumentException
     * @throws InvalidBoardException
     * @throws InvalidHandException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = microtime(true);

        $hands = new HandCollection((array)$input->getArgument(self::ARGUMENT_HANDS), Hand::COUNT_CARDS_HOLDEM);
        $board = new Board((string)$input->getOption(self::OPTION_BOARD));

        $this->validateCards($hands, $board);

        $iterations = $this->getIterations($input);
        $exhaustive = $input->getOption(self::OPTION_EXHAUSTIVE);
        $possibilities = $input->getOption(self::OPTION_POSSIBILITIES);

        $equityCollection = $this->getCalculator()->calculate($hands, $board, $iterations, $exhaustive);

        $finish = microtime(true);

        $time = number_format($finish - $start, 2);

        $output->writeln(
            $this->getBoardText($board) .
            $this->getEquityText($equityCollection) .
            $this->getPossibilitiesText($equityCollection, $possibilities) .
            $this->color("{$equityCollection->iterations()} iterations in {$time} sec.\n", 'gray')
        );

        return 0;
    }

    /**
     * @return CalculatorInterface
     */
    private function getCalculator(): CalculatorInterface
    {
        return new HoldemOddsCalculator();
    }

    /**
     * @param Board $board
     * @return string
     */
    private function getBoardText(Board $board): string
    {
        $result = '';

        if (!empty($board->cards())) {
            $coloredCards = $this->colorCards($board->cards());
            $result = $this->color("\nboard:\n{$coloredCards}\n\n", 'gray');
        }

        return $result;
    }

    /**
     * @param EquityCollection $equityCollection
     * @return string
     */
    private function getEquityText(EquityCollection $equityCollection): string
    {
        $isManyHands = count($equityCollection->hands()) > 1;

        $firstColumn = '';
        if ($isManyHands) {
            $firstColumn = 'win';
        }

        $secondColumn = '';
        foreach ($equityCollection->equities() as $equity) {
            if ($equity->ties() > 0) {
                $secondColumn = 'tie';
                break;
            }
        }

        $result = $this->color("hand      {$firstColumn}     {$secondColumn}\n", 'gray');

        foreach ($equityCollection->equities() as $equity) {
            $string = $this->colorCards($equity->hand()->cards());

            if ($isManyHands) {
                $percent = $this->percent($equity->wins() / $equity->count());
                $percentString = $this->padStart($percent);

                $color = null;
                if ($equity->isFavourite()) {
                    $color = 'green';
                } elseif ($equity->wins() === 0) {
                    $color = 'gray';
                }

                $string .= $this->color($percentString, $color);
            }

            if ($equity->ties() > 0) {
                $percent = $this->percent($equity->ties() / $equity->count());
                $string .= $this->color($this->padStart($percent), '#b1b36f');
            }

            $result .= "{$string}\n";
        }

        return "{$result}\n";
    }

    /**
     * @param EquityCollection $equityCollection
     * @param bool $possibilities
     * @return string
     */
    private function getPossibilitiesText(EquityCollection $equityCollection, bool $possibilities): string
    {
        $result = '';

        $isManyHands = count($equityCollection->hands()) > 1;

        if (!$possibilities && $isManyHands) {
            return $result;
        }

        $lengths = array_map(
            fn(string $combination) => mb_strlen($combination, 'utf-8'),
            Combinations::COMBINATIONS
        );
        $padEnd = max($lengths) + 1;

        if ($isManyHands) {
            $header = str_repeat(' ', $padEnd);
            foreach ($equityCollection->equities() as $equity) {
                $header .= '   ' . $this->colorCards($equity->hand()->cards());
            }
            $result .= "{$header}\n";
        }

        $maxes = [];
        foreach ($equityCollection->equities() as $equity) {
            $key = $equity->hand()->hand();
            $maxes[$key] = -1;
            foreach ($equity->chances() as $count) {
                if ($count > $maxes[$key]) {
                    $maxes[$key] = $count;
                }
            }
        }

        foreach (Combinations::COMBINATIONS as $combination) {
            $string = $this->padEnd($combination, $padEnd);

            foreach ($equityCollection->equities() as $equity) {
                $count = $equity->chances()[$combination];

                $color = null;
                if ($count === $maxes[$equity->hand()->hand()]) {
                    $color = 'green';
                } elseif ($count === 0) {
                    $color = 'gray';
                }

                $percent = $this->percent($count / $equity->count());
                $string .= $this->color($this->padStart($percent), $color);
            }

            $result .= "{$string}\n";
        }

        return "{$result}\n";
    }

    /**
     * @param float $number
     * @return string
     */
    private function percent(float $number): string
    {
        $result = '·';

        if ($number >= 0.0001) {
            $result = number_format($number * 100, 2) . '%';
        } elseif ($number > 0) {
            $result = '< 0.01%';
        }

        return $result;
    }

    /**
     * @param string $string
     * @param int $length
     * @param string $padString
     * @return string
     */
    private function padStart(string $string, int $length = 8, string $padString = ' '): string
    {
        $stringLength = mb_strlen($string, 'utf-8');
        if ($stringLength < $length) {
            $string = str_repeat($padString, $length - $stringLength) . $string;
        }
        return $string;
    }

    /**
     * @param string $string
     * @param int $length
     * @param string $padString
     * @return string
     */
    private function padEnd(string $string, int $length = 15, string $padString = ' '): string
    {
        $stringLength = mb_strlen($string, 'utf-8');
        if ($stringLength < $length) {
            $string = $string . str_repeat($padString, $length - $stringLength);
        }
        return $string;
    }

    /**
     * @param string $string
     * @param string|null $color
     * @param string|null $background
     * @return string
     */
    private function color(string $string, ?string $color, ?string $background = null): string
    {
        $colors = [];

        if ($color !== null) {
            $colors[] = "fg={$color}";
        }

        if ($background !== null) {
            $colors[] = "bg={$background}";
        }

        if (!empty($colors)) {
            $colors = implode(';', $colors);
            $string = "<{$colors}>{$string}</>";
        }

        return $string;
    }

    /**
     * @param Card[] $cards
     * @return string
     */
    private function colorCards(array $cards): string
    {
        $result = [];

        foreach ($cards as $card) {
            $result[] = $this->color(
                $card->value() . self::SYMBOL_CARDS[$card->suit()],
                'white',
                self::COLOR_CARDS[$card->suit()]
            );
        }

        return implode(' ', $result);
    }

    /**
     * @param HandCollection $hands
     * @param Board $board
     * @throws InvalidArgumentException
     */
    private function validateCards(HandCollection $hands, Board $board): void
    {
        $cards = [];

        foreach ($hands->cards() as $card) {
            if (array_key_exists($card->card(), $cards)) {
                throw new InvalidArgumentException('Cards must be unique for players');
            }
            $cards[$card->card()] = true;
        }

        foreach ($board->cards() as $card) {
            if (array_key_exists($card->card(), $cards)) {
                throw new InvalidArgumentException('Cards must be unique for board');
            }
            $cards[$card->card()] = true;
        }
    }

    /**
     * @param InputInterface $input
     * @return int
     * @throws InvalidArgumentException
     */
    private function getIterations(InputInterface $input): int
    {
        $result = (int)$input->getOption(self::OPTION_ITERATIONS);

        if ($result <= 0) {
            throw new InvalidArgumentException('The number of iterations must be greater than 0');
        }

        return $result;
    }
}
