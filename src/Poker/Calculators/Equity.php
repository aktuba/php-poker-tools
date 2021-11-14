<?php

declare(strict_types=1);

namespace Poker\Calculators;

/**
 * Class Equity
 * @package Poker\Calculators
 */
final class Equity
{
    /**
     * @var Hand
     */
    private Hand $hand;

    /**
     * @var int
     */
    private int $count = 0;

    /**
     * @var int
     */
    private int $wins = 0;

    /**
     * @var int
     */
    private int $ties = 0;

    /**
     * @var array
     */
    private array $chances = [];

    /**
     * @var bool
     */
    private bool $favourite = false;

    /**
     * @param Hand $hand
     */
    public function __construct(Hand $hand)
    {
        $this->hand = $hand;
        $this->chances = array_fill_keys(Combinations::COMBINATIONS, 0);
    }

    /**
     * @return Hand
     */
    public function hand(): Hand
    {
        return $this->hand;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return int
     */
    public function wins(): int
    {
        return $this->wins;
    }

    /**
     * @return int
     */
    public function ties(): int
    {
        return $this->ties;
    }

    /**
     * @return array
     */
    public function chances(): array
    {
        return $this->chances;
    }

    /**
     * @return bool
     */
    public function isFavourite(): bool
    {
        return $this->favourite;
    }

    /**
     * @return $this
     */
    public function incrementCount(): self
    {
        $this->count++;
        return $this;
    }

    /**
     * @param string $combination
     * @return $this
     */
    public function incrementCombination(string $combination): self
    {
        $this->chances[$combination]++;
        return $this;
    }

    /**
     * @param bool $isTie
     * @return $this
     */
    public function incrementWins(bool $isTie): self
    {
        if (!$isTie) {
            $this->wins++;
        } else {
            $this->ties++;
        }
        return $this;
    }

    /**
     * @param bool $favourite
     * @return $this
     */
    public function setFavourite(bool $favourite = true): self
    {
        $this->favourite = $favourite;
        return $this;
    }
}
