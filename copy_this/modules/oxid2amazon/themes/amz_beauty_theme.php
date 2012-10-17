<?php
/**
 * Beauty theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/Beauty.xsd
 */
class amz_beauty_theme extends az_amz_theme
{
	protected $_sRootTag		= 'Beauty';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'BeautyMisc'
	);
	
	protected $_aVariationThemes = array(
		'Size',
		'Color',
		'Size-Color',
		'Scent',
		'Size-Scent',
		'PatternName',

	);
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
