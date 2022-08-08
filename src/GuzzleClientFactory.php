<?php

declare(strict_type=1);

namespace EinarJohan\Http\Factories;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleClientFactory implements HttpClientFactoryInterface, HttpClientFactoryWithCookieJarInterface, HttpClientFactoryWithMiddlwareInterface
{
    private CurlHandler | MockHandler $handler;
    private string $baseUri = '';
    private int $timeout = 60;
    private array $headers = [];
    private CookieJarInterface | bool $cookieJar = false;
    private array $middleware = [];

    final public function __construct()
    {
    }

    public static function new(): static
    {
        return new static();
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function setBaseUri(string $uri): self
    {
        $this->baseUri = $uri;

        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getCookieJar(): CookieJarInterface | bool
    {
        return $this->cookieJar;
    }

    public function setCookieJar(CookieJarInterface | bool $cookieJar): self
    {
        $this->cookieJar = $cookieJar;

        return $this;
    }

    public function withRetries(
        callable $decider = null,
        callable $delay = null,
        int $maxRetries = 3
    ) {
        $this->middleware[] = Middleware::retry(
            $decider ?? function (
                $retries,
                RequestInterface $request,
                ResponseInterface $response = null,
                ClientExceptionInterface $exception = null
            ) use ($maxRetries) {
                if ($retries >= $maxRetries) {
                    return false;
                }

                // Retry connection exceptions
                if ($exception instanceof NetworkExceptionInterface) {
                    return true;
                }

                if ($response) {
                    // Retry on server errors
                    if ($response->getStatusCode() >= 500) {
                        return true;
                    }
                }

                return false;
            },
            $delay
        );

        return $this;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function setHandler($handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function addMiddleware($middleware): self
    {
        $this->middleware[] = $middleware;

        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middleware;
    }

    public function create(): ClientInterface
    {
        $stack = HandlerStack::create($this->getHandler());
        foreach ($this->getMiddlewares() as $middleware) {
            $stack->push($middleware);
        }

        return new Client([
            'base_uri' => $this->getBaseUri(),
            'headers' => $this->getHeaders(),
            'timeout' => $this->getTimeout(),
            'cookies' => $this->getCookieJar(),
            'allow_redirects' => false,
            'handler' => $stack,
        ]);
    }
}
