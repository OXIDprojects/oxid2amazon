<?php
/**
 * Abstract class for feed. Actual feeds (inventory, price, product, productimages, shipping) should
 * inherit from this class
 * Example of usage:
 * <code>
 * $feed = Az_Amz_Feed::create(Az_Amz_Feed::TYPE_PRODUCT);
 * $feed->setDestinationId('abcsdefiojwo');
 * $feed->setDryRun(true); // optional in this case calling process twice would produce files with same content
 * $feed->process();
 * </code>
 * 
 *
 */
abstract class Az_Amz_Feed
{
    const TYPE_INVENTORY 		= 'inventoryfeed';
    const TYPE_PRICE	 		= 'pricefeed';
    const TYPE_PRODUCT 			= 'productfeed';
    const TYPE_PRODUCT_IMAGES 	= 'productimagesfeed';
    const TYPE_SHIPPING 		= 'shippingfeed';
    const TYPE_REMOVE_ALL 		= 'removeallfeed';
    const TYPE_RELATION			= 'relationshipfeed';
	/**
	 * new line character for xml files
	 * @var string
	 */
	public $nl = "\n";
	/**
	 * Destination id
	 * @var string
	 */
	protected $_destinationId;
	
	/**
	 * Xml MessageType
	 * @var unknown_type
	 */
	protected $_messageType = '';
	
	/**
	 * 
	 * @var Az_Amz_Destination 
	 */
	protected $_destination;
	
	/**
	 * xml file name
	 * @var string
	 */
	protected $_fileName = null;
	
	/**
	 * File handle of the export
	 *  
	 * @var resource
	 */
	protected $_fileHandle;
	
	/**
	 * Directory where the export file is temporary kept before upload to AMTU server
	 * @var string
	 */
	protected $_temporaryDir;
	
	/**
	 * Feed name
	 * 
	 * @var string
	 */	
	protected $_sFeedName = 'Feed';
	
	/**
	 * Message counter of the export
	 * @var int
	 */
	
	protected $_messageId  = 0;
	/**
	 * shows if this feed is run in dry run (no changes in database will be done)
	 * 
	 * @var bool
	 */
	protected $_dryRun = false;
	/**
	 * Config object
	 * @var az_amz_Config
	 */
	protected $_az_amz_config = null;
	
	/**
	 * date for filename
	 * @var $_sFileDate
	 */
	protected $_sFileDate = null;
	
	/**
	 * File name base
	 * @var $_sFileNameBase
	 */
	protected $_sFileNameBase = 'unknown';
	
	protected $_sSkuField = null;
	protected $_sSkuProperty = null;
		
	/**
	 * 
	 * @param string $sFeedType
	 * @param string $sDestinationId 
	 * @return Az_Amz_Feed
	 */
	public static function create($sFeedType, $sDestinationId, $blDryRun = false)
	{
	    $sFeedType = strtr($sFeedType, array('/' => '', '\\' => ''));
	    // TODO: path to module has to be dynamic, put in config
	    require_once getShopBasePath()."/modules/oxid2amazon/feeds/az_amz_". $sFeedType . '.php';
	    $oFeed = oxNew('az_amz_'. $sFeedType);
	    $oFeed->setDestinationId($sDestinationId);
	    $oFeed->setDryRun($blDryRun);
	    return $oFeed;
	}
	/**
	 * Set directory, where the compiled xml will be stored until it will
	 * be uploaded to AMTU server 
	 * @param $sDir directory
	 * @return null
	 */
	public function setTemporaryExportDir($sDir)
	{
		$this->_temporaryDir = $sDir;
	}
	
	/**
	 * Call this function before calling "self::process" function
	 * if you want to generate a demo export 
	 * 
	 * @param boolean $dryRun (optional, default=true) set dry run
	 */
	public function setDryRun($dryRun = true)
	{
	    $this->_dryRun = $dryRun;
	}
	
	public function getDryRun()
	{
	    return $this->_dryRun;
	}
	
