<?php

namespace SKuhnow\LexoRank;

use SKuhnow\LexoRank\Exception\LexoRankException;
use SKuhnow\LexoRank\NumeralSystems\ILexoNumeralSystem;
use SKuhnow\LexoRank\Utils\StringBuilder;

class LexoInteger
{

    public static function parse(string $strFull, ILexoNumeralSystem $system): LexoInteger
    {
        $str = $strFull;
        $sign = 1;
        if (strpos($strFull, $system->getPositiveChar()) === 0) {
            $str = substr($strFull, 1);
        } else {
            if (strpos($strFull, $system->getNegativeChar()) === 0) {
                $str = substr($strFull, 1);
                $sign = -1;
            }
        }

        $man = [];
        $strIndex = strlen($str) - 1;

        for ($magIndex = 0; $strIndex >= 0; $magIndex++) {
            $man[$magIndex] = $system->toDigit($str[$strIndex]);
            $strIndex--;
        }

        return LexoInteger::make($system, $sign, $man);
    }

    public static function zero(ILexoNumeralSystem $sys): LexoInteger
    {
        return new LexoInteger($sys, 0, LexoInteger::ZERO_MAG);
    }

    public static function one(ILexoNumeralSystem $sys): LexoInteger
    {
        return LexoInteger::make($sys, 1, LexoInteger::ONE_MAG);
    }

    public static function make(ILexoNumeralSystem $sys, int $sign, array $mag): LexoInteger
    {
        for ($actualLength = count($mag); $actualLength > 0 && $mag[$actualLength - 1] === 0; $actualLength--) {
            // ignore
        }

        if ($actualLength === 0) {
            return LexoInteger::zero($sys);
        }

        if ($actualLength === count($mag)) {
            return new LexoInteger($sys, $sign, $mag);
        }

        $nmag = array_fill(0, $actualLength, 0);

        LexoHelper::arrayCopy($mag, 0, $nmag, 0, $actualLength);

        return new LexoInteger($sys, $sign, $nmag);
    }


    const ZERO_MAG = [0];
    const ONE_MAG = [1];
    const NEGATIVE_SIGN = -1;
    const ZERO_SIGN = 0;
    const POSITIVE_SIGN = 1;

    private static function addInternal(ILexoNumeralSystem $sys, array $l, array $r): array
    {
        $estimatedSize = max(count($l), count($r));
        $result = array_fill(0, $estimatedSize, 0);
        $carry = 0;
        for ($i = 0; $i < $estimatedSize; $i++) {
            $lnum = $i < count($l) ? $l[$i] : 0;
            $rnum = $i < count($r) ? $r[$i] : 0;
            $sum = $lnum + $rnum + $carry;
            for ($carry = 0; $sum >= $sys->getBase(); $sum -= $sys->getBase()) {
                $carry++;
            }

            $result[$i] = $sum;
        }

        return LexoInteger::extendWithCarry($result, $carry);
    }

    private static function extendWithCarry(array $mag, int $carry): array
    {
        if ($carry > 0) {
            $extendedMag = array_fill(0, count($mag) + 1, 0 );
            LexoHelper::arrayCopy($mag, 0, $extendedMag, 0, count($mag));
            $extendedMag[count($extendedMag) - 1] = $carry;

            return $extendedMag;
        }

        return $mag;
    }

    private static function subtractInternal(ILexoNumeralSystem $sys, array $l, array $r): array
    {
        $rComplement = LexoInteger::complementInternal($sys, $r, count($l));
        $rSum = LexoInteger::addInternal($sys, $l, $rComplement);
        $rSum[count($rSum) - 1] = 0;

        return LexoInteger::addInternal($sys, $rSum, LexoInteger::ONE_MAG);
    }

    private static function multiplyInternal(ILexoNumeralSystem $sys, array $l, array $r): array
    {
        $result = array_fill(0, count($l) + count($r), 0);
        foreach ($l as $li => $liValue) {
            foreach ($r as $ri => $riValue) {
                $resultIndex = $li + $ri;
                for (
                  $result[$resultIndex] += $liValue * $riValue;
                  $result[$resultIndex] >= $sys->getBase();
                  $result[$resultIndex] -= $sys->getBase()
                ) {
                  $result[$resultIndex + 1]++;
                }
            }
        }

        return $result;
    }

    private static function complementInternal(ILexoNumeralSystem $sys, array $mag, int $digits): array
    {
        if ($digits <= 0) {
            throw new LexoRankException('Expected at least 1 digit');
        }

        $nmag = array_fill(0, $digits, $sys->getBase() - 1);

        for ($i = 0, $iMax = count($mag); $i < $iMax; $i++) {
            $nmag[$i] = $sys->getBase() - 1 - $mag[$i];
        }

        return $nmag;
    }

    private static function compare(array $l, array $r): int
    {
        if (count($l) < count($r)) {
            return -1;
        }

        if (count($l) > count($r)) {
            return 1;
        }

        for ($i = count($l) - 1; $i >= 0; $i--) {
            if ($l[$i] < $r[$i]) {
                return -1;
            }
            if ($l[$i] > $r[$i]) {
                return 1;
            }
        }

        return 0;
    }

    private $sys;
    private $sign;
    private $mag;

    public function __construct(ILexoNumeralSystem $system, int $sign, array $mag)
    {
        $this->sys = $system;
        $this->sign = $sign;
        $this->mag = $mag;
    }

    public function add(LexoInteger $other): LexoInteger
    {
        $this->checkSystem($other);
        if ($this->isZero()) {
            return $other;
        }

        if ($other->isZero()) {
            return $this;
        }

        if ($this->sign !== $other->sign) {

            if ($this->sign === -1) {
                $pos = $this->negate();
                return $pos->subtract($other)->negate();
            }

            $pos = $other->negate();

            return $this->subtract($pos);
        }

        $result = LexoInteger::addInternal($this->sys, $this->mag, $other->mag);

        return LexoInteger::make($this->sys, $this->sign, $result);
    }

