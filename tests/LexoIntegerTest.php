<?php

namespace SKuhnow\LexoRank\Tests;

use PHPUnit\Framework\TestCase;
use SKuhnow\LexoRank\LexoInteger;
use SKuhnow\LexoRank\NumeralSystems\LexoNumeralSystem10;

class LexoIntegerTest extends TestCase
{

    public function testParsePositive()
    {
        $lexoInteger = LexoInteger::parse('123', new LexoNumeralSystem10());
        $this->assertSame('123', $lexoInteger->format());
    }

    public function testParseNegative()
    {
        $lexoInteger = LexoInteger::parse('-123', new LexoNumeralSystem10());
        $this->assertSame('-123', $lexoInteger->format());
    }

    public function testZero()
    {
        $lexoInteger = LexoInteger::zero(new LexoNumeralSystem10());
        $this->assertSame('0', $lexoInteger->format());
    }

    public function testOne()
    {
        $lexoInteger = LexoInteger::one(new LexoNumeralSystem10());
        $this->assertSame('1', $lexoInteger->format());
    }

    public function testMake()
    {
        $lexoInteger = LexoInteger::make(new LexoNumeralSystem10(), 1, [18]);
        $this->assertSame('B', $lexoInteger->format());
    }

    public function testShiftLeft()
    {
        $lexoNumeralSystem10 = new LexoNumeralSystem10();
        $lexoInteger = LexoInteger::make($lexoNumeralSystem10, 1, [18]);
        $newLexoInteger = $lexoInteger->shiftLeft(2);
        $this->assertSame('B00', $newLexoInteger->format());
    }

    public function testShiftRight()
    {
        $lexoNumeralSystem10 = new LexoNumeralSystem10();
        $lexoInteger = LexoInteger::make($lexoNumeralSystem10, 1, [18]);
        $newLexoInteger = $lexoInteger->shiftRight(2);
        $this->assertSame('0', $newLexoInteger->format());
    }
}
