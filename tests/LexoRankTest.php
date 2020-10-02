<?php

namespace SKuhnow\LexoRank\Tests;

use PHPUnit\Framework\TestCase;
use SKuhnow\LexoRank\LexoRank;

class LexoRankTest extends TestCase
{

    public function testMin()
    {
        $minRank = LexoRank::min();
        $this->assertEquals('0|000000:', (string)$minRank);
    }

    public function testMax()
    {
        $maxRank = LexoRank::max();
        $this->assertEquals('0|zzzzzz:', (string)$maxRank);
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

    public function testBetweenMinAndGetNext()
    {
        $minRank = LexoRank::min();
        $nextRank = $minRank->genNext();
        $between = $minRank->between($nextRank);
        $this->assertSame('0|0i0000:', (string)$between);
        $this->assertLessThan(0, $minRank->compareTo($between));
        $this->assertGreaterThan(0, $nextRank->compareTo($between));
    }

    public function testBetweenMaxAndGetPrev()
    {
        $maxRank = LexoRank::max();
        $prevRank = $maxRank->genPrev();
        $between = $maxRank->between($prevRank);
        $this->assertSame('0|yzzzzz:', (string)$between);
        $this->assertGreaterThan(0, $maxRank->compareTo($between));
        $this->assertLessThan(0, $prevRank->compareTo($between));
    }

    /**
     * @dataProvider dataProviderMoveTo
     *
     * @param $prevStep
     * @param $nextStep
     * @param $expected
     *
     * @throws \Exception
     */
    public function testMoveTo($prevStep, $nextStep, $expected)
    {
        $prevRank = LexoRank::min();
        $prevStepInt = (int)$prevStep;
        for ($i = 0; $i < $prevStepInt; $i++) {
            $prevRank = $prevRank->genNext();
        }

        $nextRank = LexoRank::min();
        $nextStepInt = (int)$nextStep;
        for ($i = 0; $i < $nextStepInt; $i++) {
            $nextRank = $nextRank->genNext();
        }

        $between = $prevRank->between($nextRank);
        $this->assertSame($expected, (string)$between);
    }

    public function dataProviderMoveTo()
    {
        return [
            ['0', '1', '0|0i0000:'],
            ['1', '0', '0|0i0000:'],
            ['3', '5', '0|10000o:'],
            ['5', '3', '0|10000o:'],
            ['15', '30', '0|10004s:'],
            ['31', '32', '0|10006s:'],
            ['100', '200', '0|1000x4:'],
            ['200', '100', '0|1000x4:'],
        ];
    }

    public function testCompareTo()
    {
        $rankA = LexoRank::min();
        $rankB = LexoRank::min();
        $rankMax = LexoRank::max();

        $this->assertSame(0, $rankA->compareTo($rankA));
        $this->assertSame(0, $rankA->compareTo($rankB));
        $this->assertLessThan(0, $rankA->compareTo($rankMax));
        $this->assertGreaterThan(0, $rankMax->compareTo($rankA));
    }
}
