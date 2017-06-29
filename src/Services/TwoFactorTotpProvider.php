<?php

declare(strict_types=1);

namespace Rinvex\Fort\Services;

use Exception;
use Base32\Base32;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\Image\Png;

class TwoFactorTotpProvider
{
    /**
     * Interval between key regeneration.
     *
     * @var int
     */
    const KEY_REGENERATION = 30;

    /**
     * Length of the Token generated.
     *
     * @var int
     */
    const OPT_LENGTH = 6;

    /**
     * Characters valid for Base 32.
     *
     * @var string
     */
    const VALID_FOR_B32 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a digit secret key in base32 format.
     *
     * @param int $length
     *
     * @throws \Exception
     *
     * @return string
     */
    public function generateSecretKey($length = 32, $prefix = '')
    {
        $b32 = '234567QWERTYUIOPASDFGHJKLZXCVBNM';

        $secret = $prefix ? $this->toBase32($prefix) : '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= $b32[$this->getRandomNumber()];
        }

        $this->validateSecret($secret);

        return $secret;
    }

    /**
     * Get the current Unix Timestamp divided by the KEY_REGENERATION period.
     *
     * @return int
     **/
    public function getTimestamp()
    {
        return floor(microtime(true) / static::KEY_REGENERATION);
    }

    /**
     * Decode a base32 string into a binary string.
     *
     * @param string $b32
     *
     * @throws \Exception
     *
     * @return int
     */
    public function base32Decode($b32)
    {
        $b32 = mb_strtoupper($b32);

        $this->validateSecret($b32);

        return Base32::decode($b32);
    }

    /**
     * Generate one time password based on given params.
     *
     * @param string $binaryKey
     * @param int    $timestamp
     *
     * @throws \Exception
     *
     * @return string
     */
    public function oathHotp($binaryKey, $timestamp)
    {
        if (mb_strlen($binaryKey) < 8) {
            throw new Exception('Secret key is too short. Must be at least 16 base 32 characters');
        }

        // Counter must be 64-bit int
        $bin_counter = pack('N*', 0, $timestamp);

        $hash = hash_hmac('sha1', $bin_counter, $binaryKey, true);

        return str_pad($this->oathTruncate($hash), static::OPT_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Get the current one time password for a key.
     *
     * @param string $initalizationKey
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getCurrentOtp($initalizationKey)
    {
        $timestamp = $this->getTimestamp();

        $secretKey = $this->base32Decode($initalizationKey);

        return $this->oathHotp($secretKey, $timestamp);
    }

    /**
     * Verify given key against the current timestamp.
     * Check $window keys either side of the timestamp.
     *
     * @param string $b32seed
     * @param string $key          - User specified key
     * @param int    $window
     * @param bool   $useTimeStamp
     *
     * @return bool
     **/
    public function verifyKey($b32seed, $key, $window = 1, $useTimeStamp = true)
    {
        $timeStamp = $this->getTimestamp();

        if ($useTimeStamp !== true) {
            $timeStamp = (int) $useTimeStamp;
        }

        $binarySeed = $this->base32Decode($b32seed);

        for ($ts = $timeStamp - $window; $ts <= $timeStamp + $window; $ts++) {
            if (hash_equals($this->oathHotp($binarySeed, $ts), $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract the OTP from the SHA1 hash.
     *
     * @param string $hash
     *
     * @return int
     **/
    public function oathTruncate($hash)
    {
        $offset = ord($hash[19]) & 0xf;
        $temp = unpack('N', substr($hash, $offset, 4));
        $token = $temp[1] & 0x7fffffff;

        return substr((string) $token, -static::OPT_LENGTH);
    }

    /**
     * Remove invalid chars from a base 32 string.
     *
     * @param string $string
     *
     * @return mixed
     */
    public function removeInvalidChars($string)
    {
        return preg_replace('/[^'.static::VALID_FOR_B32.']/', '', $string);
    }

    /**
     * Generate a QR code data url to display inline.
     *
     * @param string $company
     * @param string $holder
     * @param string $secret
     * @param int    $size
     * @param string $encoding Default to UTF-8
     *
     * @return string
     */
    public function getQRCodeInline($company, $holder, $secret, $size = 200, $encoding = 'utf-8')
    {
        $url = $this->getQRCodeUrl($company, $holder, $secret);

        $renderer = new Png();
        $renderer->setWidth($size);
        $renderer->setHeight($size);

        $writer = new Writer($renderer);
        $data = $writer->writeString($url, $encoding);

        return 'data:image/png;base64,'.base64_encode($data);
    }

    /**
     * Create a QR code url.
     *
     * @param $company
     * @param $holder
     * @param $secret
     *
     * @return string
     */
    public function getQRCodeUrl($company, $holder, $secret)
    {
        return 'otpauth://totp/'.$company.':'.$holder.'?secret='.$secret.'&issuer='.$company.'';
    }

    /**
     * Get a random number.
     *
     * @param int $from
     * @param int $to
     *
     * @return int
     */
    private function getRandomNumber($from = 0, $to = 31)
    {
        return random_int($from, $to);
    }

    /**
     * Validate the secret.
     *
     * @param string $b32
     *
     * @throws \Exception
     *
     * @return void
     */
    private function validateSecret($b32)
    {
        if (! preg_match('/^['.static::VALID_FOR_B32.']+$/', $b32, $match)) {
            throw new Exception('Invalid characters in the base32 string.');
        }
    }

    /**
     * Encode a string to Base32.
     *
     * @param string $string
     *
     * @return mixed
     */
    public function toBase32($string)
    {
        $encoded = Base32::encode($string);

        return str_replace('=', '', $encoded);
    }
}
