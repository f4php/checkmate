<?php

declare(strict_types=1);

namespace F4\Tests\Checkmate;
use PHPUnit\Framework\TestCase;
use F4\Checkmate;
use F4\Tests\Checkmate\Adapter\MockAdapter;
use InvalidArgumentException;

final class CheckmateTest extends TestCase
{
    public function testBasicFeatures(): void {
        $checkmate = new Checkmate();
        $this->assertSame(['mock-channel'], $checkmate->getSupportedChannels());
        $this->assertSame([], $checkmate->sendVerificationToken('mockAddress'));
        $this->assertSame(true, $checkmate->checkVerificationToken('mockAddress', '1234'));
        $this->assertSame(false, $checkmate->checkVerificationToken('mockAddress', '4321'));
    }
    public function testCustomAdapter(): void {
        $checkmate = new Checkmate()
            ->withAdapter(new MockAdapter());
        $this->assertSame(['mock-channel'], $checkmate->getSupportedChannels());
        $this->assertSame([], $checkmate->sendVerificationToken('mockAddress', 'mock-channel'));
        $this->assertSame(true, $checkmate->checkVerificationToken('mockAddress', '1234'));
        $this->assertSame(false, $checkmate->checkVerificationToken('mockAddress', '4321'));
    }
    public function testException(): void {
        $this->expectException(InvalidArgumentException::class);
        $checkmate = new Checkmate();
        $this->assertSame([], $checkmate->sendVerificationToken('mockAddress', 'unsupported-channel'));
    }
}