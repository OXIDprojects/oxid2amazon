<?php
/**
 * Gourmet theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/Gourmet.xsd
 */
class amz_gourmet_theme extends az_amz_theme
{
	protected $_sRootTag		= 'Gourmet';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'GourmetMisc'
	);
	
	protected $_aVariationThemes = array(
		'Size',
		'Color',
		'Flavor',
		'Flavor-Size', 
	);
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
