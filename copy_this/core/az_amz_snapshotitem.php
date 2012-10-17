<?php
 class az_amz_snapshotitem extends oxBase 
 {
 	/**
 	 * Item status:
 	 * 0 - not available (deleted from oxarticles)
 	 * 1 - available in oxarticles
 	 * 2 - available and changed
 	 *
 	 * @var int $_status 
 	 */
 	private $_status = null;
 	
 	protected $_aHashProductFields = array(																	   								   		
		'oxean',
		'oxdistean',
		'oxtitle',
		'oxshortdesc',
		'oxlength',
		'oxwidth',
		'oxheight',
		'oxvarselect',
	);
	
	protected $_aHashPriceFields = array(
		'oxprice',
		'oxblfixedprice',
		'oxpricea',
		'oxpriceb',
		'oxpricec',
		'oxbprice',
		'oxtprice',
	);
									
									
									
	protected $_aHashPictureFields = null;
	
	protected $_aHashInventoryFields = array(
		'oxactive', 
		'oxstock'
	);
 	
	protected $_aHashShippingFields = array(
		'az_amz_ship_option',
		'az_amz_ship_type',
		'az_amz_ship_amount'
	);
 	
 	/**
     * Core database table name. $sCoreTbl could be only original data table name and not view name.
     * @var string
     */
    protected $_sCoreTbl   = 'az_amz_snapshots';

    /**
     * Name of current class
     * @var string
     */
    protected $_sClassName = 'az_amz_snapshotitem';
    
    /**
	 * Config object
	 * @var az_amz_Config
	 */
	protected $_oAZConfig = null;

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->init( 'az_amz_snapshots' );
    }
    
    /**
     * Loads snapshot item by product id and destination id
     * @param $sProductId string Product Id
     * @param $sDestinationId string Destination Id
     * 
     * @return boolean
     */
    public function loadByProductId($sProductId, $sDestinationId) 
    {
    	$this->_addField('oxid', 0);
    	$sViewName = $this->getViewName();
    	
    	$aConditions = array($sViewName.".az_productid" => $sProductId,
    						 $sViewName.".az_destinationid" => $sDestinationId,
    						);
    	// TODO: check for EE 2.7
    	$sSelect = $this->buildSelectString($aConditions);

        return $this->_isLoaded = $this->assignRecord( $sSelect );
    }
    
    /**
     * Calculates product hash
     * 
     * @return string $sHash
     */
    public function calculateProductHash($oProduct = null)
    {
    	$sText = '';
    	$sHash = '';
    	
    	if (!$oProduct)
    		return $sHash;
    	
    	foreach ($this->_aHashProductFields as $sField)    	
    	{
    		$sFullFieldName = 'oxarticles__'.$sField;
    		if (isset($oProduct->$sFullFieldName->value))
    			$sText .= $oProduct->$sFullFieldName->value;
    	}
    	
    	if ($sText != '')    		    		
    		$sHash = md5($sText);
    	
    	return $sHash;
    }
    
    /**
     * Calculates price hash
     * 
     * @return string $sHash
     */
    public function calculatePriceHash($oProduct = null)
    {
    	$sText = '';
    	$sHash = '';
    	
    	if (!$oProduct)
    		return $sHash;
    	
    	foreach ($this->_aHashPriceFields as $sField)    	
    	{
    		$sFullFieldName = 'oxarticles__'.$sField;
    		if (isset($oProduct->$sFullFieldName->value))
    			$sText .= $oProduct->$sFullFieldName->value;
    	}
    	
    	if ($sText != '')    		    		
    		$sHash = md5($sText);
    	
    	return $sHash;
    }
    
     /**
     * Calculates pictures hash
     * 
     * @return string $sHash
     */
    public function calculatePictureHash($oProduct = null)
    {
    	$sText = '';
    	$sHash = '';
    	
    	if (!$oProduct)
    		return $sHash;
    	
    	foreach ($this->_aHashProductFields as $sField)    	
    	{
    		$sFullFieldName = 'oxarticles__'.$sField;
    		if (isset($oProduct->$sFullFieldName->value))
    			$sText .= $oProduct->$sFullFieldName->value;
    	}
    	
    	if ($sText != '')    		    		
    		$sHash = md5($sText);
    	
    	return $sHash;
    }
    
    /**
     * Assigns product field values to snapshot item values
     * 
     * @return boolean
     */
    public function assignValues($sProductId, $sSkuValue, $sDestinationId = null)
 	{
 		//##TODO: needs refactoring: config object loaded twice in this class, should be in constructor e. g.
 		if (!$this->_oAZConfig)
		{
			if (!$sShopId)
				$sShopId = oxConfig::getInstance()->getShopId();
				 		
        	$this->_oAZConfig = oxNew('az_amz_config', $sShopId);
		}
 		
 		$this->az_amz_snapshots__az_productid 		= new oxField($sProductId);
 		$this->az_amz_snapshots__az_skufield		= new oxField($sSkuValue);
// 		$this->az_amz_snapshots__az_hash			= new oxField($this->calculateProductHash($oProduct));
// 		$this->az_amz_snapshots__az_pricehash		= new oxField($this->calculatePriceHash($oProduct));
 		
 		if ($sDestinationId)
 			$this->az_amz_snapshots__az_destinationid = new oxField($sDestinationId); 			 	
 	}
    
    
    
    /**
     * Checks hash if product item is changed according snapshot item
     */
    public function doHashCheck()
    {
    	// some sql to select fields from oxarticles, 
    	// calculate hash and compare it to snapshot item hash. 
    }
    
    
    /**
     * Returns product hash calculation fields
     * 
     * @return array $aHashFields;
     */
    public function getProductHashFields()
    {
    	return $this->_aHashProductFields;	
    }
    
    /**
     * Returns price hash calculation fields
     * 
     * @return array $aPriceHashFields
     */
    public function getPriceHashFields()
    {
    	return $this->_aHashPriceFields;
    }
    
    /**
     * Returns picture hash calculation fields
     * @param string $sShopId
     * 
     * @return array $aPictureHashFields
     */
    public function getPictureHashFields($sShopId = null)
    {	
    	if (!$this->_aHashPictureFields)
    	{   
    		
    		if (!$this->_oAZConfig)
    		{
    			if (!$sShopId)
    				$sShopId = oxConfig::getInstance()->getShopId();
    				 		
            	$this->_oAZConfig = oxNew('az_amz_config', $sShopId);
    		}
            	
           	$aHashPictureFields = array();
           	
            for ($i = 1 ; $i < 9 ; $i++)
            {
            	$sImageFieldName = 'sPicField'.$i;            	
            	
            	if (isset($this->_oAZConfig->$sImageFieldName) && $this->_oAZConfig->$sImageFieldName != ''){            		          	
            		$aHashPictureFields[] = $this->_oAZConfig->$sImageFieldName;
            	} 
            }
            
            // fix: when no image fields are selected in settings - we use default oxpic1
            if (sizeof($aHashPictureFields) == 0) {            
	            $this->_oAZConfig->sPicField1 = 'oxpic1';
	            $this->_oAZConfig->saveToDatabase();
	            $aHashPictureFields[] = 'oxpic1';
            }
                        
    		$this->_aHashPictureFields = $aHashPictureFields; 
    	}
    	
    	return $this->_aHashPictureFields;    	
    }
    
    /**
     * @return array 
     */
    public function getInventoryHashFields()
    {
        return $this->_aHashInventoryFields;
    }
    
    public function getShippingHashFields()
    {
        return $this->_aHashShippingFields;
    }
 }
?>
