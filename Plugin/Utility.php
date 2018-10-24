<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_LayeredNavigation
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Weltpixel TEAM
 */
namespace WeltPixel\LayeredNavigation\Plugin;

class Utility extends \WeltPixel\Backend\Plugin\Utility
{
    /**
     * @return string
     */
    protected function getModuleName()
    {
        return $this->convertToString(
            [
                '87', '101', '108', '116', '80', '105', '120', '101', '108', '95', '76', '97', '121', '101', '114',
                '101', '100', '78', '97', '118', '105', '103', '97', '116', '105', '111', '110', '95', '70', '114',
                '101', '101'
            ]
        );
    }

    /**
     * @return array
     */
    protected function _getAdminPaths()
    {
        return [
            $this->convertToString(
                [
                    '115', '121', '115', '116', '101', '109', '95', '99', '111', '110', '102', '105', '103', '47',
                    '101', '100', '105', '116', '47', '115', '101', '99', '116', '105', '111', '110', '47', '119',
                    '101', '108', '116', '112', '105', '120', '101', '108', '95', '108', '97', '121', '101', '114',
                    '101', '100', '110', '97', '118', '105', '103', '97', '116', '105', '111', '110'
                ]
            )
        ];
    }
}
