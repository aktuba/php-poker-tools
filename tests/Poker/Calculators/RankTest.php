<?php

declare(strict_types=1);

namespace Tests\Poker\Calculators;

use PHPUnit\Framework\TestCase;
use Poker\Calculators\Board;
use Poker\Calculators\Card;
use Poker\Calculators\Combinations;
use Poker\Calculators\Hand;
use Poker\Calculators\Rank;
use Poker\Exceptions\InvalidBoardException;
use Poker\Exceptions\InvalidCardException;
use Poker\Exceptions\InvalidHandException;
use Poker\Exceptions\InvalidRankException;
use Poker\Exceptions\RuntimeException;
use Throwable;

/**
 * Class RankTest
 * @package Tests\Poker\Calculators
 */
final class RankTest extends TestCase
{
    /**
     * @return array[]
     */
    public function providerRank(): array
    {
        return [
            'empty board, empty cards, empty hand, countCards = 0' => [
                'board' => '',
                'cards' => '',
                'hand' => '',
                'countCards' => 0,
                'exception' => InvalidHandException::class,
                'expectCombination' => '',
                'expectValues' => '',
                'expectRank' => 0,
            ],
            'empty board, empty cards, empty hand, countCards = 2' => [
                'board' => '',
                'cards' => '',
                'hand' => '',
                'countCards' => 2,
                'exception' => InvalidHandException::class,
                'expectCombination' => '',
                'expectValues' => '',
                'expectRank' => 0,
            ],
            'empty board, empty cards, hand AsAd, countCards = 0' => [
                'board' => '',
                'cards' => '',
                'hand' => 'AsAd',
                'countCards' => 0,
                'exception' => InvalidHandException::class,
                'expectCombination' => '',
                'expectValues' => '',
                'expectRank' => 0,
            ],
            'empty board, empty cards, hand AsAd, countCards = 1' => [
                'board' => '',
                'cards' => '',
                'hand' => 'AsAd',
                'countCards' => 1,
                'exception' => InvalidHandException::class,
                'expectCombination' => '',
                'expectValues' => '',
                'expectRank' => 0,
            ],
            'empty board, empty cards, hand AsAd, countCards = 2' => [
                'board' => '',
                'cards' => '',
                'hand' => 'AsAd',
                'countCards' => 2,
                'exception' => InvalidRankException::class,
                'expectCombination' => '',
                'expectValues' => '',
                'expectRank' => 0,
            ],
            'empty board, 2s2d cards, hand AsAd, countCards = 2' => [
                'board' => '',
                'cards' => '2s2d',
                'hand' => 'AsAd',
                'countCards' => 2,
                'exception' => InvalidRankException::class,
                'expectCombination' => '',
                'expectValues' => '',
                'expectRank' => 0,
            ],
            'empty board, 2s3d6d7hTc cards, hand Ks2d, countCards = 2' => [
                'board' => '',
                'cards' => '2s3d6d7hTc',
                'hand' => 'Ks2d',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::ONE_PAIR,
                'expectValues' => 'KT722',
                'expectRank' => 558362558466,
            ],
            'empty board, 2s3d6d7hTc cards, hand AsAd, countCards = 2' => [
                'board' => '',
                'cards' => '2s3d6d7hTc',
                'hand' => 'AsAd',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::ONE_PAIR,
                'expectValues' => 'AAT76',
                'expectRank' => 687211581440,
            ],
            '2s3d6d7hTc board, empty cards, hand AsKd, countCards = 2' => [
                'board' => '2s3d6d7hTc',
                'cards' => '',
                'hand' => 'AsKd',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::HIGH_CARD,
                'expectValues' => 'AKT76',
                'expectRank' => 77326225408,
            ],
            '2s3d6d7hTc board, empty cards, hand AsAd, countCards = 2' => [
                'board' => '2s3d6d7hTc',
                'cards' => '',
                'hand' => 'AsAd',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::ONE_PAIR,
                'expectValues' => 'AAT76',
                'expectRank' => 687211581440,
            ],
            '2s3d6d board, 7hTc cards, hand AsAd, countCards = 2' => [
                'board' => '2s3d6d',
                'cards' => '7hTc',
                'hand' => 'AsAd',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::ONE_PAIR,
                'expectValues' => 'AAT76',
                'expectRank' => 687211581440,
            ],
            '2s3d6d board, 7hTc8dKs cards, hand AsAd, countCards = 2' => [
                'board' => '2s3d6d',
                'cards' => '7hTc8dKs',
                'hand' => 'AsAd',
                'countCards' => 2,
                'exception' => InvalidRankException::class,
                'expectCombination' => '',
                'expectValues' => '',
                'expectRank' => 0,
            ],
            '2s2d6d board, 7hTc cards, hand AsAd, countCards = 2' => [
                'board' => '2s2d6d',
                'cards' => '7hTc',
                'hand' => 'AsAd',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::TWO_PAIR,
                'expectValues' => 'AAT22',
                'expectRank' => 1236967358466,
            ],
            '2s2d6d board, 7hTc cards, hand As2h, countCards = 2' => [
                'board' => '2s2d6d',
                'cards' => '7hTc',
                'hand' => 'As2h',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::THREE_OF_A_KIND,
                'expectValues' => 'AT222',
                'expectRank' => 1718003695619,
            ],
            'As2d6d board, 7hTc cards, hand 2s2h, countCards = 2' => [
                'board' => 'As2d6d',
                'cards' => '7hTc',
                'hand' => '2s2h',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::THREE_OF_A_KIND,
                'expectValues' => 'AT222',
                'expectRank' => 1718003695619,
            ],
            '2s3d4d board, KhKc cards, hand As5d, countCards = 2' => [
                'board' => '2s3d4d',
                'cards' => 'KhKc',
                'hand' => 'As5d',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::STRAIGHT,
                'expectValues' => '5432A',
                'expectRank' => 2267742732873,
            ],
            '2s3d4d board, 6hKc cards, hand As5d, countCards = 2' => [
                'board' => '2s3d4d',
                'cards' => '6hKc',
                'hand' => 'As5d',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::STRAIGHT,
                'expectValues' => '65432',
                'expectRank' => 2199023260233,
            ],
            '2s3d4d board, 6h7c cards, hand As5d, countCards = 2' => [
                'board' => '2s3d4d',
                'cards' => '6h7c',
                'hand' => 'As5d',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::STRAIGHT,
                'expectValues' => '76543',
                'expectRank' => 2199023293000,
            ],
            '7sJdTd board, KhQc cards, hand As5d, countCards = 2' => [
                'board' => '7sJdTd',
                'cards' => 'KhQc',
                'hand' => 'As5d',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::STRAIGHT,
                'expectValues' => 'AKQJT',
                'expectRank' => 2277557403648,
            ],
            '7s3s2s board, KsQc cards, hand As5d, countCards = 2' => [
                'board' => '7s3s2s',
                'cards' => 'KsQc',
                'hand' => 'As5d',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::FLUSH,
                'expectValues' => 'AK732',
                'expectRank' => 2826088513545,
            ],
            '7h3d2s board, 3s2c cards, hand As3h, countCards = 2' => [
                'board' => '7h3d2s',
                'cards' => '3s2c',
                'hand' => 'As3h',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::FULL_HOUSE,
                'expectValues' => '33322',
                'expectRank' => 3298534883354,
            ],
            '7h3d2s board, 3s3c cards, hand As3h, countCards = 2' => [
                'board' => '7h3d2s',
                'cards' => '3s3c',
                'hand' => 'As3h',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::FOUR_OF_A_KIND,
                'expectValues' => 'A3333',
                'expectRank' => 3917010173984,
            ],
            '9s8s3c board, 7sKd cards, hand Ts6s, countCards = 2' => [
                'board' => '9s8s3c',
                'cards' => '7sKd',
                'hand' => 'Ts6s',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::STRAIGHT_FLUSH,
                'expectValues' => 'T9876',
                'expectRank' => 4398065684480,
            ],
            'KsTs3d board, Js9h cards, hand AsQs, countCards = 2' => [
                'board' => 'KsTs3d',
                'cards' => 'Js9h',
                'hand' => 'AsQs',
                'countCards' => 2,
                'exception' => null,
                'expectCombination' => Combinations::ROYAL_FLUSH,
                'expectValues' => 'AKQJT',
                'expectRank' => 5026336473088,
            ],
        ];
    }

