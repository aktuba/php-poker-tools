<?php

declare(strict_types=1);

namespace Poker\Calculators;

use Generator;
use Poker\Exceptions\InvalidCardException;

/**
 * Class AbstractCalculator
 * @package Poker\Calculators
 */
abstract class AbstractCalculator implements CalculatorInterface
{
    /**
     * @param int $count
     * @param Card[] $excludeCards
     * @return Generator
     */
    public function deckGenerator(int $count, array $excludeCards): Generator
    {
        foreach ($this->combination($this->deck($excludeCards), $count) as $combination) {
            yield $combination;
        }
    }

    /**
     * @param int $count
     * @param int $iterations
     * @param Card[] $excludeCards
     * @return Generator
     * @throws InvalidCardException
     */
    public function deckRandom(int $count, int $iterations, array $excludeCards): Generator
    {
        $deck = $this->deck($excludeCards);
        if (empty($deck)) {
            return;
        }

        $cache = [];
        for ($i = 0; $i < $iterations; $i++) {
            do {
                $result = [];
                $key = '';

                /** @var int[] $indexes */
                $indexes = array_rand($deck, $count);

                foreach ($indexes as $index) {
                    $key .= $deck[$index]->card();
                    $result[] = $deck[$index];
                }
            } while (array_key_exists($key, $cache));

            $cache[$key] = true;
            yield $result;
        }
    }

    /**
     * @param Card[] $cards
     * @param int $count
     * @return Generator
     */
    private function combination(array $cards, int $count): Generator
    {
        if (empty($count)) {
            yield [];
            return;
        }

        if (empty($cards)) {
            return;
        }

        $card = reset($cards);
        $cards = array_slice($cards, 1);

        foreach ($this->combination($cards, $count - 1) as $combination) {
            yield array_merge([$card], $combination);
        }

        foreach ($this->combination($cards, $count) as $combination) {
            yield $combination;
        }
    }

    /**
     * @param Card[] $excludeCards
     * @return Card[]
     * @throws InvalidCardException
     */
    private function deck(array $excludeCards): array
    {
        $result = [];

        $excludeMap = array_flip(array_map(
            fn(Card $card) => $card->card(),
            $excludeCards
        ));

        foreach (Card::VALUES as $value) {
            foreach (Card::SUITS as $suit) {
                $card = "{$value}{$suit}";
                if (!array_key_exists($card, $excludeMap)) {
                    $result[] = new Card($card);
                }
            }
        }

        return $result;
    }
}
