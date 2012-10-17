<?php
class az_amz_cron extends oxUBase
{
	const STATUS_OK 	= 1;
	const STATUS_ERROR 	= 0;
	
	protected $_sReportsDir = "modules/oxid2amazon/reports";
	
	/**
	 * Destination Id
	 * 
	 * @var string
	 */
	private $_destinationId = null;
	
	/**
	 * Cronjob Id
	 * 
	 * @var string
	 */
	private $_cronId		= null;
	
	/**
	 * shows if this feed is run in dry run (no changes in database will be done)
	 * 
	 * @var bool
	 */
	private $_dryRun		= false;
	
	/**
	 * Timeout of action in minutes
	 * @var int
	 */
	private $_sTimeOutPeriod = 60;
	
	
	public function init()
	{
		$sDestinationId = oxConfig::getParameter('destinationid');
		
		if (!$sDestinationId)
			die('Destination id must be provided!');	
			
		$this->_destinationId = $sDestinationId;
		
		$dryRun = oxConfig::getParameter('dryrun');		
		if ($dryRun == 'true')
			$this->_dryRun = true;
		
		// checks if last cron call is not yet finished
		if ($this->_checkLastCron())
			die('Cronjob with specified action and destinationId is still running!');
						
	}
	
	/**
	 * 
	 * @param string $type Az_Amz_Feed::TYPE_* type of feed
	 * @return null
	 */
	protected function _runExport($type)
	{		
	    $this->_markStart($type);
	    		
        $oFeed = Az_Amz_Feed::create($type, $this->_destinationId, $this->_dryRun);
             
        $iProductsCount = $oFeed->process();
        if ($iProductsCount > 0) {
        	
        	$this->_setStatus(az_amz_cron::STATUS_OK, $type.' was exported!');
        	$sPathToFile = $oFeed->getTemporaryExportDir().'/'.$oFeed->getFileNameBase();
        	        		
        } else {
        	
        	$this->_setStatus(az_amz_cron::STATUS_ERROR, 'No products were found!');
        	$sPathToFile = null;
        }
        
        
        $this->_markEnd($sPathToFile);                                  
        return;
	}
	
	/**
	 * Builds xml file for product feed
	 * @return null
	 */
	public function exportProducts()
	{
	    $this->_runExport(Az_Amz_Feed::TYPE_PRODUCT);
		return;
	}
	
	public function exportInventory()
	{
	    $this->_runExport(Az_Amz_Feed::TYPE_INVENTORY);
	    return;
	}
	
	/**
	 * Builds xml file for product image feed
	 * @return null
	 */
	public function exportProductImages()
	{
		$this->_runExport(Az_Amz_Feed::TYPE_PRODUCT_IMAGES);
		return;
	}
	
	/**
	 * Builds xml file for product price feed
	 * @return null
	 */
	public function exportProductPrices()
	{		
		$this->_runExport(Az_Amz_Feed::TYPE_PRICE);		
		return;
	}
	/**
	 * Builds xml file for product shipping override feed
	 * @return null
	 */
	public function exportShipping()
	{		
		$this->_runExport(Az_Amz_Feed::TYPE_SHIPPING);		
		return;
	}
	
	/**
	 * 
	 */
	public function exportRemoveAll()
	{
		
	    $this->_runExport(Az_Amz_Feed::TYPE_REMOVE_ALL);
	    // after export we must upload it instantly
	    $this->uploadRemoveAll();
	    return;
	}
	
	/**
	 * Builds xml file for relations feed
	 * @return null
	 */
	public function exportRelations()
	{		
	    $this->_runExport(Az_Amz_Feed::TYPE_RELATION);
		return;
	}
	
	/**
	 * Upload products
	 * @return null
	 */
	public function uploadProducts()
	{					
		$sRemoteFile 	= 'productfeed_'.date('YmdHis').'.xml';
		$this->_uploadFeed(Az_Amz_Feed::TYPE_PRODUCT, $sRemoteFile);
	}
	
	/**
	 * Upload product images
	 * @return null
	 */
	public function uploadProductImages()
	{
		$sRemoteFile 	= 'productimage_feed_'.date('YmdHis').'.xml';
		$this->_uploadFeed(Az_Amz_Feed::TYPE_PRODUCT_IMAGES, $sRemoteFile);
	}
	
	/**
	 * Upload product prices
	 * @return null
	 */
	public function uploadProductPrices()
	{
		$sRemoteFile 	= 'price_feed_'.date('YmdHis').'.xml';
		$this->_uploadFeed(Az_Amz_Feed::TYPE_PRICE, $sRemoteFile);
	}
	
