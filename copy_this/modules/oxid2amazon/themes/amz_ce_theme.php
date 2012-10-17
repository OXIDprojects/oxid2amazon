<?php
/**
 * ConsumerElectronics theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/CE.xsd
 */
class amz_ce_theme extends az_amz_theme
{
	protected $_sRootTag		= 'CE';
	
	protected $_sCategoryTag 	= 'ProductType';
	
	protected $_sCatType 		= 'choice';
	
	protected $_aCategories = array(
		'PC',
		'PDA',
		'ConsumerElectronics',		
	);
	
	protected $_aVariationThemes = null;
	
	protected $_sSubCategoryTag = 'ProductSubtype';
	
	protected $_sSubCatType 		= 'string';
	
	protected $_aSubCategories = array(
		'Antenna',
		'AVFurniture',
		'BarCodeReader',
		'Battery',
		'BlankMedia',
		'CableOrAdapter',
		'CarAudioOrTheater',
		'CECarryingCaseOrBag',
		'CombinedAvDevice',
		'Computer',
		'ComputerDriveOrStorage',
		'ComputerProcessor',
		'ComputerVideoGameController',
		'DigitalVideoRecorder',
		'DVDPlayerOrRecorder',
		'FlashMemory',
		'GPSOrNavigationAccessory',
		'GPSOrNavigationSystem',
		'HandheldOrPDA',
		'HomeTheaterSystemOrHTIB',
		'Keyboards',
		'MemoryReader',
		'Microphone',
		'Monitor',
		'MP3Player',
		'MultifunctionOfficeMachine',
		'NetworkAdapter',
		'NetworkMediaPlayer',
		'NetworkStorage',
		'NetworkTransceiver',
		'NetworkingDevice',
		'NetworkingHub',
		'Phone',
		'PointingDevice',
		'PortableAudio',
		'PortableElectronics',
		'Printer',
		'PrinterConsumable',
		'ReceiverOrAmplifier',
		'RemoteControl',
		'SatelliteOrDSS',
		'Scanner',
		'SoundCard',
		'Speakers',
		'SystemCabinet',
		'SystemPowerDevice',
		'Television',
		'TwoWayRadio',
		'VCR',
		'VideoCard',
		'VideoProjector',
		'Webcam'
	);
}
?>
