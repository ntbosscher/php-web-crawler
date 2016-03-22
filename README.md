# PHP Web Crawler

This CLI script will crawl a given url for any path that resolves within the given domain or url path provided. An optional folder parameter allows you to download to a specific location.

## Usage

```
php crawl.php <url> <optional:output-dir>
```

## Examples

```
php crawl.php http://www.foo.com

php crawl.php http://www.foo.com ~/websites/foo.com

php crawl.php http://www.foo.com/some-sub-dir/
# will only download files within /some-sub-dir
```

## Tests

Selected tests can be run using the following:
Note: this has very little code coverage, only used for some basic functions.

```
php test/test.php
```