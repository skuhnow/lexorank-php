<?php

namespace SKuhnow\LexoRank\NumeralSystems;

use SKuhnow\LexoRank\Exception\LexoRankException;

class LexoNumeralSystem36 implements ILexoNumeralSystem
{

    private $digits;

    public function __construct()
    {
        $this->digits = str_split('0123456789abcdefghijklmnopqrstuvwxyz');
    }

    public function getBase(): int
    {
        return 36;
    }

    public function getPositiveChar(): string
    {
        return '+';
    }

    public function getNegativeChar(): string
    {
        return '-';
    }

    public function getRadixPointChar(): string
    {
        return ':';
    }

    public function toDigit(string $char): int
    {
        if ($char >= '0' && $char <= '9') {
            return ord($char) - 48;
        }

        if ($char >= 'a' && $char <= 'z') {
            return ord($char) - 97 + 10;
        }

        throw new LexoRankException('Not valid digit: ' . $char);
    }

    public function toChar(int $digit): string
    {
        return $this->digits[$digit];
    }
}
