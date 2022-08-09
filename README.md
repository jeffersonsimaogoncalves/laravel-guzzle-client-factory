# Guzzle Client Factory

[![Latest Version on Packagist](https://img.shields.io/packagist/v/einar-hansen/guzzle-client-factory.svg?style=flat-square)](https://packagist.org/packages/einar-hansen/guzzle-client-factory)
[![Total Downloads](https://img.shields.io/packagist/dt/einar-hansen/guzzle-client-factory.svg?style=flat-square)](https://packagist.org/packages/einar-hansen/guzzle-client-factory)

This repository holds the GuzzleFactory for creating new Guzzle clients.

The installable [package][package-url] and [dependents][implementation-url] are listed on Packagist.

[package-url]: https://packagist.org/packages/einar-hansen/guzzle-client-factory
[implementation-url]: https://packagist.org/packages/einar-hansen/guzzle-client-factory/dependents

## Installation
```bash
composer require einar-hansen/guzzle-client-factory
```

## Testing
```bash
# Install packages
docker run --rm --interactive --tty --volume $(pwd):/app composer install

# Run code style formatting and static analysis
docker run -it -v $(pwd):/app -w /app php:8.1-alpine vendor/bin/pint
docker run -it -v $(pwd):/app -w /app php:8.1-alpine vendor/bin/phpstan --level=9 analyse src
```

## License
The MIT License (MIT).
