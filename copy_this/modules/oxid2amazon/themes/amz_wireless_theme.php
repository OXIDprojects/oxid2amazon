<?php
/**
 * Wireless theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/Wireless.xsd
 */
class amz_wireless_theme extends az_amz_theme
{
	protected $_sRootTag		= 'Wireless';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'WirelessAccessories',
		'WirelessDownloads'
	);
	
	protected $_aVariationThemes = null;
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