	/**
	 * Upload inventory
	 * 
	 * @return null
	 */
	public function uploadInventory()
	{
	    $sRemoteFile = 'inventory_feed_'.date('YmdHis').'.xml';
	    $this->_uploadFeed(Az_Amz_Feed::TYPE_INVENTORY, $sRemoteFile);
	}
	/**
	 * Upload shipping (override feed)
	 * 
	 * @return null
	 */
	public function uploadShipping()
	{
	    $sRemoteFile = 'override_feed_'.date('YmdHis').'.xml';
	    $this->_uploadFeed(Az_Amz_Feed::TYPE_SHIPPING, $sRemoteFile);
	}
	
	/**
	 * Upload removeAll (remove all feed)
	 * 
	 * @return null
	 */
	public function uploadRemoveAll()
	{
	    $sRemoteFile = 'remove_feed_'.date('YmdHis').'.xml';
	    $this->_uploadFeed(Az_Amz_Feed::TYPE_REMOVE_ALL, $sRemoteFile);
	}
	
	/**
	 * Upload relations (relationship feed)
	 * 
	 * @return null
	 */
	public function uploadRelations()
	{
	    $sRemoteFile = 'relations_feed_'.date('YmdHis').'.xml';
	    $this->_uploadFeed(Az_Amz_Feed::TYPE_RELATION, $sRemoteFile);
	}
	
	/**
	 * Upload feed file to AMTU server
	 * 
	 * @param string $sFeedType Feed type
	 * @param string $sRemoteFile Remote file name
	 * 
	 * @return null
	 */
	protected function _uploadFeed($sFeedType, $sRemoteFile)
	{
		$oDestination = & oxNew('az_amz_destination');
		$oDestination->load($this->_destinationId);
		
		$oFtp = oxNew('az_amz_ftp');
		$blSuccess = $oFtp->connect($oDestination->az_amz_destinations__az_server->value,
									$oDestination->az_amz_destinations__az_ftpuser->value,
									$oDestination->az_amz_destinations__az_ftppassword->value,
									$oDestination->az_amz_destinations__az_ftppassivemode->value
									);
		if ($blSuccess)
		{
			$oDb	= oxDb::getDb(false);			
			$sQ = "SELECT oxid, fileName 
					FROM az_amz_cronjobs
					WHERE destinationId = '".$this->_destinationId."'
					  AND feedType = '".$sFeedType."'
					  AND uploadDate = '0000-00-00 00:00:00'
					  AND endDate != '0000-00-00 00:00:00'
					  LIMIT 1
					";	
			
			$sFileName = null;
							
			if ($aCron = $oDb->getRow($sQ))
			{
				$sFileName = $aCron[1];
				$this->_cronId = $aCron[0]; 
			}
						
			if ($sFileName)
			{											
				$sRemoteDir		= $oDestination->az_amz_destinations__az_ftpdirectory->value;
				$blRet = $oFtp->uploadFile($sFileName, $sRemoteFile, $sRemoteDir);
								
				if ($blRet)
					$sHistoryMsg = "Upload successful. File (".$sFileName.") was uploaded to directory ". $sRemoteDir." on ". $oDestination->az_amz_destinations__az_server->value;			
				else
					$sHistoryMsg = "Upload failed. File (".$sFileName.") can`t be uploaded to directory ". $sRemoteDir." on ". $oDestination->az_amz_destinations__az_server->value;
					
				$oHistory = oxNew('az_amz_history');
				
				$oHistory->addRecord($oDestination, $sFeedType."_upload", $sHistoryMsg);
				
				$this->_setStatus(az_amz_cron::STATUS_OK, $sHistoryMsg);
				
				$this->_markUploaded();
			}else
				$this->_setStatus(az_amz_cron::STATUS_ERROR, 'Error: can`t find feed files to upload!');
		}
	}
	
	
	/**
	 * Sets status of action
	 * 
	 * @param int $iStatus Status id
	 * @param string $sMsg Status message
	 * 
	 * @retun null
	 */
	protected function _setStatus($iStatus, $sMsg)
	{
		$this->_blStatus 	= $iStatus;
		$this->_sStatusMsg 	= $sMsg;
		
		return;		
	}
	
	/**
	 * Show status message
	 * 
	 */
	public function showStatus()
	{
		echo $this->_sStatusMsg;
	}
	
