<?php
declare(strict_types=1);

namespace F4\Tests\Checkmate\Adapter;

use F4\Checkmate\Adapter\AdapterInterface;
use F4\Checkmate\ExceptionHandlerTrait;
use F4\Checkmate\OptionsAwareAdapterTrait;
use InvalidArgumentException;

class MockAdapter implements AdapterInterface
{
    use ExceptionHandlerTrait;
    use OptionsAwareAdapterTrait;

    public function checkVerificationToken(string $to, string $code, array $options = []): bool
    {
        return $code === '1234';
    }
    public function getSupportedChannels(): array
    {
        return ['mock-channel'];
    }
    public function sendVerificationToken(string $to, ?string $channel = null, array $options = []): mixed
    {
        if (!in_array($channel, $this->getSupportedChannels())) {
            throw new InvalidArgumentException("Unsupported channel: {$channel}");
        }
        return [];
    }
   public function withOption(string $name, mixed $value): static
    {
        return $this;
    }
}