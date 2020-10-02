<?php

namespace SKlocke\LexoRank\Tests;

use PHPUnit\Framework\TestCase;
use SKlocke\LexoRank\LexoRankBucket;

class LexoRankBucketTest extends TestCase
{

    public function testBucketCycleNext()
    {
        $bucket_0 = LexoRankBucket::BUCKET_0();
        $this->assertInstanceOf(LexoRankBucket::class, $bucket_0);
        $this->assertSame('0', $bucket_0->format());
        $bucket_1 = $bucket_0->next();
        $this->assertInstanceOf(LexoRankBucket::class, $bucket_1);
        $this->assertSame('1', $bucket_1->format());

        $bucket_2 = $bucket_1->next();
        $this->assertInstanceOf(LexoRankBucket::class, $bucket_2);
        $this->assertSame('2', $bucket_2->format());

        $bucketBegin = $bucket_2->next();
        $this->assertInstanceOf(LexoRankBucket::class, $bucketBegin);
        $this->assertSame('0', $bucketBegin->format());
    }

    public function testBucketCyclePrev()
    {
        $bucket_0 = LexoRankBucket::BUCKET_0();
        $this->assertInstanceOf(LexoRankBucket::class, $bucket_0);
        $this->assertSame('0', $bucket_0->format());
        $bucket_2 = $bucket_0->prev();
        $this->assertInstanceOf(LexoRankBucket::class, $bucket_2);
        $this->assertSame('2', $bucket_2->format());

        $bucket_1 = $bucket_2->prev();
        $this->assertInstanceOf(LexoRankBucket::class, $bucket_1);
        $this->assertSame('1', $bucket_1->format());

        $bucketBegin = $bucket_1->prev();
        $this->assertInstanceOf(LexoRankBucket::class, $bucketBegin);
        $this->assertSame('0', $bucketBegin->format());
    }

    public function testEquals()
    {
        $bucket_0 = LexoRankBucket::BUCKET_0();
        $this->assertTrue($bucket_0->equals($bucket_0));
        $this->assertTrue($bucket_0->equals(LexoRankBucket::BUCKET_0()));
    }

    public function testMax()
    {
        $bucket = LexoRankBucket::max();
        $this->assertInstanceOf(LexoRankBucket::class, $bucket);
        $this->assertSame('2', $bucket->format());
    }

    public function testFrom()
    {
        $bucket = LexoRankBucket::from('2');
        $this->assertInstanceOf(LexoRankBucket::class, $bucket);
        $this->assertSame('2', $bucket->format());
    }

    public function testFromInvalidIndex()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unknown bucket: 3');
        LexoRankBucket::from('3');
    }

    public function testResolve()
    {
        $bucket = LexoRankBucket::resolve(2);
        $this->assertInstanceOf(LexoRankBucket::class, $bucket);
        $this->assertSame('2', $bucket->format());
    }

    public function testFormat()
    {
        $this->assertEquals('0', LexoRankBucket::BUCKET_0()->format());
        $this->assertEquals('1', LexoRankBucket::BUCKET_1()->format());
        $this->assertEquals('2', LexoRankBucket::BUCKET_2()->format());
    }

    public function testNext()
    {
        $bucket = LexoRankBucket::BUCKET_0();
        $this->assertEquals('1', $bucket->next()->format());
    }

    public function testPrev()
    {
        $bucket = LexoRankBucket::BUCKET_1();
        $this->assertEquals('0', $bucket->prev()->format());
    }
}
