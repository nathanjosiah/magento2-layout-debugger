<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Nathanjosiah\LayoutDebugger\Observer;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout\Data\Structure;
use Nathanjosiah\LayoutDebugger\Model\DebuggerConfig;
use Nathanjosiah\LayoutDebugger\Model\OpenLayout;

/**
 *
 */
class InlineCommentsObserver implements ObserverInterface
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
    /**
     * @var DebuggerConfig
     */
    private $debuggerConfig;

    /**
     * InlineCommentsObserver constructor.
     * @param ScopeConfigInterface $config
     * @param State $appState
     * @param DebuggerConfig $debuggerConfig
     */
    public function __construct(ScopeConfigInterface $config, State $appState, DebuggerConfig $debuggerConfig)
    {
        $this->config = $config;
        $this->appState = $appState;
        $this->debuggerConfig = $debuggerConfig ?? ObjectManager::getInstance()->get(DebuggerConfig::class);
    }

    public function execute(Observer $observer)
    {
        if (!$this->debuggerConfig->isCommentsEnabled()) {
            return;
        }

        $event = $observer->getEvent();
        $subject = $event->getDataByKey('layout');
        $name = $event->getDataByKey('element_name');
        $transport = $event->getDataByKey('transport');

        if (!$subject instanceof OpenLayout) {
            return;
        }

        if (!$this->structure) {
            $this->structure = $subject->getStructure();
        }

        $element = $this->structure->getElement($name);
        $output = $transport->getData('output');

        if ($output) {
            $output = "<!--{$name} type=\"{$element['type']}\" parent=\"{$this->structure->getParentId($name)}\"-->{$output}<!--/{$name}-->\n";
        } else {
            $output = "<!--{$name} type=\"{$element['type']}\" parent=\"{$this->structure->getParentId($name)}\"/-->\n";
        }

        $transport->setData('output', $output);
    }
}
