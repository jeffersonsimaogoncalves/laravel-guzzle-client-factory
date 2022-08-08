<?php

declare(strict_type=1);

namespace EinarHansen\Http\Guzzle;

use EinarHansen\Http\Factories\ClientFactoryInterface;
use EinarHansen\Http\Factories\ClientWithMiddlewareInterface;
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

class GuzzleClientFactory implements ClientFactoryInterface, ClientWithCookieJarInterface, ClientWithMiddlewareInterface
{
    /**
     * The Guzzle handler to be implemented.
     *
     * @var \GuzzleHttp\Handler\CurlHandler|\GuzzleHttp\Handler\MockHandler
     */
    private CurlHandler | MockHandler $handler;

    /**
     * The base URI to be used.
     *
     * @var string
     */
    private string $baseUri = '';

    /**
     * The number of seconds a request should be allowed to run.
     *
     * @var int
     */
    private int $timeout = 60;

    /**
     * The headers that should be sent with each request.
     *
     * @var array<string, string>
     */
    private array $headers = [];

    /**
     * If we should use cookies. It defaults to false.
     * false: we should not use cookies.
     * true: we use a cookiejar that is saved as an array (not persited)
     * CookieJarInterface: You can provide you own implementation of Cookies.
     *
     * @var \GuzzleHttp\Cookie\CookieJarInterface|bool
     */
    private CookieJarInterface | bool $cookieJar = false;

    /**
     * The middlewares that should be added to each request.
     *
     * @var array<int, callable>
     */
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
    ): self {
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

    public function getHandler(): CurlHandler|MockHandler
    {
        return $this->handler;
    }

    public function setHandler(CurlHandler|MockHandler $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Getter for headers to be used with each request.
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function addMiddleware(callable $middleware): self
    {
        $this->middleware[] = $middleware;

        return $this;
    }

    /**
     * Getter for the middlewares to be used with each request.
     *
     * @return array<int, callable>
     */
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
