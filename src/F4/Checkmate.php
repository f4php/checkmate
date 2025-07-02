<?php
/**
 *      This is Checkmate, user verification library for F4 framework
 *
 *      @package F4
 *      @author Dennis Kreminsky, dennis at kreminsky dot com
 *      @copyright Copyright (c) 2012-2025 Dennis Kreminsky, dennis at kreminsky dot com
 */

declare(strict_types=1);

namespace F4;

use F4\Config;
use F4\Checkmate\Adapter\AdapterInterface;
use F4\Checkmate\ExceptionHandlerTrait;
use F4\Checkmate\UserVerificationServiceInterface;

class Checkmate implements UserVerificationServiceInterface
{
    use ExceptionHandlerTrait;
    protected AdapterInterface $adapter;
    public function __construct(string|AdapterInterface $adapter = Config::CHECKMATE_ADAPTER_CLASS)
    {
        $this->withAdapter($adapter);
    }
    public function checkVerificationToken(string $address, string $code, array $options = []): bool
    {
        return $this->adapter->checkVerificationToken($address, $code, $options);
    }
    public function getSupportedChannels(): array
    {
        return $this->adapter->getSupportedChannels();
    }
    public function sendVerificationToken(string $address, string $channel = Config::CHECKMATE_DEFAULT_CHANNEL, array $options = []): mixed
    {
        return $this->adapter->sendVerificationToken($address, $channel, $options);
    }
    public function withAdapter(string|AdapterInterface $adapter): static
    {
        $this->adapter = $adapter instanceof UserVerificationServiceInterface ? $adapter : new $adapter();
        return $this;
    }
}