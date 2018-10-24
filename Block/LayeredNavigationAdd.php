<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */
namespace WeltPixel\LayeredNavigation\Block;
use Magento\Framework\View\Element\Template\Context;
use WeltPixel\LayeredNavigation\Helper\Data;
class LayeredNavigationAdd extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var Data
     */
    protected $_wpHelper;
    /**
     * AjaxInfiniteScroll constructor.
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_wpHelper = $helper;
        parent::__construct($context, $data);
    }
    /**
     * check if module enabled and ajax mode is enabled
     *
     * @return bool
     */
    public function isLnEnabled() {
        $is = false;
        if($this->_wpHelper->isEnabled()) {
            $is = ($this->_wpHelper->isAjaxEnabled()) ? true : false;
        }
        return $is;
    }
}