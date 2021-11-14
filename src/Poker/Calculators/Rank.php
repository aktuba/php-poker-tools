<?php

declare(strict_types=1);

namespace Poker\Calculators;

use Poker\Exceptions\InvalidRankException;
use Poker\Exceptions\RuntimeException;

/**
 * Class Rank
 * @package Poker\Calculators
 */
class Rank implements RankInterface
{
    protected const MAX_BOARD_CARDS = 5;
    protected const MAX_COUNT_CARDS = 7;

    /**
     * @var string
     */
    private string $combination;

    /**
     * @var array
     */
    private array $values;

    /**
     * @var int
     */
    private int $rank;

    /**
     * @param Board $board
     * @param Card[] $cards
     * @param Hand $hand
     * @throws InvalidRankException
     * @throws RuntimeException
     */
    public function __construct(Board $board, array $cards, Hand $hand)
    {
        $this->calculate($board, $cards, $hand);
    }

    /**
     * @return string
     */
    public function combination(): string
    {
        return $this->combination;
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return $this->values;
    }

    /**
     * @return int
     */
    public function rank(): int
    {
        return $this->rank;
    }

    /**
     * @param Board $board
     * @param Card[] $cards
     * @param Hand $hand
     * @return void
     * @throws InvalidRankException
     * @throws RuntimeException
     */
    private function calculate(Board $board, array $cards, Hand $hand): void
    {
        $cards = array_merge($board->cards(), $cards, $hand->cards());
        if (count($cards) < self::MAX_BOARD_CARDS || count($cards) > self::MAX_COUNT_CARDS) {
            throw new InvalidRankException('Incorrect set of cards for calculating the rank');
        }

        $indexes = $this->getIndexes($cards);

        usort(
            $cards,
            fn(Card $card1, Card $card2) => $indexes[$card2->value()] <=> $indexes[$card1->value()]
        );

        $values = [];
        $suits = [];
        foreach ($cards as $card) {
            $values[] = $card->value();
            $suits[] = $card->suit();
        }

        $map = $this->buildCardsMap($values, $indexes);
        $flush = $this->getFlushSuit($suits);
        $flushValues = $this->getFlushValues($flush, $cards);
        $straightValues = $this->getStraightValues($map);

        $this->combination = $this->getCombination($map, $straightValues, $flush, $flushValues);
        $this->values = $this->getValues($map, $straightValues, $flushValues);
        $this->rank = $this->getRank();
    }

    /**
     * @param array $map
     * @param array|null $straightValues
     * @param string|null $flush
     * @param array|null $flushValues
     * @return string
     * @throws RuntimeException
     */
    private function getCombination(array $map, ?array $straightValues, ?string $flush, ?array $flushValues): string
    {
        uasort(
            $map,
            static function (array $value1, array $value2): int {
                $result = $value2['count'] <=> $value1['count'];

                if ($result === 0) {
                    $result = $value2['index'] <=> $value1['index'];
                }

                return $result;
            }
        );

        $first = reset($map);
        $next = next($map);
        if (!is_array($first) || !is_array($next)) {
            throw new RuntimeException('Error getting first or second value');
        }

        $isStraight = $straightValues !== null;
        $flushStart = null;
        if ($flushValues !== null) {
            $flushStart = reset($flushValues);
        }

        return match (true) {
            $flush !== null && $isStraight && $flushStart === 'A' => Combinations::ROYAL_FLUSH,
            $flush !== null && $isStraight => Combinations::STRAIGHT_FLUSH,
            $first['count'] === 4 => Combinations::FOUR_OF_A_KIND,
            $first['count'] === 3 && $next['count'] > 1 => Combinations::FULL_HOUSE,
            $flush !== null => Combinations::FLUSH,
            $isStraight => Combinations::STRAIGHT,
            $first['count'] === 3 => Combinations::THREE_OF_A_KIND,
            $first['count'] === 2 && $next['count'] > 1 => Combinations::TWO_PAIR,
            $first['count'] === 2 => Combinations::ONE_PAIR,
            default => Combinations::HIGH_CARD,
        };
    }

