<?php

declare(strict_types=1);

namespace Poker\Calculators;

/**
 * Interface RankInterface
 * @package Poker\Calculators
 */
interface RankInterface
{
    /**
     * @return int
     */
    public function rank(): int;
}
