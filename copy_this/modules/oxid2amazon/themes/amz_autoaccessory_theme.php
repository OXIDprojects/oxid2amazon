<?php
/**
 * Auto accessory theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/AutoAccessory.xsd
 */
class amz_autoaccessory_theme extends az_amz_theme
{
	protected $_sRootTag		= 'AutoAccessory';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'AutoAccessoryMisc',
		'AutoPart',
		'PowersportsPart',
		'PowersportsVehicle',
		'ProtectiveGear',
		'Helmet',
		'RidingApparel',
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
