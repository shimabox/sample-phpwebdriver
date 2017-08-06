<?php

namespace SMB\PhpWebDriver\Modules\Elements;

/**
 * Spec
 */
class Spec
{
    /**
     * 等しい ===
     * @var string
     */
    const EQUAL = '===';

    /**
     * 等しくない !==
     * @var string
     */
    const NOT_EQUAL = '!==';

    /**
     * ～より大きい >
     * @var string
     */
    const GREATER_THAN = '>';

    /**
     * ～より小さい <
     * @var string
     */
    const LESS_THAN = '<';

    /**
     * ～以上 >=
     * @var string
     */
    const GREATER_THAN_OR_EQUAL = '>=';

    /**
     * ～以下 <=
     * @var string
     */
    const LESS_THAN_OR_EQUAL = '<=';

    /**
     * css selector
     * @var string
     */
    private $selector = '';

    /**
     * 条件
     * @var string
     */
    private $condition = '';

    /**
     * 期待する要素の出現数
     * @var int
     */
    private $expectedElementCount = 1;

    /**
     * コンストラクタ
     * @param string $selector
     * @param string $condition default '==='
     * @param int $expectedElementCount default 1
     */
    public function __construct($selector, $condition=self::EQUAL, $expectedElementCount = 1)
    {
        $this->selector = $selector;
        $this->condition = $condition;
        $this->expectedElementCount = $expectedElementCount;
    }

    /**
     * css selector ゲッター
     * @return string
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * 条件 ゲッター
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * 期待する要素の出現数
     * @return int
     */
    public function getExpectedElementCount()
    {
        return $this->expectedElementCount;
    }
}
