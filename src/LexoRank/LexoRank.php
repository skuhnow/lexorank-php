<?php

namespace SKlocke\LexoRank\LexoRank;


use Exception;
use SKlocke\LexoRank\NumeralSystems\LexoNumeralSystem36;
use SKlocke\LexoRank\Utils\StringBuilder;

class LexoRank
{

    private static $_NUMERAL_SYSTEM;
    private static $_ZERO_DECIMAL;
    private static $_ONE_DECIMAL;
    private static $_EIGHT_DECIMAL;
    private static $_MIN_DECIMAL;
    private static $_MAX_DECIMAL;
    private static $_MID_DECIMAL;
    private static $_INITIAL_MIN_DECIMAL;
    private static $_INITIAL_MAX_DECIMAL;

    public static function NUMERAL_SYSTEM()
    {
        if (!self::$_NUMERAL_SYSTEM) {
            self::$_NUMERAL_SYSTEM = new LexoNumeralSystem36();
        }
        return self::$_NUMERAL_SYSTEM;
    }

    private static function ZERO_DECIMAL(): LexoDecimal
    {
        if (!self::$_ZERO_DECIMAL) {
            self::$_ZERO_DECIMAL = LexoDecimal::parse('0', LexoRank::NUMERAL_SYSTEM());
        }

        return self::$_ZERO_DECIMAL;
    }

    private static function ONE_DECIMAL(): LexoDecimal
    {
        if (!self::$_ONE_DECIMAL) {
            self::$_ONE_DECIMAL = LexoDecimal::parse('1', LexoRank::NUMERAL_SYSTEM());
        }

        return self::$_ONE_DECIMAL;
    }

    private static function EIGHT_DECIMAL(): LexoDecimal
    {
        if (!self::$_EIGHT_DECIMAL) {
            self::$_EIGHT_DECIMAL = LexoDecimal::parse('8', LexoRank::NUMERAL_SYSTEM());
        }

        return self::$_EIGHT_DECIMAL;
    }

    private static function MIN_DECIMAL(): LexoDecimal
    {
        if (!self::$_MIN_DECIMAL) {
            self::$_MIN_DECIMAL = LexoRank::ZERO_DECIMAL();
        }

        return self::$_MIN_DECIMAL;
    }

    private static function MAX_DECIMAL(): LexoDecimal
    {
        if (!self::$_MAX_DECIMAL) {
            self::$_MAX_DECIMAL = LexoDecimal::parse('1000000', LexoRank::NUMERAL_SYSTEM())->subtract(
                LexoRank::ONE_DECIMAL()
            );
        }

        return self::$_MAX_DECIMAL;
    }

    private static function MID_DECIMAL(): LexoDecimal
    {
        if (!self::$_MID_DECIMAL) {
            self::$_MID_DECIMAL = LexoRank::betweenStatic(LexoRank::MIN_DECIMAL(), LexoRank::MAX_DECIMAL());
        }

        return self::$_MID_DECIMAL;
    }

    private static function INITIAL_MIN_DECIMAL(): LexoDecimal
    {
        if (!self::$_INITIAL_MIN_DECIMAL) {
            self::$_INITIAL_MIN_DECIMAL = LexoDecimal::parse('100000', LexoRank::NUMERAL_SYSTEM());
        }

        return self::$_INITIAL_MIN_DECIMAL;
    }

    private static function INITIAL_MAX_DECIMAL(): LexoDecimal
    {
        if (!self::$_INITIAL_MAX_DECIMAL) {
            self::$_INITIAL_MAX_DECIMAL = LexoDecimal::parse(
                LexoRank::NUMERAL_SYSTEM()->toChar(LexoRank::NUMERAL_SYSTEM()->getBase() - 2) . '00000',
                LexoRank::NUMERAL_SYSTEM(),
            );
        }

        return self::$_INITIAL_MAX_DECIMAL;
    }

    public static function min(): LexoRank
    {
        return LexoRank::from(LexoRankBucket::BUCKET_0(), LexoRank::MIN_DECIMAL());
    }

    public static function middle(): LexoRank
    {
        $minLexoRank = LexoRank::min();
        return $minLexoRank->between(LexoRank::max($minLexoRank->bucket));
    }

    public static function max(LexoRankBucket $bucket = null): LexoRank
    {
        if ($bucket === null) {
            $bucket = LexoRankBucket::BUCKET_0();
        }
        return LexoRank::from($bucket, LexoRank::MAX_DECIMAL());
    }

