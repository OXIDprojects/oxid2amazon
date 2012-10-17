<?php
/**
 * Musical Instruments
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/MusicalInstruments.xsd
 */
class amz_musicalinstruments_theme extends az_amz_theme
{
	protected $_sRootTag		= 'MusicalInstruments';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'BrassAndWoodwindInstruments',
		'Guitars',
		'InstrumentPartsAndAccessories',
		'KeyboardInstruments',
		'MiscWorldInstruments',
		'PercussionInstruments',
		'SoundAndRecordingEquipment',
		'StringedInstruments'
	);
	
	protected $_aVariationThemes = array(
		'Color'
	);
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
}
?>
