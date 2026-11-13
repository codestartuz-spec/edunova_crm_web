<?php

namespace App\Services\Sms;

final class SmsResult
{
    public function __construct(public readonly bool $ok, public readonly string $response, public readonly string $provider) {}

    public static function ok(string $provider, string $response): self
    {
        return new self(true, $response, $provider);
    }

    public static function fail(string $provider, string $response): self
    {
        return new self(false, $response, $provider);
    }
}
