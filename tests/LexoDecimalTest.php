<?php

namespace SKuhnow\LexoRank\Tests;

use PHPUnit\Framework\TestCase;
use SKuhnow\LexoRank\LexoDecimal;
use SKuhnow\LexoRank\NumeralSystems\LexoNumeralSystem36;

class LexoDecimalTest extends TestCase
{

    public function testHalf()
    {
        $lexoDecimal = LexoDecimal::half(new LexoNumeralSystem36());
        $this->assertSame('0:i', (string)$lexoDecimal->format());
    }
}
