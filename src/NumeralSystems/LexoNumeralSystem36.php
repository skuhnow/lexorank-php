<?php

namespace SKuhnow\LexoRank\NumeralSystems;

use Exception;

class LexoNumeralSystem36 implements ILexoNumeralSystem
{

    private array $digits;

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

    public function toDigit(string $ch): int
    {
        if ($ch >= '0' && $ch <= '9') {
            return ord($ch) - 48;
        }

        if ($ch >= 'a' && $ch <= 'z') {
            return ord($ch) - 97 + 10;
        }

        throw new Exception('Not valid digit: ' . $ch);
    }

    public function toChar(int $digit): string
    {
        return $this->digits[$digit];
    }
}
