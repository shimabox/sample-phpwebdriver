<?php

namespace SMB\PhpWebDriver\Tests;

use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;

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
        $cap = new Util\Capabilities(Util\Capabilities::CHROME);

        $driver = $this->createDriver($cap);
        $driver->get('https://www.yahoo.co.jp/');

        $this->assertEquals('https://www.yahoo.co.jp/', $driver->getCurrentURL());

        $this->takeScreenshot($driver, 'it_can_access_to_yahoo_of_pc_chrome');
    }

    /**
     * Yahoo(SP:chrome)トップページにアクセスできる
     * @test
     * @group chrome
     */
    public function it_can_access_to_yahoo_of_sp_chrome()
    {
        $cap = new Util\Capabilities(Util\Capabilities::CHROME);
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
    }

    /**
     * Yahoo(PC:firefox)トップページにアクセスできる
     * @test
     * @group firefox
     */
    public function it_can_access_to_yahoo_of_pc_firefox()
    {
        $cap = new Util\Capabilities(Util\Capabilities::FIREFOX);

        $driver = $this->createDriver($cap);
        $driver->get('https://www.yahoo.co.jp/');

        $this->assertEquals('https://www.yahoo.co.jp/', $driver->getCurrentURL());

        $this->takeScreenshot($driver, 'it_can_access_to_yahoo_of_pc_firefox');
    }

    /**
     * Yahoo(SP:firefox)トップページにアクセスできる
     * @test
     * @group firefox
     */
    public function it_can_access_to_yahoo_of_sp_firefox()
    {
        $cap = new Util\Capabilities(Util\Capabilities::FIREFOX);
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
    }
}
