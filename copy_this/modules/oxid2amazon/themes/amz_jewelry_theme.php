<?php
/**
 * Jewelry theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/Jewelry.xsd
 */
class amz_jewelry_theme extends az_amz_theme
{
	protected $_sRootTag		= 'Jewelry';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'Watch',
		'FashionNecklaceBraceletAnklet',
		'FashionRing',
		'FashionEarring',
		'FashionOther',
		'FineNecklaceBraceletAnklet',
		'FineRing', 
		'FineEarring',	 
		'FineOther'
	);
	
	protected $_aVariationThemes = array(
		'BandColor'
	);
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
