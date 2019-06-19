<?php

namespace SMB\PhpWebDriver\Modules\View;

use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Observer
 */
class Observer implements Observable
{
    /**
     * はじめて画面が描画されたときに行う処理
     * @var \Closure
     */
    private $_processForFirstRender;

    /**
     * はじめて画面が描画されたときに行う処理をセット
     * @param \Closure $func
     */
    public function processForFirstRender(\Closure $func)
    {
        $this->_processForFirstRender = $func;
    }

    /**
     * clear processForFirstRender
     */
    public function clearProcessForFirstRender()
    {
        $this->_processForFirstRender = null;
    }

    /**
     * はじめて画面が描画されたときに行う処理
     * 
     * @param RemoteWebDriver $driver
     * @param int $contentsWidth  実際のコンテンツ横幅
     * @param int $contentsHeight 実際のコンテンツ縦幅
     * @param int $scrolledWidth  現在スクロール済みの横幅
     * @param int $scrolledHeight 現在スクロール済みの縦幅
     */
    public function notifyFirstRender(
        RemoteWebDriver $driver,
        $contentsWidth,
        $contentsHeight,
        $scrolledWidth,
        $scrolledHeight
    ) {
        if ($this->_processForFirstRender === null) {
            return;
        }

        call_user_func_array($this->_processForFirstRender, [
            $driver,
            $contentsWidth,
            $contentsHeight,
            $scrolledWidth,
            $scrolledHeight
        ]);
    }

    /**
     * 画面の切り替えが行われたときに行う処理
     * @var \Closure
     */
    private $_processForScreenSwitching;

    /**
     * 画面の切り替えが行われたときに行う処理をセット
     * @param \Closure $func
     */
    public function processForScreenSwitching(\Closure $func)
    {
        $this->_processForScreenSwitching = $func;
    }

    /**
     * clear processForScreenSwitching
     */
    public function clearProcessForScreenSwitching()
    {
        $this->_processForScreenSwitching = null;
    }

    /**
     * 画面の切り替えが行われたときに行う処理
     * 
     * @param RemoteWebDriver $driver
     * @param int $contentsWidth  実際のコンテンツ横幅
     * @param int $contentsHeight 実際のコンテンツ縦幅
     * @param int $scrolledWidth  現在スクロール済みの横幅
     * @param int $scrolledHeight 現在スクロール済みの縦幅
     */
    public function notifyScreenSwitching(
        RemoteWebDriver $driver,
        $contentsWidth,
        $contentsHeight,
        $scrolledWidth,
        $scrolledHeight
    ) {
        if ($this->_processForScreenSwitching === null) {
            return;
        }

        call_user_func_array($this->_processForScreenSwitching, [
            $driver,
            $contentsWidth,
            $contentsHeight,
            $scrolledWidth,
            $scrolledHeight
        ]);
    }

    /**
     * 横幅が最初に末端まで到達したときに行う処理
     * @var \Closure
     */
    private $_processForViewWidthHasReachedEndForFirst;

    /**
     * 横幅が最初に末端まで到達したときに行う処理をセット
     * @param \Closure $func
     */
    public function processForViewWidthHasReachedEndForFirst(\Closure $func)
    {
        $this->_processForViewWidthHasReachedEndForFirst = $func;
    }

    /**
     * clear processForViewWidthHasReachedEndForFirst
     */
    public function clearProcessForViewWidthHasReachedEndForFirst()
    {
        $this->_processForViewWidthHasReachedEndForFirst = null;
    }
 
    /**
     * 横幅が最初に末端まで到達したときに行う処理
     * 
     * @param RemoteWebDriver $driver
     * @param int $contentsWidth  実際のコンテンツ横幅
     * @param int $contentsHeight 実際のコンテンツ縦幅
     * @param int $scrolledWidth  現在スクロール済みの横幅
     * @param int $scrolledHeight 現在スクロール済みの縦幅
     */
    public function notifyThatViewWidthHasReachedEndForFirst(
        RemoteWebDriver $driver,
        $contentsWidth,
        $contentsHeight,
        $scrolledWidth,
        $scrolledHeight
    ) {
        if ($this->_processForViewWidthHasReachedEndForFirst === null) {
            return;
        }

        call_user_func_array($this->_processForViewWidthHasReachedEndForFirst, [
            $driver,
            $contentsWidth,
            $contentsHeight,
            $scrolledWidth,
            $scrolledHeight
        ]);
    }

    /**
     * 最初の縦スクロールが行われたときに行う処理
     * @var \Closure
     */
    private $_processForFirstVerticalScroll;

    /**
     * 最初の縦スクロールが行われたときに行う処理処理をセット
     * @param \Closure $func
     */
    public function processForFirstVerticalScroll(\Closure $func)
    {
        $this->_processForFirstVerticalScroll = $func;
    }

    /**
     * clear processForFirstVerticalScroll
     */
    public function clearProcessForFirstVerticalScroll()
    {
        $this->_processForFirstVerticalScroll = null;
    }

