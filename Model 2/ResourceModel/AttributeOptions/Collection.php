<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Model\ResourceModel\AttributeOptions;

/**
 * Class Collection
 * @package WeltPixel\LayeredNavigation\Model\ResourceModel\AttributeOptions
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'weltpixel_ln_atribute_options_collection';
    protected $_eventObject = 'attribute_options_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\LayeredNavigation\Model\AttributeOptions', 'WeltPixel\LayeredNavigation\Model\ResourceModel\AttributeOptions');
    }

}