    public function subtract(LexoInteger $other): LexoInteger
    {
        $this->checkSystem($other);
        if ($this->isZero()) {
            return $other->negate();
        }

        if ($other->isZero()) {
            return $this;
        }

        if ($this->sign !== $other->sign) {

            if ($this->sign === -1) {
                $negate = $this->negate();
                return $negate->add($other)->negate();
            }

            $negate = $other->negate();

            return $this->add($negate);
        }

        $cmp = LexoInteger::compare($this->mag, $other->mag);
        if ($cmp === 0) {
            return LexoInteger::zero($this->sys);
        }

        return $cmp < 0
            ? LexoInteger::make($this->sys, $this->sign === -1 ? 1 : -1, LexoInteger::subtractInternal($this->sys, $other->mag, $this->mag))
            : LexoInteger::make($this->sys, $this->sign === -1 ? -1 : 1, LexoInteger::subtractInternal($this->sys, $this->mag, $other->mag));
    }

    public function multiply(LexoInteger $other): LexoInteger
    {
        $this->checkSystem($other);
        if ($this->isZero()) {
            return $this;
        }

        if ($other->isZero()) {
            return $other;
        }

        if ($this->isOneish()) {
            return $this->sign === $other->sign
                ? LexoInteger::make($this->sys, 1, $other->mag)
                : LexoInteger::make($this->sys, -1, $other->mag);
        }

        if ($other->isOneish()) {
            return $this->sign === $other->sign
                ? LexoInteger::make($this->sys, 1, $this->mag)
                : LexoInteger::make($this->sys, -1, $this->mag);
        }

        $newMag = LexoInteger::multiplyInternal($this->sys, $this->mag, $other->mag);
        return $this->sign === $other->sign ? LexoInteger::make($this->sys, 1, $newMag) : LexoInteger::make($this->sys, -1, $newMag);
    }

    public function negate(): LexoInteger
    {
        return $this->isZero() ? $this : LexoInteger::make($this->sys, $this->sign === 1 ? -1 : 1, $this->mag);
    }

    public function shiftLeft(int $times = 1): LexoInteger
    {
        if ($times === 0) {
            return $this;
        }

        if ($times < 0) {
            return $this->shiftRight(abs($times));
        }

        $nmag = array_fill(0, count($this->mag) + $times, 0);

        LexoHelper::arrayCopy($this->mag, 0, $nmag, $times, count($this->mag));

        return LexoInteger::make($this->sys, $this->sign, $nmag);
    }

    public function shiftRight(int $times = 1): LexoInteger
    {
        if (count($this->mag) - $times <= 0) {
            return LexoInteger::zero($this->sys);
        }

        $nmag = array_fill(0, count($this->mag) - $times, 0);

        LexoHelper::arrayCopy($this->mag, $times, $nmag, 0, count($nmag));

        return LexoInteger::make($this->sys, $this->sign, $nmag);
    }

    public function complement(): LexoInteger
    {
        return $this->complementDigits(count($this->mag));
    }

    public function complementDigits(int $digits): LexoInteger
    {
        return LexoInteger::make($this->sys, $this->sign, LexoInteger::complementInternal($this->sys, $this->mag, $digits));
    }

    public function isZero(): bool
    {
        return $this->sign === 0 && count($this->mag) === 1 && $this->mag[0] === 0;
    }

    public function isOne(): bool
    {
        return $this->sign === 1 && count($this->mag) === 1 && $this->mag[0] === 1;
    }

    public function getMag(int $index): int
    {
        return $this->mag[$index] ?? $this->mag[$index-1];
    }

    public function compareTo(LexoInteger $other): int
    {
        if ($this === $other) {
            return 0;
        }

        if (!$other) {
            return 1;
        }

        if ($this->sign === -1) {
            if ($other->sign === -1) {
                $cmp = LexoInteger::compare($this->mag, $other->mag);
                if ($cmp === -1) {
                    return 1;
                }
                return $cmp === 1 ? -1 : 0;
            }

            return -1;
        }

        if ($this->sign === 1) {
            return $other->sign === 1 ? LexoInteger::compare($this->mag, $other->mag) : 1;
        }

        if ($other->sign === -1) {
            return 1;
        }

        return $other->sign === 1 ? -1 : 0;
    }

    public function getSystem(): ILexoNumeralSystem {
        return $this->sys;
    }

    public function format(): string
    {
        if ($this->isZero()) {
            return '' . $this->sys->toChar(0);
        }

        $sb = new StringBuilder();
        $var2 = $this->mag;

        foreach ($var2 as $var4Value) {
            $digit = $var4Value;
            $sb->insert(0, $this->sys->toChar($digit));
        }

        if ($this->sign === -1) {
            $sb->insert(0, $this->sys->getNegativeChar());
        }

        return $sb->__toString();
    }

    public function equals(LexoInteger $other): bool
    {
        if ($this === $other) {
            return true;
        }

        if (!$other) {
            return false;
        }

        return $this->sys->getBase() === $other->sys->getBase() && $this->compareTo($other) === 0;
    }

    public function __toString(): string
    {
        return $this->format();
    }

    private function isOneish(): bool
    {
        return count($this->mag) === 1 && $this->mag[0] === 1;
    }

    private function checkSystem(LexoInteger $other)
    {
        if ($this->sys->getBase() !== $other->sys->getBase()) {
            throw new LexoRankException('Expected numbers of same numeral sys');
        }
    }
}
