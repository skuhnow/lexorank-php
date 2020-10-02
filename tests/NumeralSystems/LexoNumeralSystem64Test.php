<?php

namespace SKlocke\LexoRank\Tests\NumeralSystems;

use Exception;
use PHPUnit\Framework\TestCase;
use SKlocke\LexoRank\NumeralSystems\LexoNumeralSystem64;

class LexoNumeralSystem64Test extends TestCase
{

    public function testToDigit()
    {
        $lexoNumeralSystem64 = new LexoNumeralSystem64();
        $this->assertSame(0, $lexoNumeralSystem64->toDigit('0'));
        $this->assertSame(9, $lexoNumeralSystem64->toDigit('9'));
        $this->assertSame(10, $lexoNumeralSystem64->toDigit('A'));
        $this->assertSame(35, $lexoNumeralSystem64->toDigit('Z'));
        $this->assertSame(36, $lexoNumeralSystem64->toDigit('^'));
        $this->assertSame(37, $lexoNumeralSystem64->toDigit('_'));
        $this->assertSame(38, $lexoNumeralSystem64->toDigit('a'));
        $this->assertSame(63, $lexoNumeralSystem64->toDigit('z'));
    }

    public function testNotValidDigit()
    {
        $lexoNumeralSystem64 = new LexoNumeralSystem64();
        $this->expectException(Exception::class);
        $lexoNumeralSystem64->toDigit(':');
    }

    public function testToChar()
    {
        $lexoNumeralSystem64 = new LexoNumeralSystem64();
        $this->assertSame('_', $lexoNumeralSystem64->toChar(37));
    }

}
