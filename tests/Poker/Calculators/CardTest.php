<?php

declare(strict_types=1);

namespace Tests\Poker\Calculators;

use PHPUnit\Framework\TestCase;
use Poker\Calculators\Card;
use Poker\Exceptions\InvalidCardException;
use Throwable;

/**
 * Class CardTest
 * @package Tests\Poker\Calculators
 */
final class CardTest extends TestCase
{
    /**
     * @return array[]
     */
    public function providerCard(): array
    {
        return [
            'empty card' => [
                'card' => '',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'card without suit' => [
                'card' => 'A',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'card without value' => [
                'card' => 's',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'invalid card 1' => [
                'card' => 'AA',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'invalid card 2' => [
                'card' => 'random',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'As' => [
                'card' => 'As',
                'expect' => 'As',
                'exception' => null,
            ],
            '2d' => [
                'card' => '2d',
                'expect' => '2d',
                'exception' => null,
            ],
        ];
    }

    /**
     * @dataProvider providerCard
     * @covers       Card::card
     * @param string $card
     * @param string $expect
     * @param class-string<Throwable>|null $exception
     * @throws InvalidCardException
     */
    public function testCard(string $card, string $expect, ?string $exception): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        $card = new Card($card);

        $this->assertEquals($expect, $card->card());
    }

    /**
     * @return array[]
     */
    public function providerValue(): array
    {
        return [
            'empty card' => [
                'card' => '',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'card without suit' => [
                'card' => 'A',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'card without value' => [
                'card' => 's',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'invalid card 1' => [
                'card' => 'AA',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'invalid card 2' => [
                'card' => 'random',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'As' => [
                'card' => 'As',
                'expect' => 'A',
                'exception' => null,
            ],
            '2d' => [
                'card' => '2d',
                'expect' => '2',
                'exception' => null,
            ],
        ];
    }

    /**
     * @dataProvider providerValue
     * @covers       Card::value
     * @param string $card
     * @param string $expect
     * @param class-string<Throwable>|null $exception
     * @throws InvalidCardException
     */
    public function testValue(string $card, string $expect, ?string $exception): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        $card = new Card($card);

        $this->assertEquals($expect, $card->value());
    }

    /**
     * @return array[]
     */
    public function providerSuit(): array
    {
        return [
            'empty card' => [
                'card' => '',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'card without suit' => [
                'card' => 'A',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'card without value' => [
                'card' => 's',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'invalid card 1' => [
                'card' => 'AA',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'invalid card 2' => [
                'card' => 'random',
                'expect' => '',
                'exception' => InvalidCardException::class,
            ],
            'As' => [
                'card' => 'As',
                'expect' => 's',
                'exception' => null,
            ],
            '2d' => [
                'card' => '2d',
                'expect' => 'd',
                'exception' => null,
            ],
        ];
    }

    /**
     * @dataProvider providerSuit
     * @covers       Card::suit
     * @param string $card
     * @param string $expect
     * @param class-string<Throwable>|null $exception
     * @throws InvalidCardException
     */
    public function testSuit(string $card, string $expect, ?string $exception): void
    {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        $card = new Card($card);

        $this->assertEquals($expect, $card->suit());
    }
}
