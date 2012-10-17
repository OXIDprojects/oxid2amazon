<?php
/**
 * Health theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/Health.xsd
 */
class amz_health_theme extends az_amz_theme
{
	protected $_sRootTag		= 'Health';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'HealthMisc',
		'PersonalCareAppliances',
	);
	
	protected $_aVariationThemes = array(
		'Size',
		'Color',
		'Count',
		'Scent',
		'Flavor',
		'Size-Color',
		'Flavor-Count',
		'Flavor-Size',
		'Size-Scent',
	);
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
