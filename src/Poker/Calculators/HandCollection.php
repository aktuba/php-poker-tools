<?php

declare(strict_types=1);

namespace Poker\Calculators;

use Poker\Exceptions\InvalidArgumentException;
use Poker\Exceptions\InvalidHandException;

/**
 * Class HandCollection
 * @package Poker\Calculators
 */
final class HandCollection
{
    /**
     * @var Hand[]
     */
    private array $hands;

    /**
     * @param array $hands
     * @param int $countCardsInHand
     * @throws InvalidArgumentException
     * @throws InvalidHandException
     */
    public function __construct(array $hands, int $countCardsInHand)
    {
        if (empty($hands)) {
            throw new InvalidArgumentException('Players hands not found');
        }

        $this->hands = array_map(
            fn(string $hand) => new Hand($hand, $countCardsInHand),
            $hands
        );
    }

    /**
     * @return Hand[]
     */
    public function hands(): array
    {
        return $this->hands;
    }

    /**
     * @return Card[]
     */
    public function cards(): array
    {
        $collection = array_map(
            fn(Hand $hand) => $hand->cards(),
            $this->hands
        );
        return array_merge(...$collection);
    }
}
