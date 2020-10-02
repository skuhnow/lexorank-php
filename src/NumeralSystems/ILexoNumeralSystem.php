<?php

namespace SKuhnow\LexoRank\NumeralSystems;

interface ILexoNumeralSystem {

    public function getBase(): int;

    public function getPositiveChar(): string;

    public function getNegativeChar(): string;

    public function getRadixPointChar(): string;

    public function toDigit(string $var1): int;

    public function toChar(int $var1): string;
}
