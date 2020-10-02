<?php

namespace SKuhnow\LexoRank\NumeralSystems;

use SKuhnow\LexoRank\Exception\LexoRankException;

class LexoNumeralSystem64 implements ILexoNumeralSystem
{

    private $digits;

    public function __construct()
    {
        $this->digits = str_split('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ^_abcdefghijklmnopqrstuvwxyz');
    }

    public function getBase(): int
    {
        return 64;
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

        if ($char >= 'A' && $char <= 'Z') {
            return ord($char) - 65 + 10;
        }

        if ($char === '^') {
            return 36;
        }

        if ($char === '_') {
            return 37;
        }

        if ($char >= 'a' && $char <= 'z') {
            return ord($char) - 97 + 38;
        }

        throw new LexoRankException('Not valid digit: ' . $char);
    }

    public function toChar(int $digit): string
    {
        return $this->digits[$digit];
    }
}
