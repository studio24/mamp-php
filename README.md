# Manage PHP versions on the command line in MAMP

Simple tool to help manage and switch PHP versions when using MAMP. We don't use MAMP's native PHP CLI integration since 
this (in our experience) does not work reliably.

## Usage

Show current and available versions:

    bin/mamp-php show

Switch to a different version of PHP:

    bin/mamp-php use 7.1.8

## Installation

This CLI scripts uses the [Symfony Console](http://symfony.com/doc/current/components/console/index.html) component. 
Use [Composer](http://getcomposer.org) to load this.

To install run the following commands:

```
git clone https://github.com/studio24/mamp-php
cd mamp-php
composer install
```

## bash_profile
Make sure you don't already have a custon PHP version set in your `~/.bash_profile` file, for example `/Applications/MAMP/bin/php/php7.1.8/bin/php`

MAMP's default functionality saves paths in `~/.profile` which if you don't want to use MAMP to manange CLI paths you will need to remove.

## License

MIT License (MIT)  
Copyright (c) 2018 Studio 24 Ltd (www.studio24.net)

