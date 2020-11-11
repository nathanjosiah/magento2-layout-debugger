<?php
declare(strict_types=1);

namespace Nathanjosiah\LayoutDebugger\Plugin;

use Magento\Framework\View\TemplateEngineInterface;
use Nathanjosiah\LayoutDebugger\Model\DebuggerConfig;

class TemplateEnginePlugin
{
    /**
     * @var DebuggerConfig
     */
    private $debuggerConfig;

    /**
     * TemplateEnginePlugin constructor.
     * @param DebuggerConfig $debuggerConfig
     */
    public function __construct(DebuggerConfig $debuggerConfig)
    {
        $this->debuggerConfig = $debuggerConfig;
    }

    /**
     * Maybe add the template helper comments to the rendered template
     *
     * @param TemplateEngineInterface $subject
     * @param \Magento\Framework\View\Element\BlockInterface $block
     * @param $templateFile
     * @param array $dictionary
     * @param $result
     */
    public function afterRender(
        TemplateEngineInterface $subject,
        $result,
        \Magento\Framework\View\Element\BlockInterface $block,
        $templateFile,
        array $dictionary = []
    ) {
        if (!$this->debuggerConfig->isTemplateHintsEnabled()) {
            return $result;
        }

        return '<!-- template: ' . $templateFile . ' -->' . $result . '<!-- end template: ' . $templateFile . ' -->';
    }
}
