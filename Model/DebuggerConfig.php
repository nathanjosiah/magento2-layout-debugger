<?php
declare(strict_types=1);

namespace Nathanjosiah\LayoutDebugger\Model;


use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;

class DebuggerConfig
{
    const CONFIG_PATH_WIDGET_COMMENTS = 'dev/debug/layout_debugger_widget_comments_enabled';
    const CONFIG_PATH_DUMP_FRONTEND = 'dev/debug/layout_debugger_dump_enabled_frontend';
    const CONFIG_PATH_DUMP_ADMINHTML = 'dev/debug/layout_debugger_dump_enabled_adminhtml';
    const CONFIG_PATH_COMMENTS_FRONTEND = 'dev/debug/layout_debugger_comments_enabled_frontend';
    const CONFIG_PATH_COMMENTS_ADMINHTML = 'dev/debug/layout_debugger_comments_enabled_adminhtml';
    const CONFIG_PATH_TEMPLATE_HINTS = 'dev/debug/layout_debugger_template_hints';

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

    /**
     * Return if template comments are enabled
     *
     * @return bool
     */
    public function isTemplateHintsEnabled(): bool
    {
        if (!$this->config->getValue(self::CONFIG_PATH_TEMPLATE_HINTS)) {
            return false;
        }

        return true;
    }

    /**
     * Return if debugger comments are enabled for normal layout
     *
     * @return bool
     */
    public function isCommentsEnabled(): bool
    {
        if ($this->appState->getAreaCode() === Area::AREA_FRONTEND
            && !$this->config->getValue(self::CONFIG_PATH_COMMENTS_FRONTEND)
            || $this->appState->getAreaCode() === Area::AREA_ADMINHTML
            && !$this->config->getValue(self::CONFIG_PATH_COMMENTS_ADMINHTML)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Return if the dump feature is currently enabled
     *
     * @return bool
     */
    public function isDumpEnabled(): bool
    {
        if ($this->appState->getAreaCode() === Area::AREA_FRONTEND
            && !$this->config->getValue(self::CONFIG_PATH_DUMP_FRONTEND)
            || $this->appState->getAreaCode() === Area::AREA_ADMINHTML
            && !$this->config->getValue(self::CONFIG_PATH_DUMP_ADMINHTML)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Return if widget debugging comments are currently enabled
     *
     * @return bool
     */
    public function isWidgetCommentsEnabled(): bool
    {
        if (!$this->config->getValue(self::CONFIG_PATH_WIDGET_COMMENTS)) {
            return false;
        }

        return true;
    }
}
