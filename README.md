# React adapter for PHP HTTP

[![Latest Version](https://img.shields.io/github/release/php-http/react-adapter.svg?style=flat-square)](https://github.com/php-http/react-adapter/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/php-http/react-adapter.svg?style=flat-square)](https://travis-ci.org/php-http/react-adapter)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/php-http/react-adapter.svg?style=flat-square)](https://scrutinizer-ci.com/g/php-http/react-adapter)
[![Quality Score](https://img.shields.io/scrutinizer/g/php-http/react-adapter.svg?style=flat-square)](https://scrutinizer-ci.com/g/php-http/react-adapter)
[![Total Downloads](https://img.shields.io/packagist/dt/php-http/react-adapter.svg?style=flat-square)](https://packagist.org/packages/php-http/react-adapter)

[ReactHTTP](http://reactphp.org/) adapter.


## Install

Via Composer

``` bash
$ composer require php-http/react-adapter
```

## Features

This lib adapt ReactPHP behaviour to use the [PSR7 interfaces](https://github.com/php-fig/http-message).
Also `sync` and `async` requests are possible without more code than a function call.

For a deeper `async` comprehension, you must check how ReactPHP engine work.

## Usage

The `ReactHttpAdapter` class need a [message factory](https://github.com/php-http/message-factory) in order to work:

```php
$client = new Http\Adapter\ReactHttpAdapter($messageFactory);
```

For more control, it can also be configured with a specific `React\EventLoop\LoopInterface` and / or a specific `React\HttpClient\Client`:

```php
$loop = Http\Adapter\ReactFactory::buildEventLoop();
$client = new Http\Adapter\ReactHttpAdapter($messageFactory, $loop);

//or

$reactClient = Http\Adapter\ReactFactory::buildHttpClient($loop);
$client = new Http\Adapter\ReactHttpAdapter(
    $messageFactory,
    $loop,
    $reactClient
);
```

If you don't want to use the `Http\Adapter\ReactFactory` to build instances you must rely on React documentation on Github: https://github.com/reactphp/http-client#example

## Testing

First launch the http server:

```bash
$ ./vendor/bin/http_test_server > /dev/null 2>&1 &
```

Then the test suite:

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.


## Security

If you discover any security related issues, please contact us at [security@php-http.org](mailto:security@php-http.org).


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
