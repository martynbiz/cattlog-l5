# Cattlog #

## Introduction ##

A CLI tool for scanning text documents and extracting keys. Store those keys in
text files (e.g. PHP array, CSV, JSON, or custom format). Especially useful for
scanning view files to extract keys for multi-language applications, although could
have other uses too.

## Installation ##

Install with composer

    composer require martynbiz/cattlog

## Usage ##

### Initialize config ###

Cattlog requires a cattlog.json config file at the root of the project to know where to scan source, save to file, filter to use etc:

    ./vendor/bin/cattlog init

This will create the following file (/path/to/project/cattlog.json):

```json
{
    "src": [
	   "./path/to/source/files"
    ],
    "dest": "./path/to/dest/{name}.json",
    "format": "json"
    "pattern": [
   	    "_\\(\\'(.*)\',",
        "_n\\(\\'(.*)\',"
    ],
    "valid_names": [
        "en",
        "ja"
    ]
}
```

Note: as JSON will use the backslash to escape characters within it's strings, and regular expressions require backslashes tp escape special characters (e.g. "(" ), double backslashes may be require. For example, the regular expression "/\$ml\->_(\'\')/" would be represented as "/\\$ml\\->_(\\'\\')/".

TODO Need to implement support for multiple patterns

### Update keys ###

Running this command will update all the keys from source files for the en file. If it finds new keys in source, it will save keys with empty values. It will also remove any keys which as no longer used. It will run `scan` first so you can see what keys will be added/removed before updating the destination file.

    ./vendor/bin/cattlog update en

### List keys ###

List all key / value combinations in a file

    ./vendor/bin/cattlog list en

### Setting values ###

Set an existing key value

    ./vendor/bin/cattlog set_value en MY_KEY=something

Set a value. Create a new key if one doesn't exist already.

    ./vendor/bin/cattlog set_value en MY_KEY=something --create

### List options ###

Passing no parameters will list all options

    ./vendor/bin/cattlog

## Filters ##

Filters are used to encode and decode data and text respectively. They are selected by setting the "filter" property in the config file.

### Built in filters ###

The following built in filters are available:

* csv
* php
* json

To set in config, do:

    "filter": "json"

### Create a custom filter ##"

The prebuilt filters may not serve your purpose. Instead, you can define custom filters. These will let you generate the destination files in the format you need.

To define a custom format, create a file within `.cattlog/filter/Myfilter.php` (uppercase first letter only)

To set in config, do:

    "filter": "myfilter"

You can also overwrite existing filters. For example, the following file has been created to overwrite "php" filter. Cattlog will first check if the class exists within custom filters directory before checking built in filters:

    .cattlog/filters/Php.php

So if `"filter": "php"` is set, Cattlog will pick the custom filter and use that.

##TODO##

* Allow a array of patterns
* Can cattlog create a dest folder e.g. mkdir -p ...? new languages?
* Remove filters from l5 cattlog, we use include instead of $text
* if the file doesn't exist, create it?
* only overwrite (or get files that are configured right)
* cache getDestFiles
* how much can be taken out of Cattlog class? Filesystem stuff, testable stuff remains
* write tests for addKeys and removeKeys
