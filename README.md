# Symfony DI tools

[![Author](https://img.shields.io/badge/author-@RemiSan-blue.svg?style=flat-square)](https://twitter.com/RemiSan)
[![Build Status](https://img.shields.io/travis/remi-san/sf-di-tools/master.svg?style=flat-square)](https://travis-ci.org/remi-san/sf-di-tools)
[![Quality Score](https://img.shields.io/scrutinizer/g/remi-san/sf-di-tools.svg?style=flat-square)](https://scrutinizer-ci.com/g/remi-san/sf-di-tools)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Packagist Version](https://img.shields.io/packagist/v/remi-san/sf-di-tools.svg?style=flat-square)](https://packagist.org/packages/remi-san/sf-di-tools)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/remi-san/sf-di-tools.svg?style=flat-square)](https://scrutinizer-ci.com/g/remi-san/sf-di-tools/code-structure)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/dcf877df-84dd-4852-8a25-797c1c542982/small.png)](https://insight.sensiolabs.com/projects/dcf877df-84dd-4852-8a25-797c1c542982)

Providing easy-to-use tools for symfony

Content
-------

This lib provides the following util classes:
 - `YamlCachedContainerBuilder` allows to manage cached `Symfony Dependency Injector` container with `YAML` config files easily.

Usage example
------------

**YamlCachedContainerBuilder**

```php
$cacheBuilder = new YamlCachedContainerBuilder('path/to/cache/dir', false);
$container = $cacheBuilder->build('path/to/config/dir', 'root-config-file.yml', 'MyCachedContainer', 'MyProject\\Cache');
```
