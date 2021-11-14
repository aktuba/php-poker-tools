<?php

declare(strict_types=1);

namespace Poker\Calculators;

use Poker\Exceptions\InvalidCardException;

/**
 * Class Card
 * @package Poker\Calculators
 */
final class Card
{
    public const VALUES = ['2', '3', '4', '5', '6', '7', '8', '9', 'T', 'J', 'Q', 'K', 'A'];
    public const SUITS = ['s', 'h', 'd', 'c'];

    public const CARD_PATTERN = '~[AKQJT2-9.][schd.]~';

    private string $card;
    private string $value;
    private string $suit;

    /**
     * @param string $card
     * @throws InvalidCardException
     */
    public function __construct(string $card)
    {
        if (!preg_match(self::CARD_PATTERN, $card)) {
            throw new InvalidCardException("Card {$card} invalid, eg As");
        }

        $this->card = $card;
        $this->value = substr($card, 0, 1);
        $this->suit = substr($card, 1, 1);
    }

    /**
     * @return string
     */
    public function card(): string
    {
        return $this->card;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function suit(): string
    {
        return $this->suit;
    }
}
