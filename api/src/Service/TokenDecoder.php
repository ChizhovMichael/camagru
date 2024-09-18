<?php

declare(strict_types=1);

namespace Camagru\Service;

class TokenDecoder
{
    public static function encode(array $data = [], $alg = 'sha1', $salt = ''): string
    {
        $salt = base64_encode($salt);
        $header = base64_encode(json_encode(['alg' => $alg, 'typ' => 'JWT']));
        $jsonPayload = json_encode($data);

        $payload = base64_encode($jsonPayload);
        $sign = hash_hmac($alg, $header . '.' . $payload, $salt);

        return sprintf('%s.%s.%s', $header, $payload, $sign);
    }

    /**
     * @throws \JsonException
     */
    public static function decode($token, $salt = '')
    {
        $tokenData = explode('.', $token);
        if (count($tokenData) < 3) {
            return false;
        }

        list($rawHeader, $rawPayload, $rawSign) = $tokenData;

        $header = json_decode(base64_decode($rawHeader), true, 512, JSON_THROW_ON_ERROR);
        if (!$header) {
            return false;
        }

        if (empty($header['typ']) || $header['typ'] !== 'JWT') {
            return false;
        }

        // check exists hash alg
        if (empty($header['alg'])) {
            return false;
        }

        // verify signature
        if ($rawSign !== hash_hmac($header['alg'], $rawHeader . '.' . $rawPayload, base64_encode($salt))) {
            return false;
        }

        $payload = base64_decode($rawPayload);
        if (!$payload) {
            return false;
        }

        return json_decode($payload, true);
    }
}