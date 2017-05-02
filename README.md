# sample-phpwebdriver
Test with phpunit and phpwebdriver

## Requirements

- PHP 5.5+ or newer
- [Composer](https://getcomposer.org)
- Java(JDK) >=1.8
  - http://www.oracle.com/technetwork/java/javase/downloads/index.html
- selenium-server-standalone 3.4.0
  - http://selenium-release.storage.googleapis.com/3.4/selenium-server-standalone-3.4.0.jar
- [geckodriver v0.16.1](https://github.com/shimabox/sample-phpwebdriver#geckodriver)
- [chromedriver v2.29](https://github.com/shimabox/sample-phpwebdriver#chromedriver)

#### geckodriver

- [Release v0.16.1 · mozilla/geckodriver · GitHub](https://github.com/mozilla/geckodriver/releases/tag/v0.16.1 "Release v0.16.1 · mozilla/geckodriver · GitHub")
- macOS https://github.com/mozilla/geckodriver/releases/download/v0.16.1/geckodriver-v0.16.1-macos.tar.gz
```
$ mv geckodriver /usr/local/bin/
$ chmod +x /usr/local/bin/geckodriver
```

#### chromedriver

- [https://chromedriver.storage.googleapis.com/index.html?path=2.29/](https://chromedriver.storage.googleapis.com/index.html?path=2.29/ "")
- macOS https://chromedriver.storage.googleapis.com/2.29/chromedriver_mac64.zip
```
$ mv chromedriver /usr/local/bin/
$ chmod +x /usr/local/bin/chromedriver
```

## Installation

```
$ clone https://github.com/shimabox/sample-phpwebdriver.git
$ cd sample-phpwebdriver
$ composer install --dev
```

## Usage

1. Run selenium-server-standalone
```
$ java -jar selenium-server-standalone-3.4.0.jar &
```
2. Run phpunit
```
$ vendor/bin/phpunit
```
3. Stop selenium-server-standalone
```
$ sh kill_selenium.sh
```

## See Also

- [php-webdriverをmacのローカルで試す | Shimabox Blog](https://blog.shimabox.net/2017/04/30/try_php-webdriver_locally_on_mac/ "php-webdriverをmacのローカルで試す | Shimabox Blog")

## License

- MIT License
