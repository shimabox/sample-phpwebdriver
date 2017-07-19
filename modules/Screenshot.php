<?php

namespace SMB\PhpWebDriver\Modules;

use SMB\PhpWebDriver\Modules\Elements\SpecInfo;
use SMB\PhpWebDriver\Modules\Elements\Spec;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Remote\WebDriverBrowserType;

/**
 * Screenshot
 */
class Screenshot
{
    /**
     * スクロールバー非表示用 style
     * @var string
     */
    private static $hiddenScrollBarStyle = "document.getElementsByTagName('body')[0].style.overflow='hidden'";

    /**
     * 画面キャプチャ
     * @param RemoteWebDriver $driver
     * @param string $filename
     * @param int $sleep Sleep for seconds
     * @return string キャプチャ画像ファイルパス
     */
    public function take(RemoteWebDriver $driver, $filename, $sleep=1)
    {
        (int)$sleep <= 0 ?: sleep((int)$sleep);

        $driver->executeScript(self::$hiddenScrollBarStyle);
        $driver->takeScreenshot($filename);

        $this->throwExceptionIfNotExistsFile($filename, 'Could not save screenshot');

        return $filename;
    }

    /**
     * 全画面キャプチャ
     * @param RemoteWebDriver $driver
     * @param string $filepath
     * @param string $filename
     * @param string $browser
     * @param int $sleep Sleep for seconds
     * @return string キャプチャ画像ファイルパス
     */
    public function takeFull(RemoteWebDriver $driver, $filepath, $filename, $browser, $sleep=1)
    {
        (int)$sleep <= 0 ?: sleep((int)$sleep);

        $captureFile = rtrim($filepath, '/') . '/' . $filename;

        if ($browser === WebDriverBrowserType::IE) { // IE(internet explorer)はページ全体を撮ってくれる
            return $this->take($driver, $captureFile, 0);
        }

        // スクロールバー非表示
        $driver->executeScript(self::$hiddenScrollBarStyle);

        // ページの左上までスクロール
        $driver->executeScript("window.scrollTo(0, 0);");

        // コンテンツサイズ取得
        $contentsWidth = $driver->executeScript("return Math.max(document.body.scrollWidth, document.body.offsetWidth, document.documentElement.clientWidth, document.documentElement.scrollWidth, document.documentElement.offsetWidth);");
        $contentsHeight = $driver->executeScript("return Math.max(document.body.scrollHeight, document.body.offsetHeight, document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight);");

        // 画面サイズ取得
        $viewWidth = $driver->executeScript("return window.innerWidth;");
        $viewHeight = $driver->executeScript("return window.innerHeight;");

        // 画像枠 この枠に対して切り取った画像を継ぎ接ぎしていく
        $imgFrame = imagecreatetruecolor($contentsWidth, $contentsHeight);

        // スクロール操作用
        $scrollWidth = 0;
        $scrollHeight = 0;

        // 縦分割数
        $rowCount = 0;

        // 縦スクロールの処理
        while ($scrollHeight < $contentsHeight) {

            // 横分割数
            $colCount = 0;
            // 横スクロール初期化
            $scrollWidth = 0;
            // 画面位置の設定
            $driver->executeScript(sprintf("window.scrollTo(%d, %d);", $scrollWidth, $scrollHeight));

            // 横スクロールの処理
            while ($scrollWidth < $contentsWidth) {

                if ($colCount > 0) {
                    // 画面サイズ分横スクロール
                    $driver->executeScript("window.scrollBy(". (string)$viewWidth . ", 0);");
                }

                // 現在表示されている範囲のキャプチャをとる
                $tmpFile = $filepath . sprintf($browser . '_tmp_%d_%d_', $rowCount, $colCount) . '_' . time() . '.png';
                $driver->takeScreenshot($tmpFile);

                $this->throwExceptionIfNotExistsFile($tmpFile, 'Could not save tmp screenshot');

                // 貼り付け元画像を作成
                $src = imagecreatefrompng($tmpFile);

                // 右端か下端に到達したら画像を切り取ってimgFrameに貼り付ける
                if (
                    $this->isOverContentsWidth($scrollWidth, $viewWidth, $contentsWidth)
                    || $this->isOverContentsHeight($scrollHeight, $viewHeight, $contentsHeight)
                ) {
                    $newWidth = $viewWidth;
                    $newHeight= $viewHeight;

                    $srcX = 0;
                    $srcY = 0;

                    // 右端に到達
                    if ($this->isOverContentsWidth($scrollWidth, $viewWidth, $contentsWidth)) {
                        $newWidth = $contentsWidth - $scrollWidth;
                        $srcX = $viewWidth - $newWidth;
                    }

                    // 下端に到達
                    if ($this->isOverContentsHeight($scrollHeight, $viewHeight, $contentsHeight)) {
                        $newHeight = $contentsHeight - $scrollHeight;
                        $srcY = $viewHeight - $newHeight;
                        $colCount += 1;
                    }

                    $this->toPatchTheImage($tmpFile, $captureFile, $imgFrame, $src, $scrollWidth, $scrollHeight, $srcX, $srcY, $newWidth, $newHeight);

                    $scrollWidth += $newWidth;

                } else { // 普通に貼り付ける

                    $this->toPatchTheImage($tmpFile, $captureFile, $imgFrame, $src, $scrollWidth, $scrollHeight, 0, 0, $viewWidth, $viewHeight);

                    $scrollWidth += $viewWidth;
                    $colCount += 1;
                }
            }

            $scrollHeight += $viewHeight;
            $rowCount += 1;
        }

        $this->throwExceptionIfNotExistsFile($captureFile, 'Could not save full screenshot');

        return $captureFile;
    }

