<?php

declare(strict_types=1);

namespace Poker\Calculators;

use Poker\Exceptions\InvalidBoardException;
use Poker\Exceptions\InvalidCardException;

/**
 * Class Board
 * @package Poker\Calculators
 */
final class Board
{
    public const MAX_BOARD_CARDS = 5;

    /**
     * @var Card[]
     */
    private array $cards;

    /**
     * @param string $board
     * @throws InvalidBoardException
     */
    public function __construct(string $board)
    {
        $this->cards = $this->getCards($board);
        if (count($this->cards) > self::MAX_BOARD_CARDS) {
            throw new InvalidBoardException('The maximum number of cards on the board is ' . self::MAX_BOARD_CARDS);
        }
    }

    /**
     * @return string
     */
    public function board(): string
    {
        return implode('', array_map(
            fn(Card $card) => $card->card(),
            $this->cards
        ));
    }

    /**
     * @return Card[]
     */
    public function cards(): array
    {
        return $this->cards;
    }

    /**
     * @param string $board
     * @return Card[]
     * @throws InvalidBoardException
     */
    private function getCards(string $board): array
    {
        $result = [];

        if (!empty($board)) {
            foreach (str_split($board, 2) as $card) {
                try {
                    $result[] = new Card($card);
                } catch (InvalidCardException $exception) {
                    throw new InvalidBoardException("Board {$board} invalid, eg AsAcKd");
                }
            }
        }

        return $result;
    }
}
