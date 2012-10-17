<?php
/**
 * Tires and wheels theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/TiresAndWheels.xsd
 */
class amz_tiresandwheels_theme extends az_amz_theme
{
	protected $_sRootTag		= 'TiresAndWheels';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'Tires',
		'Wheels',		
	);
	
	protected $_aVariationThemes = null;
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
