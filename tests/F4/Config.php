<?php

declare(strict_types=1);

namespace F4;

use F4\Tests\Checkmate\Adapter\MockAdapter;

class Config {
    public const string CHECKMATE_ADAPTER_CLASS = MockAdapter::class;
    public const string CHECKMATE_DEFAULT_CHANNEL = 'mock-channel';
}