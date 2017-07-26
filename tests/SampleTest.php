<?php

namespace SMB\PhpWebDriver\Tests;

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
}
