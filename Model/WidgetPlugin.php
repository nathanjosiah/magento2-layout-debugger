<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Nathanjosiah\LayoutDebugger\Model;

use Magento\Cms\Model\Template\Filter;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;

/**
 *
 */
class WidgetPlugin
{
    /**
     * @var State
     */
    private $appState;
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct(
        State $appState,
        ScopeConfigInterface $config
    ) {
        $this->appState = $appState;
        $this->config = $config;
    }

    public function afterWidgetDirective(Filter $subject, $result, $construction)
    {
        if ($this->appState->getAreaCode() === Area::AREA_FRONTEND
            && !$this->config->getValue('dev/debug/layout_debugger_widget_comments_enabled')
            || $this->appState->getAreaCode() === Area::AREA_ADMINHTML
            && !$this->config->getValue('dev/debug/layout_debugger_widget_comments_enabled')
        ) {
            return $result;
        }

        return '<!-- ' . $construction[1] . $construction[2] . ' -->' . $result . '<!-- /' . $construction[1] . ' -->';
    }
}
