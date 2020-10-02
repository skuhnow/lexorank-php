<?php

namespace SKlocke\LexoRank;

use Exception;
use SKlocke\LexoRank\NumeralSystems\ILexoNumeralSystem;
use SKlocke\LexoRank\Utils\StringBuilder;

class LexoDecimal
{

    public static function half(ILexoNumeralSystem $sys): LexoDecimal
    {
        $mid = ($sys->getBase() / 2) | 0;
        return LexoDecimal::make(LexoInteger::make($sys, 1, [$mid]), 1);
    }

    public static function parse(
        string $str,
        ILexoNumeralSystem $system
    ): LexoDecimal {
        $partialIndex = strpos($str, $system->getRadixPointChar());
        if (strrpos($str, $system->getRadixPointChar()) !== $partialIndex) {
            throw new Exception('More than one ' . $system->getRadixPointChar());
        }

        if ($partialIndex === false) {
            return LexoDecimal::make(LexoInteger::parse($str, $system), 0);
        }

        $intStr = substr($str, 0, $partialIndex) . substr($str, $partialIndex + 1);
        return LexoDecimal::make(LexoInteger::parse($intStr, $system), strlen($str) - 1 - $partialIndex);
    }

    public static function from(LexoInteger $integer): LexoDecimal
    {
        return LexoDecimal::make($integer, 0);
    }

    public static function make(LexoInteger $integer, int $sig): LexoDecimal
    {
        if ($integer->isZero()) {
            return new LexoDecimal($integer, 0);
        }

        $zeroCount = 0;
        for ($i = 1; $i < $sig && $integer->getMag($i) === 0; $i++) {
            $zeroCount++;
        }

        $newInteger = $integer->shiftRight($zeroCount);
        $newSig = $sig - $zeroCount;

        return new LexoDecimal($newInteger, $newSig);
    }

    private LexoInteger $mag;
    private int $sig;

    private function __construct(LexoInteger $mag, int $sig)
    {
        $this->mag = $mag;
        $this->sig = $sig;
    }

    public function getSystem(): ILexoNumeralSystem
    {
        return $this->mag->getSystem();
    }

    public function add(LexoDecimal $other): LexoDecimal
    {
        $tmag = $this->mag;
        $tsig = $this->sig;
        $omag = $other->mag;

        for ($osig = $other->sig; $tsig < $osig; $tsig++) {
            $tmag = $tmag->shiftLeft();
        }

        while ($tsig > $osig) {
            $omag = $omag->shiftLeft();
            $osig++;
        }

        return LexoDecimal::make($tmag->add($omag), $tsig);
    }

    public function subtract(LexoDecimal $other): LexoDecimal
    {
        $thisMag = $this->mag;
        $thisSig = $this->sig;
        $otherMag = $other->mag;

        for ($otherSig = $other->sig; $thisSig < $otherSig; $thisSig++) {
            $thisMag = $thisMag->shiftLeft();
        }

        while ($thisSig > $otherSig) {
            $otherMag = $otherMag->shiftLeft();
            $otherSig++;
        }

        return LexoDecimal::make($thisMag->subtract($otherMag), $thisSig);
    }

    public function multiply(LexoDecimal $other): LexoDecimal
    {
        return LexoDecimal::make($this->mag->multiply($other->mag), $this->sig + $other->sig);
    }

    public function floor(): LexoInteger
    {
        return $this->mag->shiftRight($this->sig);
    }

    public function ceil(): LexoInteger
    {
        if ($this->isExact()) {
            return $this->mag;
        }

        $floor = $this->floor();
        return $floor->add(LexoInteger::one($floor->getSystem()));
    }

    public function isExact(): bool
    {
        if ($this->sig === 0) {
            return true;
        }

        for ($i = 0; $i < $this->sig; $i++) {
            if ($this->mag->getMag($i) !== 0) {
                return false;
            }
        }

        return true;
    }

    public function getScale(): int
    {
        return $this->sig;
    }

    public function setScale(int $nsig, bool $ceiling = false): LexoDecimal
    {
        if ($nsig >= $this->sig) {
            return $this;
        }

        if ($nsig < 0) {
            $nsig = 0;
        }

        $diff = $this->sig - $nsig;
        $nmag = $this->mag->shiftRight($diff);
        if ($ceiling) {
            $nmag = $nmag->add(LexoInteger::one($nmag->getSystem()));
        }

        return LexoDecimal::make($nmag, $nsig);
    }

    public function compareTo(LexoDecimal $other): int
    {
        if ($this === $other) {
            return 0;
        }

        if (!$other) {
            return 1;
        }

        $tMag = $this->mag;
        $oMag = $other->mag;
        if ($this->sig > $other->sig) {
            $oMag = $oMag->shiftLeft($this->sig - $other->sig);
        } else {
            if ($this->sig < $other->sig) {
                $tMag = $tMag->shiftLeft($other->sig - $this->sig);
            }
        }
        return $tMag->compareTo($oMag);
    }

    public function format(): string
    {
        $intStr = $this->mag->format();
        if ($this->sig === 0) {
            return $intStr;
        }

        $sb = new StringBuilder($intStr);
        $sbStr = (string)$sb;
        $head = $sbStr[0];
        $specialHead =
            $head === $this->mag->getSystem()->getPositiveChar() || $head === $this->mag->getSystem()->getNegativeChar();

        if ($specialHead) {
            $sb->remove(0, 1);
        }

        while ($sb->getLength() < $this->sig + 1) {
            $sb->insert(0, $this->mag->getSystem()->toChar(0));
        }

        $sb->insert($sb->getLength() - $this->sig, $this->mag->getSystem()->getRadixPointChar());

        if ($sb->getLength() - $this->sig === 0) {
            $sb->insert(0, $this->mag->getSystem()->toChar(0));
        }

        if ($specialHead) {
            $sb->insert(0, $head);
        }

        return $sb->__toString();
    }

    public function equals(LexoDecimal $other): bool
    {
        if ($this === $other) {
            return true;
        }

        if (!$other) {
            return false;
        }

        return $this->mag->equals($other->mag) && $this->sig === $other->sig;
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
