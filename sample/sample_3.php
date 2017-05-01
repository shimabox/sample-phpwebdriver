<?php

require_once realpath(__DIR__ . '/../vendor') . '/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\Chrome; // add for chrome
use Facebook\WebDriver\Firefox; // add for firefox

/**
 * selenium php-webdriver 実行のサンプル
 * @param string $browser chrome or firefox
 * @param array $size ['w' => xxx, 'h' => xxx]
 * @param string overrideUA true : override Useragent
 */
function sample_3 ($browser, array $size = [], $overrideUA = '')
{
    // selenium
    $host = 'http://localhost:4444/wd/hub';

    switch ($browser) {
        case 'chrome': // chrome
            $cap = DesiredCapabilities::chrome();
 
            if ($overrideUA === '') {
                break;
            }

            $options = new Chrome\ChromeOptions();
            $options->addArguments(['--user-agent=' . $overrideUA]);

            $cap->setCapability(Chrome\ChromeOptions::CAPABILITY, $options);

            break;
        case 'firefox': // firefox
            $cap = DesiredCapabilities::firefox();

            if ($overrideUA === '') {
                break;
            }

            // iPhone(iOS10) のUAをセット
            $profile = new Firefox\FirefoxProfile();
            $profile->setPreference('general.useragent.override', $overrideUA);

            $cap->setCapability(Firefox\FirefoxDriver::PROFILE, $profile);

            break;
    }


    // ドライバーの起動
    $driver = RemoteWebDriver::create($host, $cap);

    // 画面サイズをMAXに
    $driver->manage()->window()->maximize();

    if (isset($size['w']) && isset($size['h'])) {
        // サイズを指定
        $dimension = new WebDriverDimension($size['w'], $size['h']);
        $driver->manage()->window()->setSize($dimension);
    }

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

// iPhone6のサイズ
$size4iPhone6 = ['w' => 375, 'h' => 667];
// iOS10のUA
$ua4iOS = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1';

// chrome
sample_3('chrome', $size4iPhone6, $ua4iOS);

// firefox
sample_3('firefox', $size4iPhone6, $ua4iOS);
