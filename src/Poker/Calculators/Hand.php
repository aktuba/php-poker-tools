<?php

declare(strict_types=1);

namespace Poker\Calculators;

use Poker\Exceptions\InvalidCardException;
use Poker\Exceptions\InvalidHandException;

/**
 * Class Hand
 * @package Poker\Calculators
 */
final class Hand
{
    public const COUNT_CARDS_HOLDEM = 2;

    /**
     * @var string
     */
    private string $hand;

    /** @var Card[] */
    private array $cards;

    /**
     * @param string $hand
     * @param int $countCards
     * @throws InvalidHandException
     */
    public function __construct(string $hand, int $countCards)
    {
        $this->hand = $hand;
        $this->cards = $this->getCards($hand);
        if (count($this->cards) !== $countCards) {
            throw new InvalidHandException('The number of cards must be ' . $countCards);
        }
    }

    /**
     * @return string
     */
    public function hand(): string
    {
        return $this->hand;
    }

    /**
     * @return Card[]
     */
    public function cards(): array
    {
        return $this->cards;
    }

    /**
     * @param string $hand
     * @return Card[]
     * @throws InvalidHandException
     */
    private function getCards(string $hand): array
    {
        $result = [];

        foreach (str_split($hand, 2) as $card) {
            try {
                $result[] = new Card($card);
            } catch (InvalidCardException $exception) {
                throw new InvalidHandException("Hand {$hand} invalid, eg AsAc");
            }
        }

        return $result;
    }
}
