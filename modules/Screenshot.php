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

        // 実際のコンテンツサイズを取得
        $contentsWidth = $driver->executeScript("return Math.max(document.body.scrollWidth, document.body.offsetWidth, document.documentElement.clientWidth, document.documentElement.scrollWidth, document.documentElement.offsetWidth);");
        $contentsHeight = $driver->executeScript("return Math.max(document.body.scrollHeight, document.body.offsetHeight, document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight);");

        // 現在表示されている画面のサイズを取得
        $viewWidth = $driver->executeScript("return window.innerWidth;");
        $viewHeight = $driver->executeScript("return window.innerHeight;");

        // 画像枠 この枠に対して切り取った画像を継ぎ接ぎしていく
        $imgFrame = imagecreatetruecolor($contentsWidth, $contentsHeight);

        // Macだと画像を継ぎ足した部分の隅(右下)が若干粗くなる(黒ずむ)ので透過にして少しごまかす
        imagealphablending($imgFrame, false);
        imagesavealpha($imgFrame, true);

        // スクロール操作用
        $scrollWidth = 0;
        $scrollHeight = 0;

        // 縦分割数
        $rowCount = 0;

        // 縦スクロールの処理
        // コンテンツの縦幅を超えるまで現在見えている画面の縦幅サイズずつスクロールさせる
        while ($scrollHeight < $contentsHeight) {

            // 横分割数
            $colCount = 0;
            // 横スクロール初期化
            $scrollWidth = 0;
            // 画面位置の設定
            $driver->executeScript(sprintf("window.scrollTo(%d, %d);", $scrollWidth, $scrollHeight));

            // 横スクロールの処理
            // コンテンツの横幅を超えるまで現在見えている画面の横幅サイズずつスクロールさせる
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

                // スクロール済の画面幅 + 現在表示中の幅 がコンテンツの右端(幅)に到達したか
                $reachedContentsWidth = $this->toReachContentsWidth(($scrollWidth + $viewWidth), $contentsWidth);
                // スクロール済の画面高さ + 現在表示中の高さ がコンテンツの下端(高さ)に到達したか
                $reachedContentsHeight = $this->toReachContentsHeight(($scrollHeight + $viewHeight), $contentsHeight);

                // スクロール量がコンテンツの右端か下端に到達したら画像を切り取ってimgFrameに貼り付ける
                if ($reachedContentsWidth || $reachedContentsHeight) {

                    $newWidth = $viewWidth;
                    $newHeight = $viewHeight;

                    $srcX = 0;
                    $srcY = 0;

                    // スクロール済の画面幅 + 現在表示中の幅 がコンテンツの幅に到達
                    if ($reachedContentsWidth) {
                        // キャプチャに足りない部分の横幅を求める
                        $newWidth = $contentsWidth - $scrollWidth;
                        // 現在表示されている範囲のキャプチャから切り取る範囲のx座標を求める
                        $srcX = $viewWidth - $newWidth;
                    }

                    // スクロール済の画面高さ + 現在表示中の高さ がコンテンツの高さに到達
                    if ($reachedContentsHeight) {
                        // キャプチャに足りない部分の縦幅を求める
                        $newHeight = $contentsHeight - $scrollHeight;
                        // 現在表示されている範囲のキャプチャから切り取る範囲のy座標を求める
                        $srcY = $viewHeight - $newHeight;
                        // 高さが超えている間は横にスクロールさせる
                        $colCount += 1;
                    }

                    // 現在表示されている範囲のキャプチャから指定した範囲で切り取った画像を
                    // imgFrameに貼り付ける
                    $this->toPatchTheImage($tmpFile, $captureFile, $imgFrame, $src, $scrollWidth, $scrollHeight, $srcX, $srcY, $newWidth, $newHeight);

                    $scrollWidth += $newWidth;

                    continue;
                }

                // 右端か下端に到達していない限り現在表示されている範囲のキャプチャは
                // そのままimgFrameに貼り付ける
                $this->toPatchTheImage($tmpFile, $captureFile, $imgFrame, $src, $scrollWidth, $scrollHeight, 0, 0, $viewWidth, $viewHeight);

                $scrollWidth += $viewWidth;
                $colCount += 1;
            }

            $scrollHeight += $viewHeight;
            $rowCount += 1;
        }

        $this->throwExceptionIfNotExistsFile($captureFile, 'Could not save full screenshot');

        return $captureFile;
    }

    /**
     * スクロール済の画面幅 + 現在表示中の幅 がコンテンツの右端(幅)に到達したか
     * @param int $targetWidth   スクロール済の画面幅 + 現在表示中の画面幅
     * @param int $contentsWidth コンテンツの幅
     * @return boolean
     */
    private function toReachContentsWidth($targetWidth, $contentsWidth)
    {
        return $targetWidth >= $contentsWidth;
    }

    /**
     * スクロール済の画面高さ + 現在表示中の高さ がコンテンツの下端(高さ)に到達したか
     * @param int $targetHeight   スクロール済の画面高さ + 現在表示中の画面高さ
     * @param int $contentsHeight コンテンツの高さ
     * @return boolean
     */
    private function toReachContentsHeight($targetHeight, $contentsHeight)
    {
        return $targetHeight >= $contentsHeight;
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
