<?php

/**
 * Quick Product Content Element for Isotope eCommerce and Contao Open Source CMS
 *
 * Copyright (C) 2015 - 2022 Rhyme.Digital
 *
 * @package    Isotope_Quickproducts
 * @link       http://rhyme.digital
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

\Controller::loadDataContainer('tl_module');
\Controller::loadLanguageFile('tl_module');

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['iso_quickproducts'] = '{type_legend},type,headline;{include_legend},iso_products;{config_legend},iso_listingSortField,iso_listingSortDirection,iso_cols,iso_use_quantity,iso_buttons,iso_addProductJumpTo;{template_legend},customTpl,iso_gallery,iso_list_layout;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['iso_products'] = array
(
    'label'                         => &$GLOBALS['TL_LANG']['tl_content']['iso_products'],
    'exclude'                       => true,
    'inputType'                     => 'tableLookup',
    'sql'                           => "blob NULL",
    'eval' => array
    (
        'mandatory'                 => true,
        'doNotSaveEmpty'            => true,
        'tl_class'                  => 'clr',
        'foreignTable'              => 'tl_iso_product',
        'fieldType'                 => 'checkbox',
/*        'listFields'                => array('type'=>"(SELECT name FROM " . \Isotope\Model\ProductType::getTable() . " WHERE " . \Isotope\Model\Product::getTable() . ".type=" . \Isotope\Model\ProductType::getTable() . ".id)", 'name', 'sku'),*/
        'listFields'                => array('type'=>"type", 'name', 'sku'),
        'searchFields'              => array('name', 'alias', 'sku', 'description'),
        'sqlWhere'                  => '',
        'searchLabel'               => &$GLOBALS['TL_LANG']['tl_content']['iso_products_searchLabel'],
        'enableSorting'             => true,
    ),
);

$GLOBALS['TL_DCA']['tl_content']['fields']['iso_gallery'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_content']['iso_gallery'],
    'exclude'                   => true,
    'inputType'                 => 'select',
    'foreignKey'                => \Isotope\Model\Gallery::getTable().'.name',
    'eval'                      => array('includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
    'sql'                       => "int(10) unsigned NOT NULL default '0'",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['iso_list_layout'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_content']['iso_list_layout'],
    'exclude'                   => true,
    'inputType'                 => 'select',
    'options_callback'          => function(\DataContainer $dc) {
        return \Isotope\Backend::getTemplates('iso_list_');
    },
    'eval'                      => array('includeBlankOption'=>true, 'tl_class'=>'w50', 'chosen'=>true),
    'sql'                       => "varchar(64) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['iso_use_quantity'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_content']['iso_use_quantity'],
    'exclude'                   => true,
    'inputType'                 => 'checkbox',
    'eval'                      => array('tl_class'=>'w50 m12'),
    'sql'                       => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['iso_cols'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_content']['iso_cols'],
    'exclude'                   => true,
    'default'                   => 1,
    'inputType'                 => 'text',
    'eval'                      => array('maxlength'=>1, 'rgxp'=>'digit', 'tl_class'=>'w50'),
    'sql'                       => "int(1) unsigned NOT NULL default '1'",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['iso_buttons'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_content']['iso_buttons'],
    'exclude'                   => true,
    'inputType'                 => 'checkboxWizard',
    'default'                   => array('add_to_cart'),
    'options_callback'          => array('Isotope\Backend\Module\Callback', 'getButtons'),
    'eval'                      => array('multiple'=>true, 'tl_class'=>'clr'),
    'sql'                       => "blob NULL",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['iso_listingSortField'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_content']['iso_listingSortField'],
    'exclude'                   => true,
    'inputType'                 => 'select',
    'options_callback'          => array('Isotope\Backend\Module\Callback', 'getSortingFields'),
    'eval'                      => array('includeBlankOption'=>true, 'tl_class'=>'clr w50'),
    'sql'                       => "varchar(255) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['iso_listingSortDirection'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_content']['iso_listingSortDirection'],
    'exclude'                   => true,
    'default'                   => 'DESC',
    'inputType'                 => 'select',
    'options'                   => array('DESC', 'ASC'),
    'reference'                 => &$GLOBALS['TL_LANG']['tl_content']['sortingDirection'],
    'eval'                      => array('tl_class'=>'w50'),
    'sql'                       => "varchar(8) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['iso_cart_jumpTo'] = array
(
    'label'                     => &$GLOBALS['TL_LANG']['tl_content']['iso_cart_jumpTo'],
    'exclude'                   => true,
    'inputType'                 => 'pageTree',
    'foreignKey'                => 'tl_page.title',
    'eval'                      => array('fieldType'=>'radio', 'tl_class'=>'clr'),
    'explanation'               => 'jumpTo',
    'sql'                       => "int(10) unsigned NOT NULL default '0'",
    'relation'                  => array('type'=>'hasOne', 'load'=>'lazy'),
);

$GLOBALS['TL_DCA']['tl_content']['fields']['iso_addProductJumpTo'] = array(
    'label'                     => &$GLOBALS['TL_LANG']['tl_module']['iso_addProductJumpTo'],
    'exclude'                   => true,
    'inputType'                 => 'pageTree',
    'foreignKey'                => 'tl_page.title',
    'eval'                      => array('fieldType'=>'radio', 'tl_class'=>'clr'),
    'explanation'               => 'jumpTo',
    'sql'                       => "int(10) unsigned NOT NULL default '0'",
    'relation'                  => array('type'=>'hasOne', 'load'=>'lazy'),
);
