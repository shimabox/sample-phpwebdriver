<?php

namespace SMB\PhpWebDriver\Modules\Elements;

use SMB\PhpWebDriver\Modules\Elements\Spec;

/**
 * SpecPool
 */
class SpecPool
{
    /**
     * Spec
     * @var array [\SMB\PhpWebDriver\Modules\Elements\Spec]
     */
    private $spec = array();

    /**
     * Spec 追加
     * @param \SMB\PhpWebDriver\Modules\Elements\Spec $spec
     * @return \SMB\PhpWebDriver\Modules\Elements\SpecPool
     */
    public function addSpec(Spec $spec)
    {
        $this->spec[] = $spec;
        return $this;
    }

    /**
     * Spec ゲッター
     * @return array [\SMB\PhpWebDriver\Modules\Elements\Spec]
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * Spec clear
     */
    public function clearSpec()
    {
        $this->spec = [];
    }
}