    /**
     * @param array $map
     * @param array|null $straightValues
     * @param array|null $flushValues
     * @return array
     * @throws RuntimeException
     */
    private function getValues(array $map, ?array $straightValues, ?array $flushValues): array
    {
        if (
            $this->combination === Combinations::ROYAL_FLUSH ||
            $this->combination === Combinations::STRAIGHT_FLUSH ||
            $this->combination === Combinations::FLUSH
        ) {
            if (empty($flushValues)) {
                throw new RuntimeException('Error flush values');
            }

            return array_slice($flushValues, 0, self::MAX_BOARD_CARDS);
        }

        if ($this->combination === Combinations::STRAIGHT) {
            if (empty($straightValues)) {
                throw new RuntimeException('Error straight values');
            }

            return $straightValues;
        }

        $result = [];

        uasort(
            $map,
            static function (array $value1, array $value2): int {
                $result = $value2['count'] <=> $value1['count'];

                if ($result === 0) {
                    $result = $value2['index'] <=> $value1['index'];
                }

                return $result;
            }
        );

        $needCount = self::MAX_BOARD_CARDS;
        foreach ($map as $value) {
            $count = min($value['count'], $needCount);

            for ($i = 0; $i < $count; $i++) {
                $result[] = $value['value'];
            }

            $needCount -= $count;
        }

        usort(
            $result,
            fn(string $value1, string $value2) => $map[$value2[0]]['index'] <=> $map[$value1[0]]['index']
        );

        return $result;
    }

    /**
     * @return int
     * @throws RuntimeException
     */
    private function getRank(): int
    {
        $result = 0;

        $map = [];
        foreach ($this->values as $value) {
            if (!array_key_exists($value, $map)) {
                $map[$value] = 0;
            }
            $map[$value]++;
        }

        foreach ($map as $value => $count) {
            $offset = array_search($value, Card::VALUES);
            if (!is_int($offset)) {
                throw new RuntimeException('Offset calculation error');
            }
            $result += (1 << ($offset * 3)) * $count;
        }

        $pos = array_search($this->combination, Combinations::COMBINATIONS);
        if (!is_int($pos)) {
            throw new RuntimeException('Error getting the position of the combination');
        }

        $offset = count(Card::VALUES) * 3;
        $result += (1 << $offset) * $pos;

        return $result;
    }

    /**
     * @param array $map
     * @return array|null
     */
    private function getStraightValues(array $map): ?array
    {
        $result = null;

        $extend = 'A*';

        if (array_key_exists('A', $map)) {
            $map[$extend] = [
                'value' => 'A',
                'index' => -1,
                'count' => 1,
            ];
        }

        uasort(
            $map,
            fn(array $value1, array $value2) => $value2['index'] <=> $value1['index']
        );

        $values = array_keys($map);

        $count = 0;
        $start = 0;
        $current = -1;
        foreach ($values as $pos => $value) {
            if ($current === -1 || $map[$value]['index'] + 1 === $current) {
                $count++;
                if ($count >= self::MAX_BOARD_CARDS) {
                    break;
                }
            } elseif ($current !== $map[$value]['index']) {
                $count = 1;
                $start = $pos;
            }
            $current = $map[$value]['index'];
        }

        if ($count >= self::MAX_BOARD_CARDS) {
            $result = array_map(
                static function (string $value) use ($extend): string {
                    if ($value === $extend) {
                        $value = 'A';
                    }
                    return $value;
                },
                array_slice($values, $start, self::MAX_BOARD_CARDS)
            );
        }

        return $result;
    }

    /**
     * @param array $suits
     * @return string|null
     */
    private function getFlushSuit(array $suits): ?string
    {
        $result = null;

        $map = [];
        foreach ($suits as $suit) {
            if (!array_key_exists($suit, $map)) {
                $map[$suit] = 0;
            }
            $map[$suit]++;

            if ($map[$suit] > 4) {
                $result = $suit;
                break;
            }
        }

        return $result;
    }

    /**
     * @param string|null $flush
     * @param Card[] $cards
     * @return array|null
     */
    private function getFlushValues(?string $flush, array $cards): ?array
    {
        $result = null;

        if ($flush !== null) {
            $result = [];

            foreach ($cards as $card) {
                if ($card->suit() === $flush) {
                    $result[] = $card->value();
                }
            }
        }

        return $result;
    }

    /**
     * @param Card[] $cards
     * @return int[]
     * @throws RuntimeException
     */
    private function getIndexes(array $cards): array
    {
        $result = [];

        foreach ($cards as $card) {
            $value = $card->value();
            if (!array_key_exists($value, $result)) {
                $position = array_search($value, Card::VALUES);

                if (!is_int($position)) {
                    throw new RuntimeException("Error card value {$value}");
                }

                $result[$value] = $position;
            }
        }

        return $result;
    }

    /**
     * @param array $values
     * @param array $indexes
     * @return array
     */
    private function buildCardsMap(array $values, array $indexes): array
    {
        $result = [];

        foreach ($values as $value) {
            if (!array_key_exists($value, $result)) {
                $result[$value] = [
                    'value' => $value,
                    'index' => $indexes[$value],
                    'count' => 0,
                ];
            }
            $result[$value]['count']++;
        }

        return $result;
    }
}
