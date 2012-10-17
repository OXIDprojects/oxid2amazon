<?php

class az_amz_Config
{
	protected $_aEntries = null;
	protected $_aDirtyKeys = array();
	protected $_aNewKeys = array();
	protected $_aLoadedKeys = array();
	protected $_sShopId = null;
	public    $sPathToModule = "modules/oxid2amazon";
	
	protected static $_aAmzThemes = array(	
		"Clothing",
	  	"Miscellaneous",
	  	"CameraPhoto",
	  	"Home",
	  	"Sports",
	  	"Tools",
	  	"FoodAndBeverages",
	  	"Gourmet",
	  	"Jewelry",
	  	"Health",
		"CE",
		"SoftwareVideoGames",
		"Wireless",
		"Beauty",
		"Office",
		"MusicalInstruments",
		"AutoAccessory",
		"PetSupplies",
		"ToysBaby",
		"TiresAndWheels",
	);
	
			
	protected static $_aDefaults = array(
		'sEanField' => 'oxean',
	);
	
	public function __construct($sShopId = null)
	{
		if(!isset($sShopId)) {
			$this->_sShopId = oxConfig::getInstance()->getShopId();
		}
		else {
			$this->_sShopId = $sShopId;
		}
		$this->_aEntries = array();
		// load defaults
		foreach(self::$_aDefaults as $key => $val) {
			$this->_aEntries[$key] = $val; // no need to set dirty flags
		}
		$this->loadFromDb();
		
		// config file
		include(getShopBasePath().$this->sPathToModule."/az_amazon_config.inc.php");		
	}
	
	public function loadFromDb()
	{
		$db = oxDb::getDb(false);
		
		$rsResult =  $db->Execute(
			"SELECT az_varname, az_varvalue FROM az_amz_config WHERE oxshopid=?", 
			array($this->_sShopId)
		);
		
		if(false !== $rsResult) {
			while(!$rsResult->EOF) {
				$this->_aEntries[$rsResult->fields[0]] = unserialize($rsResult->fields[1]);
				$this->_aLoadedKeys[$rsResult->fields[0]] = true;
				$rsResult->MoveNext();
			}
			$rsResult->close();
		}
		
	}
	
	public function __get($key)
	{		
		if(isset($this->_aEntries[$key])) {		
			return $this->_aEntries[$key];
		} 
	}
	
	public function __set($key, $val) 
	{
		if (isset($this->_aEntries[$key])) {
			if ($this->_aEntries[$key] === $val) {
				return;
			} elseif(isset($this->_aLoadedKeys[$key])) {
				$this->_aDirtyKeys[$key] = true;
			} else {
			    $this->_aNewKeys[$key] = true;
			}
		} else {
			$this->_aNewKeys[$key] = true;
		}
		
		$this->_aEntries[$key] = $val; 
	}
	
	public function __isset($key)
	{
		return isset($this->_aEntries[$key]);
	}
	
	public function assignArray($arr)
	{
		foreach($arr as $key => $val) {
			$this->$key = $val;
		}
	}
	
	public function getAmazonThemes()
	{
		return self::$_aAmzThemes;
	}		
	
	public function saveToDatabase()
	{
		$db = oxDb::getDb();
		$sUpdateQ = "UPDATE az_amz_config 
			   SET az_varname=?, az_varvalue=? 
			   WHERE oxshopid='{$this->_sShopId}' AND az_varname=?";
		// update all changed keys
		foreach(array_keys($this->_aDirtyKeys) as $key) 
		{
			$db->Execute($sUpdateQ, array($key, serialize($this->_aEntries[$key]), $key));
		}
		
		$sExistsQ = "SELECT oxid FROM az_amz_config WHERE oxshopid='{$this->_sShopId}' AND az_varname=?";
		$sInsertQ = "INSERT INTO az_amz_config SET oxid=?, oxshopid='{$this->_sShopId}', az_varname=?, az_varvalue=?";
		$utils = oxUtilsObject::getInstance();
		// add all new new keys
		foreach(array_keys($this->_aNewKeys) as $key) {
			// check if exists already
			if(false === $db->GetOne($sExistsQ, array($key))) {// doesn't exist insert
				$db->Execute($sInsertQ, array($utils->generateUID(), $key, serialize($this->_aEntries[$key])));
			}
			else {
				$db->Execute($sUpdateQ, array($key, serialize($this->_aEntries[$key]), $key));
			}
		}
	}
	
	public function isRequiredOperatorValue($sOperator) {
		
		if (!(isset($this->aOperators) && is_array($this->aOperators)))
			return false;
		
		foreach($this->aOperators as $sKey => $oper) {
			
			if ($oper['operator'] == $sOperator) {
				if (isset($oper['value_required']) && $oper['value_required']) {
					return true;
				}	
			}			
		}
		
		return false;
	}
	
	public function logError($sMsg)
	{
		error_log( strftime('[%Y-%m-%d %H:%M:%S] ') . "$sMsg\n\n", 3, getShopBasePath() . 'log/oxid2amazon.log' );
	}
	
}
