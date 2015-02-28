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

## Installing via Composer

The recommended way to install `Fsilva\HttpMessage` package is through
[Composer][].

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

You can add `Fsilva\HttpMessage` package as a dependency using the composer.phar CLI:

``` bash
$ composer require fsilva/http-message
```

Alternatively, you can `Fsilva\HttpMessage` package as a dependency in your
project’s existing composer.json file:

```javascript
{
    "require": {
        "fsilva/http-message": "*"
    }
}  
```

## Basic usage

### Creating a very basic HTTP message
``` php
use Fsilva/HttpMessage/Message;
use Fsilva/HttpMessage/Stream/Buffer;

$message = new Message();
$body = new Buffer();
$body->write(json_encode(['some' => 'value']));

$request = $message->withProtocolVersion(Message::HTTP_1_1)
    ->withHeader('X-Requested-With', 'XMLHttpRequest')
    ->withHeader('User-Agent', 'PHP Request call')
    ->withBody($body)
    ->withoutHeader('pragma')
    ->withAddedHeader('X-Forwarded-For', ['client1', 'proxy1', 'proxy2']);
    
$strMessage = "GET / HTTP/". $request->getProtocolVersion();

foreach($request->getHeaders() as $name => $values) {
    $strMessage .= "\n{$name}: ". implode(', ', $values);
}

// $strMessage is now a very simple HTTP message.
$strMessage .= "\n\n". $request->getBody()->getContents();

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

[Composer]: https://getcomposer.org/
