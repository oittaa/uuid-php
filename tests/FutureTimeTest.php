<?php

declare(strict_types=1);

namespace UUID\Test;

use PHPUnit\Framework\TestCase;
use UUID\UUID;

/**
 * @covers \UUID\UUID
 */
final class FutureTimeTest extends TestCase
{
    protected function setUp(): void
    {
        $a = new UUID();
        $reflection = new \ReflectionClass($a);
        $property = $reflection->getProperty('unixts');
        $property->setAccessible(true);
        $property->setValue($a, 9000000000);
        $property = $reflection->getProperty('subsec');
        $property->setAccessible(true);
        $property->setValue($a, 9999990);
    }

    public function testFutureTimeVersion6()
    {
        $uuid1 = UUID::uuid6();
        for ($x = 0; $x < 1000; $x++) {
            $this->assertMatchesRegularExpression(
                '/^[0-9a-f]{8}\-[0-9a-f]{4}\-6[0-9a-f]{3}\-[89ab][0-9a-f]{3}\-[0-9a-f]{12}$/',
                $uuid1
            );
            $uuid2 = UUID::uuid6();
            $this->assertGreaterThan(
                $uuid1,
                $uuid2
            );
            $uuid1 = $uuid2;
        }
    }

    public function testFutureTimeVersion7()
    {
        $uuid1 = UUID::uuid7();
        for ($x = 0; $x < 1000; $x++) {
            $this->assertMatchesRegularExpression(
                '/^[0-9a-f]{8}\-[0-9a-f]{4}\-7[0-9a-f]{3}\-[89ab][0-9a-f]{3}\-[0-9a-f]{12}$/',
                $uuid1
            );
            $uuid2 = UUID::uuid7();
            $this->assertGreaterThan(
                $uuid1,
                $uuid2
            );
            $uuid1 = $uuid2;
        }
    }
}
