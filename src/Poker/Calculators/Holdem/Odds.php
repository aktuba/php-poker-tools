<?php

declare(strict_types=1);

namespace Poker\Calculators\Holdem;

use Poker\Calculators\AbstractCalculator;
use Poker\Calculators\Board;
use Poker\Calculators\Card;
use Poker\Calculators\EquityCollection;
use Poker\Calculators\HandCollection;
use Poker\Calculators\Rank;
use Poker\Exceptions\InvalidCardException;
use Poker\Exceptions\RuntimeException;

/**
 * Class Odds
 * @package Poker\Calculators\Holdem
 */
class Odds extends AbstractCalculator
{
    /**
     * @param HandCollection $hands
     * @param Board $board
     * @param int $iterations
     * @param bool $exhaustive
     * @return EquityCollection
     * @throws InvalidCardException
     * @throws RuntimeException
     */
    public function calculate(HandCollection $hands, Board $board, int $iterations, bool $exhaustive): EquityCollection
    {
        $equityCollection = new EquityCollection($hands);

        $usedCards = array_merge($board->cards(), $hands->cards());
        $needCards = Board::MAX_BOARD_CARDS - count($board->cards());

        $generator = match (true) {
            $needCards === 0 => (fn() => yield [])(),
            $needCards < 3 || $exhaustive => $this->deckGenerator($needCards, $usedCards),
            default => $this->deckRandom($needCards, $iterations, $usedCards)
        };

        foreach ($generator as $cards) {
            $ranks = $this->getRanks($equityCollection, $board, $cards);
            $bestRank = $this->getBestRank($ranks);
            $isTie = $this->isTie($ranks, $bestRank);

            foreach ($ranks as $hand => $rank) {
                $equityCollection
                    ->incrementCount($hand)
                    ->incrementCombination($hand, $rank->combination());

                if ($rank->rank() === $bestRank) {
                    $equityCollection->incrementWins($hand, $isTie);
                }
            }
        }

        $count = $equityCollection->maxWins();
        foreach ($equityCollection->equities() as $equity) {
            if ($equity->wins() === $count) {
                $equity->setFavourite(true);
            }
        }

        return $equityCollection;
    }

    /**
     * @param EquityCollection $equityCollection
     * @param Board $board
     * @param Card[] $cards
     * @return Rank[]
     * @throws InvalidCardException
     * @throws RuntimeException
     */
    private function getRanks(EquityCollection $equityCollection, Board $board, array $cards): array
    {
        $result = [];

        foreach ($equityCollection->hands() as $hand) {
            $result[$hand->hand()] = new Rank($board, $cards, $hand);
        }

        return $result;
    }

    /**
     * @param Rank[] $ranks
     * @return int
     */
    private function getBestRank(array $ranks): int
    {
        $result = 0;

        foreach ($ranks as $rank) {
            if ($rank->rank() > $result) {
                $result = $rank->rank();
            }
        }

        return $result;
    }

    /**
     * @param Rank[] $ranks
     * @param int $bestRank
     * @return bool
     */
    private function isTie(array $ranks, int $bestRank): bool
    {
        $result = false;

        $count = 0;
        foreach ($ranks as $rank) {
            if ($rank->rank() === $bestRank) {
                $count++;
                if ($count > 1) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }
}
