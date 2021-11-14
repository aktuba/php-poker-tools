<?php

declare(strict_types=1);

namespace Poker\Calculators;

/**
 * Class Combinations
 * @package Poker\Calculators
 */
final class Combinations
{
    public const HIGH_CARD = 'high card';
    public const ONE_PAIR = 'one pair';
    public const TWO_PAIR = 'two pair';
    public const THREE_OF_A_KIND = 'three of a kind';
    public const STRAIGHT = 'straight';
    public const FLUSH = 'flush';
    public const FULL_HOUSE = 'full house';
    public const FOUR_OF_A_KIND = 'four of a kind';
    public const STRAIGHT_FLUSH = 'straight flush';
    public const ROYAL_FLUSH = 'royal flush';

    public const COMBINATIONS = [
        Combinations::HIGH_CARD,
        Combinations::ONE_PAIR,
        Combinations::TWO_PAIR,
        Combinations::THREE_OF_A_KIND,
        Combinations::STRAIGHT,
        Combinations::FLUSH,
        Combinations::FULL_HOUSE,
        Combinations::FOUR_OF_A_KIND,
        Combinations::STRAIGHT_FLUSH,
        Combinations::ROYAL_FLUSH,
    ];
}
