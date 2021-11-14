<?php

declare(strict_types=1);

namespace Poker\Calculators;

/**
 * Class EquityCollection
 * @package Poker\Calculators
 */
final class EquityCollection
{
    /**
     * @var Hand[]
     */
    private array $hands;

    /**
     * @var Equity[]
     */
    private array $equities;

    /**
     * @param HandCollection $hands
     */
    public function __construct(HandCollection $hands)
    {
        $this->equities = [];
        foreach ($hands->hands() as $hand) {
            $this->equities[$hand->hand()] = new Equity($hand);
        }
        $this->hands = $hands->hands();
    }

    /**
     * @return Equity[]
     */
    public function equities(): array
    {
        return array_values($this->equities);
    }

    /**
     * @return Hand[]
     */
    public function hands(): array
    {
        return $this->hands;
    }

    /**
     * @return int
     */
    public function iterations(): int
    {
        $result = 0;

        if (!empty($this->equities)) {
            $result = reset($this->equities)->count();
        }

        return $result;
    }

    /**
     * @param string $hand
     * @return $this
     */
    public function incrementCount(string $hand): self
    {
        $this->equities[$hand]->incrementCount();
        return $this;
    }

    /**
     * @param string $hand
     * @param string $combination
     * @return $this
     */
    public function incrementCombination(string $hand, string $combination): self
    {
        $this->equities[$hand]->incrementCombination($combination);
        return $this;
    }

    /**
     * @param string $hand
     * @param bool $isTie
     * @return $this
     */
    public function incrementWins(string $hand, bool $isTie): self
    {
        $this->equities[$hand]->incrementWins($isTie);
        return $this;
    }

    /**
     * @return int
     */
    public function maxWins(): int
    {
        $result = 0;

        foreach ($this->equities as $equity) {
            if ($equity->wins() > $result) {
                $result = $equity->wins();
            }
        }

        return $result;
    }
}
