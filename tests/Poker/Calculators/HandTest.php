<?php

declare(strict_types=1);

namespace Tests\Poker\Calculators;

use PHPUnit\Framework\TestCase;
use Poker\Calculators\Card;
use Poker\Calculators\Hand;
use Poker\Exceptions\InvalidHandException;
use Throwable;

/**
 * Class HandTest
 * @package Tests\Poker\Calculators
 */
final class HandTest extends TestCase
{
    /**
     * @return array[]
     */
    public function providerHand(): array
    {
        return [
            'holdem empty hand' => [
                'hand' => '',
                'count' => Hand::COUNT_CARDS_HOLDEM,
                'expect' => '',
                'exception' => InvalidHandException::class,
            ],
            'holdem one card' => [
                'hand' => 'As',
                'count' => Hand::COUNT_CARDS_HOLDEM,
                'expect' => '',
                'exception' => InvalidHandException::class,
            ],
            'holdem two cards' => [
                'hand' => 'AsKs',
                'count' => Hand::COUNT_CARDS_HOLDEM,
                'expect' => 'AsKs',
                'exception' => null,
            ],
            'holdem four cards' => [
                'hand' => 'AsKsQhJd',
                'count' => Hand::COUNT_CARDS_HOLDEM,
                'expect' => '',
                'exception' => InvalidHandException::class,
            ],
        ];
    }

    /**
     * @dataProvider providerHand
     * @covers       Hand::hand
     * @param string $hand
     * @param int $count
     * @param string $expect
     * @param class-string<Throwable>|null $exception
     * @throws InvalidHandException
     */
    public function testHand(string $hand, int $count, string $expect, ?string $exception): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        $hand = new Hand($hand, $count);

        $this->assertEquals($expect, $hand->hand());
    }

    /**
     * @return array[]
     */
    public function providerCards(): array
    {
        return [
            'holdem empty hand' => [
                'hand' => '',
                'count' => Hand::COUNT_CARDS_HOLDEM,
                'expect' => [],
                'exception' => InvalidHandException::class,
            ],
            'holdem one card' => [
                'hand' => 'As',
                'count' => Hand::COUNT_CARDS_HOLDEM,
                'expect' => [],
                'exception' => InvalidHandException::class,
            ],
            'holdem two cards' => [
                'hand' => 'AsKs',
                'count' => Hand::COUNT_CARDS_HOLDEM,
                'expect' => ['As', 'Ks'],
                'exception' => null,
            ],
            'holdem four cards' => [
                'hand' => 'AsKsQhJd',
                'count' => Hand::COUNT_CARDS_HOLDEM,
                'expect' => [],
                'exception' => InvalidHandException::class,
            ],
        ];
    }

    /**
     * @dataProvider providerCards
     * @covers       Hand::cards
     * @param string $hand
     * @param int $count
     * @param array $expect
     * @param class-string<Throwable>|null $exception
     * @throws InvalidHandException
     */
    public function testCards(string $hand, int $count, array $expect, ?string $exception): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        $hand = new Hand($hand, $count);

        $this->assertEquals(
            $expect,
            array_map(
                fn(Card $card) => $card->card(),
                $hand->cards()
            )
        );
    }
}
