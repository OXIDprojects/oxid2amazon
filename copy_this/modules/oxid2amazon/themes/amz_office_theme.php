<?php
/**
 * Office theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/Office.xsd
 */
class amz_office_theme extends az_amz_theme
{
	protected $_sRootTag		= 'Office';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'ArtSupplies',
		'EducationalSupplies',
		'OfficeProducts',
		'PaperProducts',
		'WritingInstruments',
	);
	
	protected $_aVariationThemes = array(
		'Color',
		'PaperSize',
		'Color-PaperSize',
		'MaximumExpandableSize',
		'LineSize', 
	);
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
