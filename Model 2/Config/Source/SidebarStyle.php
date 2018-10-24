<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class SidebarStyle
 * @package WeltPixel\LayeredNavigation\Model\Config\Source
 */
class SidebarStyle implements ArrayInterface
{
    /**
     * @var array
     */
    protected $_styles = array(
        '0' => 'Default',
        '1' => 'Slide In',
    );

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->_styles as $id => $style) :
            $options[] = array(
                'value' => $id,
                'label' => $style
            );
        endforeach;
        return $options;
    }
}