    public static function initial(LexoRankBucket $bucket): LexoRank
    {
        return $bucket === LexoRankBucket::BUCKET_0()
            ? LexoRank::from($bucket, LexoRank::INITIAL_MIN_DECIMAL())
            : LexoRank::from($bucket, LexoRank::INITIAL_MAX_DECIMAL());
    }

    public static function betweenStatic(LexoDecimal $oLeft, LexoDecimal $oRight): LexoDecimal
    {
        if ($oLeft->getSystem()->getBase() !== $oRight->getSystem()->getBase()) {
            throw new Exception('Expected same system');
        }

        $left = $oLeft;
        $right = $oRight;

        if ($oLeft->getScale() < $oRight->getScale()) {
            $nLeft = $oRight->setScale($oLeft->getScale(), false);
            if ($oLeft->compareTo($nLeft) >= 0) {
                return LexoRank::mid($oLeft, $oRight);
            }

            $right = $nLeft;
        }

        if ($oLeft->getScale() > $right->getScale()) {
            $nLeft = $oLeft->setScale($right->getScale(), true);
            if ($nLeft->compareTo($right) >= 0) {
                return LexoRank::mid($oLeft, $oRight);
            }

            $left = $nLeft;
        }


        // TODO ???
        $nRight = null;
        for ($scale = $left->getScale(); $scale > 0; $right = $nRight) {
            $nScale1 = $scale - 1;
            $nLeft1 = $left->setScale($nScale1, true);
            $nRight = $right->setScale($nScale1, false);
            $cmp = $nLeft1->compareTo($nRight);
            if ($cmp === 0) {
                return LexoRank::checkMid($oLeft, $oRight, $nLeft1);
            }
            if ($nLeft1->compareTo($nRight) > 0) {
                break;
            }

            $scale = $nScale1;
            $left = $nLeft1;
        }

        $mid = LexoRank::middleInternal($oLeft, $oRight, $left, $right);

        // TODO ???
        $nScale = 0;
        for ($mScale = $mid->getScale(); $mScale > 0; $mScale = $nScale) {
            $nScale = $mScale - 1;
            $nMid = $mid->setScale($nScale);
            if ($oLeft->compareTo($nMid) >= 0 || $nMid->compareTo($oRight) >= 0) {
                break;
            }

            $mid = $nMid;
        }

        return $mid;
    }

    public static function parse(string $str): LexoRank
    {
        $parts = explode('|', $str);
        $bucket = LexoRankBucket::from($parts[0]);
        $decimal = LexoDecimal::parse($parts[1], LexoRank::NUMERAL_SYSTEM());

        return new LexoRank($bucket, $decimal);
    }

    public static function from(LexoRankBucket $bucket, LexoDecimal $decimal): LexoRank
    {
        if ($decimal->getSystem()->getBase() !== LexoRank::NUMERAL_SYSTEM()->getBase()) {
            throw new Exception('Expected different system');
        }

        return new LexoRank($bucket, $decimal);
    }

    private static function middleInternal(
        LexoDecimal $lbound,
        LexoDecimal $rbound,
        LexoDecimal $left,
        LexoDecimal $right
    ): LexoDecimal {
        $mid = LexoRank::mid($left, $right);

        return LexoRank::checkMid($lbound, $rbound, $mid);
    }

    private static function checkMid(LexoDecimal $lbound, LexoDecimal $rbound, LexoDecimal $mid): LexoDecimal
    {
        if ($lbound->compareTo($mid) >= 0) {
            return LexoRank::mid($lbound, $rbound);
        }

        return $mid->compareTo($rbound) >= 0 ? LexoRank::mid($lbound, $rbound) : $mid;
    }

    private static function mid(LexoDecimal $left, LexoDecimal $right): LexoDecimal
    {
        $sum = $left->add($right);
        $mid = $sum->multiply(LexoDecimal::half($left->getSystem()));
        $scale = $left->getScale() > $right->getScale() ? $left->getScale() : $right->getScale();
        if ($mid->getScale() > $scale) {
            $roundDown = $mid->setScale($scale, false);
            if ($roundDown->compareTo($left) > 0) {
                return $roundDown;
            }
            $roundUp = $mid->setScale($scale, true);
            if ($roundUp->compareTo($right) < 0) {
                return $roundUp;
            }
        }
        return $mid;
    }

