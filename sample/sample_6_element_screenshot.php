<?php

require_once realpath(__DIR__ . '/../vendor') . '/autoload.php';

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\Chrome;
use Facebook\WebDriver\Firefox;

use SMB\PhpWebDriver\Modules\Screenshot;
use SMB\PhpWebDriver\Modules\Elements\Spec;
use SMB\PhpWebDriver\Modules\Elements\SpecPool;

/**
 * selenium php-webdriver 指定した要素のキャプチャ サンプル
 * @param string $browser chrome or firefox or ie
 * @param array $size ['w' => xxx, 'h' => xxx]
 * @param string overrideUA true : override Useragent
 */
function sample_6($browser, array $size=[], $overrideUA = '')
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
    $findElement = $driver->findElement(WebDriverBy::name('q'));
    // 検索Boxにキーワードを入力して
    $findElement->sendKeys('お盆の予定');
    // 検索実行
    $findElement->submit();

    // pc と sp で指定要素を変える
    $selector = $overrideUA === '' ? '.rc' : '#rso > div > div.mnr-c';
    $selector2 = $overrideUA === '' ? '.brs_col' : 'a._bCp';

    // 要素のセレクターを定義して
    $spec = new Spec($selector, Spec::GREATER_THAN_OR_EQUAL, 10);
    $spec2 = new Spec($selector2, Spec::GREATER_THAN, 1);

    // SpecPoolに突っ込む
    $specPool = (new SpecPool())
                ->addSpec($spec)
                ->addSpec($spec2);

    // キャプチャ (ファイル名は拡張子無し / pngになります)
    $fileName = $overrideUA === '' ? __METHOD__ . "_{$browser}" : __METHOD__ . "_sp_{$browser}";
    $captureDirectoryPath = realpath(__DIR__ . '/../capture') . '/';

    $screenshot = new Screenshot();
    $screenshot->takeElement($driver, $captureDirectoryPath, $fileName, $browser, $specPool);

    // ブラウザを閉じる
    $driver->close();
}

// iPhone6のサイズ
$size4iPhone6 = ['w' => 375, 'h' => 667];

// iOS10のUA
$ua4iOS = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1';

/**
 |------------------------------------------------------------------------------
 | 有効にしたいドライバーの値を true にしてください
 |------------------------------------------------------------------------------
 */

// chrome
if (getenv('ENABLED_CHROME_DRIVER') === 'true') {
    sample_6(WebDriverBrowserType::CHROME);
    sample_6(WebDriverBrowserType::CHROME, $size4iPhone6, $ua4iOS);
}

// firefox
if (getenv('ENABLED_FIREFOX_DRIVER') === 'true') {
    sample_6(WebDriverBrowserType::FIREFOX);
    sample_6(WebDriverBrowserType::FIREFOX, $size4iPhone6, $ua4iOS);
}

// ie
if (getenv('IE_DRIVER_PATH') !== '') {
    sample_6(WebDriverBrowserType::IE);
}
