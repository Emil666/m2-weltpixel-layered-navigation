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
 * Class FilterButton
 * @package WeltPixel\LayeredNavigation\Model\Config\Source
 */
class FilterButton implements ArrayInterface
{
    /**
     * @var array
     */
    protected $_styles = array(
        0 => 'Icon Only',
        1 => 'Icon + Label',
        2 => 'Label Only',
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