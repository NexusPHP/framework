<?php

declare(strict_types=1);

/**
 * This file is part of the Nexus framework.
 *
 * (c) John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Nexus\Encryption;

use Nexus\Encryption\Exception\InvalidEncodingVariantException;

final readonly class Cryptex
{
    /**
     * Version tag in the form 'v' + major + minor + patch.
     *
     * Note: Increment only the major and minor versions when making updates.
     */
    public const string HEADER_VERSION = "\x76\x01\x00\x00";

    /**
     * Length of the version header.
     */
    public const int HEADER_VERSION_SIZE = 4;

    public const int ENCODE_NONE = 0;
    public const int ENCODE_BASE64_ORIGINAL = 1;
    public const int ENCODE_BASE64_ORIGINAL_NO_PADDING = 2;
    public const int ENCODE_BASE64_URL_SAFE = 3;
    public const int ENCODE_BASE64_URL_SAFE_NO_PADDING = 4;
    public const int ENCODE_HEX = 5;

    /**
     * Gets the encoder. If `self::ENCODE_NONE` is chosen, any subsequent encode
     * and decode operations will just return the strings as-is.
     *
     * @throws InvalidEncodingVariantException
     */
    public static function encoder(int $variant = self::ENCODE_HEX): Encoding\EncoderInterface
    {
        return match ($variant) {
            self::ENCODE_NONE => new Encoding\NullEncoder(),
            self::ENCODE_BASE64_ORIGINAL => new Encoding\Base64OriginalEncoder(),
            self::ENCODE_BASE64_ORIGINAL_NO_PADDING => new Encoding\Base64OriginalNoPaddingEncoder(),
            self::ENCODE_BASE64_URL_SAFE => new Encoding\Base64UrlSafeEncoder(),
            self::ENCODE_BASE64_URL_SAFE_NO_PADDING => new Encoding\Base64UrlSafeNoPaddingEncoder(),
            self::ENCODE_HEX => new Encoding\HexEncoder(),
            default => throw new InvalidEncodingVariantException(),
        };
    }
}
