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


/**
 * Register PSR-0 namespace
 */
NamespaceClassLoader::add('Rhyme', 'system/modules/isotope_quickproducts/library');


/**
 * Register classes outside the namespace folder
 */
NamespaceClassLoader::addClassMap(array
(

));

/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    //Content Elements
    'ce_iso_quickproducts'      => 'system/modules/isotope_quickproducts/templates/element',
));