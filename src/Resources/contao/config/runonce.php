<?php

/**
 * Quick Product Content Element for Isotope eCommerce and Contao Open Source CMS
 *
 * Copyright (C) 2015 Rhyme.Digital
 *
 * @package    Isotope_Quickproducts
 * @link       http://rhyme.digital
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

class IsoQuickProductsRunOnce extends Controller
{

    /**
     * Initialize the object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('Database');
    }


    /**
     * Run the controller
     */
    public function run()
    {
        $this->updateTo202();
    }


    /**
     * Update to version 2.0.2
     */
    private function updateTo202()
    {

        // get the activeThemes and activeModules so we can merge them
        $objData = $this->Database->execute("SELECT type FROM tl_content WHERE type='isotope_quick'");
        if($objData->numRows < 1) {
            return;
        }

        //Update the existing fields with the new identifier
        $arrSet = array();
        $arrSet['type'] = 'iso_quickproducts';

        $this->Database->prepare("UPDATE tl_content %s WHERE type='isotope_quick'")
            ->set($arrSet)
            ->execute();

    }
}


/**
 * Instantiate controller
 */
$objIsoQuickProductsRunOnce = new IsoQuickProductsRunOnce();
$objIsoQuickProductsRunOnce->run();