	/**
	 * Mark start of action
	 * @param string $sFeedType Type of feed
	 * 
	 * @return null
	 */
	protected function _markStart($sFeedType)
	{
		if ($this->_dryRun)
			return;
			
		$oDb	= oxDb::getDb(false);
		$sOXID 	= oxUtilsObject::getInstance()->generateUID();
		
		$sQ = "INSERT INTO az_amz_cronjobs(`oxid`,`destinationId`,`feedType`,`startDate`,`action`)
				 VALUES('{$sOXID}',".$oDb->quote( $this->_destinationId).", '{$sFeedType}', NOW(), ".$oDb->quote( $this->_sFnc).")";
		
		if ($oDb->execute($sQ))
			$this->_cronId = $sOXID;
	}
	
	/**
	 * Mark end of action
	 * @param string $sFileName name of feed xml
	 * 
	 * @return null 
	 */
	protected function _markEnd($sFileName)
	{
		if ($this->_dryRun)
			return;
			
		$oDb = oxDb::getDb(false);
		
		$sCronId = $this->_cronId;
		if ($sFileName) {
			$sQ = "UPDATE az_amz_cronjobs SET endDate = NOW(), fileName = '{$sFileName}' WHERE az_amz_cronjobs.oxid = '$sCronId'";
		} else {
			$sQ = "DELETE FROM az_amz_cronjobs WHERE az_amz_cronjobs.oxid = '$sCronId'";
		}
		
		$oDb->execute($sQ);
	}
	
	/**
	 * Updates cronjob with date when feed was uploaded
	 * 
	 * @return null
	 */
	protected function _markUploaded()
	{
		$oDb = oxDb::getDb(false);
		
		$sCronId = $this->_cronId;
		
		$sQ = "UPDATE az_amz_cronjobs SET uploadDate = NOW() WHERE az_amz_cronjobs.oxid = '{$sCronId}'";
		
		$oDb->execute($sQ);
	}
	
	/**
	 * Check last action and set id of last cron
	 * 
	 * @return boolean 
	 */
	protected function _checkLastCron()
	{				
		if ($this->_dryRun)
			return false;
		
		$oDb = oxDb::getDb(false);
		
		$sQ = "SELECT az_amz_cronjobs.oxid
				FROM az_amz_cronjobs
				WHERE az_amz_cronjobs.destinationId = ".$oDb->quote( $this->_destinationId)."
				  AND az_amz_cronjobs.action = ".$oDb->quote( $this->_sFnc)."
				  AND az_amz_cronjobs.endDate = '0000-00-00 00:00:00'
				  AND DATE_ADD(az_amz_cronjobs.startDate, INTERVAL {$this->_sTimeOutPeriod} MINUTE) > NOW()
				  AND az_amz_cronjobs.fileName = ''				
			 ";
		
		$sCronId = $oDb->getOne($sQ);
		
		if($sCronId)
			return true;
		
		return false;
	}
	
	public function render()
	{
		$this->showStatus();
		die();
	}
	
	public function downloadOrderReports()
	{
		$this->_downloadReports('ORDER');
		
	}
	
	protected function _downloadReports($sType)
	{
		$oDestination = & oxNew('az_amz_destination');
		$oDestination->load($this->_destinationId);
		
		$oFtp = oxNew('az_amz_ftp');
		$blSuccess = $oFtp->connect($oDestination->az_amz_destinations__az_server->value,
									$oDestination->az_amz_destinations__az_ftpuser->value,
									$oDestination->az_amz_destinations__az_ftppassword->value,
									$oDestination->az_amz_destinations__az_ftppassivemode->value
									);
		if ($blSuccess)
		{
			//echo "YES";
			$sLocalDir = $this->getConfig()->getConfigParam('sShopDir').$this->_sReportsDir;
			//die($sLocalDir);
			$sRemoteDir		= $oDestination->az_amz_destinations__az_reportsdirectory->value;
			
			$iFiles = $oFtp->ftp_sync($sLocalDir, $sRemoteDir, $sType);
			
			$this->_sStatusMsg = "$iFiles Order Reports successfully downloaded.";
		}
	}
	
	public function importOrders()
	{
		$oAmzOrders = oxNew("az_amz_orders");
		$oAmzOrders->setSourceDir($this->_sReportsDir);
		$oAmzOrders->readFiles();
		$oAmzOrders->importOrders();
		$oAmzOrders->deleteFilesFromAMTU($this->_destinationId);
	}
}
?>
