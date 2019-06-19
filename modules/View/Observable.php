<?php

namespace SMB\PhpWebDriver\Modules\View;

use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Interface of Observer
 * 
 * |  view  |<br>
 * |1.|2.|3.|<br>
 * |4.|2.|2.|<br>
 * |5.|2.|6.|
 * 
 * |1.|<br>
 * |4.|<br>
 * |2.|<br>
 * |6.|
 * 
 * Notification priority.<br>
 * 1. > 6. > 3. > 4. > 5. > 2. > 7.
 * 
 */
interface Observable
{
    /**
     * 1. はじめて画面が描画されることを通知
     * Notify that the first view is drawn.
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
    );

    /**
     * 2. 画面スクロールを通知
     * Notify screen scroll.
     * 
     * @param RemoteWebDriver $driver
     * @param int $contentsWidth  実際のコンテンツ横幅
     * @param int $contentsHeight 実際のコンテンツ縦幅
     * @param int $scrolledWidth  現在スクロール済みの横幅
     * @param int $scrolledHeight 現在スクロール済みの縦幅
     */
    public function notifyScreenScroll(
        RemoteWebDriver $driver,
        $contentsWidth,
        $contentsHeight,
        $scrolledWidth,
        $scrolledHeight
    );

    /**
     * 3. 横幅が最初に末端まで到達したことを通知
     * Notify that the view width has reached the end for the first.
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
    );

    /**
     * 4. 最初の縦スクロールを通知
     * Notify first vertical scroll.
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
    );

    /**
     * 5. 縦幅が最初に末端まで到達したことを通知
     * Notify that the view height has reached the end for the first.
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
    );

    /**
     * 6. 最後の画面が描画されることを通知
     * Notify that the last view is drawn.
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
    );

    /**
     * 7. 画面の描画が完了したことを通知
     * Notify that screen drawing is complete.
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
    );
}
