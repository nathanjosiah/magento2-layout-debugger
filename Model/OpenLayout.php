<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Nathanjosiah\LayoutDebugger\Model;

use Magento\Framework\View\Layout;

/**
 *
 */
class OpenLayout extends Layout
{
    public function getOutputElements()
    {
        return $this->_output;
    }

    public function getStructure()
    {
        return $this->structure;
    }
}
