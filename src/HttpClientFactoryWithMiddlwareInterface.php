<?php

declare(strict_type=1);

namespace EinarJohan\Http\Factories;

interface HttpClientFactoryWithMiddlwareInterface
{
    public function addMiddleware($middleware): self;

    public function getMiddlewares(): array;
}
