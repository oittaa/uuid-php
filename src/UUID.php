<?php

declare(strict_types=1);

namespace UUID;

/**
 * Represents a universally unique identifier (UUID), according to RFC 9562.
 *
 * This class provides the static methods `uuid3()`, `uuid4()`, `uuid5()`,
 * `uuid6()`, `uuid7()`, and `uuid8()` for generating version 3, 4, 5,
 * 6, 7, and 8 UUIDs.
 *
 * If all you want is a unique ID, you should call `uuid4()`.
 *
 * @link https://datatracker.ietf.org/doc/rfc9562/
 * @link http://en.wikipedia.org/wiki/Universally_unique_identifier
 */
class UUID
{
    /**
     * When this namespace is specified, the name string is a fully-qualified domain name.
     * @var string
     * @link https://www.rfc-editor.org/rfc/rfc9562.html#name-namespace-id-usage-and-allo
     */
    public const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    /**
     * When this namespace is specified, the name string is a URL.
     * @var string
     * @link https://www.rfc-editor.org/rfc/rfc9562.html#name-namespace-id-usage-and-allo
     */
    public const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    /**
     * When this namespace is specified, the name string is an ISO OID.
     * @var string
     * @link https://www.rfc-editor.org/rfc/rfc9562.html#name-namespace-id-usage-and-allo
     */
    public const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    /**
     * When this namespace is specified, the name string is an X.500 DN in DER or a text output format.
     * @var string
     * @link https://www.rfc-editor.org/rfc/rfc9562.html#name-namespace-id-usage-and-allo
     */
    public const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';
    /**
     * The nil UUID is special form of UUID that is specified to have all 128 bits set to zero.
     * @var string
     * @link https://www.rfc-editor.org/rfc/rfc9562.html#name-nil-uuid
     */
    public const NIL = '00000000-0000-0000-0000-000000000000';
    /**
     * The Max UUID is special form of UUID that is specified to have all 128 bits set to one.
     * @var string
     * @link https://www.rfc-editor.org/rfc/rfc9562.html#name-max-uuid
     */
    public const MAX = 'FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF';

    /**
     * 0x01b21dd213814000 is the number of 100-ns intervals between the
     * UUID epoch 1582-10-15 00:00:00 and the Unix epoch 1970-01-01 00:00:00.
     * @var int
     * @link https://www.rfc-editor.org/rfc/rfc9562.html#name-test-vectors
     */
    public const TIME_OFFSET_INT = 0x01b21dd213814000;

    /** @internal */
    private const SUBSEC_RANGE = 10_000_000;

    /** @internal */
    private const V7_SUBSEC_RANGE = 10_000;

    /** @internal */
    private const V8_SUBSEC_RANGE = 10_000;

    /** @internal */
    private const V8_SUBSEC_BITS = 14;

    /** @internal */
    private const UUID_REGEX = '/^(?:urn:)?(?:uuid:)?(\{)?([0-9a-f]{8})\-?([0-9a-f]{4})'
    . '\-?([0-9a-f]{4})\-?([0-9a-f]{4})\-?([0-9a-f]{12})(?(1)\}|)$/i';

    /** @internal */
    private static $unixts = 0;

    /** @internal */
    private static $subsec = 0;

    /** @internal */
    private static $unixts_ms = 0;

    /** @internal */
    private static function getUnixTimeSubsec(): array
    {
        $timestamp = microtime(false);
        $unixts = intval(substr($timestamp, 11), 10);
        $subsec = intval(substr($timestamp, 2, 7), 10);
        if (self::$unixts > $unixts || self::$unixts === $unixts && self::$subsec >= $subsec) {
            $unixts = self::$unixts;
            $subsec = self::$subsec;
            if ($subsec >= self::SUBSEC_RANGE - 1) {
                $subsec = 0;
                $unixts++;
            } else {
                $subsec++;
            }
        }
        self::$unixts = $unixts;
        self::$subsec = $subsec;
        return [$unixts, $subsec];
    }

    /** @internal */
    private static function getUnixTimeMs(): int
    {
        $timestamp = microtime(false);
        $unixts = intval(substr($timestamp, 11), 10);
        $unixts_ms = $unixts * 1000 + intval(substr($timestamp, 2, 3), 10);
        if (self::$unixts_ms >= $unixts_ms) {
            $unixts_ms = self::$unixts_ms + 1;
        }
        self::$unixts_ms = $unixts_ms;
        return $unixts_ms;
    }

