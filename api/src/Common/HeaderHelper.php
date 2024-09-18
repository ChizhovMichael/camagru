<?php

declare(strict_types=1);

namespace Camagru\Common;

class HeaderHelper
{
    public const DISPOSITION_ATTACHMENT = 'attachment';

    public static function makeDisposition(string $disposition, string $filename, string $filenameFallback = ''): string
    {
        $params = ['filename' => $filenameFallback];
        if ($filename !== $filenameFallback) {
            $params['filename*'] = "utf-8''".rawurlencode($filename);
        }

        return $disposition.'; '.self::toString($params, ';');
    }

    public static function toString(array $assoc, string $separator): string
    {
        $parts = [];
        foreach ($assoc as $name => $value) {
            if (true === $value) {
                $parts[] = $name;
            } else {
                $parts[] = $name.'='.self::quote($value);
            }
        }

        return implode($separator.' ', $parts);
    }

    public static function quote(string $s): string
    {
        if (preg_match('/^[a-z0-9!#$%&\'*.^_`|~-]+$/i', $s)) {
            return $s;
        }

        return '"'.addcslashes($s, '"\\"').'"';
    }
}
