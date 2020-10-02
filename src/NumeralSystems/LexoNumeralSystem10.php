<?php

namespace SKuhnow\LexoRank\NumeralSystems;

use SKuhnow\LexoRank\Exception\LexoRankException;

class LexoNumeralSystem10 implements ILexoNumeralSystem
{

    public function getBase(): int
    {
        return 10;
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
        return '.';
    }

    public function toDigit(string $char): int
    {
        if ($char >= '0' && $char <= '9') {
            return ord($char) - 48;
        }

        throw new LexoRankException(sprintf("Not valid digit: %s", $char));
    }

    public function toChar(int $digit): string
    {
        return chr($digit + 48);
    }
}
