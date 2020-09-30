<?php

namespace SKlocke\LexoRank\Tests;

use PHPUnit\Framework\TestCase;
use SKlocke\LexoRank\LexoRank\LexoRank;

class LexoRankTest extends TestCase
{

    public function testMin()
    {
        $minRank = LexoRank::min();
        $this->assertEquals('0|000000:', $minRank->__toString());
    }
}
