<?php

namespace SMB\PhpWebDriver\Tests;

use SMB\PhpWebDriver\Tests\Util\Capabilities;
use SMB\PhpWebDriver\Tests\Exception\NotExistsWebDriverException;
use SMB\PhpWebDriver\Modules\Screenshot;

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
     * Capabilities
     * @var \Facebook\WebDriver\Remote\Desired\Capabilities
     */
    protected $capabilities;

    /**
     * Screenshot
     * @var \SMB\PhpWebDriver\Modules\Screenshot
     */
    protected $screenshot;

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

        $this->screenshot = new Screenshot();
    }

    /**
     * テスト実行後の処理<br>
     * 1テストにつき1回呼ばれる
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->closeDriver();
    }

    /**
     * driver close
     */
    protected function closeDriver()
    {
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

        /* @var \Facebook\WebDriver\Remote\Desired\Capabilities */
        $this->capabilities = $cap->get();

        // ドライバーの起動
        $driver = RemoteWebDriver::create($this->host, $this->capabilities);

        // サイズの指定があるか
        if ($dimension !== null) {
            $driver->manage()->window()->setSize($dimension);
        }

        $this->driver = $driver;

        return $this->driver;
    }

    /**
     * 画面サイズをMAXに
     * @param RemoteWebDriver $driver
     */
    protected function windowMaximize(RemoteWebDriver $driver)
    {
        $driver->manage()->window()->maximize();
    }

    /**
     * Util\Capabilities 生成
     * @param string $browser chrome or firefox
     * @return \SMB\PhpWebDriver\Tests\Util\Capabilities
     */
    protected function createCapabilities($browser='')
    {
        try {
            return new Capabilities($browser);
        } catch (NotExistsWebDriverException $e) {
            // 対象のWebDriverが設定されていなければskipしておく
            $this->markTestSkipped($e->getMessage());
        }
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
     * @param string $filename Without extension
     * @param int $sleep Sleep for seconds
     * @param string $dir capture以下に階層が必要だったら階層を追加
     */
    protected function takeScreenshot(RemoteWebDriver $driver, $filename, $sleep=1, $dir='')
    {
        $path = $this->capturePath($dir);
        $this->screenshot->take($driver, $path . $filename.'.png', $sleep);
    }

    /**
     * 全画面スクリーンショット
     * @param RemoteWebDriver $driver
     * @param string $filename Without extension
     * @param int $sleep Sleep for seconds
     * @param string $dir capture以下に階層が必要だったら階層を追加
     */
    protected function takeFullScreenshot(RemoteWebDriver $driver, $filename, $sleep=1, $dir='')
    {
        $path = $this->capturePath($dir);
        $this->screenshot->takeFull($driver, $path, $filename.'.png', $this->capabilities->getBrowserName(), $sleep);
    }

    /**
     * キャプチャ保存用のパスを返す
     * @param string $dir capture以下の階層
     * @return string
     */
    protected function capturePath($dir='')
    {
        $_dir = $dir === '' ? '' : trim($dir, '/') . '/';
        return realpath(__DIR__ . '/../capture') . '/' . $_dir;
    }
}
