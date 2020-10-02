<?php

namespace SKlocke\LexoRank\Tests;

use PHPUnit\Framework\TestCase;
use SKlocke\LexoRank\LexoInteger;
use SKlocke\LexoRank\NumeralSystems\LexoNumeralSystem10;

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
}
