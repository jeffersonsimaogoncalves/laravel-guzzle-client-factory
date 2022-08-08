Guzzle Client Factory
===========

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
docker run -it -v $(pwd):/app  php:8.1-alpine /app/vendor/bin/pint /app
docker run -it -v $(pwd):/app  php:8.1-alpine /app/vendor/bin/phpstan --level=9 analyse /app/src
```

## License
The MIT License (MIT).
