<?php

namespace SKuhnow\LexoRank\Tests\NumeralSystems;

use Exception;
use PHPUnit\Framework\TestCase;
use SKuhnow\LexoRank\NumeralSystems\LexoNumeralSystem36;

class LexoNumeralSystem36Test extends TestCase
{

    public function testToDigit()
    {
        $lexoNumeralSystem36 = new LexoNumeralSystem36();
        $this->assertSame(0, $lexoNumeralSystem36->toDigit('0'));
        $this->assertSame(9, $lexoNumeralSystem36->toDigit('9'));
        $this->assertSame(10, $lexoNumeralSystem36->toDigit('a'));
        $this->assertSame(35, $lexoNumeralSystem36->toDigit('z'));
    }

    public function testNotValidDigit()
    {
        $lexoNumeralSystem36 = new LexoNumeralSystem36();
        $this->expectException(Exception::class);
        $lexoNumeralSystem36->toDigit('12');

        $this->expectException(Exception::class);
        $lexoNumeralSystem36->toDigit('_');
    }

    public function testToChar()
    {
        $lexoNumeralSystem36 = new LexoNumeralSystem36();
        $this->assertSame('6', $lexoNumeralSystem36->toChar(6));
    }

}
