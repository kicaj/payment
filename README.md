# Payment plugin for CakePHP

**NOTE:** It's still in development mode, do not use in production yet!

## Requirements

It is developed for CakePHP min. 4.0.

## Installation

You can install plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require kicaj/payment dev-master
```

### Load the Plugin

Ensure the Payment plugin is loaded in your src/Application.php file

```
$this->addPlugin('Payment');
```

or add manually by `cake` command

```
cake plugin load Payment
```

### Configuration

Now use Migrations plugin to create tables in your database.

```
cake migrations migrate -p Payment
```


## TODOs

- Payment gateways
  - [ ] P24 (przelewy24.pl)
  - [x] Payu (payu.pl)
  - [ ] Dotpay (dotpay.pl)
