<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Plugin\Layer;

use  WeltPixel\LayeredNavigation\Helper\Data;

/**
 * Class FilterList
 * @package WeltPixel\LayeredNavigation\Plugin\Layer
 */
class FilterList
{
    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;

    /**
     * FilterList constructor.
     * @param Data $wpHelper
     */
    public function __construct(
        Data $wpHelper
    )
    {
        $this->_wpHelper = $wpHelper;
    }

    /**
     * Remove category filter if disabled in configuration
     *
     * @param \Magento\Catalog\Model\Layer\FilterList $subject
     * @param $result
     * @return array
     */
    public function afterGetFilters(\Magento\Catalog\Model\Layer\FilterList $subject, $result)
    {
        if(!$this->_wpHelper->isEnabled()){
            return $result;
        }
        $filteredResult = [];
        if(!$this->_wpHelper->showCategoriesBlock()) {
            foreach($result as $r) {
                if($r->getRequestVar() != 'cat') {
                    $filteredResult[] = $r;
                }
            }
            return $filteredResult;

        } else {
            return $result;
        }


    }
}
