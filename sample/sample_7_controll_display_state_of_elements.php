<?php

require_once realpath(__DIR__ . '/../vendor') . '/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome;
use Facebook\WebDriver\Firefox;

use SMB\PhpWebDriver\Modules\Screenshot;
use SMB\PhpWebDriver\Modules\Elements\Spec;
use SMB\PhpWebDriver\Modules\Elements\SpecPool;
use SMB\PhpWebDriver\Modules\View\Observer;

/**
 * selenium php-webdriver 要素の表示状態のコントロール サンプル
 * @param string $browser chrome or firefox or ie
 * @param array $size ['w' => xxx, 'h' => xxx]
 * @param string overrideUA true : override Useragent
 */
function sample_7($browser, array $size=[], $overrideUA = '')
{
    // selenium
    $host = 'http://localhost:4444/wd/hub';

    switch ($browser) {
        case WebDriverBrowserType::CHROME :
            $cap = DesiredCapabilities::chrome();

            if ($overrideUA !== '') {
                $options = new Chrome\ChromeOptions();
                $options->addArguments(['--user-agent=' . $overrideUA]);

                $cap->setCapability(Chrome\ChromeOptions::CAPABILITY, $options);
            }

            if (getenv('CHROME_DRIVER_PATH') !== '') {
                putenv('webdriver.chrome.driver=' . getenv('CHROME_DRIVER_PATH'));
            }

            $driver = RemoteWebDriver::create($host, $cap);

            break;
        case WebDriverBrowserType::FIREFOX :
            $cap = DesiredCapabilities::firefox();

            if ($overrideUA !== '') {
                $profile = new Firefox\FirefoxProfile();
                $profile->setPreference('general.useragent.override', $overrideUA);

                $cap->setCapability(Firefox\FirefoxDriver::PROFILE, $profile);
            }

            if (getenv('FIREFOX_DRIVER_PATH') !== '') {
                putenv('webdriver.gecko.driver=' . getenv('FIREFOX_DRIVER_PATH'));
            }

            $driver = RemoteWebDriver::create($host, $cap);

            break;
        case WebDriverBrowserType::IE :
            if (getenv('IE_DRIVER_PATH') !== '') {
                putenv('webdriver.ie.driver=' . getenv('IE_DRIVER_PATH'));
            }
            $driver = RemoteWebDriver::create($host, DesiredCapabilities::internetExplorer());
            break;
    }

    // 画面サイズをMAXにする場合
    // $driver->manage()->window()->maximize();

    // 画面サイズの指定あり
    if (isset($size['w']) && isset($size['h'])) {
        $dimension = new WebDriverDimension($size['w'], $size['h']);
        $driver->manage()->window()->setSize($dimension);
    }

    // 指定URLへ遷移 (Google)
    $driver->get('https://www.google.co.jp/');

    // 検索Box
    $element = $driver->findElement(WebDriverBy::name('q'));
    // 検索Boxにキーワードを入力して
    $element->sendKeys('夏休みの予定');
    // 検索実行
    $element->submit();

    // 指定した要素がコンテンツに出現するまで待ちます.
    // #botstuff をターゲットにします.
    $driver->wait(10)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('botstuff'))
    );

    // キャプチャ
    $fileName = $overrideUA === '' ? __METHOD__ . "_{$browser}.png" : __METHOD__ . "_override_ua_{$browser}.png";
    $captureDirectoryPath = realpath(__DIR__ . '/../capture') . '/';

    // オブザーバー
    $observer = new Observer();
    // 最初に縦スクロールが行われたら追従するヘッダー(スティッキーヘッダー)を消す
    $observer->processForFirstVerticalScroll(function($driver) {
        $driver->executeScript("document.querySelector('#searchform') ? document.querySelector('#searchform').style.display = 'none' : null;");
    });
    // レンダリングが完了したら元に戻す
    $observer->processForRenderComplete(function($driver,$contentsWidth, $contentsHeight, $scrollWidth, $scrollHeight) {
        $driver->executeScript("document.querySelector('#searchform') ? document.querySelector('#searchform').style.display = 'inherit' : null;");
    });

    $screenshot = new Screenshot();

    // オブザーバーをセット
    $screenshot->setObserver($observer);

    // フルスクリーン
    $screenshot->takeFull($driver, $captureDirectoryPath, $fileName);

    $selector = $overrideUA === '' ? '.RNNXgb' : '#sfcnt';
    $spec = new Spec($selector, Spec::EQUAL, 1);
    $specPool = (new SpecPool())->addSpec($spec);
    // 要素のキャプチャ
    $screenshot->takeElement($driver, $captureDirectoryPath, $fileName, $specPool);

    // ブラウザを閉じる
    $driver->close();
}

// iPhone6のサイズ
$size4iPhone6 = ['w' => 375, 'h' => 667];

// iOS12のUA
$ua4iOS = 'Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1';

/**
 |------------------------------------------------------------------------------
 | 有効にしたいドライバーの値を true にしてください
 |------------------------------------------------------------------------------
 */

// chrome
if (getenv('ENABLED_CHROME_DRIVER') === 'true') {
    sample_7(WebDriverBrowserType::CHROME);
    sample_7(WebDriverBrowserType::CHROME, $size4iPhone6, $ua4iOS);
}

// firefox
if (getenv('ENABLED_FIREFOX_DRIVER') === 'true') {
    sample_7(WebDriverBrowserType::FIREFOX);
    sample_7(WebDriverBrowserType::FIREFOX, $size4iPhone6, $ua4iOS);
}

// ie
if (getenv('ENABLED_IE_DRIVER') === 'true') {
    sample_7(WebDriverBrowserType::IE);
}
