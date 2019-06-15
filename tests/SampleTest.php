<?php

namespace SMB\PhpWebDriver\Tests;

use SMB\PhpWebDriver\Modules\Elements\Spec;
use SMB\PhpWebDriver\Modules\Elements\SpecPool;

use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\WebDriverBrowserType;

/**
 * phpwebdriverを使ったテストの簡単なサンプルです
 */
class SampleTest extends Base
{
    /**
     * Yahoo(PC:chrome)トップページにアクセスできる
     * @test
     * @group chrome
     */
    public function it_can_access_to_yahoo_of_pc_chrome()
    {
        $cap = $this->createCapabilities(WebDriverBrowserType::CHROME);

        $driver = $this->createDriver($cap);
        $driver->get('https://www.yahoo.co.jp/');

        $this->assertEquals('https://www.yahoo.co.jp/', $driver->getCurrentURL());

        // $driver->takeScreenshot($filename);
        $this->takeScreenshot($driver, 'it_can_access_to_yahoo_of_pc_chrome');

        // 全画面キャプチャ
        $this->takeFullScreenshot($driver, 'it_can_access_to_yahoo_of_pc_chrome_fullscreen');
    }

    /**
     * Yahoo(SP:chrome)トップページにアクセスできる
     * @test
     * @group chrome
     */
    public function it_can_access_to_yahoo_of_sp_chrome()
    {
        $cap = $this->createCapabilities(WebDriverBrowserType::CHROME);
        $cap->settingDefaultUserAgent();

        $dimension = $this->createDimension(['w' => 375, 'h' => 667]);

        $driver = $this->createDriver($cap, $dimension);
        $driver->get('https://m.yahoo.co.jp/');

        // a.Header__userLink が現れるまで10秒待つ
        $driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
                WebDriverBy::cssSelector('a.Header__userLink')
            )
        );

        // ヘッダーにログインという文字があること
        $expected = 'ログイン';
        $actual = $driver->findElement(WebDriverBy::cssSelector('a.Header__userLink'))->getText();
        $this->assertEquals($expected, $actual);

        // https://www.yahoo.co.jp/ にリダイレクトされていないこと
        $this->assertEquals('https://m.yahoo.co.jp/', $driver->getCurrentURL());

        $this->takeScreenshot($driver, 'it_can_access_to_yahoo_of_sp_chrome');

        $this->takeFullScreenshot($driver, 'it_can_access_to_yahoo_of_sp_chrome_fullscreen');
    }

    /**
     * Yahoo(PC:firefox)トップページにアクセスできる
     * @test
     * @group firefox
     */
    public function it_can_access_to_yahoo_of_pc_firefox()
    {
        $cap = $this->createCapabilities(WebDriverBrowserType::FIREFOX);

        $driver = $this->createDriver($cap);

        // 画面サイズをMAXにする場合
        $this->windowMaximize($driver);

        $driver->get('https://www.yahoo.co.jp/');

        $this->assertEquals('https://www.yahoo.co.jp/', $driver->getCurrentURL());

        $this->takeScreenshot($driver, 'it_can_access_to_yahoo_of_pc_firefox');

        $this->takeFullScreenshot($driver, 'it_can_access_to_yahoo_of_pc_firefox_fullscreen');
    }

    /**
     * Yahoo(SP:firefox)トップページにアクセスできる
     * @test
     * @group firefox
     */
    public function it_can_access_to_yahoo_of_sp_firefox()
    {
        $cap = $this->createCapabilities(WebDriverBrowserType::FIREFOX);
        $cap->settingDefaultUserAgent();

        $dimension = $this->createDimension(['w' => 375, 'h' => 667]);

        $driver = $this->createDriver($cap, $dimension);
        $driver->get('https://m.yahoo.co.jp/');

        // a.Header__userLink が現れるまで10秒待つ
        $driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
                WebDriverBy::cssSelector('a.Header__userLink')
            )
        );

        // ヘッダーにログインという文字があること
        $expected = 'ログイン';
        $actual = $driver->findElement(WebDriverBy::cssSelector('a.Header__userLink'))->getText();
        $this->assertEquals($expected, $actual);

        // https://www.yahoo.co.jp/ にリダイレクトされていないこと
        $this->assertEquals('https://m.yahoo.co.jp/', $driver->getCurrentURL());

        $this->takeScreenshot($driver, 'it_can_access_to_yahoo_of_sp_firefox');

        $this->takeFullScreenshot($driver, 'it_can_access_to_yahoo_of_sp_firefox_fullscreen');
    }

    /**
     * Yahoo(PC:internet explorer)トップページにアクセスできる
     * @test
     * @group ie
     */
    public function it_can_access_to_yahoo_of_pc_ie()
    {
        $cap = $this->createCapabilities(WebDriverBrowserType::IE);

        $driver = $this->createDriver($cap);
        $driver->get('https://www.yahoo.co.jp/');

        $this->assertEquals('https://www.yahoo.co.jp/', $driver->getCurrentURL());

        // IEは $driver->takeScreenshot($filename); で全画面キャプチャをとってくれる
        $this->takeScreenshot($driver, 'it_can_access_to_yahoo_of_pc_ie');

        $this->takeFullScreenshot($driver, 'it_can_access_to_yahoo_of_pc_ie_fullscreen');
    }

    /**
     * (PC:chrome) 要素のキャプチャが撮れる
     * @test
     * @group chrome
     */
    public function it_can_take_element_of_pc_chrome()
    {
        $path = $this->capturePath();
        $captureFileName = 'it_can_take_element_of_pc_chrome';
        $targetCaptureFiles = [
            $path. $captureFileName . '_0_0.png',
            $path. $captureFileName . '_0_1.png',
            $path. $captureFileName . '_0_2.png',
            $path. $captureFileName . '_0_3.png',
            $path. $captureFileName . '_0_4.png',
            $path. $captureFileName . '_0_5.png',
            $path. $captureFileName . '_0_6.png',
            $path. $captureFileName . '_0_7.png',
            $path. $captureFileName . '_0_8.png',
            $path. $captureFileName . '_0_9.png',
            $path. $captureFileName . '_1_0.png',
            $path. $captureFileName . '_1_1.png',
        ];

        $this->deleteImageFiles($targetCaptureFiles);

        $cap = $this->createCapabilities(WebDriverBrowserType::CHROME);

        $driver = $this->createDriver($cap);
        $driver->get('https://www.google.co.jp/');

        // 検索Box
        $findElement = $driver->findElement(WebDriverBy::name('q'));
        // 検索Boxにキーワードを入力して
        $findElement->sendKeys('お盆の予定');
        // 検索実行
        $findElement->submit();

        $selector  = '.rc';
        $selector2 = '.brs_col';

        // 要素のセレクターを定義して
        $spec = new Spec($selector, Spec::GREATER_THAN_OR_EQUAL, 10);
        $spec2 = new Spec($selector2, Spec::GREATER_THAN, 1);

        // SpecPoolに突っ込む
        $specPool = (new SpecPool())
                    ->addSpec($spec)
                    ->addSpec($spec2);
        
        $this->takeElementScreenshot($driver, $captureFileName, $specPool);

        foreach ($targetCaptureFiles as $file) {
            $this->assertFileExists($file);
        }
    }

    /**
     * (SP:chrome) 要素のキャプチャが撮れる
     * @test
     * @group chrome
     */
    public function it_can_take_element_of_sp_chrome()
    {
        $path = $this->capturePath();
        $captureFileName = 'it_can_take_element_of_sp_chrome';
        $targetCaptureFiles = [
            $path. $captureFileName . '_0_0.png',
            $path. $captureFileName . '_0_1.png',
            $path. $captureFileName . '_0_2.png',
            $path. $captureFileName . '_0_3.png',
            $path. $captureFileName . '_0_4.png',
            $path. $captureFileName . '_0_5.png',
            $path. $captureFileName . '_0_6.png',
            $path. $captureFileName . '_0_7.png',
            $path. $captureFileName . '_0_8.png',
            $path. $captureFileName . '_0_9.png',
            $path. $captureFileName . '_1_0.png',
        ];

        $this->deleteImageFiles($targetCaptureFiles);

        $cap = $this->createCapabilities(WebDriverBrowserType::CHROME);
        $cap->settingDefaultUserAgent();

        $dimension = $this->createDimension(['w' => 375, 'h' => 667]);

        $driver = $this->createDriver($cap, $dimension);
        $driver->get('https://www.google.co.jp/');

        // 検索Box
        $findElement = $driver->findElement(WebDriverBy::name('q'));
        // 検索Boxにキーワードを入力して
        $findElement->sendKeys('お盆の予定');
        // 検索実行
        $findElement->submit();

        $selector  = '.uUPGi';
        $selector2 = '#sfcnt';

        // 要素のセレクターを定義して
        $spec = new Spec($selector, Spec::GREATER_THAN_OR_EQUAL, 10);
        $spec2 = new Spec($selector2, Spec::EQUAL, 1);

        // SpecPoolに突っ込む
        $specPool = (new SpecPool())
                    ->addSpec($spec)
                    ->addSpec($spec2);

        $this->takeElementScreenshot($driver, $captureFileName, $specPool);

        foreach ($targetCaptureFiles as $file) {
            $this->assertFileExists($file);
        }
    }

    /**
     * (PC:firefox) 要素のキャプチャが撮れる
     * @test
     * @group firefox
     */
    public function it_can_take_element_of_pc_firefox()
    {
        $path = $this->capturePath();
        $captureFileName = 'it_can_take_element_of_pc_firefox';
        $targetCaptureFiles = [
            $path. $captureFileName . '_0_0.png',
            $path. $captureFileName . '_0_1.png',
            $path. $captureFileName . '_0_2.png',
            $path. $captureFileName . '_0_3.png',
            $path. $captureFileName . '_0_4.png',
            $path. $captureFileName . '_0_5.png',
            $path. $captureFileName . '_0_6.png',
            $path. $captureFileName . '_0_7.png',
            $path. $captureFileName . '_0_8.png',
            $path. $captureFileName . '_0_9.png',
            $path. $captureFileName . '_1_0.png',
            $path. $captureFileName . '_1_1.png',
        ];

        $this->deleteImageFiles($targetCaptureFiles);

        $cap = $this->createCapabilities(WebDriverBrowserType::FIREFOX);

        $driver = $this->createDriver($cap);
        $driver->get('https://www.google.co.jp/');

        // 検索Box
        $findElement = $driver->findElement(WebDriverBy::name('q'));
        // 検索Boxにキーワードを入力して
        $findElement->sendKeys('お盆の予定');
        // 検索実行
        $findElement->submit();

        $selector  = '.rc';
        $selector2 = '.brs_col';

        // 要素のセレクターを定義して
        $spec = new Spec($selector, Spec::GREATER_THAN_OR_EQUAL, 10);
        $spec2 = new Spec($selector2, Spec::GREATER_THAN, 1);

        // SpecPoolに突っ込む
        $specPool = (new SpecPool())
                    ->addSpec($spec)
                    ->addSpec($spec2);

        $this->takeElementScreenshot($driver, $captureFileName, $specPool);

        foreach ($targetCaptureFiles as $file) {
            $this->assertFileExists($file);
        }
    }

    /**
     * (SP:firefox) 要素のキャプチャが撮れる
     * @test
     * @group firefox
     */
    public function it_can_take_element_of_sp_firefox()
    {
        $path = $this->capturePath();
        $captureFileName = 'it_can_take_element_of_sp_firefox';
        $targetCaptureFiles = [
            $path. $captureFileName . '_0_0.png',
            $path. $captureFileName . '_0_1.png',
            $path. $captureFileName . '_0_2.png',
            $path. $captureFileName . '_0_3.png',
            $path. $captureFileName . '_0_4.png',
            $path. $captureFileName . '_0_5.png',
            $path. $captureFileName . '_0_6.png',
            $path. $captureFileName . '_0_7.png',
            $path. $captureFileName . '_0_8.png',
            $path. $captureFileName . '_0_9.png',
            $path. $captureFileName . '_1_0.png',
        ];

        $this->deleteImageFiles($targetCaptureFiles);

        $cap = $this->createCapabilities(WebDriverBrowserType::FIREFOX);
        $cap->settingDefaultUserAgent();

        $dimension = $this->createDimension(['w' => 375, 'h' => 667]);

        $driver = $this->createDriver($cap, $dimension);
        $driver->get('https://www.google.co.jp/');

        // 検索Box
        $findElement = $driver->findElement(WebDriverBy::name('q'));
        // 検索Boxにキーワードを入力して
        $findElement->sendKeys('お盆の予定');
        // 検索実行
        $findElement->submit();

        $selector  = '.uUPGi';
        $selector2 = '#sfcnt';

        // 要素のセレクターを定義して
        $spec = new Spec($selector, Spec::GREATER_THAN_OR_EQUAL, 10);
        $spec2 = new Spec($selector2, Spec::EQUAL, 1);

        // SpecPoolに突っ込む
        $specPool = (new SpecPool())
                    ->addSpec($spec)
                    ->addSpec($spec2);

        $this->takeElementScreenshot($driver, $captureFileName, $specPool);

        foreach ($targetCaptureFiles as $file) {
            $this->assertFileExists($file);
        }
    }

    /**
     * (PC:internet explorer) 要素のキャプチャが撮れる
     * @test
     * @group ie
     */
    public function it_can_take_element_of_pc_ie()
    {
        $path = $this->capturePath();
        $captureFileName = 'it_can_take_element_of_pc_ie';
        $targetCaptureFiles = [
            $path. $captureFileName . '_0_0.png',
            $path. $captureFileName . '_0_1.png',
            $path. $captureFileName . '_0_2.png',
            $path. $captureFileName . '_0_3.png',
            $path. $captureFileName . '_0_4.png',
            $path. $captureFileName . '_0_5.png',
            $path. $captureFileName . '_0_6.png',
            $path. $captureFileName . '_0_7.png',
            $path. $captureFileName . '_0_8.png',
            $path. $captureFileName . '_0_9.png',
            $path. $captureFileName . '_1_0.png',
            $path. $captureFileName . '_1_1.png',
        ];

        $this->deleteImageFiles($targetCaptureFiles);

        $cap = $this->createCapabilities(WebDriverBrowserType::IE);

        $driver = $this->createDriver($cap);
        $driver->get('https://www.google.co.jp/');

        // 検索Box
        $findElement = $driver->findElement(WebDriverBy::name('q'));
        // 検索Boxにキーワードを入力して
        $findElement->sendKeys('お盆の予定');
        // 検索実行
        $findElement->submit();

        $selector  = '.rc';
        $selector2 = '.brs_col';

        // 要素のセレクターを定義して
        $spec = new Spec($selector, Spec::GREATER_THAN_OR_EQUAL, 10);
        $spec2 = new Spec($selector2, Spec::GREATER_THAN, 1);

        // SpecPoolに突っ込む
        $specPool = (new SpecPool())
                    ->addSpec($spec)
                    ->addSpec($spec2);

        $this->takeElementScreenshot($driver, $captureFileName, $specPool);

        foreach ($targetCaptureFiles as $file) {
            $this->assertFileExists($file);
        }
    }

    /**
     * 画像の削除
     * @param array $imageFiles
     */
    private function deleteImageFiles(array $imageFiles)
    {
        foreach ($imageFiles as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }
}