	/**
	 * returns directory where export file is kept 
	 * before it is uploaded to amtu server
	 * If this directory is not set implicitly by setTemporaryExportDir it defaults to 
	 * /export dir in shop root
	 * 
	 * @return string
	 */
	public function getTemporaryExportDir()
	{
		if(!isset($this->_temporaryDir)) {
			$sExportDir = oxConfig::getInstance()->getConfigParam('sShopDir');
			$sExportDir = rtrim($sExportDir, '/\\').'/'. 'export';
			$this->setTemporaryExportDir($sExportDir);
		}
		return $this->_temporaryDir;
	}
	
	/**
	 * Set destination id
	 * @param $id string
	 * @return null
	 */
	public function setDestinationId($id)
	{
		$this->_destinationId = $id;
		if(isset($this->_destination) && $this->_destination->getId() != $id) {
		    unset($this->_destination);
		}
	}
	
	/**
	 * 
	 * @return string
	 * @throws Exception if destination id is not set
	 */
	public function getDestinationId()
	{
		if(!isset($this->_destinationId)) {
			throw new Exception('destination id not set in az_amz_feed');
		}
		return $this->_destinationId;
	}
	
	/**
	 * Load destination object
	 * @return az_amz_Destination
	 */
    public function getDestination()
    {
        if(!isset($this->_destination)) {
        	$this->_destination = oxNew('az_amz_destination');
        	$this->_destination->load($this->getDestinationId());
        }
        return $this->_destination;
    }
    
    /**
     * Returns config object
     * @return unknown_type
     */
    protected function _getAmzConfig()
    {
        if(!isset($this->_az_amz_config)) {
        	$dest = $this->getDestination();
            $this->_az_amz_config = oxNew('az_amz_config', $dest->az_amz_destinations__oxshopid->value);
            
        }
        return $this->_az_amz_config;
    }
    
    public function getSkuField()
    {
    	$this->_sSkuField = $this->_getAmzConfig()->sSkuField;
    	if(!$this->_sSkuField)
    		$this->_sSkuField = "oxartnum";
    	return $this->_sSkuField;
    }
    
    public function getSkuProperty()
    {
    	$this->_sSkuProperty = "oxarticles__".$this->getSkuField();
    	return $this->_sSkuProperty;
    }
    
	
	public function setFileName($fileName)
	{
	    if(is_resource($this->_fileHandle)) {
	        throw new Exception('This feed already has an open file');
	    }
		$this->_fileName = $fileName;
	}
	
	/**
	 * 
	 * @param int $iFileNumber file number of this feed
	 * @return unknown_type
	 */
	public function getFileName($iFileNumber = 0)
	{
		if(!isset($this->_fileName)) {
			// generate file name			
			
			$this->setFileName($this->generateFileName($iFileNumber));
		}
		
		return $this->_fileName;
	}
	
	/**
	 * Generate file name for current feed, is called when
	 * File name is not set implicitly
	 * 
	 * @return string generated file name
	 */
	public function generateFileName($fileIndex = 0)
	{	
    	$fileIndex = ($fileIndex > 0 ? '_'.$fileIndex : '');
    		
        return $this->getFileNameBase() . $fileIndex. '.xml';    
	}
	
