# Plist

A simple php library for reading and writing plist files in xml format

[![Latest Stable Version](https://poser.pugx.org/mcfedr/plist/v/stable.png)](https://packagist.org/packages/mcfedr/plist)
[![License](https://poser.pugx.org/mcfedr/plist/license.png)](https://packagist.org/packages/mcfedr/plist)
[![Build Status](https://travis-ci.org/mcfedr/plist.svg?branch=master)](https://travis-ci.org/mcfedr/plist)

- Handling 'invalid' xml characters (such as received from MDM)
- Should handle larger files

## Install

```bash
composer require mcfedr/plist
```

## Usage

### Reading

```php
$reader = new PlistReader();
$plist = $reader->read($xml);
```

### Writing

```php
$plist = new Plist();
$writer = new PlistWriter();
$xml = $writer->write($plist);
```
