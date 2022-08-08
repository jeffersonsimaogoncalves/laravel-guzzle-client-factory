<?php

declare(strict_type=1);

namespace EinarJohan\Http\Factories;

use GuzzleHttp\Cookie\CookieJarInterface;

interface HttpClientFactoryWithCookieJarInterface
{
    public function getCookieJar(): CookieJarInterface | bool;

    public function setCookieJar(CookieJarInterface | bool $cookieJar): self;
}
