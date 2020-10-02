<?php

namespace SKlocke\LexoRank\Tests;

use PHPUnit\Framework\TestCase;
use SKlocke\LexoRank\LexoDecimal;
use SKlocke\LexoRank\NumeralSystems\LexoNumeralSystem36;

class LexoDecimalTest extends TestCase
{

    public function testHalf()
    {
        $lexoDecimal = LexoDecimal::half(new LexoNumeralSystem36());
        $this->assertSame('0:i', (string)$lexoDecimal->format());
    }
}
