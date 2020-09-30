<?php

namespace SKlocke\LexoRank\NumeralSystems;

use Exception;

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

    public function toDigit(string $var1): int
    {
        if ($var1 >= '0' && $var1 <= '9') {
            return ord($var1) - 48;
        }

        throw new Exception(sprintf("Not valid digit: %s", $var1));
    }

    public function toChar(int $digit): string
    {
        return chr($digit + 48);
    }
}
