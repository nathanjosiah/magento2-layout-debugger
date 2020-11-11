<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Nathanjosiah\LayoutDebugger\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Design;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;
use Magento\Framework\View\Page\Config;
use Psr\Log\LoggerInterface as Logger;

/**
 *
 */
class OpenLayout extends Layout
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var Config
     */
    private $pageConfig;
    /**
     * @var DebuggerConfig
     */
    private $debuggerConfig;

    /**
     * OpenLayout constructor.
     * @param Layout\ProcessorFactory $processorFactory
     * @param ManagerInterface $eventManager
     * @param Layout\Data\Structure $structure
     * @param MessageManagerInterface $messageManager
     * @param Design\Theme\ResolverInterface $themeResolver
     * @param Layout\ReaderPool $readerPool
     * @param Layout\GeneratorPool $generatorPool
     * @param FrontendInterface $cache
     * @param Layout\Reader\ContextFactory $readerContextFactory
     * @param Layout\Generator\ContextFactory $generatorContextFactory
     * @param AppState $appState
     * @param Logger $logger
     * @param bool $cacheable
     * @param SerializerInterface|null $serializer
     * @param ScopeConfigInterface|null $config
     * @param Config|null $pageConfig
     * @param DebuggerConfig|null $debuggerConfig
     */
    public function __construct(
        Layout\ProcessorFactory $processorFactory,
        ManagerInterface $eventManager,
        Layout\Data\Structure $structure,
        MessageManagerInterface $messageManager,
        Design\Theme\ResolverInterface $themeResolver,
        Layout\ReaderPool $readerPool,
        Layout\GeneratorPool $generatorPool,
        FrontendInterface $cache,
        Layout\Reader\ContextFactory $readerContextFactory,
        Layout\Generator\ContextFactory $generatorContextFactory,
        AppState $appState,
        Logger $logger,
        bool $cacheable = true,
        ?SerializerInterface $serializer = null,
        ScopeConfigInterface $config = null,
        Config $pageConfig = null,
        DebuggerConfig $debuggerConfig = null
    ) {
        parent::__construct(
            $processorFactory,
            $eventManager,
            $structure,
            $messageManager,
            $themeResolver,
            $readerPool,
            $generatorPool,
            $cache,
            $readerContextFactory,
            $generatorContextFactory,
            $appState,
            $logger,
            $cacheable,
            $serializer
        );
        $this->config = $config ?? ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        $this->pageConfig = $pageConfig ?? ObjectManager::getInstance()->get(Config::class);
        $this->debuggerConfig = $debuggerConfig ?? ObjectManager::getInstance()->get(DebuggerConfig::class);
    }

    public function getOutput()
    {
        if (!$this->debuggerConfig->isDumpEnabled()) {
            return parent::getOutput();
        }

        $this->addBlock(Template::class, 'layout_debugger', 'after.body.start');
        /** @var Template $block */
        $block = $this->getBlock('layout_debugger');
        $block->setTemplate('Nathanjosiah_LayoutDebugger::output.phtml');

        $document = new \DOMDocument();
        $document->formatOutput = true;
        $node = $document->createElement('outputElements');
        $document->appendChild($node);

        foreach ($this->_output as $output) {
            $child = $this->renderDebugXmlChild($output, $output, $document);
            $node->appendChild($child);
        }

        $block->setData('pageLayout', $this->pageConfig->getPageLayout());
        $block->setData('handles', $this->getUpdate()->getHandles());
        $block->setData('serializedLayout', $document->saveHTML());

        return parent::getOutput();
    }

    public function getStructure()
    {
        return $this->structure;
    }

    private function renderDebugXmlChild($name, $alias, $document)
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

        if ($element['type'] === 'block') {
            $node->setAttribute('class', get_class($this->_blocks[$name]));

            if ($this->_blocks[$name] instanceof Template
                && $this->_blocks[$name]->getTemplate()
            ) {
                $node->setAttribute('template', $this->_blocks[$name]->getTemplate());
            }
        }

        foreach ($children as $childName => $alias) {
            $child = $this->renderDebugXmlChild($childName, $alias, $document);
            $node->appendChild($child);
        }

        return $node;
    }
}
