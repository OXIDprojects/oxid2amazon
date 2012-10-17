<?php
/**
 * Software & video games theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/SWVG.xsd
 */
class amz_swvg_theme extends az_amz_theme
{
	protected $_sRootTag		= 'SoftwareVideoGames';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'Software',
		'HandheldSoftwareDownloads',
		'SoftwareGames',
		'VideoGames',
		'VideoGamesAccessories',
		'VideoGamesHardware',
	);
	
	protected $_aVariationThemes = null;
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