    /**
     * スクロール済の画面幅 + 現在表示中の幅 がコンテンツの幅を超えたかどうか
     * @param int $scrollWidth   現在スクロール済みの幅
     * @param int $viewWidth     現在表示中の幅
     * @param int $contentsWidth コンテンツの幅
     * @return boolean
     */
    private function isOverContentsWidth($scrollWidth, $viewWidth, $contentsWidth)
    {
        return ($scrollWidth + $viewWidth) >= $contentsWidth;
    }

    /**
     * スクロール済の画面高さ + 現在表示中の高さ がコンテンツの高さを超えたかどうか
     * @param int $scrollHeight   現在スクロール済みの高さ
     * @param int $viewHeight     現在表示中の高さ
     * @param int $contentsHeight コンテンツの高さ
     * @return boolean
     */
    private function isOverContentsHeight($scrollHeight, $viewHeight, $contentsHeight)
    {
        return ($scrollHeight + $viewHeight) >= $contentsHeight;
    }

    /**
     * 画像の継ぎ接ぎをする
     * @param string    $tmpFile     現在表示されている範囲のキャプチャ画像
     * @param string    $captureFile キャプチャ画像パス
     * @param resource  $dest        貼り付け先画像
     * @param resource  $src         貼り付け元画像
     * @param int       $destX       貼り付け先画像のx座標
     * @param int       $destY       貼り付け先画像のy座標
     * @param int       $srcX        貼り付け元画像のx座標
     * @param int       $srcY        貼り付け元画像のy座標
     * @param int       $srcW        貼り付け元画像の幅
     * @param int       $srcH        貼り付け元画像の高さ
     */
    private function toPatchTheImage($tmpFile, $captureFile, $dest, $src, $destX, $destY, $srcX, $srcY, $srcW, $srcH)
    {
        // copy
        imagecopy($dest, $src, $destX, $destY, $srcX, $srcY, $srcW, $srcH);

        // save
        imagepng($dest, $captureFile);

        @unlink($tmpFile); // unlink function might be restricted in mac os x.
    }

    /**
     * 画像ファイルが存在しない場合、例外を投げる
     *
     * @param string $file
     * @param string $message
     * @throws \Exception
     */
    private function throwExceptionIfNotExistsFile($file, $message)
    {
        if( ! file_exists($file)) {
            throw new \Exception($message);
        }
    }
}
