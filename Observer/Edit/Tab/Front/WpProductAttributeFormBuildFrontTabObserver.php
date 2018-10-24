<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */

namespace WeltPixel\LayeredNavigation\Observer\Edit\Tab\Front;

use Magento\Config\Model\Config\Source;
use Magento\Framework\Module\Manager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;
use WeltPixel\LayeredNavigation\Model\AttributeOptions;

/**
 * Class WpProductAttributeFormBuildFrontTabObserver
 * @package WeltPixel\LayeredNavigation\Observer\Edit\Tab\Front
 */
class WpProductAttributeFormBuildFrontTabObserver implements ObserverInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $optionList;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var Http
     */
    protected $_request;

    /**
     * @var AttributeOptions
     */
    protected $_wpModel;

    protected $_wpAttributeObj = false;

    /**
     * WpProductAttributeFormBuildFrontTabObserver constructor.
     * @param Manager $moduleManager
     * @param Source\Yesno $optionList
     * @param Http $request
     */
    public function __construct(
        Manager $moduleManager,
        Source\Yesno $optionList,
        Http $request,
        AttributeOptions $wpModel

    )
    {
        $this->optionList = $optionList;
        $this->moduleManager = $moduleManager;
        $this->_request = $request;
        $this->_wpModel = $wpModel;

        $this->_getWpAttributeOptionValues();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->moduleManager->isOutputEnabled('WeltPixel_LayeredNavigation')) {
            return;
        }

        /** @var \Magento\Framework\Data\Form\AbstractForm $form */
        $form = $observer->getForm();

        $fieldset = $form->addFieldset(
            'wp_front_fieldset',
            ['legend' => __('WeltPixel Layered Navigation Properties'), 'collapsable' => false]
        );

        $fieldset->addField(
            'wp_display_options',
            'select',
            [
                'name' => 'wp_display_options',
                'label' => __("Filter Display Options"),
                'title' => __('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
                'note' => __('Can be used only with catalog input type Dropdown, Multiple Select and Price.'),
                'values' => [
                    ['value' => 0, 'label' => __('Closed')],
                    ['value' => 1, 'label' => __('Fully Opened')],
                    ['value' => 2, 'label' => __('Expandable')],
                ],
                'value' => $this->_getDisplayOption(),
            ]
        );

        $field = $fieldset->addField(
            'wp_visible_options',
            'text',
            [
                'name' => 'wp_visible_options',
                'label' => __("Initial Number of Visible Options"),
                'value' => $this->_getVisibleOptions(),
                'title' => __('The number of attribute option(s) that will be initially visible. Can be used only with Filter Display Options - Expandable.'),
                'note' => __('The number of attribute option(s) that will be initially visible. Can be used only with Filter Display Options - Expandable.')

            ]
        );

        $field = $fieldset->addField(
            'wp_visible_options_step',
            'select',
            [
                'name' => 'wp_visible_options_step',
                'label' => __("Expandable items behaviour"),
                'values' => [
                    ['value' =>99, 'label' => __('All')],
                    ['value' => 5, 'label' => __('Show 5 more')],
                    ['value' => 10, 'label' => __('Show 10 more')],
                    ['value' => 15, 'label' => __('Show 15 more')],
                ],
                'value' => $this->_getVisibleOptions(),
                'title' => __('Select the number of attribute option(s) to show/hide when using Expand feature. Can be used only with Filter Display Options - Expandable.'),
                'note' => __('Select the number of attribute option(s) to show/hide when using Expand feature. Can be used only with Filter Display Options - Expandable.')

            ]
        );

        $fieldset->addField(
            'wp_is_multiselect',
            'select',
            [
                'name' => 'wp_is_multiselect',
                'label' => __("Enable Multiselect"),
                'value' => $this->_getIsMultiselect(),
                'values' => [
                    ['value' => 0, 'label' => __('No')],
                    ['value' => 1, 'label' => __('Yes')],
                ],
                'title' => __('Allow to filter multiple options from the same attribute.'),
                'note' => __('Allow to filter multiple options from the same attribute.')

            ]
        );

        $fieldset->addField(
            'wp_show_quantity',
            'select',
            [
                'name' => 'wp_show_quantity',
                'label' => __("Show Item Counter"),
                'value' => $this->_getShowQuantity(),
                'values' => [
                    ['value' => 0, 'label' => __('No')],
                    ['value' => 1, 'label' => __('Yes')],
                ],
                'title' => __('Show item counter next to the current attribute options.'),
                'note' => __('Show item counter next to the current attribute options.')

            ]
        );

        $fieldset->addField(
            'wp_sort_by',
            'select',
            [
                'name' => 'wp_sort_by',
                'label' => __("Sort By"),
                'value' => $this->_getSortBy(),
                'values' => [
                    ['value' => 1, 'label' => __('Position')],
                    ['value' => 2, 'label' => __('Name')],
                ],
                'title' => __('Select the sorting of the current attribute options.'),
                'note' => __('Select the sorting of the current attribute options.')

            ]
        );


        $field->setAfterElementHtml(
            "<script>
                   //<![CDATA[
                       require(['jquery', 'jquery/ui'], function($){ 
                            var acceptedTypeArray = ['select','multiselect','price','swatch_visual','swatch_text'],
                                wpDisplayOptionEl = $('#wp_display_options'),
                                wpVisibleOptionsEl = $('#wp_visible_options'),
                                wpVisibleOptionsStepEl = $('#wp_visible_options_step'),
                                wpIsMultiselectEl = $('#wp_is_multiselect'),
                                wpShowQuantityEl = $('#wp_show_quantity'),
                                wpSortByEl = $('#wp_sort_by'),
                                mageFrontendInpEl = $('#frontend_input'),
                            
                                displayOpt = '" . $this->_getDisplayOption() . "',
                                visibleOpt = '" . $this->_getVisibleOptions() . "',
                                visibleOptStep = '" . $this->_getVisibleOptionsStep() . "',
                                isMultiselect = '" . $this->_getIsMultiselect() . "',
                                showQuantity = '" . $this->_getShowQuantity() . "',
                                sortBy = '" . $this->_getSortBy() . "';
                                wpDisplayOptionEl.val(displayOpt);
                                wpVisibleOptionsEl.val(visibleOpt);
                                wpVisibleOptionsStepEl.val(visibleOptStep);
                                wpIsMultiselectEl.val(isMultiselect);
                                wpShowQuantityEl.val(showQuantity);
                                wpSortByEl.val(sortBy);
                                
                                setWpVisibility();
                            
                            
                            wpDisplayOptionEl.change(function(){
                                
                                if($(this).val() == 2) {
                                    enableElement(wpVisibleOptionsEl);
                                    enableElement(wpVisibleOptionsStepEl);
                                    wpVisibleOptionsEl.val(visibleOpt);
                                    wpVisibleOptionsStepEl.val(visibleOptStep);
                                } else {
                                    wpVisibleOptionsEl.val(99);
                                    wpVisibleOptionsStepEl.val(99);
                                    disableElement(wpVisibleOptionsEl);
                                    disableElement(wpVisibleOptionsStepEl);
                                }
                            });
                            
                            mageFrontendInpEl.change(function(){
                                var selVal = $(this).val();
                                if($.inArray(selVal, acceptedTypeArray) !== -1) {
                                    enableElement(wpDisplayOptionEl);
                                    enableElement(wpIsMultiselectEl);
                                    enableElement(wpShowQuantityEl);
                                    enableElement(wpSortByEl);
                                    if(wpDisplayOptionEl.val() == 2){
                                       enableElement(wpVisibleOptionsEl);
                                       enableElement(wpVisibleOptionsStepEl);
                                    } else {
                                        disableElement(wpVisibleOptionsEl);
                                        disableElement(wpVisibleOptionsStepEl);
                                    }
                                } else {
                                    disableElement(wpVisibleOptionsEl);
                                    disableElement(wpVisibleOptionsStepEl);
                                    disableElement(wpDisplayOptionEl);
                                    disableElement(wpIsMultiselectEl);
                                    disableElement(wpShowQuantityEl);
                                    disableElement(wpSortByEl);
                                }
                            });
                            
                            /**
                            * set wp fields property(disabled)
                            */
                            function setWpVisibility(){                            
                                var elVal = mageFrontendInpEl.val();
                                if($.inArray(elVal, acceptedTypeArray) !== -1) {                                  
                                    enableElement(wpDisplayOptionEl);
                                    enableElement(wpIsMultiselectEl);
                                    enableElement(wpShowQuantityEl);
                                    enableElement(wpSortByEl);
                                    
                                } else {                                    
                                    disableElement(wpVisibleOptionsEl);
                                    disableElement(wpVisibleOptionsStepEl);
                                    disableElement(wpDisplayOptionEl);
                                    disableElement(wpIsMultiselectEl);
                                    disableElement(wpShowQuantityEl);
                                    disableElement(wpSortByEl);
                                }
                                
                                if(wpDisplayOptionEl.val() != 2){
                                   disableElement(wpVisibleOptionsEl);
                                   disableElement(wpVisibleOptionsStepEl);
                                }
                                                             
                            }
                            
                            /**
                            * set element as disabled
                            * @param element
                            */
                            function disableElement(element) {
                                element.prop('disabled', true);
                            }
                            
                            /**
                            * remove 'disabled' attribute from element
                            * @param element
                            */
                            function enableElement(element) {
                                element.removeAttr('disabled');
                            }
                        });
                   //]]>
             </script>"
        );

    }

    /**
     * @return $this|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getWpAttributeOptionValues()
    {
        $attributeId = $this->_request->getParam('attribute_id');

        if ($attributeId) {
            $this->_wpAttributeObj = $this->_wpModel->getDisplayOptionsByAttribute($attributeId);
        }

        return $this;
    }

    /**
     * Get attribute 'display_option' value
     * @return int
     */
    protected function _getDisplayOption()
    {
        $val = AttributeOptions::DISPLAY_OPTION_DEF_VAL;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getDisplayOption();
            }

        }

        return $val;
    }

    /**
     * Get attribute 'visible_options' value
     * @return int
     */
    protected function _getVisibleOptions()
    {
        $val = '';
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getVisibleOptions();
            }

        }

        return $val;
    }

    /**
     * Get attribute 'visible_options' value
     * @return int
     */
    protected function _getVisibleOptionsStep()
    {
        $val = '';
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getVisibleOptionsStep();
            }
        }

        return $val;
    }

    /**
     * Get attribute 'is_multiselect' value
     * @return int
     */
    protected function _getIsMultiselect()
    {
        $val = 0;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getIsMultiselect();
            }
        }

        return $val;
    }

    /**
     * Get attribute 'show_quantity' value
     * @return int
     */
    protected function _getShowQuantity()
    {
        $val = 0;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getShowQuantity();
            }
        }

        return $val;
    }

    /**
     * Get attribute 'sort_by' value
     * @return int
     */
    protected function _getSortBy()
    {
        $val = 1;
        if($this->_wpAttributeObj) {
            if(!empty($this->_wpAttributeObj->getData())) {
                $val = $this->_wpAttributeObj->getSortBy();
            }
        }

        return $val;
    }

}