    private static function formatDecimal(LexoDecimal $decimal): string
    {
        $formatVal = $decimal->format();
        $val = new StringBuilder($formatVal);
        $partialIndex = strpos($formatVal, LexoRank::NUMERAL_SYSTEM()->getRadixPointChar());
        $zero = LexoRank::NUMERAL_SYSTEM()->toChar(0);
        if ($partialIndex < 0) {
            $partialIndex = strlen($formatVal);
            $val->append(LexoRank::NUMERAL_SYSTEM()->getRadixPointChar());
        }

        while ($partialIndex < 6) {
            $val->insert(0, $zero);
            ++$partialIndex;
        }

        while ($val[$val->getLength() - 1] === $zero) {
            $val->setLength($val->getLength() - 1);
        }

        return $val->__toString();
    }

    private string $value;
    private LexoRankBucket $bucket;
    private LexoDecimal $decimal;

    public function __construct(LexoRankBucket $bucket, LexoDecimal $decimal)
    {
        $this->value = $bucket->format() . '|' . LexoRank::formatDecimal($decimal);
        $this->bucket = $bucket;
        $this->decimal = $decimal;
    }

    public function genPrev(): LexoRank
    {
        if ($this->isMax()) {
            return new LexoRank($this->bucket, LexoRank::INITIAL_MAX_DECIMAL());
        }

        $floorInteger = $this->decimal->floor();
        $floorDecimal = LexoDecimal::from($floorInteger);
        $nextDecimal = $floorDecimal->subtract(LexoRank::EIGHT_DECIMAL());
        if ($nextDecimal->compareTo(LexoRank::MIN_DECIMAL()) <= 0) {
            $nextDecimal = LexoRank::betweenStatic(LexoRank::MIN_DECIMAL(), $this->decimal);
        }

        return new LexoRank($this->bucket, $nextDecimal);
    }

    public function genNext(): LexoRank
    {
        if ($this->isMin()) {
            return new LexoRank($this->bucket, LexoRank::INITIAL_MIN_DECIMAL());
        }
        $ceilInteger = $this->decimal->ceil();
        $ceilDecimal = LexoDecimal::from($ceilInteger);
        $nextDecimal = $ceilDecimal->add(LexoRank::EIGHT_DECIMAL());
        if ($nextDecimal->compareTo(LexoRank::MAX_DECIMAL()) >= 0) {
            $nextDecimal = LexoRank::betweenStatic($this->decimal, LexoRank::MAX_DECIMAL());
        }

        return new LexoRank($this->bucket, $nextDecimal);
    }

    public function between(LexoRank $other): LexoRank
    {
        if (!$this->bucket->equals($other->bucket)) {
            throw new Exception('Between works only within the same bucket');
        }

        $cmp = $this->decimal->compareTo($other->decimal);
        if ($cmp > 0) {
            return new LexoRank($this->bucket, LexoRank::betweenStatic($other->decimal, $this->decimal));
        }

        if ($cmp === 0) {
            throw new Exception(
                'Try to rank between issues with same rank this=' .
                $this .
                ' other=' .
                $other .
                ' this.decimal=' .
                $this->decimal .
                ' other.decimal=' .
                $other->decimal,
            );
        }

        return new LexoRank($this->bucket, LexoRank::betweenStatic($this->decimal, $other->decimal));
    }

    public function getBucket(): LexoRankBucket
    {
        return $this->bucket;
    }

    public function getDecimal(): LexoDecimal
    {
        return $this->decimal;
    }

    public function inNextBucket(): LexoRank
    {
        return LexoRank::from($this->bucket->next(), $this->decimal);
    }

    public function inPrevBucket(): LexoRank
    {
        return LexoRank::from($this->bucket->prev(), $this->decimal);
    }

    public function isMin(): bool
    {
        return $this->decimal->equals(LexoRank::MIN_DECIMAL());
    }

    public function isMax(): bool
    {
        return $this->decimal->equals(LexoRank::MAX_DECIMAL());
    }

    public function format(): string
    {
        return $this->value;
    }

    public function equals(LexoRank $other): bool
    {
        if ($this === $other) {
            return true;
        }

        if (!$other) {
            return false;
        }

        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function compareTo(LexoRank $other): int
    {
        if ($this === $other) {
            return 0;
        }

        if (!$other) {
            return 1;
        }

        return ($this->value === $other->value) ? 0 : -1;
    }
}
