<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Nathanjosiah\LayoutDebugger\Plugin;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\View\Layout\Data\Structure;
use Nathanjosiah\LayoutDebugger\Model\OpenLayout;

/**
 *
 */
class InlineCommentsPlugin
{
    /**
     * @var Structure
     */
    private $structure;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var State
     */
    private $appState;

    public function __construct(ScopeConfigInterface $config, State $appState)
    {
        $this->config = $config;
        $this->appState = $appState;
    }

    public function aroundRenderNonCachedElement(OpenLayout $subject, \Closure $next, $name)
    {
        if ($this->appState->getAreaCode() === Area::AREA_FRONTEND
            && !$this->config->getValue('dev/debug/layout_debugger_comments_enabled_frontend')
            || $this->appState->getAreaCode() === Area::AREA_ADMINHTML
            && !$this->config->getValue('dev/debug/layout_debugger_comments_enabled_adminhtml')
        ) {
            return $next($name);
        }

        if (!$this->structure) {
            $this->structure = $subject->getStructure();
        }

        $element = $this->structure->getElement($name);
        $output = $next($name);

        if ($output) {
            $output = "<!-- layout: {$name} ::: {$element['type']} ::: parent {$this->structure->getParentId($name)} -->{$output}<!-- {$name} | end -->\n";
        } else {
            $output = "<!-- layout: {$name} ::: {$element['type']} ::: parent {$this->structure->getParentId($name)} | (no children) -->\n";
        }

        return $output;
    }
}