    /**
     * @dataProvider providerRank
     * @covers       Rank::combination
     * @covers       Rank::values
     * @covers       Rank::rank
     *
     * @param string $board
     * @param string $cards
     * @param string $hand
     * @param int $countCards
     * @param class-string<Throwable>|null $exception
     * @param string $expectCombination
     * @param string $expectValues
     * @param int $expectRank
     *
     * @throws InvalidBoardException
     * @throws InvalidCardException
     * @throws InvalidHandException
     * @throws InvalidRankException
     * @throws RuntimeException
     */
    public function testRank(
        string $board,
        string $cards,
        string $hand,
        int $countCards,
        ?string $exception,
        string $expectCombination,
        string $expectValues,
        int $expectRank
    ): void {
        if ($exception !== null) {
            $this->expectException($exception);
        }

        $preparedCards = [];
        if (!empty($cards)) {
            $preparedCards = array_map(
                fn(string $card) => new Card($card),
                str_split($cards, 2)
            );
        }

        $rank = new Rank(
            new Board($board),
            $preparedCards,
            new Hand($hand, $countCards)
        );

        $this->assertEquals($expectCombination, $rank->combination());
        $this->assertEquals($expectValues, implode('', $rank->values()));
        $this->assertEquals($expectRank, $rank->rank());
    }
}
