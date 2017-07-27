<?php

namespace SMB\PhpWebDriver\Tests\Util;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\Chrome;
use Facebook\WebDriver\Firefox;

use SMB\PhpWebDriver\Tests\Exception\DisabledWebDriverException;
use SMB\PhpWebDriver\Tests\Exception\NotExistsWebDriverException;

/**
 * Capabilities
 */
class Capabilities
{
    /**
     * DesiredCapabilities
     * @var \Facebook\WebDriver\Remote\DesiredCapabilities
     */
    protected $capabilities;

    /**
     * Default UserAgent
     * @var string default iOS10
     */
    protected $defaultUserAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1';

    /**
     * browser name
     * @var type
     */
    private $browser = '';

    /**
     * コンストラクタ
     * @param string $browser chrome or ie or firefox
     * @throws DisabledWebDriverException
     * @throws NotExistsWebDriverException
     */
    public function __construct($browser)
    {
        switch ($browser) {
            case WebDriverBrowserType::CHROME: // chrome
                if (getenv('ENABLED_CHROME_DRIVER') !== 'true') {
                    throw new DisabledWebDriverException('Disabled chrome webdriver');
                }

                if (getenv('IS_PLATFORM_WINDOWS') === 'true' && getenv('CHROME_DRIVER_PATH') === '') {
                    throw new NotExistsWebDriverException('not exists chrome webdriver');
                } elseif (getenv('CHROME_DRIVER_PATH') !== '') {
                    putenv('webdriver.chrome.driver=' . getenv('CHROME_DRIVER_PATH'));
                }

                $this->capabilities = DesiredCapabilities::chrome();
                $this->browser = $browser;
                break;
            case WebDriverBrowserType::IE: // internet explorer
                if (getenv('ENABLED_IE_DRIVER') !== 'true') {
                    throw new DisabledWebDriverException('Disabled ie webdriver');
                }

                if (getenv('IS_PLATFORM_WINDOWS') === 'true' && getenv('IE_DRIVER_PATH') === '') {
                    throw new NotExistsWebDriverException('not exists ie webdriver');
                } elseif (getenv('IE_DRIVER_PATH') !== '') {
                    putenv('webdriver.ie.driver=' . getenv('IE_DRIVER_PATH'));
                }

                $this->capabilities = DesiredCapabilities::internetExplorer();
                $this->browser = $browser;
                break;
            case WebDriverBrowserType::FIREFOX: // firefox
            default :
                if (getenv('ENABLED_FIREFOX_DRIVER') !== 'true') {
                    throw new DisabledWebDriverException('Disabled firefox webdriver');
                }

                if (getenv('IS_PLATFORM_WINDOWS') === 'true' && getenv('FIREFOX_DRIVER_PATH') === '') {
                    throw new NotExistsWebDriverException('not exists firefox webdriver');
                } elseif (getenv('FIREFOX_DRIVER_PATH') !== '') {
                    putenv('webdriver.gecko.driver=' . getenv('FIREFOX_DRIVER_PATH'));
                }

                $this->capabilities = DesiredCapabilities::firefox();
                $this->browser = WebDriverBrowserType::FIREFOX;
                break;
        }
    }

    /**
     * getter DesiredCapabilities
     * @return \Facebook\WebDriver\Remote\DesiredCapabilities
     */
    public function get()
    {
        return $this->capabilities;
    }

    /**
     * setting default UserAgent
     */
    public function settingDefaultUserAgent()
    {
        $this->settingUserAgent($this->defaultUserAgent);
    }

    /**
     * setter UserAgent
     * @param string $ua
     */
    public function setUserAgent($ua)
    {
        $this->settingUserAgent($ua);
    }

    /**
     * setting UserAgent
     * @param string $ua
     */
    protected function settingUserAgent($ua)
    {
        switch ($this->browser) {
            case WebDriverBrowserType::CHROME:
                $options = new Chrome\ChromeOptions();
                $options->addArguments(['--user-agent=' . $ua]);
                $this->capabilities->setCapability(Chrome\ChromeOptions::CAPABILITY, $options);
                break;
            case WebDriverBrowserType::FIREFOX:
                $profile = new Firefox\FirefoxProfile();
                $profile->setPreference('general.useragent.override', $ua);
                $this->capabilities->setCapability(Firefox\FirefoxDriver::PROFILE, $profile);
            default :
                break;
        }
    }
}
