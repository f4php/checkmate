<?php
declare(strict_types=1);

namespace F4\Checkmate;

use Composer\Pcre\Preg;
use F4\Checkmate\Adapter\AdapterInterface;
use F4\Checkmate\ExceptionHandlerTrait;
use F4\Checkmate\OptionsAwareAdapterTrait;
use F4\Config;
use GuzzleHttp\Client as Guzzle;

use ErrorException;
use InvalidArgumentException;
use Throwable;

use function filter_var;
use function http_build_query;
use function in_array;
use function json_decode;
use function strpos;
use function strtoupper;

// Documentation: https://www.twilio.com/docs/verify/api

class TwilioAdapter implements AdapterInterface
{
    use ExceptionHandlerTrait;
    use OptionsAwareAdapterTrait;

    public const string BASE_URL = 'https://verify.twilio.com/v2';
    public const int REQUEST_TIMEOUT = 10;
    public const array SUPPORTED_CHANNELS = ['auto', 'call', 'email', 'sms', 'sna', 'whatsapp'];
    protected array $options = [];
    public function __construct()
    {
        $this->setOption('accountSid', Config::TWILIO_ACCOUNT_SID);
        $this->setOption('authToken', Config::TWILIO_AUTH_TOKEN);
        $this->setOption('verifySid', Config::TWILIO_VERIFY_SID);
        $this->setOption('defaultChannel', Config::TWILIO_DEFAULT_CHANNEL);
    }
    public function checkVerificationToken(string $to, string $code, array $options = []): bool
    {
        if (!self::isValidEmail($to) && !self::isValidE164($to)) {
            throw new InvalidArgumentException("Invalid address: {$to}");
        }
        $result = (array) $this->sendRequest(
            "/Services/{$this->getOption('verifySid')}/VerificationCheck",
            'POST',
            [
                ...$options,
                'To' => $to,
                'Code' => $code,
            ],
        );
        return ($result['status'] ?? null) === 'approved';
    }
    public function getSupportedChannels(): array
    {
        return self::SUPPORTED_CHANNELS;
    }
    public function sendVerificationToken(string $to, string $channel, array $options = []): mixed
    {
        $channel = $channel ?? $this->getOption('defaultChannel');
        if (!in_array($channel, $this->getSupportedChannels())) {
            throw new InvalidArgumentException("Unsupported channel: {$channel}");
        }
        if (!self::isValidEmail($to) && !self::isValidE164($to)) {
            throw new InvalidArgumentException("Invalid address: {$to}");
        }
        return $this->sendRequest(
            "/Services/{$this->getOption('verifySid')}/Verifications",
            'POST',
            [
                ...$options,
                'To' => $to,
                'Channel' => $channel
            ],
        );
    }

    // Utility methods below

    protected function sendRequest(string $URL = '/', string $method = 'GET', array $data = [], array $headers = [], array $attachments = [], int $recursionLock = 4)
    {
        if ($recursionLock <= 0) {
            throw new ErrorException('Recursion lock error', 500);
        }
        $requestMethod = strtoupper($method);
        $requestURL = static::BASE_URL . $URL;
        $requestOptions = [
            'connect_timeout' => static::REQUEST_TIMEOUT,
            'headers' => [
                // 'Content-Type' => 'application/json; charset=utf-8',
                ...$headers,
            ],
            'auth' => [
                $this->getOption('accountSid'),
                $this->getOption('authToken'),
            ],
        ];
        if (!empty($data)) {
            if ($requestMethod === 'GET') {
                $requestURL .= '?' . http_build_query($data, '', '&', PHP_QUERY_RFC3986);
            } else {
                $requestOptions['form_params'] = $data;
            }
        }
        try {
            $response = (new Guzzle)->request($requestMethod, $requestURL, $requestOptions);
            $responseBody = (string) $response->getBody();
            if (false !== strpos($response->getHeaderLine('Content-Type'), 'application/json')) {
                $responseBody = json_decode($responseBody, true, JSON_THROW_ON_ERROR);
            }
        } catch (Throwable $e) {
            $this->processException($e);
        }
        return $responseBody;
    }
    protected static function isValidEmail(string $address): bool
    {
        return filter_var($address, FILTER_VALIDATE_EMAIL) !== false;
    }
    protected static function isValidE164(string $address): bool
    {
        return Preg::isMatch('/^\+[1-9]\d{1,14}$/', $address);
    }
    public function withOption(string $name, mixed $value): static
    {
        $this->options[$name] = $value;
        return $this;
    }
}