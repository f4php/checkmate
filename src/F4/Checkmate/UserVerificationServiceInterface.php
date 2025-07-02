<?php
/**
 *      This is Checkmate, user verification library for F4 framework
 *
 *      @package F4
 *      @author Dennis Kreminsky, dennis at kreminsky dot com
 *      @copyright Copyright (c) 2012-2025 Dennis Kreminsky, dennis at kreminsky dot com
 */

declare(strict_types=1);

namespace F4\Checkmate;

use F4\Checkmate\Adapter\AdapterInterface;

interface UserVerificationServiceInterface extends AdapterInterface
{
    public function withAdapter(string|AdapterInterface $adapter): static;
}
