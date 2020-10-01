<?php

namespace SKlocke\LexoRank\Tests;

use PHPUnit\Framework\TestCase;
use SKlocke\LexoRank\LexoRank;

class LexoRankTest extends TestCase
{

    public function testMin()
    {
        $minRank = LexoRank::min();
        $this->assertEquals('0|000000:', $minRank->__toString());
    }

    public function testBetweenMinMax()
    {
        $minRank = LexoRank::min();
        $maxRank = LexoRank::max();
        $between = $minRank->between($maxRank);
        $this->assertEquals('0|hzzzzz:', (string)$between);
        $this->assertLessThan(0, $minRank->compareTo($between));
        $this->assertGreaterThan(0, $maxRank->compareTo($between));
    }
}
