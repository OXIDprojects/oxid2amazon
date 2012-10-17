<?php
/**
 * Home theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/Home.xsd
 */
class amz_home_theme extends az_amz_theme
{
	protected $_sRootTag		= 'Home';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'BedAndBath', 
		'FurnitureAndDecor', 
		'Kitchen', 
		'OutdoorLiving', 
		'SeedsAndPlants',	
	);
	
	protected $_aVariationThemes = array(
		'Size',
		'Color',
		'Scent',
		'Size-Color',
		'Size-Scent',
		'DisplayLength-DisplayWidth',
		'DisplayLength-Material',
		'DisplayLength-Size',
		'DisplayLength-Color',
		'DisplayLength-DisplayHeight',
		'DisplayWidth-Material',
		'DisplayWidth-Size',
		'DisplayWidth-Color',
		'DisplayWidth-DisplayHeight',
		'ItemPackageQuantity-Material',
		'ItemPackageQuantity-Size',
		'ItemPackageQuantity-Color',
		'ItemPackageQuantity-DisplayHeight',
		'DisplayWeight-ItemPackageQuantity',
		'DisplayWeight-Material',
		'DisplayWeight-Size',
		'DisplayWeight-Color',
		'DisplayWeight-DisplayHeight',
		'Material-DisplayLength',
		'Material-DisplayWidth',
		'Material-Size',
		'Material-Color',
		'Material-DisplayHeight',
		'Size-DisplayLength',
		'Size-DisplayWidth',
		'Size-DisplayWeight',
		'Size-Material',
		'Size-Color',
		'Size-DisplayHeight',
		'Color-DisplayLength',
		'Color-DisplayWidth',
		'Color-ItemPackageQuantity',
		'Color-DisplayWeight',
		'Color-Material',
		'Color-Size',
		'Color-DisplayHeight',
		'DisplayHeight',
		'Material',
		'DisplayWeight',
		'DisplayLength',
		'ItemPackageQuantity',
		'DisplayLength-PatternName',
		'DisplayLength-StyleName',
		'DisplayWidth-PatternName',
		'DisplayWidth-StyleName',
		'Occasion-PatternName',
		'Occasion-ItemPackageQuantity',
		'Occasion-Material',
		'Occasion-StyleName',
		'Occasion-Size',
		'Occasion-Color',
		'Occasion-DisplayHeight',
		'PatternName-DisplayLength',
		'PatternName-DisplayWidth',
		'PatternName-Occasion',
		'PatternName-Material',
		'PatternName-StyleName',
		'PatternName-Size',
		'PatternName-Color',
		'PatternName-DisplayHeight',
		'MatteStyle-Material',
		'MatteStyle-StyleName',
		'MatteStyle-Size',
		'MatteStyle-Color',
		'ItemPackageQuantity-Occasion',
		'ItemPackageQuantity-StyleName',
		'DisplayWeight-StyleName',
		'Material-PatternName',
		'Material-MatteStyle',
		'Material-StyleName',
		'StyleName-DisplayLength',
		'StyleName-DisplayWidth',
		'StyleName-Occasion',
		'StyleName-PatternName',
		'StyleName-DisplayWeight',
		'StyleName-Material',
		'StyleName-Size',
		'StyleName-Color',
		'Size-Occasion',
		'Size-PatternName',
		'Size-MatteStyle',
		'Size-StyleName',
		'Color-Occasion',
		'Color-PatternName',
		'Color-MatteStyle',
		'Color-StyleName',
		'MatteStyle',
		'PatternName',
		'Occasion',
		'StyleName',
		'DisplayWeight-DisplayLength-Color',
		'Occasion-Size-Color',
		'DisplayWeight-DisplayLength-Material',
		'DisplayWeight-DisplayLength-StyleName',
		'PatternName-Size-Occasion',
	);
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
	
	/**
 	 * Returns body of theme
 	 * @param oxarticle $oProduct Product object
 	 * 
 	 * @return string
 	 */
	protected function _getXmlBody($oProduct)
 	{
 		$sXML = parent::_getXmlBody($oProduct);
 		
 		$sXML .= "<Parentage>base-product</Parentage>";
 		
 		return $sXML;
 	}
	
}
?>