    /** @internal */
    private static function stripExtras(string $uuid): string
    {
        if (preg_match(self::UUID_REGEX, $uuid, $m) !== 1) {
            throw new \InvalidArgumentException('Invalid UUID string: ' . $uuid);
        }
        // Get hexadecimal components of UUID
        return strtolower($m[2] . $m[3] . $m[4] . $m[5] . $m[6]);
    }

    /** @internal */
    private static function getBytes(string $uuid): string
    {
        return pack('H*', self::stripExtras($uuid));
    }

    /** @internal */
    private static function uuidFromHex(string $uhex, int $version): string
    {
        return sprintf(
            '%08s-%04s-%04x-%04x-%12s',
            // 32 bits for "time_low"
            substr($uhex, 0, 8),
            // 16 bits for "time_mid"
            substr($uhex, 8, 4),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number
            (hexdec(substr($uhex, 12, 4)) & 0x0fff) | $version << 12,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($uhex, 16, 4)) & 0x3fff) | 0x8000,
            // 48 bits for "node"
            substr($uhex, 20, 12)
        );
    }

    /** @internal */
    private static function encodeSubsec(int $value): int
    {
        return intdiv($value << self::V8_SUBSEC_BITS, self::V8_SUBSEC_RANGE);
    }

    /** @internal */
    private static function decodeSubsec(int $value): int
    {
        return -(-$value * self::V8_SUBSEC_RANGE >> self::V8_SUBSEC_BITS);
    }

    /**
     * Generate a version 3 UUID based on the MD5 hash of a namespace identifier
     * (which is a UUID) and a name (which is a string).
     *
     * @param string $namespace The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     * @return string The string standard representation of the UUID
     */
    public static function uuid3(string $namespace, string $name): string
    {
        $nbytes = self::getBytes($namespace);
        $uhex = md5($nbytes . $name);
        return self::uuidFromHex($uhex, 3);
    }

    /**
     * Generate a version 4 (random) UUID.
     *
     * @return string The string standard representation of the UUID
     */
    public static function uuid4(): string
    {
        $uhex = bin2hex(random_bytes(16));
        return self::uuidFromHex($uhex, 4);
    }

    /**
     * Generate a version 5 UUID based on the SHA-1 hash of a namespace
     * identifier (which is a UUID) and a name (which is a string).
     *
     * @param string $namespace The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     * @return string The string standard representation of the UUID
     */
    public static function uuid5(string $namespace, string $name): string
    {
        $nbytes = self::getBytes($namespace);
        $uhex = sha1($nbytes . $name);
        return self::uuidFromHex($uhex, 5);
    }

    /**
     * UUID version 6 is a field-compatible version of UUIDv1, reordered for improved
     * DB locality. It is expected that UUIDv6 will primarily be used in contexts
     * where there are existing v1 UUIDs. Systems that do not involve legacy UUIDv1
     * SHOULD consider using UUIDv7 instead.
     *
     * @return string The string standard representation of the UUID
     */
    public static function uuid6(): string
    {
        [$unixts, $subsec] = self::getUnixTimeSubsec();
        $timestamp = $unixts * self::SUBSEC_RANGE + $subsec;
        $timehex = str_pad(dechex($timestamp + self::TIME_OFFSET_INT), 15, '0', \STR_PAD_LEFT);
        $uhex = substr_replace(substr($timehex, -15), '6', -3, 0);
        $uhex .= bin2hex(random_bytes(8));
        return self::uuidFromHex($uhex, 6);
    }

    /**
     * UUID version 7 features a time-ordered value field derived from the widely
     * implemented and well known Unix Epoch timestamp source, the number of
     * milliseconds seconds since midnight 1 Jan 1970 UTC, leap seconds excluded. As
     * well as improved entropy characteristics over versions 1 or 6.
     *
     * Implementations SHOULD utilize UUID version 7 over UUID version 1 and 6 if
     * possible.
     *
     * @return string The string standard representation of the UUID
     */
    public static function uuid7(): string
    {
        $unixtsms = self::getUnixTimeMs();
        $uhex = substr(str_pad(dechex($unixtsms), 12, '0', \STR_PAD_LEFT), -12);
        $uhex .= bin2hex(random_bytes(10));
        return self::uuidFromHex($uhex, 7);
    }

    /**
     * Generate a version 8 UUID. A v8 UUID is lexicographically sortable and is
     * designed to encode a Unix timestamp with arbitrary sub-second precision.
     *
     * @return string The string standard representation of the UUID
     */
    public static function uuid8(): string
    {
        [$unixts, $subsec] = self::getUnixTimeSubsec();
        $unixtsms = $unixts * 1000 + intdiv($subsec, self::V8_SUBSEC_RANGE);
        $subsec = self::encodeSubsec($subsec % self::V8_SUBSEC_RANGE);
        $subsecA = $subsec >> 2;
        $subsecB = $subsec & 0x03;
        $randB = random_bytes(8);
        $randB[0] = chr(ord($randB[0]) & 0x0f | $subsecB << 4);
        $uhex = substr(str_pad(dechex($unixtsms), 12, '0', \STR_PAD_LEFT), -12);
        $uhex .= '8' . str_pad(dechex($subsecA), 3, '0', \STR_PAD_LEFT);
        $uhex .= bin2hex($randB);
        return self::uuidFromHex($uhex, 8);
    }

    /**
     * Check if a string is a valid UUID.
     *
     * @param string $uuid The string UUID to test
     * @return boolean Returns `true` if uuid is valid, `false` otherwise
     */
    public static function isValid(string $uuid): bool
    {
        return preg_match(self::UUID_REGEX, $uuid) === 1;
    }

    /**
     * Check if two UUIDs are equal.
     *
     * @param string $uuid1 The first UUID to test
     * @param string $uuid2 The second UUID to test
     * @return boolean Returns `true` if uuid1 is equal to uuid2, `false` otherwise
     */
    public static function equals(string $uuid1, string $uuid2): bool
    {
        return self::stripExtras($uuid1) === self::stripExtras($uuid2);
    }

    /**
     * Returns Unix time from a UUID.
     *
     * @param string $uuid The UUID string
     * @return string Unix time
     */
    public static function getTime(string $uuid): ?string
    {
        $uuid = self::stripExtras($uuid);
        $version = self::getVersion($uuid);
        $timehex = '0' . substr($uuid, 0, 12) . substr($uuid, 13, 3);
        $retval = null;
        if ($version === 6) {
            $retval = '';
            $ts = hexdec($timehex) - self::TIME_OFFSET_INT;
            if ($ts < 0) {
                $retval = '-';
                $ts = abs($ts);
            }
            $retval .= substr_replace(str_pad(strval($ts), 8, '0', \STR_PAD_LEFT), '.', -7, 0);
        } elseif ($version === 7) {
            $unixts = hexdec(substr($timehex, 0, 13));
            $retval = strval($unixts * self::V7_SUBSEC_RANGE);
            $retval = substr_replace(str_pad($retval, 8, '0', \STR_PAD_LEFT), '.', -7, 0);
        } elseif ($version === 8) {
            $unixts = hexdec(substr($timehex, 0, 13));
            $subsec = self::decodeSubsec((hexdec(substr($timehex, 13)) << 2) + (hexdec(substr($uuid, 16, 1)) & 0x03));
            $retval = strval($unixts * self::V8_SUBSEC_RANGE + $subsec);
            $retval = substr_replace(str_pad($retval, 8, '0', \STR_PAD_LEFT), '.', -7, 0);
        }
        return $retval;
    }

    /**
     * Returns the UUID version.
     *
     * @param string $uuid The UUID string
     * @return int Version number of the UUID
     */
    public static function getVersion(string $uuid): int
    {
        return intval(self::stripExtras($uuid)[12], 16);
    }

    /**
     * UUID comparison.
     *
     * @param string $uuid1 The first UUID to test
     * @param string $uuid2 The second UUID to test
     * @return int Returns < 0 if uuid1 is less than uuid2; > 0 if uuid1 is
     *             greater than uuid2, and 0 if they are equal.
     */
    public static function cmp(string $uuid1, string $uuid2): int
    {
        return strcmp(self::stripExtras($uuid1), self::stripExtras($uuid2));
    }

    /**
     * The string standard representation of the UUID.
     *
     * @param string $uuid The UUID string
     * @return string The string standard representation of the UUID
     */
    public static function toString(string $uuid): string
    {
        $uhex = self::stripExtras($uuid);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($uhex, 4));
    }

    /**
     * @see UUID::uuid3() Alias
     * @return string
     */
    public static function v3(...$args): string
    {
        return self::uuid3(...$args);
    }
    /**
     * @see UUID::uuid4() Alias
     * @return string
     */
    public static function v4(): string
    {
        return self::uuid4();
    }
    /**
     * @see UUID::uuid5() Alias
     * @return string
     */
    public static function v5(...$args): string
    {
        return self::uuid5(...$args);
    }
    /**
     * @see UUID::uuid6() Alias
     * @return string
     */
    public static function v6(): string
    {
        return self::uuid6();
    }
    /**
     * @see UUID::uuid7() Alias
     * @return string
     */
    public static function v7(): string
    {
        return self::uuid7();
    }
    /**
     * @see UUID::uuid8() Alias
     * @return string
     */
    public static function v8(): string
    {
        return self::uuid8();
    }
}
