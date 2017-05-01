<?php

namespace SMB\PhpWebDriver\Tests;

use SMB\PhpWebDriver\Tests\Util\Capabilities;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverDimension;

/**
 * Base
 */
abstract class Base extends \PHPUnit_Framework_TestCase
{
    /**
     * host
     * @var string
     */
    protected $host = 'http://localhost:4444/wd/hub';

    /**
     * DesiredCapabilities
     * @var \Facebook\WebDriver\Remote\DesiredCapabilities
     */
    protected $capabilities;

    /**
     * RemoteWebDriver
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    private $driver;

    /**
     * テスト実行前の処理<br>
     * 1テストにつき1回呼ばれる
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * テスト実行後の処理<br>
     * 1テストにつき1回呼ばれる
     */
    protected function tearDown()
    {
        parent::tearDown();

        if ($this->driver instanceof RemoteWebDriver) {
            $this->driver->close();
        }
    }

    /**
     * RemoteWebDriver 生成
     * @param \SMB\PhpWebDriver\Tests\Util\Capabilities $cap
     * @param \Facebook\WebDriver\WebDriverDimension $dimension
     */
    protected function createDriver(
        Capabilities $cap             = null,
        WebDriverDimension $dimension = null
    )
    {
        if ($cap === null) {
            $cap = $this->createCapabilities();
        }

        // ドライバーの起動
        $driver = RemoteWebDriver::create($this->host, $cap->get());

        // 画面サイズをMAXに
        $driver->manage()->window()->maximize();

        // サイズの指定があるか
        if ($dimension !== null) {
            $driver->manage()->window()->setSize($dimension);
        }

        $this->driver = $driver;

        return $this->driver;
    }

    /**
     * DesiredCapabilities 生成
     * @param string $browser chrome or firefox
     * @return \Facebook\WebDriver\Remote\DesiredCapabilities
     */
    protected function createCapabilities($browser='')
    {
        return new Capabilities($browser);
    }

    /**
     * WebDriverDimension 生成
     * @param array $size ['w' => xxx, 'h' => xxx]
     * @return \Facebook\WebDriver\WebDriverDimension
     */
    protected function createDimension(array $size)
    {
        return new WebDriverDimension($size['w'], $size['h']);
    }

    /**
     * スクリーンショット
     * @param RemoteWebDriver $driver
     * @param string $fileName Without extension
     * @param string $dir capture以下に階層が必要だったら階層を追加
     */
    protected function takeScreenshot(RemoteWebDriver $driver, $fileName, $dir='')
    {
        $path = $this->capturePath($dir);
        $driver->takeScreenshot($path . $fileName .'.png');
    }

    /**
     * キャプチャ保存用のパスを返す
     * @param string $dir capture以下の階層
     * @return string
     */
    private function capturePath($dir='')
    {
        $_dir = $dir === '' ? '' : trim($dir, '/') . '/';
        return realpath(__DIR__ . '/../capture') . '/' . $_dir;
    }
}
