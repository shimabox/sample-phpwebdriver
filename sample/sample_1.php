<?php

require_once realpath(__DIR__ . '/../vendor') . '/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;

/**
 * selenium php-webdriver 実行のサンプル
 * @param string $browser chrome or firefox
 */
function sample_1 ($browser)
{
    // selenium
    $host = 'http://localhost:4444/wd/hub';

    switch ($browser) {
        case WebDriverBrowserType::CHROME : // chrome ドライバーの起動
            if (getenv('CHROME_DRIVER_PATH') !== '') {
                putenv('webdriver.chrome.driver=' . getenv('CHROME_DRIVER_PATH'));
            }
            $driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
            break;
        case WebDriverBrowserType::FIREFOX : // firefox ドライバーの起動
            if (getenv('FIREFOX_DRIVER_PATH') !== '') {
                putenv('webdriver.gecko.driver=' . getenv('FIREFOX_DRIVER_PATH'));
            }
            $driver = RemoteWebDriver::create($host, DesiredCapabilities::firefox());
            break;
    }

    // 画面サイズをMAXに
    $driver->manage()->window()->maximize();

    // 指定URLへ遷移 (Google)
    $driver->get('https://www.google.co.jp/');

    // 検索Box
    $element = $driver->findElement(WebDriverBy::name('q'));
    // 検索Boxにキーワードを入力して
    $element->sendKeys('GWの予定');
    // 検索実行
    $element->submit();

    // 検索結果画面のタイトルが 'GWの予定 - Google 検索' になるまで10秒間待機する
    // 指定したタイトルにならずに10秒以上経ったら
    // 'Facebook\WebDriver\Exception\TimeOutException' がthrowされる
    $driver->wait(10)->until(
        WebDriverExpectedCondition::titleIs('GWの予定 - Google 検索')
    );

    // GWの予定 - Google 検索 というタイトルが取得できることを確認する
    if ($driver->getTitle() !== 'GWの予定 - Google 検索') {
        throw new Exception('fail');
    }

    // キャプチャ
    $file = realpath(__DIR__ . '/../capture') . '/' . __METHOD__ . "_{$browser}.png";
    $driver->takeScreenshot($file);

    // ブラウザを閉じる
    $driver->close();
}

// chrome
if (getenv('ENABLED_CHROME_DRIVER') === 'true') {
    sample_1(WebDriverBrowserType::CHROME);
}

// firefox
if (getenv('ENABLED_FIREFOX_DRIVER') === 'true') {
    sample_1(WebDriverBrowserType::FIREFOX);
}