	public function process()
	{				
	    $dryRun = $this->getDryRun();
		
        $aIds = $this->getChangedProductIds();
        
        $aArtSKUs = $this->getDeletedProductArtNums();
        $iTotalProducts = sizeof($aIds)  + sizeof($aArtSKUs);
        if ($iTotalProducts >0) {
	        $sizeLimit = 10485760 - 100; // 100 byte buffer for closing envelope should be enough and 
	        $fileIndex = 0;
	        $bytesWritten = $this->startFile(false, $fileIndex++);
	        
			foreach($aIds as $id) {
			    $sUpdateXml = $this->getUpdateXml($id);
			    if(strlen($sUpdateXml) + $bytesWritten > $sizeLimit) {
			        // close old file and open a new one, reset counter
			        $this->endFile();
			        $bytesWritten = $this->startFile(false, $fileIndex++);
			        
			    }
				$bytesWritten += $this->write($sUpdateXml);
			}
			
	
			
			if(!$dryRun) {
	            $this->updateItems($aIds);
		         // History message      
	            $sHistoryMsg = "Generated ".$this->getFeedName()." (".$this->getFileName()."). ".sizeof($aIds). " product(-s) will be updated/added ";
			}
			
			unset($aIds);				
			
			foreach($aArtSKUs as $id) {
			    $sDeleteXml = $this->getDeleteXml($id);
			    if(strlen($sDeleteXml) + $bytesWritten > $sizeLimit) {
			        $this->endFile();
			        $bytesWritten = $this->startFile(false, $fileIndex++);
			    } 
				$bytesWritten += $this->write($sDeleteXml);
			}
			$this->endFile();
			
			if (!$dryRun) {
			    $this->deleteItemsByArtNum($aArtSKUs);
			    
			    if ($this->getFeedAction() == az_amz_history::ACTION_EXPORT_PRODUCTS) {
	                $sHistoryMsg .= "and ".sizeof($aIds)." will be removed. ";
			    }	            
			    
			}
        } else {
        	$sHistoryMsg = $this->getFeedName(). " file was not generated, because no products were found!";
        }
        
        $oHistory = oxNew('az_amz_history');            
	    $oHistory->addRecord($this->getDestination(), $this->getFeedAction(), $sHistoryMsg);
		
		return $iTotalProducts;
	}
	
	/**
	 * 
	 * @param boolean $overwrite if the file with same name exists write on top
	 * @param int $fileNumber file number of this feed (when the feed is split because of size restrictions)
	 * @return int|boolean bytes written to file
	 * @throws Exception with error code -2999
	 */
	public function startFile($overwrite = false, $fileNumber = 0)
	{
		$oDestination = $this->getDestination();
		$fileName = $this->getTemporaryExportDir().DIRECTORY_SEPARATOR.$this->getFileName($fileNumber);
		if(!$overwrite && file_exists($fileName)) {
		    throw new Exception('file with this name already exists', -2999);
		}
		$this->_fileHandle = fopen($fileName, 'wb');
		//TODO find out about encoding, in case of UTF8 write BOM if needed
		
	    $size = $this->write('<?xml version="1.0" encoding="UTF-8"?>'.$this->nl);
	    $size += $this->write('<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">'.$this->nl);
	    $size += $this->write('<Header><DocumentVersion>1.01</DocumentVersion><MerchantIdentifier>'.$oDestination->az_amz_destinations__az_amz_merchantid->value.'</MerchantIdentifier></Header>'.$this->nl);
	    $size += $this->write('<MessageType>' . $this->_messageType .'</MessageType>'.$this->nl);
		return $size;
	}
	/**
	 * Writes the message to file (encodes to the file encoding if needed)
	 * @param string $message
	 * @return int bytes written
	 */
	public function write($message)
	{
		//TODO find out about encoding, in case of UTF8 encode here
		return fwrite($this->_fileHandle, utf8_encode($message));
	}
	
	/**
	 * Closes AmazonEnvelope and the output file
	 * @return void
	 */
	public function endFile()
	{
	    $this->write('</AmazonEnvelope>'.$this->nl);
		fclose($this->_fileHandle);
	}
	
	/**
	 * Returns ids of products that were changed and are subject of this feed
	 * @return array of ids
	 */
	public function getChangedProductIds()
	{
		$oSnapshot = oxNew('az_amz_snapshot');
		$oSnapshot->setDestination($this->getDestination());                      
		$aIds = $oSnapshot->getChangedProductIds($this->_sAction);
		
		return $aIds;
	}
	
	/**
     * Updates items, recalculates their hashes
     * 
     * @param array $aIds Array of product ids
     * 
     * @return boolean
     */
    public function updateItems($aIds = null)
    {
    	if ($aIds)
		{
			$oSnapshot = oxNew('az_amz_snapshot');
			$oSnapshot->setDestination($this->getDestination());						
			//$oSnapshot->markExportedItems($aIds, true, false, false);
			$oSnapshot->markExportedItems($aIds, $this->_sAction);
			return true;
		}
		
		return false;
    }
	
