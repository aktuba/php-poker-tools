<?php

declare(strict_types=1);

namespace Tests\Poker\Calculators;

use PHPUnit\Framework\TestCase;
use Poker\Calculators\Board;
use Poker\Calculators\Card;
use Poker\Exceptions\InvalidBoardException;
use Throwable;

/**
 * Class BoardTest
 * @package Tests\Poker\Calculators
 */
final class BoardTest extends TestCase
{
    /**
     * @return array[]
     */
    public function providerBoard(): array
    {
        return [
            'empty board' => [
                'board' => '',
                'expect' => '',
                'exception' => null,
            ],
            'one card' => [
                'board' => 'As',
                'expect' => 'As',
                'exception' => null,
            ],
            'two cards' => [
                'board' => 'AsKc',
                'expect' => 'AsKc',
                'exception' => null,
            ],
            'three cards' => [
                'board' => 'AsKcQh',
                'expect' => 'AsKcQh',
                'exception' => null,
            ],
            'six cards' => [
                'board' => 'AsKcQh7d2c3h',
                'expect' => '',
                'exception' => InvalidBoardException::class,
            ],
        ];
    }

    /**
     * @dataProvider providerBoard
     * @covers       Board::board
     * @param string $board
     * @param string $expect
     * @param class-string<Throwable>|null $exception
     * @throws InvalidBoardException
     */
    public function testBoard(string $board, string $expect, ?string $exception): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        $board = new Board($board);

        $this->assertEquals($expect, $board->board());
    }

    /**
     * @return array[]
     */
    public function providerCards(): array
    {
        return [
            'empty board' => [
                'board' => '',
                'expect' => [],
                'exception' => null,
            ],
            'one card' => [
                'board' => 'As',
                'expect' => ['As'],
                'exception' => null,
            ],
            'two cards' => [
                'board' => 'AsKc',
                'expect' => ['As', 'Kc'],
                'exception' => null,
            ],
            'three cards' => [
                'board' => 'AsKcQh',
                'expect' => ['As', 'Kc', 'Qh'],
                'exception' => null,
            ],
            'six cards' => [
                'board' => 'AsKcQh7d2c3h',
                'expect' => [],
                'exception' => InvalidBoardException::class,
            ],
        ];
    }

    /**
     * @dataProvider providerCards
     * @covers       Board::cards
     * @param string $board
     * @param array $expect
     * @param class-string<Throwable>|null $exception
     * @throws InvalidBoardException
     */
    public function testCards(string $board, array $expect, ?string $exception): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        $board = new Board($board);

        $this->assertEquals(
            $expect,
            array_map(
                fn(Card $card) => $card->card(),
                $board->cards()
            )
        );
    }
}
