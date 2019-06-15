# sample-phpwebdriver
Test with phpunit and phpwebdriver

## Attention!!
This library was transferred to [shimabox/screru](https://github.com/shimabox/screru "shimabox/screru: Screru is a library that supplements php-webdriver")

## Requirements

- PHP 5.6+ or newer
- [Composer](https://getcomposer.org)
- Java(JDK) >=1.8
  - http://www.oracle.com/technetwork/java/javase/downloads/index.html

## Installation

```
$ git clone https://github.com/shimabox/sample-phpwebdriver.git
$ cd sample-phpwebdriver
$ composer install --dev # or composer update
$ cp .env.example .env
```

## Preparation

Download selenium-server-standalone, ChromeDriver, geckodriver, IEDriverServer etc.

|Platform|selenium-server-standalone|ChromeDriver|geckodriver|IEDriverServer|
|:---|:---|:---|:---|:---|
|Mac|[3.8.1](https://selenium-release.storage.googleapis.com/3.8/selenium-server-standalone-3.8.1.jar)|[75.0.3770.90](https://chromedriver.storage.googleapis.com/75.0.3770.90/chromedriver_mac64.zip)|[0.24.0](https://github.com/mozilla/geckodriver/releases/download/v0.24.0/geckodriver-v0.24.0-macos.tar.gz)|-|
|Windows(64bit)|[3.8.1](https://selenium-release.storage.googleapis.com/3.8/selenium-server-standalone-3.8.1.jar)|[75.0.3770.90](https://chromedriver.storage.googleapis.com/75.0.3770.90/chromedriver_win32.zip)|[0.24.0](https://github.com/mozilla/geckodriver/releases/download/v0.24.0/geckodriver-v0.24.0-win64.zip)|[3.14.0](https://selenium-release.storage.googleapis.com/3.14/IEDriverServer_Win32_3.14.0.zip)|
|Linux(CentOS 6.9)|[3.8.1](https://selenium-release.storage.googleapis.com/3.8/selenium-server-standalone-3.8.1.jar)|-|[0.24.0](https://github.com/mozilla/geckodriver/releases/download/v0.24.0/geckodriver-v0.24.0-linux64.tar.gz)|-|
|Linux(Ubuntu trusty)|[3.8.1](https://selenium-release.storage.googleapis.com/3.8/selenium-server-standalone-3.8.1.jar)|[75.0.3770.90](https://chromedriver.storage.googleapis.com/75.0.3770.90/chromedriver_linux64.zip)|[0.24.0](https://github.com/mozilla/geckodriver/releases/download/v0.24.0/geckodriver-v0.24.0-linux64.tar.gz)|-|

### Use downloader.

- e.g) For Mac.
```
$ php selenium_downloader.php -p m -d . -s 3.8.1 -c 75.0.3770.90 -g 0.24.0
```
- e.g) For Windows.
```
$ php selenium_downloader.php -p w -d . -s 3.8.1 -c 75.0.3770.90 -g 0.24.0 -i 3.14.0
```
- e.g) For Linux.
```
$ php selenium_downloader.php -p l -d . -s 3.8.1 -g 0.24.0
```

@see [selenium-downloader/README.md at master · shimabox/selenium-downloader · GitHub](https://github.com/shimabox/selenium-downloader/blob/master/README.md "selenium-downloader/README.md at master · shimabox/selenium-downloader · GitHub")

## Linux (CentOS)

- Operation confirmed in version 6.9

### Firefox

- install
```
$ sudo yum -y install firefox
```
- version 60.7.0
```
$ firefox -v
Mozilla Firefox 60.7.0
```

### Xvfb

- install
```
$ sudo yum -y install xorg-x11-server-Xvfb
$ sudo yum -y groupinstall "Japanese Support"
```

### selenium-server-standalone

- selenium-server-standalone 3.8.1
  - http://selenium-release.storage.googleapis.com/3.8/selenium-server-standalone-3.8.1.jar

### geckodriver

- [geckodriver v0.24.0](https://github.com/mozilla/geckodriver/releases/tag/v0.24.0)
  - https://github.com/mozilla/geckodriver/releases/download/v0.24.0/geckodriver-v0.24.0-linux64.tar.gz

```
$ sudo mv geckodriver /usr/local/bin/
$ sudo chmod +x /usr/local/bin/geckodriver
```

#### .env

- Edit ```.env```
```
ENABLED_FIREFOX_DRIVER=true
```

### Run

1. Run Xvfb & selenium-server-standalone
```
$ sudo sh start_selenium.sh
```
2. Run phpunit
```
$ vendor/bin/phpunit
```
3. Stop Xvfb & selenium-server-standalone & geckodriver
```
$ sudo sh kill_selenium.sh
```

## macOS

- Operation confirmed in macOS Mojave 10.14.5

### selenium-server-standalone

- selenium-server-standalone 3.8.1
  - http://selenium-release.storage.googleapis.com/3.8/selenium-server-standalone-3.8.1.jar

### geckodriver

- [geckodriver v0.24.0](https://github.com/mozilla/geckodriver/releases/tag/v0.24.0)
  - https://github.com/mozilla/geckodriver/releases/download/v0.24.0/geckodriver-v0.24.0-macos.tar.gz

```
$ mv geckodriver /usr/local/bin/
$ chmod +x /usr/local/bin/geckodriver
```

### chromedriver

- [chromedriver 75.0.3770.90](https://chromedriver.storage.googleapis.com/index.html?path=75.0.3770.90/ "")
  - https://chromedriver.storage.googleapis.com/75.0.3770.90/chromedriver_mac64.zip

```
$ mv chromedriver /usr/local/bin/
$ chmod +x /usr/local/bin/chromedriver
```

#### .env

- Edit ```.env```
```
ENABLED_FIREFOX_DRIVER=true
ENABLED_CHROME_DRIVER=true
```

### Run

1. Run selenium-server-standalone
```
$ java -jar selenium-server-standalone-3.8.1.jar -enablePassThrough false
```
2. Run phpunit
```
$ vendor/bin/phpunit
```

## windows(64bit)

### selenium-server-standalone

- selenium-server-standalone 3.8.1
  - http://selenium-release.storage.googleapis.com/3.8/selenium-server-standalone-3.8.1.jar

### geckodriver.exe

- [geckodriver v0.24.0](https://github.com/mozilla/geckodriver/releases/tag/v0.24.0)
  - https://github.com/mozilla/geckodriver/releases/download/v0.24.0/geckodriver-v0.24.0-win64.zip

### chromedriver.exe

- [chromedriver 75.0.3770.90](https://chromedriver.storage.googleapis.com/index.html?path=75.0.3770.90/ "")
  - https://chromedriver.storage.googleapis.com/75.0.3770.90/chromedriver_win32.zip

### IEDriverServer.exe

- [IEDriverServer_x64_3.14.0.zip](http://selenium-release.storage.googleapis.com/index.html?path=3.14/)
  - http://selenium-release.storage.googleapis.com/3.14/IEDriverServer_x64_3.14.0.zip

#### .env

- Edit ```.env```
```
ENABLED_CHROME_DRIVER=true
ENABLED_FIREFOX_DRIVER=true
ENABLED_IE_DRIVER=true
// true to platform is windows
IS_PLATFORM_WINDOWS=true
// describe the webdriver path if necessary
CHROME_DRIVER_PATH='your chromedriver.exe path'
FIREFOX_DRIVER_PATH='your geckodriver.exe path'
IE_DRIVER_PATH='your IEDriverServer.exe path'
```

### Run

1. Open ```cmd``` etc.
2. Run selenium-server-standalone
```shell
$ java -jar selenium-server-standalone-3.8.1.jar -enablePassThrough false
```
3. Open a new ```cmd``` etc.
4. Run phpunit
```
$ vendor/bin/phpunit
```

### Example

- ``` $ php sample/sample_1.php ```
- ``` $ php sample/sample_2.php ```
- ``` $ php sample/sample_3.php ```
- ``` $ php sample/sample_4_win_64bit.php ```
- ``` $ php sample/sample_5_fullscreenshot.php ```
- ``` $ php sample/sample_6_element_screenshot.php ```

## See Also

- [php-webdriverをmacのローカルで試す | Shimabox Blog](https://blog.shimabox.net/2017/04/30/try_php-webdriver_locally_on_mac/ "php-webdriverをmacのローカルで試す | Shimabox Blog")
- [php-webdriverをWindowsのローカルで試す | Shimabox Blog](https://blog.shimabox.net/2017/06/09/try_php-webdriver_locally_on_windows "php-webdriverをWindowsのローカルで試す | Shimabox Blog")
- [CentOS 6.8 でphp-webdriverを試す (Firefox編) | Shimabox Blog](https://blog.shimabox.net/2017/05/04/try_php-webdriver_with_centos-6-8_for_firefox/ "CentOS 6.8 でphp-webdriverを試す (Firefox編) | Shimabox Blog")
- [php-webdriverを使ってフルスクリーンのキャプチャを撮る | Shimabox Blog](https://blog.shimabox.net/2017/07/31/take_full_screen_capture_with_php-webdriver/ "php-webdriverを使ってフルスクリーンのキャプチャを撮る | Shimabox Blog")
- [php-webdriverを使って指定した要素のキャプチャを撮る | Shimabox Blog](https://blog.shimabox.net/2017/08/07/take_a_capture_of_the_specified_element/ "php-webdriverを使って指定した要素のキャプチャを撮る | Shimabox Blog")

## License

- MIT License