    /**
     * 最初の縦スクロールが行われたときに行う処理
     * 
     * @param RemoteWebDriver $driver
     * @param int $contentsWidth  実際のコンテンツ横幅
     * @param int $contentsHeight 実際のコンテンツ縦幅
     * @param int $scrolledWidth  現在スクロール済みの横幅
     * @param int $scrolledHeight 現在スクロール済みの縦幅
     */
    public function notifyFirstVerticalScroll(
        RemoteWebDriver $driver,
        $contentsWidth,
        $contentsHeight,
        $scrolledWidth,
        $scrolledHeight
    ) {
        if ($this->_processForFirstVerticalScroll === null) {
            return;
        }

        call_user_func_array($this->_processForFirstVerticalScroll, [
            $driver,
            $contentsWidth,
            $contentsHeight,
            $scrolledWidth,
            $scrolledHeight
        ]);
    }

    /**
     * 縦幅が最初に末端まで到達したときに行う処理
     * @var \Closure
     */
    private $_processForViewHeightHasReachedEndForFirst;

    /**
     * 縦幅が最初に末端まで到達したときに行う処理をセット
     * @param \Closure $func
     */
    public function processForViewHeightHasReachedEndForFirst(\Closure $func)
    {
        $this->_processForViewHeightHasReachedEndForFirst = $func;
    }

    /**
     * clear processForViewHeightHasReachedEndForFirst
     */
    public function clearProcessForViewHeightHasReachedEndForFirst()
    {
        $this->_processForViewHeightHasReachedEndForFirst = null;
    }

    /**
     * 縦幅が最初に末端まで到達したときに行う処理
     * 
     * @param RemoteWebDriver $driver
     * @param int $contentsWidth  実際のコンテンツ横幅
     * @param int $contentsHeight 実際のコンテンツ縦幅
     * @param int $scrolledWidth  現在スクロール済みの横幅
     * @param int $scrolledHeight 現在スクロール済みの縦幅
     */
    public function notifyThatViewHeightHasReachedEndForFirst(
        RemoteWebDriver $driver,
        $contentsWidth,
        $contentsHeight,
        $scrolledWidth,
        $scrolledHeight
    ) {
        if ($this->_processForViewHeightHasReachedEndForFirst === null) {
            return;
        }

        call_user_func_array($this->_processForViewHeightHasReachedEndForFirst, [
            $driver,
            $contentsWidth,
            $contentsHeight,
            $scrolledWidth,
            $scrolledHeight
        ]);
    }

    /**
     * 最後の画面が描画されるときに行う処理
     * @var \Closure
     */
    private $_processForLastRender;

    /**
     * 最後の画面が描画されるときに行う処理をセット
     * @param \Closure $func
     */
    public function processForLastRender(\Closure $func)
    {
        $this->_processForLastRender = $func;
    }

    /**
     * clear processForLastRender
     */
    public function clearProcessForLastRender()
    {
        $this->_processForLastRender = null;
    }

    /**
     * 最後の画面が描画されるときに行う処理
     * 
     * @param RemoteWebDriver $driver
     * @param int $contentsWidth  実際のコンテンツ横幅
     * @param int $contentsHeight 実際のコンテンツ縦幅
     * @param int $scrolledWidth  現在スクロール済みの横幅
     * @param int $scrolledHeight 現在スクロール済みの縦幅
     */
    public function notifyLastRender(
        RemoteWebDriver $driver,
        $contentsWidth,
        $contentsHeight,
        $scrolledWidth,
        $scrolledHeight
    ) {
        if ($this->_processForLastRender === null) {
            return;
        }

        call_user_func_array($this->_processForLastRender, [
            $driver,
            $contentsWidth,
            $contentsHeight,
            $scrolledWidth,
            $scrolledHeight
        ]);
    }

    /**
     * 画面の描画が完了したときに行う処理
     * @var \Closure
     */
    private $_processForRenderComplete;

    /**
     * 画面の描画が完了したときに行う処理をセット
     * @param \Closure $func
     */
    public function processForRenderComplete(\Closure $func)
    {
        $this->_processForRenderComplete = $func;
    }

    /**
     * clear processForRenderComplete
     */
    public function clearProcessForRenderComplete()
    {
        $this->_processForRenderComplete = null;
    }

    /**
     * 画面の描画が完了したときに行う処理
     * 
     * @param RemoteWebDriver $driver
     * @param int $contentsWidth  実際のコンテンツ横幅
     * @param int $contentsHeight 実際のコンテンツ縦幅
     * @param int $scrolledWidth  現在スクロール済みの横幅
     * @param int $scrolledHeight 現在スクロール済みの縦幅
     */
    public function notifyRenderComplete(
        RemoteWebDriver $driver,
        $contentsWidth,
        $contentsHeight,
        $scrolledWidth,
        $scrolledHeight
    ) {
        if ($this->_processForRenderComplete === null) {
            return;
        }

        call_user_func_array($this->_processForRenderComplete, [
            $driver,
            $contentsWidth,
            $contentsHeight,
            $scrolledWidth,
            $scrolledHeight
        ]);
    }
}