    /**
     * Returns deleted product product numbers
     * @return array of article numbers
     */
	public function getDeletedProductArtNums()
	{
		return array();
	}
	
   /**
     * Delete snapshot items by article numbers
     * 
     * @param array $aArtSKUs Array of article SKUs
     * 
     * @return boolean 
     */
    public function deleteItemsByArtNum($aArtSKUs)
    {
        return false;
    }
	
    /**
     * Return the message of the update operation to specified product id
     * @param string $id product id
     * @return string xml
     */
	public function getUpdateXml($id)
	{
		return '';
	}
	/**
	 * Return delete message xml
	 * 
	 * @param string $id of the product
	 * @return string xml
	 */
	public function getDeleteXml($id)
	{
		return '';
	}
	
    /**
     * If value is not empty, writes xml for tagName with value, and puts attributes
     * utility method
     * 
     * @param string $tagName
     * @param string $value
     * @param array $attributes 
     * @return string xml
     */
    protected function _getXmlIfExists($tagName, $value, $attributes = array())
    {
        if(isset($value) && strlen($value) > 0) {
            $sXml = '<'.$tagName ;
            if(count($attributes)) {
                foreach($attributes as $attrName => $attrValue) {
                    $sXml .= ' '. $attrName . ' ="' . $this->_escapeXmlAttributeValue($attrValue) . '"';
                }
            }
            $sXml .= '>' .$this->_escapeXmlValue($value) . '</'.$tagName.'>';
            return $sXml;
        }
        return '';
    }
    
    /**
     * Utility method to escape xml value
     * 
     * @param string $xml xml value
     * @return string escaped xml string
     */
    protected function _escapeXmlValue($xml) 
    {
        return htmlspecialchars($xml, ENT_NOQUOTES);
    }
    /**
     * Utility method to escape xml attribute value
     * 
     * @param string $text
     * @return string
     */
    protected function _escapeXmlAttributeValue($text) 
    {
        return htmlspecialchars($text, ENT_QUOTES);
    }
    
    /**
     * Returns oxarticle object
     * @param string $sProductId Product Id
     * 
     * @return oxarticle
     */
    protected function _getProduct($sProductId)
    {
    	$oDestination = $this->getDestination();
    	$oProduct = oxNew('oxarticle');
    	$oProduct->setNoVariantLoading(true);
    	$oProduct->setLanguage($oDestination->az_amz_destinations__az_language->value);
    	$sCur = oxConfig::getParameter('cur');
    	$_POST['cur'] 	= $oDestination->az_amz_destinations__az_currency->value;
    	$_GET['cur'] 	= $oDestination->az_amz_destinations__az_currency->value;    	    
        $oProduct->load($sProductId);
        $_POST['cur'] 	= $sCur;
        $_GET['cur'] 	= $sCur;
       
        return $oProduct;
    }
    
    /**
     * Returns feed name
     * 
     * @return string
     */
    public function getFeedName()
    {
    	return $this->_sFeedName;
    }
    
    /**
     * returns action name
     * 
     * @return string
     */
    public function getFeedAction()
    {
    	return $this->_sAction;
    }
    
    /**
     * returns filename base
     * 
     * @return string
     */
    public function getFileNameBase()
    {
    	// one date for all splitted files
    	if (!$this->_sFileDate)
				$this->_sFileDate = date('ymd_His');
				
    	return $this->_sFileNameBase . '_' . $this->_sFileDate;
    }
    
	public function azHasAnyVariant( $oProduct, $blForceCoreTable = false )
    {
        $sArticleTable = $oProduct->getTableNameForActiveSnippet( $blForceCoreTable );
        return (bool) oxDb::getDb()->getOne( "select 1 from $sArticleTable where oxparentid='".$oProduct->getId()."'" );
    }
}