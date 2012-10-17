<?php
/**
 * Food and beverages theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/FoodAndBeverages.xsd
 */
class amz_foodandbeverages_theme extends az_amz_theme
{
	protected $_sRootTag		= 'FoodAndBeverages';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'Food',
		'Beverages',
		'AlcoholicBeverages'
	);
	
	protected $_aVariationThemes = array(
		'Size',
		'Flavor',
		'Flavor-Size',
		'PatternName', 
	);
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
