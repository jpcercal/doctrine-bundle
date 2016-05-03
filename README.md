# Cekurte\DoctrineBundle

[![Build Status](https://img.shields.io/travis/jpcercal/doctrine-bundle/master.svg?style=square)](http://travis-ci.org/jpcercal/doctrine-bundle)
[![Code Climate](https://codeclimate.com/github/jpcercal/doctrine-bundle/badges/gpa.svg)](https://codeclimate.com/github/jpcercal/doctrine-bundle)
[![Coverage Status](https://coveralls.io/repos/jpcercal/doctrine-bundle/badge.svg)](https://coveralls.io/r/jpcercal/doctrine-bundle)
[![Latest Stable Version](https://img.shields.io/packagist/v/cekurte/doctrinebundle.svg?style=square)](https://packagist.org/packages/cekurte/doctrinebundle)
[![License](https://img.shields.io/packagist/l/cekurte/doctrinebundle.svg?style=square)](https://packagist.org/packages/cekurte/doctrinebundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6f36066c-20d6-4985-98c9-833f628206ef/mini.png)](https://insight.sensiolabs.com/projects/6f36066c-20d6-4985-98c9-833f628206ef)

- An extension to Doctrine DBAL that can be used to switch between database connections in runtime with Symfony 2, **contribute with this project**!

**If you liked of this library, give me a *star =)*.**

## Installation

- The package is available on [Packagist](http://packagist.org/packages/cekurte/doctrinebundle).
- The source files is [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) compatible.
- Autoloading is [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) compatible.

```shell
composer require cekurte/doctrinebundle
```

After, register the bundle in your AppKernel like this:

```php
// app/AppKernel.php

// ...
public function registerBundles()
{
    $bundles = array(
        // ...
        new Cekurte\DoctrineBundle\CekurteDoctrineBundle(),
        // ...
    );

    // ...
    return $bundles;
}
```

## Documentation

Well, firstly you must configure a doctrine database connection. So, add this in your config file.

```yml
# app/config/config.yml

# ...
doctrine:
    dbal:
        default_connection: dynamic
        connections:
            dynamic:
                driver:   "%database_driver%"
                host:     "%database_host%"
                port:     "%database_port%"
                dbname:   "%database_name%"
                user:     "%database_user%"
                password: "%database_password%"
                charset:  UTF8
                wrapper_class: "Cekurte\DoctrineBundle\DBAL\ConnectionWrapper"
```

After that, when you need change the database connection you can retrieve a service id named `doctrine.dbal.dynamic_connection` and call the method `forceSwitch`, see the example below.

```php
<?php

namespace YourNamespace\YourBundleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class YourController extends Controller
{
    public function indexAction()
    {
        // ...

        // Change the current database connection...
        $this
            ->get('doctrine.dbal.dynamic_connection')
            ->forceSwitch(
                $dbHost,
                $dbName,
                $dbUser,
                $dbPassword,
                $dbOptions
            )
        ;
    }
}
```

Contributing
------------

1. Give me a star **=)**
1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Make your changes
4. Run the tests, adding new ones for your own code if necessary (`vendor/bin/phpunit`)
5. Commit your changes (`git commit -am 'Added some feature'`)
6. Push to the branch (`git push origin my-new-feature`)
7. Create new Pull Request
