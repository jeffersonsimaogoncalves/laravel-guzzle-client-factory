<?php

declare(strict_type=1);

namespace EinarHansen\Http\Guzzle;

use GuzzleHttp\Cookie\CookieJarInterface;

interface ClientWithCookieJarInterface
{
    /**
     * Get the cookie jar.
     */
    public function getCookieJar(): CookieJarInterface | bool;

    /**
     * Factory accepts an instance of a cookie jar or a bool to determine
     * if cookies should be used.
     *
     * Here is a description of the possible parameters that should be passed
     * - false: No cookies
     * - true: Use cookies, apply default array cookie jar (no persitance)
     * - CookieJarInterface: You can provide you own implementation of Cookies.
     */
    public function setCookieJar(CookieJarInterface | bool $cookieJar): self;
}
