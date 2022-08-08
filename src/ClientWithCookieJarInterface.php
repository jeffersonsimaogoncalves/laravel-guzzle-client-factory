<?php

declare(strict_type=1);

namespace EinarHansen\Http\Guzzle;

use GuzzleHttp\Cookie\CookieJarInterface;

interface ClientWithCookieJarInterface
{
    public function getCookieJar(): CookieJarInterface | bool;

    public function setCookieJar(CookieJarInterface | bool $cookieJar): self;
}
