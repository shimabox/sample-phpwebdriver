<?php

namespace SMB\PhpWebDriver\Tests\Util;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome;
use Facebook\WebDriver\Firefox;

/**
 * Capabilities
 */
class Capabilities
{
    /**
     * @var string
     */
    const CHROME = 'chrome';

    /**
     * @var string
     */
    const FIREFOX = 'firefox';

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
     * @param string $browser chrome or firefox
     */
    public function __construct($browser)
    {
        switch ($browser) {
            case self::CHROME: // chrome
                $this->capabilities = DesiredCapabilities::chrome();
                $this->browser = $browser;
                break;
            case self::FIREFOX: // firefox
            default :
                $this->capabilities = DesiredCapabilities::firefox();
                $this->browser = self::FIREFOX;
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
            case self::CHROME:
                $options = new Chrome\ChromeOptions();
                $options->addArguments(['--user-agent=' . $ua]);
                $this->capabilities->setCapability(Chrome\ChromeOptions::CAPABILITY, $options);
                break;
            case self::FIREFOX:
                $profile = new Firefox\FirefoxProfile();
                $profile->setPreference('general.useragent.override', $ua);
                $this->capabilities->setCapability(Firefox\FirefoxDriver::PROFILE, $profile);
            default :
                break;
        }
    }
}
