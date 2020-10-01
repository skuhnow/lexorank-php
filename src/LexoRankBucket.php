<?php

namespace SKlocke\LexoRank;

use Exception;

class LexoRankBucket
{

    private static $_BUCKET_0;
    private static $_BUCKET_1;
    private static $_BUCKET_2;
    private static $_VALUES;

    public static function BUCKET_0(): LexoRankBucket
    {
        if (!self::$_BUCKET_0) {
            self::$_BUCKET_0 = new LexoRankBucket('0');
        }

        return self::$_BUCKET_0;
    }

    public static function BUCKET_1(): LexoRankBucket
    {
        if (!self::$_BUCKET_1) {
            self::$_BUCKET_1 = new LexoRankBucket('1');
        }

        return self::$_BUCKET_1;
    }

    public static function BUCKET_2(): LexoRankBucket
    {
        if (!self::$_BUCKET_2) {
            self::$_BUCKET_2 = new LexoRankBucket('2');
        }

        return self::$_BUCKET_2;
    }


    /**
     * @return LexoRankBucket[]
     */
    private static function VALUES(): array
    {
        if (!self::$_VALUES) {
            self::$_VALUES = [LexoRankBucket::BUCKET_0(), LexoRankBucket::BUCKET_1(), LexoRankBucket::BUCKET_2()];
        }

        return self::$_VALUES;
    }

    public static function max(): LexoRankBucket
    {
        return LexoRankBucket::VALUES()[count(LexoRankBucket::VALUES()) - 1];
    }

    public static function from(string $str): LexoRankBucket
    {
        $val = LexoInteger::parse($str, LexoRank::NUMERAL_SYSTEM());
        $var2 = LexoRankBucket::VALUES();
        $var3 = count($var2);
        for ($var4 = 0; $var4 < $var3; $var4++) {
            $bucket = $var2[$var4];
            if ($bucket->value->equals($val)) {
                return $bucket;
            }
        }

        throw new Exception('Unknown bucket: ' . $str);
    }

    public static function resolve(int $bucketId): LexoRankBucket
    {
        $var1 = LexoRankBucket::VALUES();
        $var2 = count($var1);
        for ($var3 = 0; $var3 < $var2; $var3++) {
            $bucket = $var1[$var3];
            if ($bucket->equals(LexoRankBucket::from($bucketId))) {
                return $bucket;
            }
        }

        throw new Exception('No bucket found with id ' . $bucketId);
    }


    private LexoInteger $value;

    private function __construct(string $val)
    {
        $this->value = LexoInteger::parse($val, LexoRank::NUMERAL_SYSTEM());
    }

    public function format(): string
    {
        return $this->value->format();
    }

    public function next(): LexoRankBucket
    {
        if ($this->equals(LexoRankBucket::BUCKET_0())) {
            return LexoRankBucket::BUCKET_1();
        }
        if ($this->equals(LexoRankBucket::BUCKET_1())) {
            return LexoRankBucket::BUCKET_2();
        }
        return $this->equals(LexoRankBucket::BUCKET_2()) ? LexoRankBucket::BUCKET_0() : LexoRankBucket::BUCKET_2();
    }

    public function prev(): LexoRankBucket
    {
        if ($this->equals(LexoRankBucket::BUCKET_0())) {
            return LexoRankBucket::BUCKET_2();
        }
        if ($this->equals(LexoRankBucket::BUCKET_1())) {
            return LexoRankBucket::BUCKET_0();
        }
        return $this->equals(LexoRankBucket::BUCKET_2()) ? LexoRankBucket::BUCKET_1() : LexoRankBucket::BUCKET_0();
    }

    public function equals(LexoRankBucket $other): bool
    {
        if ($this === $other) {
            return true;
        }

        if (!$other) {
            return false;
        }

        return $this->value->equals($other->value);
    }

}
