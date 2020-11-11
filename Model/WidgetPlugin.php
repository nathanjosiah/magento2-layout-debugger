<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Nathanjosiah\LayoutDebugger\Model;

use Magento\Cms\Model\Template\Filter;

/**
 *
 */
class WidgetPlugin
{
    /**
     * @var DebuggerConfig
     */
    private $debuggerConfig;

    /**
     * WidgetPlugin constructor.
     * @param DebuggerConfig $debuggerConfig
     */
    public function __construct(DebuggerConfig $debuggerConfig)
    {
        $this->debuggerConfig = $debuggerConfig;
    }

    public function afterWidgetDirective(Filter $subject, $result, $construction)
    {
        if (!$this->debuggerConfig->isWidgetCommentsEnabled()) {
            return $result;
        }

        return '<!-- ' . $construction[1] . $construction[2] . ' -->' . $result . '<!-- /' . $construction[1] . ' -->';
    }
}
