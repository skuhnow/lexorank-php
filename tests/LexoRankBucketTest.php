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
}
