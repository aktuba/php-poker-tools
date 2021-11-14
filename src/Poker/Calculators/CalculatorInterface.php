<?php

declare(strict_types=1);

namespace Poker\Calculators;

/**
 * Interface CalculatorInterface
 * @package Poker\Calculators
 */
interface CalculatorInterface
{
    /**
     * @param HandCollection $hands
     * @param Board $board
     * @param int $iterations
     * @param bool $exhaustive
     * @return EquityCollection
     */
    public function calculate(HandCollection $hands, Board $board, int $iterations, bool $exhaustive): EquityCollection;
}
