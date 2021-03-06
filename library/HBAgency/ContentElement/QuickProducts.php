<?php

/**
 * Quick Product Content Element for Isotope eCommerce and Contao Open Source CMS
 *
 * Copyright (C) 2014 HB Agency
 *
 * @package    Isotope_Quickproducts
 * @link       http://www.hbagency.com
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace HBAgency\ContentElement;

use Isotope\ContentElement\ContentElement as Iso_Element;
use Haste\Haste;
use Haste\Generator\RowClass;
use Haste\Http\Response\HtmlResponse;
use Isotope\Isotope;
use Isotope\Model\Product;
use Isotope\RequestCache\Sort;

/**
 * Class QuickProducts
 *
 * Provide methods to display Isotope products as content elements.
 * @copyright  HB Agency 2014
 * @author     Blair Winans <bwinans@hbagency.com>
 * @author     Adam Fisher <afisher@hbagency.com>
 */
class QuickProducts extends Iso_Element
{

    /**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_iso_quickproducts';

	/**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ISOTOPE ECOMMERCE: QUICK PRODUCT LIST ###';

            $objTemplate->title = $this->headline;
            $objTemplate->id    = $this->id;
            $objTemplate->link  = $this->name;

            return $objTemplate->parse();
        }

        return parent::generate();
    }
	
	protected function compile()
	{
	    $arrProducts = $this->findProducts();
	
		// No products found
        if (!is_array($arrProducts) || empty($arrProducts)) {

            global $objPage;

            // Do not index or cache the page
            $objPage->noSearch = 1;
            $objPage->cache    = 0;

            $this->Template->empty    = true;
            $this->Template->type     = 'empty';
            $this->Template->message  = $GLOBALS['TL_LANG']['MSC']['noProducts'];
            $this->Template->products = array();

            return;
        }

        $arrBuffer = array();

        foreach ($arrProducts as $objProduct) {
            $arrConfig = array(
                'module'        => $this,
                'template'      => ($this->iso_list_layout ?: $objProduct->getRelated('type')->list_template),
                'gallery'       => ($this->iso_gallery ?: $objProduct->getRelated('type')->list_gallery),
                'buttons'       => deserialize($this->iso_buttons, true),
                'useQuantity'   => $this->iso_use_quantity,
                'jumpTo'        => $this->findJumpToPage($objProduct),
            );

            if (\Environment::get('isAjaxRequest') && \Input::post('AJAX_MODULE') == $this->id && \Input::post('AJAX_PRODUCT') == $objProduct->getProductId()) {
                $objResponse = new HtmlResponse($objProduct->generate($arrConfig));
                $objResponse->send();
            }

            $arrCSS = deserialize($objProduct->cssID, true);

            $arrBuffer[] = array(
                'cssID'     => ($arrCSS[0] != '') ? ' id="' . $arrCSS[0] . '"' : '',
                'class'     => trim('product ' . ($objProduct->isNew() ? 'new ' : '') . $arrCSS[1]),
                'html'      => $objProduct->generate($arrConfig),
                'product'   => $objProduct,
            );
        }

        // HOOK: to add any product field or attribute to mod_iso_productlist template
        if (isset($GLOBALS['ISO_HOOKS']['generateProductList']) && is_array($GLOBALS['ISO_HOOKS']['generateProductList'])) {
            foreach ($GLOBALS['ISO_HOOKS']['generateProductList'] as $callback) {
                $objCallback = \System::importStatic($callback[0]);
                $arrBuffer   = $objCallback->$callback[1]($arrBuffer, $arrProducts, $this->Template, $this);
            }
        }

        RowClass::withKey('class')->addCount('product_')->addEvenOdd('product_')->addFirstLast('product_')->addGridRows($this->iso_cols)->addGridCols($this->iso_cols)->applyTo($arrBuffer);

        $this->Template->products = $arrBuffer;
	}
	
	/**
     * Find all products we need to list.
     * @param   array|null
     * @return  array
     */
    protected function findProducts()
    {
        $arrProducts = deserialize($this->iso_products);
        
        $arrSorting = $this->getSorting();

        $objProducts = Product::findAvailableByIds($arrProducts, array('sorting' => $arrSorting));
  
        return (null === $objProducts) ? array() : $objProducts->getModels();
    }
    
    
    /**
     * Get sorting configuration
     * @param boolean
     * @return array
     */
    protected function getSorting()
    {
        $arrSorting = array();

        if ($this->iso_listingSortField != '') {
            $arrSorting[$this->iso_listingSortField] = ($this->iso_listingSortDirection == 'DESC' ? Sort::descending() : Sort::ascending());
        }

        return $arrSorting;
    }
    
    /**
     * Find jumpTo page for current category scope
     * @param   Product
     * @return  PageModel
     */
    protected function findJumpToPage($objProduct)
    {
        global $objPage;
        global $objIsotopeListPage;

        $arrCategories = $objProduct->getCategories();

        // If our current category scope does not match with any product category, use the first product category in the current root page
        if (empty($arrCategories)) {
            $arrCategories = array_intersect($objProduct->getCategories(), \Database::getInstance()->getChildRecords($objPage->rootId, $objPage->getTable()));
        }

        foreach ($arrCategories as $intCategory) {
            $objCategory = \PageModel::findByPk($intCategory);

            if ($objCategory->alias == 'index' && count($arrCategories) > 1) {
                continue;
            }

            return $objCategory;
        }

        return $objIsotopeListPage ? : $objPage;
    }


}
