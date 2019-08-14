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
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout\Data\Structure;
use Magento\Framework\View\Page\Config;
use Nathanjosiah\LayoutDebugger\Model\OpenLayout;

/**
 *
 */
class XmlDumpPlugin
{
    /**
     * @var Structure
     */
    private $structure;

    /**
     * @var AbstractBlock[]
     */
    private $blocks;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var State
     */
    private $appState;

    private $outputElements;
    /**
     * @var Config
     */
    private $pageConfig;

    public function __construct(ScopeConfigInterface $config, State $appState, Config $pageConfig)
    {
        $this->config = $config;
        $this->appState = $appState;
        $this->pageConfig = $pageConfig;
    }

    public function beforeGetOutput(OpenLayout $subject)
    {
        if ($this->appState->getAreaCode() === Area::AREA_FRONTEND
            && !$this->config->getValue('dev/debug/layout_debugger_dump_enabled_frontend')
            || $this->appState->getAreaCode() === Area::AREA_ADMINHTML
            && !$this->config->getValue('dev/debug/layout_debugger_dump_enabled_adminhtml')
        ) {
            return;
        }

        $subject->addBlock(Template::class, 'layout_debugger', 'content');
        /** @var Template $block */
        $block = $subject->getBlock('layout_debugger');
        $block->setTemplate('Nathanjosiah_LayoutDebugger::output.phtml');

        $this->blocks = $subject->getAllBlocks();
        $this->outputElements = $subject->getOutputElements();
        $this->structure = $subject->getStructure();

        $document = new \DOMDocument();
        $document->formatOutput = true;
        $node = $document->createElement('outputElements');
        $document->appendChild($node);

        foreach ($this->outputElements as $output) {
            $child = $this->renderChild($output, $output, $document);
            $node->appendChild($child);
        }

        $block->setData('pageLayout', $this->pageConfig->getPageLayout());
        $block->setData('handles', $subject->getUpdate()->getHandles());
        $block->setData('serializedLayout', $document->saveHTML());
    }

    private function renderChild($name, $alias, $document)
    {
        $element = $this->structure->getElement($name);
        $children = $this->structure->getChildren($name);
        $node = $document->createElement($element['type']);
        $node->setAttribute('name', $name);

        if ($alias && $name !== $alias) {
            $node->setAttribute('as', $alias);
        }

        if (!empty($element['display'])) {
            $node->setAttribute('display', $element['display']);
        }

        if ($element['type'] === 'block'
            && $this->blocks[$name] instanceof Template
            && $this->blocks[$name]->getTemplate()
        ) {
            $node->setAttribute('template', $this->blocks[$name]->getTemplate());
        }

        foreach ($children as $childName => $alias) {
            $child = $this->renderChild($childName, $alias, $document);
            $node->appendChild($child);
        }

        return $node;
    }
}
