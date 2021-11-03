<?php

namespace UUID;

use PHPUnit\Framework\TestCase;

/**
 * @covers UUID
 */
final class UuidTest extends TestCase
{
    public function testCanGenerateValidVersion3()
    {
        $this->assertEquals(
            '11a38b9a-b3da-360f-9353-a5a725514269',
            UUID::uuid3(UUID::NAMESPACE_DNS, 'php.net')
        );
    }

    public function testCanGenerateValidVersion4()
    {
        $uuid1 = UUID::uuid4();
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}$/',
            $uuid1
        );
        $uuid2 = UUID::uuid4();
        $this->assertNotEquals(
            $uuid1,
            $uuid2
        );
    }

    public function testCanGenerateValidVersion5()
    {
        $this->assertEquals(
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
            UUID::uuid5(UUID::NAMESPACE_DNS, 'php.net')
        );
    }

    public function testCanGenerateValidVersion6()
    {
        $uuid1 = UUID::uuid6();
        for ($x = 0; $x <= 10; $x++) {
            usleep(1);
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
        $this->assertEquals(
            3,
            UUID::getVersion('11a38b9a-b3da-360f-9353-a5a725514269')
        );
        $this->assertEquals(
            5,
            UUID::getVersion('c4a760a8-dbcf-5254-a0d9-6a4474bd1b62')
        );
    }

    public function testCanCompare()
    {
        $this->assertEquals(
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
        $this->assertEquals(
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
            UUID::toString('{C4A760A8-DBCF-5254-A0D9-6A4474BD1B62}')
        );
    }

    public function testCanUseAliases()
    {
        $this->assertEquals(
            '11a38b9a-b3da-360f-9353-a5a725514269',
            UUID::v3(UUID::NAMESPACE_DNS, 'php.net')
        );
        $this->assertEquals(
            4,
            UUID::getVersion(UUID::v4())
        );
        $this->assertEquals(
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
            UUID::v5(UUID::NAMESPACE_DNS, 'php.net')
        );
        $this->assertEquals(
            6,
            UUID::getVersion(UUID::v6())
        );
    }
}
