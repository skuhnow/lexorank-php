<?php

namespace SKuhnow\LexoRank\NumeralSystems;

interface ILexoNumeralSystem {

    public function getBase(): int;

    public function getPositiveChar(): string;

    public function getNegativeChar(): string;

    public function getRadixPointChar(): string;

    public function toDigit(string $char): int;

    public function toChar(int $digit): string;
}
