# HTTP Message

[![Latest Version](https://img.shields.io/github/release/silvamfilipe/http-message.svg?style=flat-square)](https://github.com/silvamfilipe/http-message/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/silvamfilipe/http-message/master.svg?style=flat-square)](https://travis-ci.org/silvamfilipe/http-message)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/silvamfilipe/http-message.svg?style=flat-square)](https://scrutinizer-ci.com/g/silvamfilipe/http-message/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/silvamfilipe/http-message.svg?style=flat-square)](https://scrutinizer-ci.com/g/silvamfilipe/http-message)
[![Total Downloads](https://img.shields.io/packagist/dt/silvamfilipe/http-message.svg?style=flat-square)](https://packagist.org/packages/silvamfilipe/http-message)

PSR Http Message compliant package. The goal is to have an HTTP message abstraction
that can be used to implement a request/response strategy application.

This package is compliant with PSR-2 code standards and PSR-4 autoload standards. It
also applies the [semantic version 2.0.0](http://semver.org) specification.

## Install

Via Composer

``` bash
$ composer require fsilva/http-message
```

## Usage

``` php
use Fsilva/HttpMessage/Message;
use Fsilva/HttpMessage/Stream/Buffer;

$message = new Message();
$body = new Buffer();

$request = $message->withHeader('X-Requested-With', 'XMLHttpRequest')
    ->withHeader('User-Agent', 'PHP Request call')
    ->withBody($body)
    ->withoutHeader('pragma')
    ->withAddedHeader('X-Forwarded-For', ['client1', 'proxy1', 'proxy2]);
    
$request->hasHeader('User-Agent');  // Returns true
$request->getHeaders();     // Returns an associative array where header names are keys
                            // and values are array containing header values.
```

## Testing

``` bash
$ vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/silvamfilipe/http-message/blob/master/CONTRIBUTING.md) for details.

## Credits

- [Filipe Silva](https://github.com/silvamfilipe)
- [All Contributors](https://github.com/silvamfilipe/http-message/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
