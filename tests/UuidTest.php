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
    }

    public function testKnownGetTime()
    {
        $uuid6_time = UUID::getTime('1ebacf4f-a4a8-68ee-b4ec-618c14d005d5');
        $this->assertSame($uuid6_time, '1620145373.6118510');
        $uuid7_time = UUID::getTime('061d0edc-bea0-75cc-9892-f6295fd7d295');
        $this->assertSame($uuid7_time, '1641082315.9141510');
    }

    public function testGetTimeValid()
    {
        for ($i = 1; $i <= 10; $i++) {
            $now = microtime(true);
            $uuid6 = UUID::uuid6();
            $uuid7 = UUID::uuid7();
            $this->assertEqualsWithDelta($now, UUID::getTime($uuid6), 0.001);
            $this->assertEqualsWithDelta($now, UUID::getTime($uuid7), 0.001);
            usleep(100000);
        }
    }

    public function testGetTimeNull()
    {
        $uuid4_time = UUID::getTime(UUID::uuid4());
        $this->assertNull($uuid4_time);
    }
}
