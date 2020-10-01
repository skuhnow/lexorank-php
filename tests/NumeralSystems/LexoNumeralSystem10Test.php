<?php

namespace SKlocke\LexoRank\Tests\NumeralSystems;

use Exception;
use PHPUnit\Framework\TestCase;
use SKlocke\LexoRank\NumeralSystems\LexoNumeralSystem10;

class LexoNumeralSystem10Test extends TestCase
{

    public function testToDigit()
    {
        $lexoNumeralSystem10 = new LexoNumeralSystem10();
        $this->assertSame(0, $lexoNumeralSystem10->toDigit('0'));
        $this->assertSame(9, $lexoNumeralSystem10->toDigit('9'));
    }

    public function testNotValidDigit()
    {
        $lexoNumeralSystem10 = new LexoNumeralSystem10();
        $this->expectException(Exception::class);
        $lexoNumeralSystem10->toDigit('z');
    }

    public function testToChar()
    {
        $lexoNumeralSystem10 = new LexoNumeralSystem10();
        $this->assertSame('6', $lexoNumeralSystem10->toChar(6));
    }

}
