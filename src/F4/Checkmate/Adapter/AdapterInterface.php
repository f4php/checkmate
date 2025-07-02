<?php
declare(strict_types=1);

namespace F4\Checkmate\Adapter;

interface AdapterInterface
{
    public function checkVerificationToken(string $to, string $code, array $options = []): bool;
    public function sendVerificationToken(string $to, string $channel, array $options = []): mixed;
    public function getSupportedChannels(): array;
}
