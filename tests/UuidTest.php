<?php

declare(strict_types=1);

namespace UUID\Test;

use PHPUnit\Framework\TestCase;
use UUID\UUID;

/**
 * @covers \UUID\UUID
 */
final class UuidTest extends TestCase
{
    public function testCanGenerateValidVersion3()
    {
        $this->assertSame(
            '11a38b9a-b3da-360f-9353-a5a725514269',
            UUID::uuid3(UUID::NAMESPACE_DNS, 'php.net')
        );
    }

    public function testCanGenerateValidVersion4()
    {
        $uuid1 = UUID::uuid4();
        for ($x = 0; $x < 1000; $x++) {
            $this->assertMatchesRegularExpression(
                '/^[0-9a-f]{8}\-[0-9a-f]{4}\-4[0-9a-f]{3}\-[89ab][0-9a-f]{3}\-[0-9a-f]{12}$/',
                $uuid1
            );
            $uuid2 = UUID::uuid4();
            $this->assertNotEquals(
                $uuid1,
                $uuid2
            );
            $uuid1 = $uuid2;
        }
    }

    public function testCanGenerateValidVersion5()
    {
        $this->assertSame(
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
            UUID::uuid5(UUID::NAMESPACE_DNS, 'php.net')
        );
    }

    public function testCanGenerateValidVersion6()
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
            $this->assertLessThan(
                0,
                UUID::cmp($uuid1, $uuid2)
            );
            $uuid1 = $uuid2;
        }
    }

    public function testCanGenerateValidVersion7()
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
            $this->assertLessThan(
                0,
                UUID::cmp($uuid1, $uuid2)
            );
            $uuid1 = $uuid2;
        }
    }

    public function testCanGenerateValidVersion8()
    {
        $uuid1 = UUID::uuid8();
        for ($x = 0; $x < 1000; $x++) {
            $this->assertMatchesRegularExpression(
                '/^[0-9a-f]{8}\-[0-9a-f]{4}\-8[0-9a-f]{3}\-[89ab][0-9a-f]{3}\-[0-9a-f]{12}$/',
                $uuid1
            );
            $uuid2 = UUID::uuid8();
            $this->assertGreaterThan(
                $uuid1,
                $uuid2
            );
            $this->assertLessThan(
                0,
                UUID::cmp($uuid1, $uuid2)
            );
            $uuid1 = $uuid2;
        }
    }

    public function testCannotBeCreatedFromInvalidNamespace()
    {
        $this->expectException(\InvalidArgumentException::class);

        UUID::uuid5('invalid', 'php.net');
    }

    public function testCanValidate()
    {
        $this->assertTrue(
            UUID::isValid('11a38b9a-b3da-360f-9353-a5a725514269')
        );
        $this->assertFalse(
            UUID::isValid('11a38b9a-b3da-360f-9353-a5a72551426')
        );
        $this->assertFalse(
            UUID::isValid('11a38b9a-b3da-360f-9353-a5a7255142690')
        );
        $this->assertTrue(
            UUID::isValid('urn:uuid:c4a760a8-dbcf-5254-a0d9-6a4474bd1b62')
        );
        $this->assertTrue(
            UUID::isValid('{C4A760A8-DBCF-5254-A0D9-6A4474BD1B62}')
        );
        $this->assertFalse(
            UUID::isValid('{C4A760A8-DBCF-5254-A0D9-6A4474BD1B62')
        );
        $this->assertFalse(
            UUID::isValid('C4A760A8-DBCF-5254-A0D9-6A4474BD1B62}')
        );
        $this->assertTrue(
            UUID::equals(
                'urn:uuid:c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
                '{C4A760A8-DBCF-5254-A0D9-6A4474BD1B62}'
            )
        );
        $this->assertFalse(
            UUID::equals(
                'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
                '2140a926-4a47-465c-b622-4571ad9bb378'
            )
        );
    }

    public function testCanGetVersion()
    {
        $this->assertSame(
            3,
            UUID::getVersion('11a38b9a-b3da-360f-9353-a5a725514269')
        );
        $this->assertSame(
            5,
            UUID::getVersion('c4a760a8-dbcf-5254-a0d9-6a4474bd1b62')
        );
    }

    public function testCanCompare()
    {
        $this->assertSame(
            0,
            UUID::cmp('c4a760a8-dbcf-5254-a0d9-6a4474bd1b62', 'C4A760A8-DBCF-5254-A0D9-6A4474BD1B62')
        );
        $this->assertGreaterThan(
            0,
            UUID::cmp('c4a760a8-dbcf-5254-a0d9-6a4474bd1b63', 'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62')
        );
    }

    public function testToString()
    {
        $this->assertSame(
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
            UUID::toString('{C4A760A8-DBCF-5254-A0D9-6A4474BD1B62}')
        );
    }

    public function testCanUseAliases()
    {
        $this->assertSame(
            '11a38b9a-b3da-360f-9353-a5a725514269',
            UUID::v3(UUID::NAMESPACE_DNS, 'php.net')
        );
        $this->assertSame(
            4,
            UUID::getVersion(UUID::v4())
        );
        $this->assertSame(
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
            UUID::v5(UUID::NAMESPACE_DNS, 'php.net')
        );
        $this->assertSame(
            6,
            UUID::getVersion(UUID::v6())
        );
        $this->assertSame(
            7,
            UUID::getVersion(UUID::v7())
        );
        $this->assertSame(
            8,
            UUID::getVersion(UUID::v8())
        );
    }

    public function testKnownGetTime()
    {
        $uuid6_time = UUID::getTime('1EC9414C-232A-6B00-B3C8-9E6BDECED846');
        $this->assertSame('1645557742.0000000', $uuid6_time);
        $uuid7_time = UUID::getTime('017F22E2-79B0-7CC3-98C4-DC0C0C07398F');
        $this->assertSame('1645557742.0000000', $uuid7_time);
        $uuid8_time = UUID::getTime('017F22E2-79B0-8CC3-98C4-DC0C0C07398F');
        $this->assertSame('1645557742.0007977', $uuid8_time);
    }

    public function testGetTimeValid()
    {
        for ($i = 1; $i <= 10; $i++) {
            $uuid6 = UUID::uuid6();
            $this->assertEqualsWithDelta(microtime(true), UUID::getTime($uuid6), 0.001);
            $uuid7 = UUID::uuid7();
            $this->assertEqualsWithDelta(microtime(true), UUID::getTime($uuid7), 0.01);
            $uuid8 = UUID::uuid8();
            $this->assertEqualsWithDelta(microtime(true), UUID::getTime($uuid8), 0.001);
            usleep(100000);
        }
    }

    public function testGetTimeNull()
    {
        $uuid4_time = UUID::getTime(UUID::uuid4());
        $this->assertNull($uuid4_time);
    }

    public function testGetTimeNearEpoch()
    {
        $uuid6_time = UUID::getTime('1b21dd21-3814-6001-b6fa-54fb559c5fcd');
        $this->assertSame('0.0000001', $uuid6_time);
    }

    public function testGetTimeNegativeNearEpoch()
    {
        $uuid6_time = UUID::getTime('1b21dd21-3813-6fff-b678-1556dde9b80e');
        $this->assertSame('-0.0000001', $uuid6_time);
    }

    public function testGetTimeZero()
    {
        $uuid6_time = UUID::getTime('00000000-0000-6000-8000-000000000000');
        $this->assertSame('-12219292800.0000000', $uuid6_time);
        $uuid7_time = UUID::getTime('00000000-0000-7000-8000-000000000000');
        $this->assertSame('0.0000000', $uuid7_time);
        $uuid8_time = UUID::getTime('00000000-0000-8000-8000-000000000000');
        $this->assertSame('0.0000000', $uuid8_time);
    }

    public function testGetTimeMax()
    {
        $uuid6_time = UUID::getTime('ffffffff-ffff-6fff-bfff-ffffffffffff');
        $this->assertSame('103072857660.6846975', $uuid6_time);
        $uuid7_time = UUID::getTime('ffffffff-ffff-7fff-bfff-ffffffffffff');
        $this->assertSame('281474976710.6550000', $uuid7_time);
        $uuid8_time = UUID::getTime('ffffffff-ffff-8fff-bfff-ffffffffffff');
        $this->assertSame('281474976710.6560000', $uuid8_time);
    }
}
