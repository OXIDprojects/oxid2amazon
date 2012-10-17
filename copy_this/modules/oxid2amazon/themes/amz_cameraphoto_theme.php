<?php
/**
 * CameraPhoto theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/CameraPhoto.xsd
 */
class amz_cameraphoto_theme extends az_amz_theme
{
	protected $_sRootTag		= 'CameraPhoto';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	/**
	 * Describes either category is string or choice
	 */
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'FilmCamera',
		'Camcorder',
		'DigitalCamera',
		'Binocular',
		'SurveillanceSystem',
		'Telescope',
		'Microscope',
		'Darkroom',
		'Lens',
		'LensAccessory',
		'Filter',
		'Film',
		'BagCase',
		'BlankMedia',
		'PhotoPaper',
		'Cleaner',
		'Flash',
		'TripodStand',
		'Lighting',
		'Projection',
		'PhotoStudio',
		'LightMeter',
		'PowerSupply',
		'OtherAccessory',						
	);
	
	protected $_aVariationThemes = null;
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
