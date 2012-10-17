<?php
 class az_amz_history extends oxBase 
 { 	 	
 	const ACTION_EXPORT_PRODUCTS 	= 'export_products';
 	const ACTION_EXPORT_PRICES 		= 'export_prices';
 	const ACTION_EXPORT_IMAGES 		= 'export_images';
 	const ACTION_UPLOAD_PRODUCTS	= 'upload_products';
 	const ACTION_UPLOAD_PRICES		= 'upload_prices';
 	const ACTION_UPLOAD_IMAGES		= 'upload_images';
 	
 	
 	/**
     * Core database table name. $sCoreTbl could be only original data table name and not view name.
     * @var string
     */
    protected $_sCoreTbl   = 'az_amz_history';

    /**
     * Name of current class
     * @var string
     */
    protected $_sClassName = 'az_amz_history';
    
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
        
        $this->init( 'az_amz_history' );
    }
        
    /**
     * Adds record to history table
     * @param az_amz_destination $oDestination Object of destination
     * @param string $sAction Action name
     * @param string $sStatusMsg Status message
     * 
     * @return bool
     */
    public function addRecord($oDestination, $sAction, $sStatusMsg = null, $sUserId = null)
    {
    	if (!$sUserId)
    		$sUserId = $this->getUser()->oxuser__oxid->value;
    		
    	$sShopId = $oDestination->az_amz_destinations__oxshopid->value;
    	if (!$sShopId)
    		$sShopId = oxConfig::getInstance()->getShopId();
    
    	$this->az_amz_history__oxshopid 		= new oxField($sShopId);
    	$this->az_amz_history__az_destinationid = new oxField($oDestination->az_amz_destinations__oxid->value);
    	$this->az_amz_history__az_action 		= new oxField($sAction);
    	$this->az_amz_history__az_timestamp		= new oxField(date('Y-m-d H:i:s'));
    	$this->az_amz_history__oxuserid			= new oxField($sUserId);
    	
    	if ($sStatusMsg)
    		$this->az_amz_history__az_statusmsg		= new oxField($sStatusMsg);
    	
    	$this->save();
    	
    }   
    
    /**
     * Loads destination history
     * 
     * @param string $sDestinationId Destination Id
     * @param int $iRecords Number of history records
     * 
     * @return oxlist
     */  
    public function getDestinationHistory($sDestinationId, $iRecords = 100, $iStart = 0)
 	{
 		$aHistoryList = null;
 		
 		$sQ = "SELECT
 					az_amz_history.oxid,
 					az_amz_history.az_timestamp,
 					az_amz_history.az_action,
 					az_amz_history.az_statusmsg,
 					oxuser.oxusername
 				FROM az_amz_history
 					LEFT JOIN oxuser ON az_amz_history.oxuserid = oxuser.oxid
 				WHERE az_destinationid = '{$sDestinationId}'
 				ORDER BY az_amz_history.az_timestamp DESC 				
 				";
 				
 		if ($iRecords > 0) {
 			$sQ .= " LIMIT ". $iStart. ", ".$iRecords;
 		}
 			
 		$oDb = oxDb::getDb();
 		
 		$rs = $oDb->execute($sQ);
 		
 		if ($rs != false && $rs->recordCount() > 0) 
 		{
            while (!$rs->EOF) 
            {
            	$aHistoryList[$rs->fields[0]]->az_amz_history__oxid->value 			= $rs->fields[0];
            	$aHistoryList[$rs->fields[0]]->az_amz_history__az_timestamp->value 	= $rs->fields[1];
            	$aHistoryList[$rs->fields[0]]->az_amz_history__az_action->value 	= $rs->fields[2];
            	$aHistoryList[$rs->fields[0]]->az_amz_history__az_statusmsg->value 	= $rs->fields[3];            	
            	$aHistoryList[$rs->fields[0]]->oxuser__oxusername->value 		= $rs->fields[4];
            	
            	$rs->moveNext();
            }
 		}
 		
 		return $aHistoryList;
 	}
 	
 	/**
     * destination history records count
     * 
     * @param string $sDestinationId Destination Id
     * @return oxlist
     */  
    public function getDestinationHistoryCount($sDestinationId)
 	{
 		$sQ = "SELECT COUNT(*) 					
 				FROM az_amz_history
 					LEFT JOIN oxuser ON az_amz_history.oxuserid = oxuser.oxid
 				WHERE az_destinationid = '{$sDestinationId}'
 				ORDER BY az_amz_history.az_timestamp DESC 				
 				";
 				 	
 			
 		$oDb = oxDb::getDb();
 		
 		$iCount = (int)$oDb->getOne($sQ);
 		
 		return $iCount;
 	}
 }
?>
