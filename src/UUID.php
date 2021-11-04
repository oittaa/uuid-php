<?php

declare(strict_types=1);

namespace UUID;

/**
 * Represents a universally unique identifier (UUID), according to RFC 4122.
 *
 * This class provides the static methods `uuid3()`, `uuid4()`, `uuid5()`, and
 * `uuid6()` for generating version 3, 4, 5, and 6 (draft) UUIDs.
 *
 * If all you want is a unique ID, you should call `uuid4()`.
 *
 * @link http://tools.ietf.org/html/rfc4122
 * @link https://github.com/uuid6/uuid6-ietf-draft
 * @link http://en.wikipedia.org/wiki/Universally_unique_identifier
 */
class UUID
{
    /**
     * When this namespace is specified, the name string is a fully-qualified domain name.
     * @var string
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    public const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    /**
     * When this namespace is specified, the name string is a URL.
     * @var string
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    public const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    /**
     * When this namespace is specified, the name string is an ISO OID.
     * @var string
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    public const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    /**
     * When this namespace is specified, the name string is an X.500 DN in DER or a text output format.
     * @var string
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    public const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';
    /**
     * The nil UUID is special form of UUID that is specified to have all 128 bits set to zero.
     * @var string
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.7
     */
    public const NIL = '00000000-0000-0000-0000-000000000000';

    /**
     * 0x01b21dd213814000 is the number of 100-ns intervals between the
     * UUID epoch 1582-10-15 00:00:00 and the Unix epoch 1970-01-01 00:00:00.
     * @var int
     * @link https://tools.ietf.org/html/rfc4122#section-4.1.4
     */
    public const TIME_OFFSET_INT = 0x01b21dd213814000;

    /** @internal */
    private const REPLACE_ARR = array('urn:', 'uuid:', '-', '{', '}');

    /** @internal */
    private static function stripExtras($uuid)
    {
        if (!self::isValid($uuid)) {
            throw new \InvalidArgumentException('Invalid UUID string: ' . $uuid);
        }
        // Get hexadecimal components of UUID
        return str_replace(self::REPLACE_ARR, '', $uuid);
    }

    /** @internal */
    private static function getBytes($uuid)
    {
        $uhex = self::stripExtras($uuid);

        // Binary Value
        $ustr = '';

        // Convert UUID to bits
        for ($i = 0; $i < strlen($uhex); $i += 2) {
            $ustr .= chr(hexdec($uhex[$i] . $uhex[$i + 1]));
        }
        return $ustr;
    }

    /** @internal */
    private static function uuidFromHash($hash, $version)
    {
        return sprintf(
            '%08s-%04s-%04x-%04x-%12s',
            // 32 bits for "time_low"
            substr($hash, 0, 8),
            // 16 bits for "time_mid"
            substr($hash, 8, 4),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | $version << 12,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            // 48 bits for "node"
            substr($hash, 20, 12)
        );
    }

    /**
     * Generate a version 3 UUID based on the MD5 hash of a namespace identifier
     * (which is a UUID) and a name (which is a string).
     *
     * @param string $namespace The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     * @return string The string standard representation of the UUID
     */
    public static function uuid3($namespace, $name)
    {
        $nbytes = self::getBytes($namespace);

        // Calculate hash value
        $hash = md5($nbytes . $name);

        return self::uuidFromHash($hash, 3);
    }

    /**
     * Generate a version 4 (random) UUID.
     *
     * @return string The string standard representation of the UUID
     */
    public static function uuid4()
    {
        $bytes = random_bytes(16);
        $hash = bin2hex($bytes);
        return self::uuidFromHash($hash, 4);
    }

    /**
     * Generate a version 5 UUID based on the SHA-1 hash of a namespace
     * identifier (which is a UUID) and a name (which is a string).
     *
     * @param string $namespace The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     * @return string The string standard representation of the UUID
     */
    public static function uuid5($namespace, $name)
    {
        $nbytes = self::getBytes($namespace);

        // Calculate hash value
        $hash = sha1($nbytes . $name);

        return self::uuidFromHash($hash, 5);
    }

    /**
     * Generate a version 6 UUID. A v6 UUID is lexicographically sortable and contains
     * a 60-bit timestamp and 62 extra unique bits. Unlike version 1 UUID, this
     * implementation of version 6 UUID doesn't leak the MAC address of the host.
     *
     * @return string The string standard representation of the UUID
     */
    public static function uuid6()
    {
        $time = microtime(false);
        $time = substr($time, 11) . substr($time, 2, 7);
        $time = str_pad(dechex($time + self::TIME_OFFSET_INT), 16, '0', \STR_PAD_LEFT);
        $time = sprintf(
            '%012s6%03s',
            substr($time, -15, 12),
            substr($time, -3)
        );
        $bytes = random_bytes(8);
        $hash = $time . bin2hex($bytes);
        return self::uuidFromHash($hash, 6);
    }

    /**
     * Check if a string is a valid UUID.
     *
     * @param string $uuid The string UUID to test
     * @return boolean Returns `true` if uuid is valid, `false` otherwise
     */
    public static function isValid($uuid)
    {
        return preg_match('/^(urn:)?(uuid:)?(\{)?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}'
        . '\-?[0-9a-f]{4}\-?[0-9a-f]{12}(?(3)\}|)$/i', $uuid) === 1;
    }

    /**
     * Check if two UUIDs are equal.
     *
     * @param string $uuid1 The first UUID to test
     * @param string $uuid2 The second UUID to test
     * @return boolean Returns `true` if uuid1 is equal to uuid2, `false` otherwise
     */
    public static function equals($uuid1, $uuid2)
    {
        return self::getBytes($uuid1) === self::getBytes($uuid2);
    }

    /**
     * Returns the UUID version.
     *
     * @param string $uuid The UUID string
     * @return int Version number of the UUID
     */
    public static function getVersion($uuid)
    {
        $bytes = unpack('n*', self::getBytes($uuid));
        return (int) $bytes[4] >> 12;
    }

    /**
     * UUID comparison.
     *
     * @param string $uuid1 The first UUID to test
     * @param string $uuid2 The second UUID to test
     * @return int Returns < 0 if uuid1 is less than uuid2; > 0 if uuid1 is
     *             greater than uuid2, and 0 if they are equal.
     */
    public static function cmp($uuid1, $uuid2)
    {
        return strcmp(self::getBytes($uuid1), self::getBytes($uuid2));
    }

    /**
     * The string standard representation of the UUID.
     *
     * @param string $uuid The UUID string
     * @return string The string standard representation of the UUID
     */
    public static function toString($uuid)
    {
        $uhex = strtolower(self::stripExtras($uuid));
        return sprintf(
            '%08s-%04s-%04s-%04s-%12s',
            substr($uhex, 0, 8),
            substr($uhex, 8, 4),
            substr($uhex, 12, 4),
            substr($uhex, 16, 4),
            substr($uhex, 20, 12)
        );
    }

    /**
     * @see UUID::uuid3() Alias
     * @return string
     */
    public static function v3(...$args)
    {
        return self::uuid3(...$args);
    }
    /**
     * @see UUID::uuid4() Alias
     * @return string
     */
    public static function v4()
    {
        return self::uuid4();
    }
    /**
     * @see UUID::uuid5() Alias
     * @return string
     */
    public static function v5(...$args)
    {
        return self::uuid5(...$args);
    }
    /**
     * @see UUID::uuid6() Alias
     * @return string
     */
    public static function v6()
    {
        return self::uuid6();
    }
}
