<?php

declare(strict_types=1);

namespace UUID\Test;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use UUID\UUID;

#[CoversClass(UUID::class)]
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
        $property = $reflection->getProperty('unixts_ms');
        $property->setAccessible(true);
        $property->setValue($a, 9000000000090);
    }

    protected function tearDown(): void
    {
        $a = new UUID();
        $reflection = new \ReflectionClass($a);
        $property = $reflection->getProperty('unixts');
        $property->setAccessible(true);
        $property->setValue($a, 0);
        $property = $reflection->getProperty('subsec');
        $property->setAccessible(true);
        $property->setValue($a, 0);
        $property = $reflection->getProperty('unixts_ms');
        $property->setAccessible(true);
        $property->setValue($a, 0);
    }

    public function testFutureTimeVersion6()
    {
        $uuid1 = UUID::uuid6();
        for ($x = 0; $x < 1000; $x++) {
            $uuid2 = UUID::uuid6();
            $this->assertGreaterThan(
                $uuid1,
                $uuid2
            );
            $this->assertLessThan(
                0,
                strcmp(UUID::getTime($uuid1), UUID::getTime($uuid2))
            );
            $uuid1 = $uuid2;
        }
    }

    public function testFutureTimeVersion7()
    {
        $uuid1 = UUID::uuid7();
        for ($x = 0; $x < 1000; $x++) {
            $uuid2 = UUID::uuid7();
            $this->assertGreaterThan(
                $uuid1,
                $uuid2
            );
            $this->assertLessThan(
                0,
                strcmp(UUID::getTime($uuid1), UUID::getTime($uuid2))
            );
            $uuid1 = $uuid2;
        }
    }

    public function testFutureTimeVersion8()
    {
        $uuid1 = UUID::uuid8();
        for ($x = 0; $x < 1000; $x++) {
            $uuid2 = UUID::uuid8();
            $this->assertGreaterThan(
                $uuid1,
                $uuid2
            );
            $this->assertLessThan(
                0,
                strcmp(UUID::getTime($uuid1), UUID::getTime($uuid2))
            );
            $uuid1 = $uuid2;
        }
    }
}
