<?php
/**
 * Toys baby theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/ToysBaby.xsd
 */
class amz_toysbaby_theme extends az_amz_theme
{
	protected $_sRootTag		= 'ToysBaby';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'string';
	
	protected $_aCategories = array(
		'ToysAndGames', 
		'BabyProducts'
	);
	
	protected $_aVariationThemes = array(
		'Size',
		'Color',
		'Size-Color', 
	);
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
