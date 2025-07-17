# Overview

Checkmate is a user verification package for [F4](https://github.com/f4php/f4), a lighweight web development framework.

Checkmate uses [Twilio Verify API](https://www.twilio.com/docs/verify/api) by default.

Checkmate implements a very simple `F4\Checkmate\UserVerificationServiceInterface`, so you can create your own drop-in replacement if needed.

# Quick Start

```
$ composer require f4php/checkmate
```

# SDK Architecture

Checkmate uses `Guzzle` to interact with external APIs to send and verify one-time authentication tokens using various channels.

This SDK supports `F4\Checkmate\Adapter\AdapterInterface`, which allows you to add seamless support for virtually any external provider.

The following constants MUST be defined in an F4 environment config class:

```php

namespace F4;

class Config extends AbstractConfig
{
    // ...
    public string const CHECKMATE_ADAPTER_CLASS = \F4\Checkmate\Adapter\TwilioAdapter::class; // the default adapter
    public string const CHECKMATE_DEFAULT_CHANNEL = 'email'; // must be one of Adapter's supported channels, see below for channels supported by Twilio
    // ...
}
```

Additionally, [Twilio](https://www.twilio.com/docs/verify/api) integration requires that the following constants MUST be defined in an F4 environment config class:

```php

namespace F4;

use F4\Config\SensitiveParameter;

class Config extends AbstractConfig
{
    // ...
    #[SensitiveParameter]
    public string const TWILIO_ACCOUNT_SID = '...';
    #[SensitiveParameter]
    public string const TWILIO_AUTH_TOKEN = '...';
    #[SensitiveParameter]
    public string const TWILIO_VERIFY_SID = '...';
    // ...
}
```

`TwilioAdapter` supports the following channels: `auto`, `call`, `email`, `sms`, `sna`, `whatsapp`.

## Simple example

```php
use F4\Checkmate;

$checkmate = new Checkmate();

$checkmate->sendVerificationToken('email@address.com');
$booleanCheckResult = $checkmate->checkVerificationToken('email@address.com', '1234');

```

## Advanced example

```php

use F4\Checkmate;
use F4\Checkmate\Adapter\TwilioAdapter;
use Throwable;

$checkmate = new Checkmate()
  ->withAdapter(new TwilioAdapter()
    ->withOption('accountSid', '...')
    ->withOption('authToken', '...')
    ->withOption('verifySid', '...')
    ->withOption('defaultChannel', 'sms')
    ->on(Throwable::class, function(Throwable $exception) {
      // handle adapter exception
    })
  )
  ->on(Throwable::class, function(Throwable $exception) {
    // handle checkmate exception
  });

$checkmate->sendVerificationToken('+1234567890', 'whatsapp');
$booleanCheckResult = $checkmate->checkVerificationToken('+1234567890', '1234');

```
