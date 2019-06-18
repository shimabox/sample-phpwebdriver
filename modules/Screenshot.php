<?php

namespace SMB\PhpWebDriver\Modules;

use SMB\PhpWebDriver\Modules\Elements\SpecPool;
use SMB\PhpWebDriver\Modules\Elements\Spec;
use SMB\PhpWebDriver\Modules\View\Observable;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;

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
     * オブザーバー
     * @var Observable
     */
    private $observer;

    /**
     * オブザーバーのセット
     * @param Observable $observer
     * @return \SMB\PhpWebDriver\Modules\Screenshot
     */
    public function setObserver(Observable $observer)
    {
        $this->observer = $observer;
        return $this;
    }

    /**
     * オブザーバーの削除
     * @return \SMB\PhpWebDriver\Modules\Screenshot
     */
    public function removeObserver()
    {
        $this->observer = null;
        return $this;
    }

    /**
     * 画面キャプチャ
     * @param \Facebook\WebDriver\Remote\RemoteWebDriver $driver
     * @param string $filename
     * @param int $sleep Sleep for seconds
     * @return string キャプチャ画像ファイルパス
     */
    public function take(RemoteWebDriver $driver, $filename, $sleep=1)
    {
        (int)$sleep <= 0 ?: sleep((int)$sleep);

        $driver->executeScript(self::$hiddenScrollBarStyle);

        $_filename = $this->removeExtension($filename) . '.png';
        $driver->takeScreenshot($_filename);

        $this->throwExceptionIfNotExistsFile($_filename, 'Could not save screenshot');

        return $_filename;
    }

    /**
     * 全画面キャプチャ
     * @param \Facebook\WebDriver\Remote\RemoteWebDriver $driver
     * @param string $filepath
     * @param string $filename
     * @param int $sleep Sleep for seconds
     * @return string キャプチャ画像ファイルパス
     */
    public function takeFull(RemoteWebDriver $driver, $filepath, $filename, $sleep=1)
    {
        (int)$sleep <= 0 ?: sleep((int)$sleep);

        $_filename = $this->removeExtension($filename);
        $captureFile = $this->normalizeFilePath($filepath) . $_filename . '.png';

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

                // スクロール済の画面幅 + 現在表示中の幅 がコンテンツの右端(幅)に到達したか
                $reachedContentsWidth = $this->toReachContentsWidth(($scrollWidth + $viewWidth), $contentsWidth);
                // スクロール済の画面高さ + 現在表示中の高さ がコンテンツの下端(高さ)に到達したか
                $reachedContentsHeight = $this->toReachContentsHeight(($scrollHeight + $viewHeight), $contentsHeight);

                // 通知
                $this->notify(
                    $driver, 
                    $contentsWidth, $contentsHeight, 
                    $scrollWidth, $scrollHeight, 
                    $reachedContentsWidth, $reachedContentsHeight,
                    $rowCount
                );

                // 現在表示されている範囲のキャプチャをとる
                $tmpFile = $filepath . sprintf('tmp_%d_%d_', $rowCount, $colCount) . microtime(true) . '.png';
                $driver->takeScreenshot($tmpFile);

                $this->throwExceptionIfNotExistsFile($tmpFile, 'Could not save tmp screenshot');

                // $driver->takeScreenshotで撮ったキャプチャのサイズがviewサイズと違っていたらリサイズする
                $this->resizeCaptureFileToViewSize($tmpFile, $viewWidth, $viewHeight);

                // 貼り付け元画像を作成
                $src = imagecreatefrompng($tmpFile);

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
                    $this->toPatchTheImage($captureFile, $imgFrame, $src, $scrollWidth, $scrollHeight, $srcX, $srcY, $newWidth, $newHeight);

                    // 後処理
                    $this->destroyImage($src);
                    $this->deleteImageFile($tmpFile);

                    $scrollWidth += $newWidth;

                    continue;
                }

                // 右端か下端に到達していない限り現在表示されている範囲のキャプチャは
                // そのままimgFrameに貼り付ける
                $this->toPatchTheImage($captureFile, $imgFrame, $src, $scrollWidth, $scrollHeight, 0, 0, $viewWidth, $viewHeight);

                // 後処理
                $this->destroyImage($src);
                $this->deleteImageFile($tmpFile);

                $scrollWidth += $viewWidth;
                $colCount += 1;
            }

            $scrollHeight += $viewHeight;
            $rowCount += 1;
        }

        $this->throwExceptionIfNotExistsFile($captureFile, 'Could not save full screenshot');

        $this->destroyImage($imgFrame);

        return $captureFile;
    }

    /**
     * 指定された要素のキャプチャ
     * @param \Facebook\WebDriver\Remote\RemoteWebDriver $driver
     * @param string $filepath
     * @param string $filename Without extension
     * @param \SMB\Screru\Screenshot\Elements\SpecPool $specPool 取得したい要素のスペック
     * @param int $sleep Sleep for seconds
     * @return array キャプチャ画像ファイルパス
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     * @link https://github.com/facebook/php-webdriver/wiki/taking-full-screenshot-and-of-an-element
     */
    public function takeElement(RemoteWebDriver $driver, $filepath, $filename, SpecPool $specPool, $sleep=1)
    {
        // 一旦全画面のキャプチャを撮る
        $_filename = $this->removeExtension($filename);
        $tmpFullScreenshot = $this->takeFull($driver, $filepath, $_filename . '_tmp_' . microtime(true) . '.png', $sleep);

        // create image instances
        $src = imagecreatefrompng($tmpFullScreenshot);

        $captureFileList = [];

        $specList = $specPool->getSpec();
        foreach ($specList as $specIndex => $spec) {
            $elements = null;

            $driver->wait(60, 250)->until(
                function () use ($driver, $spec, &$elements) {
                    $elements = $driver->findElements(WebDriverBy::cssSelector($spec->getSelector()));

                    $count = count($elements);
                    $expected = $spec->getExpectedElementCount();

                    $conditon = $spec->getCondition();
                    switch ($conditon) {
                        case Spec::EQUAL :
                            return $count === $expected;
                        case Spec::NOT_EQUAL :
                            return $count !== $expected;
                        case Spec::GREATER_THAN :
                            return $count > $expected;
                        case Spec::LESS_THAN :
                            return $count < $expected;
                        case Spec::GREATER_THAN_OR_EQUAL :
                            return $count >= $expected;
                        case Spec::LESS_THAN_OR_EQUAL :
                            return $count <= $expected;
                    }
                }
            );

            foreach ($elements as $index => $element) {
                // 指定された要素のサイズ
                $elementWidth = $element->getSize()->getWidth();
                $elementHeight = $element->getSize()->getHeight();

                // 指定された要素が存在する座標
                $elementSrcX = $element->getLocation()->getX();
                $elementSrcY = $element->getLocation()->getY();

                $dest = imagecreatetruecolor($elementWidth, $elementHeight);
                $captureFile = $this->normalizeFilePath($filepath) . $_filename . '_' . $specIndex . '_' . $index . '.png';

                $this->toPatchTheImage($captureFile, $dest, $src, 0, 0, $elementSrcX, $elementSrcY, $elementWidth, $elementHeight);

                $this->destroyImage($dest);

                $this->throwExceptionIfNotExistsFile($captureFile, 'Could not save element screenshot');

                $captureFileList[] = $captureFile;
            }
        }

        $this->destroyImage($src);
        $this->deleteImageFile($tmpFullScreenshot);

        return $captureFileList;
    }

    /**
     * $driver->takeScreenshotで撮ったキャプチャのサイズがviewサイズ(window.innerWidth, window.innerHeight)
     * と違っていたらviewサイズにリサイズする
     * 
     * $driver->takeScreenshotで撮ったキャプチャのサイズがviewサイズと違っていると正しく全画面キャプチャができない
     * 
     * @param string $tmpCaptureFile $driver->takeScreenshotで撮ったキャプチャ
     * @param int $viewWidth  viewの横幅(window.innerWidth)
     * @param int $viewHeight viewの縦幅(window.innerHeight)
     */
    private function resizeCaptureFileToViewSize($tmpCaptureFile, $viewWidth, $viewHeight)
    {
        // キャプチャのサイズとviewサイズが一致していたら何もしない
        list($width, $height) = getimagesize($tmpCaptureFile);
        if ($width === $viewWidth && $height === $viewHeight) {
            return;
        }

        // キャプチャのresource
        $source = imagecreatefrompng($tmpCaptureFile);
        // 新しく描画する画像resourceを作成
        $dest = imagecreatetruecolor($width, $height);

        // viewサイズにリサイズ
        imagecopyresampled($dest, $source, 0, 0, 0, 0, $viewWidth, $viewHeight, $width, $height);
        imagepng($dest, $tmpCaptureFile);

        // destroy
        imagedestroy($source);
        imagedestroy($dest);
    }

    /**
     * 通知
     * @param RemoteWebDriver $driver
     * @param int $contentsWidth
     * @param int $contentsHeight
     * @param int $scrollWidth
     * @param int $scrollHeight
     * @param int $reachedContentsWidth
     * @param int $reachedContentsHeight
     * @param int $rowCount
     */
    private function notify(
        RemoteWebDriver $driver, 
        $contentsWidth, 
        $contentsHeight, 
        $scrollWidth, 
        $scrollHeight, 
        $reachedContentsWidth, 
        $reachedContentsHeight,
        $rowCount
    )
    {
        if ($this->observer === null) {
            return;
        }

        if ($scrollWidth === 0 && $scrollHeight === 0) {
            $this->observer->notifyFirstRender($driver, $contentsWidth, $contentsHeight, $scrollWidth, $scrollHeight);
            return;
        }

        if ($reachedContentsWidth && $reachedContentsHeight) {
            $this->observer->notifyLastRender($driver, $contentsWidth, $contentsHeight, $scrollWidth, $scrollHeight);
            return;
        }

        if ($scrollHeight === 0 && $reachedContentsWidth) {
            $this->observer->notifyThatViewWidthHasReachedEndForFirst($driver, $contentsWidth, $contentsHeight, $scrollWidth, $scrollHeight);
            return;
        }

        if ($scrollWidth === 0 && $rowCount === 1) {
            $this->observer->notifyFirstVerticalScroll($driver, $contentsWidth, $contentsHeight, $scrollWidth, $scrollHeight);
            return;
        }

        if ($scrollWidth === 0 && $reachedContentsHeight) {
            $this->observer->notifyThatViewHeightHasReachedEndForFirst($driver, $contentsWidth, $contentsHeight, $scrollWidth, $scrollHeight);
            return;
        }

        if ($scrollWidth > 0 || $scrollHeight > 0) {
            $this->observer->notifyScreenSwitching($driver, $contentsWidth, $contentsHeight, $scrollWidth, $scrollHeight);
            return;
        }
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
    private function toPatchTheImage($captureFile, $dest, $src, $destX, $destY, $srcX, $srcY, $srcW, $srcH)
    {
        // copy
        imagecopy($dest, $src, $destX, $destY, $srcX, $srcY, $srcW, $srcH);

        // save
        imagepng($dest, $captureFile);
    }

    /**
     * 画像リソース破棄
     * @param resource $image
     */
    private function destroyImage($image)
    {
        imagedestroy($image);
    }

    /**
     * 画像ファイルの削除
     * @param string $imgFile
     */
    private function deleteImageFile($imgFile)
    {
        @unlink($imgFile); // unlink function might be restricted in mac os x.
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

    /**
     * ファイルパスの正規化
     * @param string $filepath
     * @return string
     */
    private function normalizeFilePath($filepath)
    {
        return rtrim($filepath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * 拡張子の削除
     * @param type $filename
     * @return string
     */
    private function removeExtension($filename)
    {
        return preg_replace('/\.(png|jpg|jpeg|gif)$/i', '', $filename);
    }
}
