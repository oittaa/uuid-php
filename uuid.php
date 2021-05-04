<?php
/**
 * Represents a universally unique identifier (UUID), according to RFC 4122.
 *
 * This class provides the static methods `uuid3()`, `uuid4()`, and
 * `uuid5()` for generating version 3, 4, and 5 UUIDs as specified in RFC 4122.
 *
 * If all you want is a unique ID, you should call `uuid4()`.
 *
 * @link http://tools.ietf.org/html/rfc4122
 * @link http://en.wikipedia.org/wiki/Universally_unique_identifier
 * @link http://www.php.net/manual/en/function.uniqid.php#94959
 */
class UUID
{
    /**
     * When this namespace is specified, the name string is a fully-qualified domain name.
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    /**
     * When this namespace is specified, the name string is a URL.
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    /**
     * When this namespace is specified, the name string is an ISO OID.
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    /**
     * When this namespace is specified, the name string is an X.500 DN in DER or a text output format.
     * @link http://tools.ietf.org/html/rfc4122#appendix-C
     */
    const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';
    /**
     * The nil UUID is special form of UUID that is specified to have all 128 bits set to zero.
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.7
     */
    const NIL = '00000000-0000-0000-0000-000000000000';

    /**
     * 0x01b21dd213814000 is the number of 100-ns intervals between the
     * UUID epoch 1582-10-15 00:00:00 and the Unix epoch 1970-01-01 00:00:00.
     * @link https://tools.ietf.org/html/rfc4122#section-4.1.4
     */
    const TIME_OFFSET_INT = 0x01b21dd213814000;

    private static function getBytes($uuid) {
        if (!self::isValid($uuid)) {
            throw new InvalidArgumentException('Invalid UUID string: ' . $uuid);
        }
        // Get hexadecimal components of UUID
        $uhex = str_replace(array(
            'urn:',
            'uuid:',
            '-',
            '{',
            '}'
        ), '', $uuid);

        // Binary Value
        $ustr = '';

        // Convert UUID to bits
        for ($i = 0; $i < strlen($uhex); $i += 2) {
            $ustr .= chr(hexdec($uhex[$i] . $uhex[$i + 1]));
        }
        return $ustr;
    }

    private static function uuidFromHash($hash, $version) {
        return sprintf('%08s-%04s-%04x-%04x-%12s',
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
            substr($hash, 20, 12));
    }

    /**
     * Generate a version 3 UUID based on the MD5 hash of a namespace identifier
     * (which is a UUID) and a name (which is a string).
     *
     * @param string $namespace The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     * @return string
     */
    public static function uuid3($namespace, $name) {
        $nbytes = self::getBytes($namespace);

        // Calculate hash value
        $hash = md5($nbytes . $name);

        return self::uuidFromHash($hash, 3);
    }

    /**
     * Generate a version 4 (random) UUID.
     *
     * @return string
     */
    public static function uuid4() {
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
     * @return string
     */
    public static function uuid5($namespace, $name) {
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
     * @return string
     */
    public static function uuid6() {
        $time = microtime(false);
        $time = substr($time, 11) . substr($time, 2, 7);
        $time = str_pad(dechex($time + self::TIME_OFFSET_INT), 16, '0', \STR_PAD_LEFT);
        $time = sprintf('%012s6%03s',
            substr($time, -15, 12),
            substr($time, -3));
        $bytes = random_bytes(8);
        $hash = $time . bin2hex($bytes);
        return self::uuidFromHash($hash, 6);
    }

    /**
     * Check if a string is a valid UUID.
     *
     * @param string $uuid The string UUID to test
     * @return boolean
     */
    public static function isValid($uuid) {
        return preg_match('/^(urn:)?(uuid:)?(\{)?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}(?(3)\}|)$/i', $uuid) === 1;
    }

    /**
     * Check if two UUIDs are equal.
     *
     * @param string $uuid1 The first UUID to test
     * @param string $uuid2 The second UUID to test
     * @return boolean
     */
    public static function equals($uuid1, $uuid2) {
        return self::getBytes($uuid1) === self::getBytes($uuid2);
    }
}
