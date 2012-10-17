<?php

class az_amz_snapshot extends oxList
{
	
	/**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'az_amz_snapshotitem';
	
	protected $_oAZConfig = null;
	
	protected $_sDestinationId = null;
	
	protected $_aFilter = null;
	
	protected $_oDestination = null;
	
	protected $_sDataSeparator = ';;';
		
	
	/**
     * Class constructor
     *
     * @param string $sObjectsInListName Associated list item object type
     *
     * @return null
     */
    public function __construct( $sObjectsInListName = 'az_amz_snapshotitem')
    {
        parent::__construct( 'az_amz_snapshotitem');
        
        $this->_oAZConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
    }
          
    /**
     * Load all items from snapshot
     */
    public function loadItems()
    {
    	// load all items
    }
    
    /**
     * Filter for snapshot
     */
    public function addFilter()
    {
    	// add some filter for snapshot, 
    	// probably it will be list of fields with values (like for destination)
    	// we will construct WHERE statments for sql, before calling az_amz_snapshot::loadItems()
    }
    
    public function setDestinationId($sDestId)
    {
        $this->_sDestinationId = $sDestId;
        if(isset($this->_oDestination) && $this->_oDestination->getId() != $sDestId) {
            $this->_oDestination = null;
            $this->_aFilter = null;
        }
   
    }
    
    public function setDestination($oDestination)
    {
        $this->_oDestination = $oDestination;
        $this->_sDestinationId = $oDestination->getId();
        if(isset($this->_aFilter)) {
            unset($this->_aFilter);
        }
    }
        
    public function getFilter()
    {
        if(!isset($this->_aFilter)) {
            $oDestination = $this->getDestination();
            $aFilter = array();
            if ($oDestination->az_amz_destinations__az_productselector->value != '') {
                $aFilter = unserialize($oDestination->az_amz_destinations__az_productselector->getRawValue());
            }
            $this->_aFilter = $aFilter; 
        }
        return $this->_aFilter;
        
    }
    
    /**
     * Get destination object.
     * 
     * @return az_amz_Destination
     */
    public function getDestination()
    {
    	if ($this->_oDestionation)
    		return $this->_oDestionation;
    	
    	if (!empty($this->_sDestinationId))
    	{
    		// loading destination data
	    	$oDestination = oxNew('az_amz_destination');
	    	$oDestination->Load($this->_sDestinationId);
	    	$this->_oDestination = $oDestination;
	    	return $this->_oDestination;
    	} 
    	return false;
    }
    
    /**
     * Load preview list of articles
     * 
     * @return array $aArticles Array of articles
     */
    public function getPreviewArticles()        
    {
    	$sArtView 		= getViewName('oxarticles');
    	$sCatView 		= getViewName('oxcategories');
    	$sArt2CatView 	= getViewName('oxobject2category');
    	
    	$sQ = "SELECT $sArtView.* FROM $sArtView ";
    	$aFilter = $this->getFilter();
    	
    	if (isset($aFilter['categories'])) {   	
    		$sQ .= " LEFT JOIN $sArt2CatView ON $sArt2CatView.oxobjectid = $sArtView.oxid ";
    	}
    	    	
    	$sWhere = $this->_getFilterWhere();
    	
    	$sQ .= " WHERE ".$sWhere;
    	
    	$oArticleList = oxNew('oxlist');
    	$oArticleList->init('oxbase', 'oxarticles');
    	$oArticleList->setSqlLimit(0, 100);
    		
    	$oArticleList->selectString($sQ);
    
    	// TODO EE 2.7: replace with $oArticleList->aList
    	if (sizeof($oArticleList->getArray()) > 0)
    		return $oArticleList->getArray();
    	    	
    	return false;
    }
    
    protected function _getFilterWhere()
    {   
    	//TODO: on EE/PE version there could be a problems with table names, couse filter fields comes with oxarticles prefix
    	$oDB = oxDb::getDb();
    	
    	$sArt2CatView 	= getViewName('oxobject2category');
    	$sArtView 		= getViewName('oxarticles');
    	 
    	$aWhere = array();
    	$aFilter = $this->getFilter();
    	
    	$sEanField = $sArtView.'.oxean';
    	
    	if ($this->_oAZConfig->sEanField)
    		$sEanField = $this->_oAZConfig->sEanField;
    		
    	// only fields with non-empty EAN field value	
    	// changed by TD, main articles do not need to have an EAN code
    	//$aWhere[] = $sEanField." != '' ";
    	$aWhere[] = "($sArtView.$sEanField != '' OR $sArtView.OXVARCOUNT > 0 )";
    	$aWhere[] = $sArtView.".oxparentid = ''";

    	if (isset($aFilter['categories']))    	
    		$aWhere[] = " $sArt2CatView.oxcatnid IN ('".implode("','", $aFilter['categories'])."')";
    	
    	if (isset($aFilter['fields']) && sizeof($aFilter['fields']) > 0) {    	
    		foreach($aFilter['fields'] as $aField) {
    			
    			$sWhereLine = $aField['field'] ." ". $aField['operator'];
    			
    			if ($this->_oAZConfig->isRequiredOperatorValue($aField['operator'])) {
    				
    				$sWhereLine .= " ". $oDB->quote($aField['value']);
    			}
    			    	
    			$aWhere[] = $sWhereLine;    			
    		}
    	}
	
    	$sWhere = implode(" AND ", $aWhere);
    	
    	return $sWhere;
    }
    
    /**
     * Makes a snapshot of set
     * 
     */
    public function doSnapshot()
    {
    	$oDB = oxDb::getDb();
    	
    	$sArtView 		= getViewName('oxarticles');
    	$sCatView 		= getViewName('oxcategories');
    	$sArt2CatView 	= getViewName('oxobject2category');
    	
    	// has to be loaded first cause content comes through magic getter
    	$sSkuField		= $this->_oAZConfig->sSkuField;
    	$sEanField		= $this->_oAZConfig->sEanField;
    	
    	$sQ = "SELECT 
    		$sArtView.oxid, 
    		az_amz_snapshots.oxid, 
    		$sArtView." . $sSkuField . ", 
    		$sArtView." . $sEanField . " 
    		FROM $sArtView ";
    	
    	$sQ .= " LEFT JOIN az_amz_snapshots ON ($sArtView.oxid = az_amz_snapshots.az_productid AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}') ";
    	$aFilter = $this->getFilter();
    	if (isset($aFilter['categories']))    	
    		$sQ .= " LEFT JOIN $sArt2CatView ON $sArt2CatView.oxobjectid = $sArtView.oxid ";
    	
    	$sWhere = $this->_getFilterWhere();
    	
    	$sQ .= " WHERE ". $sWhere;
    	
    	$sQ .= " GROUP BY $sArtView.oxid" ;
    	//die($sQ);
    	$rs = $oDB->execute($sQ);
    	
    	if($rs != false && $rs->recordCount() > 0) 
    	{
            while (!$rs->EOF) 
            {
                //$oProduct = oxNew('oxarticle');
                //$oProduct->Load($rs->fields[0]);
                
        		$oAmzSnapshotItem = oxNew('az_amz_snapshotitem');
        		
        		if ($rs->fields[1] != '') {
        			$oAmzSnapshotItem->Load($rs->fields[1]);
        		}
        		
        		$oAmzSnapshotItem->assignValues($rs->fields[0], $rs->fields[2], $this->_sDestinationId);
        		
        		$oAmzSnapshotItem->save();
        		
        		//START: Variants are saved to snapshot
        		if ($this->_oAZConfig->blAmazonExportVariants == '1') {
	        		//$aVariants = $oProduct->getVariants(false);
	        		$aVariants = $this->_getArticleVariants($rs->fields[0]);
	        		
	        		if (sizeof($aVariants) > 0) {
	        			foreach ($aVariants as $oVariant) {
	        				$blSave = true;
		        			$oAmzSnapshotItem = oxNew('az_amz_snapshotitem');
		        		
			        		if ($rs->fields[1] != '') {
			        			$oAmzSnapshotItem->LoadByProductId($oVariant->oxid, $this->_sDestinationId);
			        		}
			        		
			        		$oAmzSnapshotItem->assignValues($oVariant->oxid, $oVariant->sku, $this->_sDestinationId);
			        		
			        		// item should not be saved if sku field is empty
			        		// if sku field of variant is empty, shop fills it with value from parent. so we have to check
			        		// if parent value equals variant value of sku
			        		if($oVariant->sku == $rs->fields[2])
			        			$blSave = false;
			        			
			        		// DOC: if articles have NO EAN - another field should be assigned in admin
			        		if($oVariant->ean == $rs->fields[3])
			        			$blSave = false;
			        		
			        			
			        		if($blSave)
			        			$oAmzSnapshotItem->save();
	        			}
	        		}
        		}
        		//STOP: Variants are saved to snapshot 
        		        		
                $rs->moveNext();
            }
    	}
    }
    
    protected function _getArticleVariants($sParentId)
    {
    	// has to be loaded first cause content comes through magic getter
    	$sSkuField		= $this->_oAZConfig->sSkuField;
    	$sEanField		= $this->_oAZConfig->sEanField;
    	
    	$sArtView 		= getViewName('oxarticles');
    	$sSelect = "select oxid, ". $sSkuField . ", " . $sEanField . " from $sArtView where oxparentid = '$sParentId' ";
    	$rs = oxDb::getDb()->Execute($sSelect);
    	$aVariants = array();
    	if($rs != false && $rs->RecordCount() > 0) {
    		
    		while (!$rs->EOF) {
    			$oVariant = new stdClass();
    			$oVariant->oxid	= $rs->fields[0];
    			$oVariant->sku	= $rs->fields[1];
    			$oVariant->ean	= $rs->fields[2];
    			$aVariants[]	= $oVariant;
    			$rs->MoveNext();
    		}
    	}
    	return $aVariants;
    }
    
    /**
     * Get Sql to select changed product ids
     * 
     * @return string Sql query
     */
    protected function _getChangedProductSql()
    {
		$sArtView 		= getViewName('oxarticles');
		// TODO EE 2.7: replace getBaseObject() by own function
		// possibly replaceable with oxNew()
		$oSnapshotItem	= $this->getBaseObject();
		
		$aCheckSQL 		= array();
		$aOxidFields 	= array();
		
		$sQ = "SELECT * FROM ( ";	    	    	    
		$sQ .= "SELECT $sArtView.oxid AS oxarticle_oxid, az_amz_snapshots.* ";
		
		$aHashFields = $oSnapshotItem->getProductHashFields();
		$sProductHashSQL = "SnapshotItems.".implode(",SnapshotItems.", $aHashFields);
		
		$aOxidFields = array_merge($aOxidFields, $aHashFields);
		
		$aTimestampCheckSQL[] = "az_amz_snapshots.az_timestamp < $sArtView.oxtimestamp";
		$aCheckSQL[] = "SnapshotItems.az_hash != MD5(CONCAT($sProductHashSQL))";
		
		if (sizeof($aOxidFields) > 0) {
			$sSubSelectFields = "{$sArtView}.".implode(",{$sArtView}.", $aOxidFields);		
			$sQ .= "," . $sSubSelectFields;

		}
		
		$sQ .= " FROM az_amz_snapshots, $sArtView
				  WHERE az_amz_snapshots.az_productid = $sArtView.oxid
				    AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}' 
				";
		
		if (sizeof($aTimestampCheckSQL)) {
			$sQ .= " AND (".implode(" OR ", $aTimestampCheckSQL).")";
		}
		
		$sQ .= " ORDER BY $sArtView.oxparentid, $sArtView.oxid";
			
		$sQ .= ") SnapshotItems ";
		
		if (sizeof($aCheckSQL) > 0)
			$sQ .= " WHERE (".implode(" OR ", $aCheckSQL).")";
		
		
		return $sQ;	
    }
    
    /**
     * Get Sql to select ids of products with changed prices
     * 
     * @return string Sql query
     */
    protected function _getChangedPriceSql()
    {
    	$sArtView 		= getViewName('oxarticles');
    	$oSnapshotItem	= $this->getBaseObject();
    	
    	$aCheckSQL 		= array();
    	$aOxidFields 	= array();
    	
    	$sQ = "SELECT * FROM ( ";	    	    	    
    	$sQ .= "SELECT $sArtView.oxid AS oxarticle_oxid, az_amz_snapshots.* ";
    	
    	$aHashFields = $oSnapshotItem->getPriceHashFields();
		$sPriceHashSQL = "SnapshotItems.".implode(",SnapshotItems.", $aHashFields);
		
		$aOxidFields = array_merge($aOxidFields, $aHashFields);
		
		$aTimestampCheckSQL[] = "az_amz_snapshots.az_price_timestamp < $sArtView.oxtimestamp";
		$aCheckSQL[] = "SnapshotItems.az_pricehash != MD5(CONCAT($sPriceHashSQL))";
		
		if (sizeof($aOxidFields) > 0) {
			$sSubSelectFields = "{$sArtView}.".implode(",{$sArtView}.", $aOxidFields);		
			$sQ .= "," . $sSubSelectFields;

		}
		
		$sQ .= " FROM az_amz_snapshots, $sArtView
				  WHERE az_amz_snapshots.az_productid = $sArtView.oxid
				    AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}' 
				";
				
		if (sizeof($aTimestampCheckSQL))
			$sQ .= " AND (".implode(" OR ", $aTimestampCheckSQL).")";
			
		$sQ .= ") SnapshotItems ";
		
		if (sizeof($aCheckSQL) > 0)
			$sQ .= " WHERE (".implode(" OR ", $aCheckSQL).")";
				
		return $sQ;		
    }
    
    /**
     * Get Sql to select ids of products with changed images
     * 
     * @return string Sql query
     */
    protected function _getChangedImageSql()
    {    	    
    	$sArtView 		= getViewName('oxarticles');
    	$oSnapshotItem	= $this->getBaseObject();
    	
    	$aCheckSQL 		= array();
    	$aOxidFields 	= array();
    	
    	$sQ = "SELECT * FROM ( ";	    	    	    
    	$sQ .= "SELECT $sArtView.oxid AS oxarticle_oxid, az_amz_snapshots.* ";
    	
    	$oDestination = $this->getDestination();
			
		$sShopId = null;
		
		if ($oDestination) {
			$sShopId = $oDestination->az_amz_destinations__oxshopid->value;
		}			
			

		$aHashFields = $oSnapshotItem->getPictureHashFields($sShopId);
				
		// prevent field dublicate
		$aHashFields = array_unique($aHashFields);
		
		$sPictureHashSQL = "SnapshotItems.".implode(",SnapshotItems.", $aHashFields);
		
		$aOxidFields = array_merge($aOxidFields, $aHashFields);
		
		$aTimestampCheckSQL[] = "az_amz_snapshots.az_picture_timestamp < $sArtView.oxtimestamp";
		$aCheckSQL[] = "SnapshotItems.az_picturehash != MD5(CONCAT($sPictureHashSQL))";
		
		if (sizeof($aOxidFields) > 0) {
			$sSubSelectFields = "{$sArtView}.".implode(",{$sArtView}.", $aOxidFields);		
			$sQ .= "," . $sSubSelectFields;

		}
		
		$sQ .= " FROM az_amz_snapshots, $sArtView
				  WHERE az_amz_snapshots.az_productid = $sArtView.oxid
				    AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}' 
				";
		$aIds = array();
		
		if (sizeof($aTimestampCheckSQL)) {
			$sQ .= " AND (".implode(" OR ", $aTimestampCheckSQL).")";
		}
		
		$sQ .= ") SnapshotItems ";
		
		if (sizeof($aCheckSQL) > 0) {
			$sQ .= " WHERE (".implode(" OR ", $aCheckSQL).")";
		}
		
		return $sQ;
    }
    
    /**
     * Get Sql to select ids of products with changed inventory
     * 
     * @return string Sql query
     */
    protected function _getChangedInventorySql()
    {
    	$sArtView 		= getViewName('oxarticles');
    	$oSnapshotItem	= $this->getBaseObject();
    	
    	$aCheckSQL 		= array();
    	$aOxidFields 	= array();
    	
    	$sQ = "SELECT * FROM ( ";	    	    	    
    	$sQ .= "SELECT $sArtView.oxid AS oxarticle_oxid, az_amz_snapshots.* ";
    	
    	$aHashFields = $oSnapshotItem->getInventoryHashFields();
		    
	    $sInventoryHashSQL = "SnapshotItems.".implode(",SnapshotItems.", $aHashFields);
	    
	    $aOxidFields = array_merge($aOxidFields, $aHashFields);
	    
	    // do NOT use timestamp check for inventory check, cause on orders stock is updated WITHOUT updating timestamp!
	    //$aTimestampCheckSQL[] = "az_amz_snapshots.az_inventory_timestamp < $sArtView.oxtimestamp";
	    $aCheckSQL[] = "SnapshotItems.az_inventoryhash != CONCAT({$sInventoryHashSQL})";
	    
	    if (sizeof($aOxidFields) > 0) {
			$sSubSelectFields = "{$sArtView}.".implode(",{$sArtView}.", $aOxidFields);		
			$sQ .= "," . $sSubSelectFields;

		}
		
		$sQ .= " FROM az_amz_snapshots, $sArtView
				  WHERE az_amz_snapshots.az_productid = $sArtView.oxid
				    AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}' 
				";
		$aIds = array();
		
		/*
		if (sizeof($aTimestampCheckSQL)) {
			$sQ .= " AND (".implode(" OR ", $aTimestampCheckSQL).")";
		}
		*/
			
		$sQ .= ") SnapshotItems ";
		
		if (sizeof($aCheckSQL) > 0) {
			$sQ .= " WHERE (".implode(" OR ", $aCheckSQL).")";
		}
		
		return $sQ;    	
    }
    
    /**
     * Get Sql to select ids of products with changed shipping
     * 
     * @return string Sql query
     */
    protected function _getChangedShippingSql()
    {
    	$sArtView 		= getViewName('oxarticles');
    	$oSnapshotItem	= $this->getBaseObject();
    	
    	$aCheckSQL 		= array();
    	$aOxidFields 	= array();
    	
    	$sQ = "SELECT * FROM ( ";	    	    	    
    	$sQ .= "SELECT $sArtView.oxid AS oxarticle_oxid, az_amz_snapshots.* ";
    	
    	$aHashFields = $oSnapshotItem->getShippingHashFields();
		    
	    $sShippingHashSQL = "SnapshotItems.".implode(",SnapshotItems.", $aHashFields);
	    
	    $aOxidFields = array_merge($aOxidFields, $aHashFields);
	    
	    $aTimestampCheckSQL[] = "az_amz_snapshots.az_shipping_timestamp < $sArtView.oxtimestamp";
	    $aCheckSQL[] = "SnapshotItems.az_shippinghash != MD5(CONCAT({$sShippingHashSQL}))";
	    
	    if (sizeof($aOxidFields) > 0)
		{
			$sSubSelectFields = "{$sArtView}.".implode(",{$sArtView}.", $aOxidFields);		
			$sQ .= "," . $sSubSelectFields;

		}
		
		$sQ .= " FROM az_amz_snapshots, $sArtView
				  WHERE az_amz_snapshots.az_productid = $sArtView.oxid
				    AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}' 
				";
		$aIds = array();
		
		if (sizeof($aTimestampCheckSQL)) {
			$sQ .= " AND (".implode(" OR ", $aTimestampCheckSQL).")";
		}
			
		$sQ .= ") SnapshotItems ";
		
		if (sizeof($aCheckSQL) > 0) {
			$sQ .= " WHERE (".implode(" OR ", $aCheckSQL).")";
		}
		
		return $sQ;
    }
    
    /**
     * Get Sql to select ids of products with changed variant relations
     * 
     * @return string Sql query
     */
    protected function _getChangedVariantSql()
    {
    	$sArtView 		= getViewName('oxarticles');   
    	//##TODO: can sku field be changed here? 
    	$sSkuField		= $this->_oAZConfig->sSkuField;	
    		    	    	    
    	$sQ = "SELECT TmpParents.oxid, az_amz_snapshots.* 
    			FROM az_amz_snapshots,
				  ( SELECT parents.oxid, MD5(GROUP_CONCAT(variants.$sSkuField ORDER BY variants.$sSkuField)) as variant_hash, COUNT(variants.oxid) as VarCount
				      FROM $sArtView as parents, $sArtView as variants
				      WHERE parents.oxid = variants.oxparentid
				      	AND variants.oxactive = '1'
				        AND parents.oxparentid = ''				        
				      GROUP BY parents.oxid
				      HAVING VarCount > 0
				  ) TmpParents
				WHERE TmpParents.oxid = az_amz_snapshots.az_productid
				  AND (TmpParents.variant_hash != az_amz_snapshots.az_varianthash OR az_amz_snapshots.az_varianthash IS NULL)
  			";
  		
    	return $sQ;
    }
    
    /**
     * Returns ids of changed products
     * 
     * @param boolean $blCheckProductChanges checks if product fields were changed
     * @param boolean $blCheckPriceChanges checks if price was changed.
     * @param boolean $blCheckPictureChanges checks if pictures were changed.
     * @param boolean $blCheckInventoryChanges checks if inventory were changed
     * 
     * @return array $aProductIds Product Ids
     */
    public function getChangedProductIds($sType)
    {
    	$oDB = oxDb::getDb();
    	
    	switch($sType) {
			case Az_Amz_Feed::TYPE_PRODUCT:
				$sQ = $this->_getChangedProductSql();
			break;
			
			case Az_Amz_Feed::TYPE_PRICE:
				$sQ = $this->_getChangedPriceSql();
			break;
			
			case Az_Amz_Feed::TYPE_PRODUCT_IMAGES:
				$sQ = $this->_getChangedImageSql();
			break;
			
			case Az_Amz_Feed::TYPE_INVENTORY:
				$sQ = $this->_getChangedInventorySql();
			break;
			
			case Az_Amz_Feed::TYPE_SHIPPING:
				$sQ = $this->_getChangedShippingSql();
			break;
			
			case Az_Amz_Feed::TYPE_RELATION:
				$sQ = $this->_getChangedVariantSql();
			break;
			
		}
			
		$rs = $oDB->execute($sQ);
		 
		if($rs != false && $rs->recordCount() > 0) 
    	{
            while (!$rs->EOF) 
            {
            	$aIds[] = $rs->fields[0];
            	$rs->moveNext();
            }
    	}
    	
    	return $aIds;
	}
	
	/**
	 * Returns article numbers of deleted products
	 * 
	 * @return array $aArtSKUs Product SKUs
	 */
	public function getDeletedProductArtNums()
	{
		$oDB = oxDb::getDb();
    	
    	$sArtView 		= getViewName('oxarticles');
    	$oSnapshotItem	= $this->getBaseObject();
    	
    	$aCheckSQL 		= array();
    	    	    	    	    
    	$sQ = "SELECT az_amz_snapshots.az_skufield
				  FROM az_amz_snapshots
				  	LEFT JOIN $sArtView ON az_amz_snapshots.az_productid = $sArtView.oxid
				  WHERE az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}'
				  	AND oxarticles.oxid is NULL
				";
	
		$rs = $oDB->execute($sQ);
		
		$aArtSKUs = array();
		
		if($rs != false && $rs->recordCount() > 0) 
    	{
            while (!$rs->EOF) 
            {
            	$aArtSKUs[] = $rs->fields[0];
            	$rs->moveNext();
            }
    	}
    	
    	return $aArtSKUs;
	}
	
	/**
	 * Returns list of parent and variant articles which relations must be deleted
	 * 
	 * @return array
	 */
	public function getDeletedVariantRelations() 
	{	global $myConfig;
		
		$oDB = oxDb::getDb();
		//##TODO: can sku field be changed here? 
		$sSkuField		= $this->_oAZConfig->sSkuField;	
		 
		$sQ = "SELECT parents.$sSkuField, GROUP_CONCAT(variants.$sSkuField SEPARATOR '".$this->_sDataSeparator."') as VariantIds, az_amz_snapshots.az_variant_data
				  FROM oxarticles as parents
				    LEFT JOIN oxarticles as variants ON parents.oxid = variants.oxparentid
				    LEFT JOIN az_amz_snapshots ON az_amz_snapshots.az_productid = parents.oxid
				  WHERE parents.oxparentid = ''
				  	AND variants.oxactive = '1'
				  	AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}'
					GROUP BY parents.oxid
				  HAVING VariantIds != az_amz_snapshots.az_variant_data
			";

		$rs = $oDB->Execute($sQ);	
		$aCurrentVariants 	= array();		 
		$aLastVariants		= array();
		$aReturn			= array();
		
		if ($rs != false && $rs->recordCount() > 0) {
			while(!$rs->EOF) {				
		        $aCurrentVariants 		= explode($this->_sDataSeparator, $rs->fields[1]);
		        $aLastExportVariants 	= explode($this->_sDataSeparator, $rs->fields[2]);
		        
		        $aDeletedRelations 		= array_diff( $aLastExportVariants, $aCurrentVariants);
		        if ( $aDeletedRelations && sizeof($aDeletedRelations) > 0) {
		        	foreach($aDeletedRelations as $sVariantArtNum) {
		        		if ($sVariantArtNum != '') {
		        			$aReturn[] = array($sVariantArtNum,$rs->fields[0]);
		        		}
		        	}
		        }
		        $rs->MoveNext();
		    }
		}	    
	    return $aReturn;
	}
	
	/**
	 * Returns list of variant ids which are related to Parent ($sProductId) product
	 * @param string $sProductId Parent product Id
	 * @param boolean $blOnlyNew if true - return only new relations
	 * @return array
	 */
	public function getProductRelations($sProductId, $blOnlyNew = true) 
	{
		if ($sProductId == '') {
			return false;
		} 
		
		$oSnapshotItem = oxNew('az_amz_snapshotitem');
		
		$oSnapshotItem->loadByProductId($sProductId, $this->_sDestinationId);
		$sVariantsData = $oSnapshotItem->az_amz_snapshots__az_variant_data->value;
		
		$aLastExportVariants = array();
		if ($sVariantsData != '') {
			$aLastExportVariants = explode($this->_sDataSeparator, $sVariantsData);
		}
		
		$oDb = oxDb::getDb();
		//##TODO: can sku field be changed here? 
		$sSkuField		= $this->_oAZConfig->sSkuField;	
		
		$sQ = " SELECT $sSkuField
					FROM oxarticles
					WHERE oxparentid = '{$sProductId}'
					  AND oxarticles.oxactive = '1'
				";
		
		$rs = $oDb->execute($sQ);
		$aArtSKUs = array();
		
		if($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
            	if (!in_array($rs->fields[0], $aLastExportVariants)) {
            		$aArtSKUs[] = $rs->fields[0];
            	}
            	$rs->moveNext();
            }
    	}else {
    		return false;
    	}
    	

    	return $aArtSKUs;		
	}

	public function getAllProductArtNums()
	{
	    $oDB = oxDb::getDb();
	    $sArtView  = getViewName('oxarticles');
	    //##TODO: can sku field be changed here? 
	    $sSkuField		= $this->_oAZConfig->sSkuField;	
	    $sQ = "SELECT DISTINCT a.$sSkuField 
    	    FROM $sArtView a, az_amz_snapshots s 
    	    WHERE
    	       a.oxid = s.az_productid
    	       AND s.az_destinationid = ?";
	    $res = $oDB->Execute($sQ, array($this->getDestination()->getId()));
	   
	    $ids = array();
	    while(!$res->EOF) {
	        $ids[] = $res->fields[$sSkuField];
	        $res->MoveNext();
	    }
	    $res->close();
	    return $ids;
	}
	
	
	/**
	 * Generate Product feed hash
	 * @param array $aIds Array of product ids
	 * 
	 * @return boolean
	 */
	protected function _markExportedProducts($aIds = null) 
	{
		$oDB = oxDb::getDb();
		
		$sArtView 		= getViewName('oxarticles');
			
		$oSnapshotItem	= $this->getBaseObject();
		
		if ($aIds && sizeof($aIds) > 0) {
			$sIdSQL = "'".implode("','", $aIds)."'";
		}
		
		$aHashFields 	= $oSnapshotItem->getProductHashFields();		
	    $aHashSQL[] 	= " az_amz_snapshots.az_hash = MD5(CONCAT($sArtView.".implode(",$sArtView.", $aHashFields).")) ";
	    //timestamp update
    	$aHashSQL[] = "az_amz_snapshots.az_timestamp = $sArtView.oxtimestamp";
    	
    	$sHashUpdateSQL = implode(',', $aHashSQL);
		
		$sQ = "UPDATE $sArtView, az_amz_snapshots
				SET $sHashUpdateSQL
				WHERE $sArtView.oxid = az_amz_snapshots.az_productid
				 AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}'						
				 AND az_amz_snapshots.az_productid IN ($sIdSQL)
				";
		
		if ($oDB->execute($sQ))
			return true;
			
		return false;
	}
	
	/**
	 * Generate Price feed hash
	 * @param array $aIds Array of product ids
	 * 
	 * @return boolean
	 */
	protected function _markExportedPrices($aIds = null) 
	{	
		$oDB = oxDb::getDb();
		
		$sArtView 		= getViewName('oxarticles');
			
		$oSnapshotItem	= $this->getBaseObject();
		
		if ($aIds && sizeof($aIds) > 0) {
			$sIdSQL = "'".implode("','", $aIds)."'";
		}
		
		$aHashFields	= $oSnapshotItem->getPriceHashFields();		
		$aHashSQL[] 	= " az_amz_snapshots.az_pricehash = MD5(CONCAT($sArtView.".implode(",$sArtView.", $aHashFields).")) ";
		//timestamp update
		$aHashSQL[] = "az_amz_snapshots.az_price_timestamp = $sArtView.oxtimestamp";
		
		$sHashUpdateSQL = implode(',', $aHashSQL);
		
		$sQ = "UPDATE $sArtView, az_amz_snapshots
				SET $sHashUpdateSQL
				WHERE $sArtView.oxid = az_amz_snapshots.az_productid
				 AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}'						
				 AND az_amz_snapshots.az_productid IN ($sIdSQL)
				";
		
		if ($oDB->execute($sQ))
			return true;
			
		return false;
	}
	
	
	/**
	 * Generate Picture feed hash
	 * @param array $aIds Array of product ids
	 * 
	 * @return boolean
	 */
	protected function _markExportedImages($aIds = null) 
	{
		$oDB = oxDb::getDb();
		
		$sArtView 		= getViewName('oxarticles');
			
		$oSnapshotItem	= $this->getBaseObject();
		
		if ($aIds && sizeof($aIds) > 0) {
			$sIdSQL = "'".implode("','", $aIds)."'";
		}
		
		$oDestination = $this->getDestination();
			
		$sShopId = null;
		
		if ($oDestination) {
			$sShopId = $oDestination->az_amz_destinations__oxshopid->value;
		}	
			
		$aHashFields	= $oSnapshotItem->getPictureHashFields($sShopId);
				
		$aHashSQL[] 	= " az_amz_snapshots.az_picturehash = MD5(CONCAT($sArtView.".implode(",$sArtView.", $aHashFields).")) ";
		
		//timestamp update
		$aHashSQL[] = "az_amz_snapshots.az_picture_timestamp = $sArtView.oxtimestamp";
		
		$sHashUpdateSQL = implode(',', $aHashSQL);
		
		$sQ = "UPDATE $sArtView, az_amz_snapshots
				SET $sHashUpdateSQL
				WHERE $sArtView.oxid = az_amz_snapshots.az_productid
				 AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}'						
				 AND az_amz_snapshots.az_productid IN ($sIdSQL)
				";
		
		if ($oDB->execute($sQ))
			return true;
			
		return false;
	}
	
	/**
	 * Generate Inventory feed hash
	 * @param array $aIds Array of product ids
	 * 
	 * @return boolean
	 */
	protected function _markExportedInventory($aIds = null) 
	{		
		$oDB = oxDb::getDb();
		
		$sArtView 		= getViewName('oxarticles');
			
		$oSnapshotItem	= $this->getBaseObject();
		
		if ($aIds && sizeof($aIds) > 0)
			$sIdSQL = "'".implode("','", $aIds)."'";
		
		$aHashFields = $oSnapshotItem->getInventoryHashFields();
        $sInventoryHashSQL = "{$sArtView}.".implode(",{$sArtView}.", $aHashFields);
        $aHashSQL[] = "az_amz_snapshots.az_inventoryhash = CONCAT({$sInventoryHashSQL})";
        
        //timestamp update
		$aHashSQL[] = "az_amz_snapshots.az_inventory_timestamp = $sArtView.oxtimestamp";
		
		$sHashUpdateSQL = implode(',', $aHashSQL);
		
		$sQ = "UPDATE $sArtView, az_amz_snapshots
				SET $sHashUpdateSQL
				WHERE $sArtView.oxid = az_amz_snapshots.az_productid
				 AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}'						
				 AND az_amz_snapshots.az_productid IN ($sIdSQL)
				";
		
		if ($oDB->execute($sQ))
			return true;
			
		return false;
		
		
	}
	
	/**
	 * Generate Shipping feed hash
	 * @param array $aIds Array of product ids
	 * 
	 * @return boolean
	 */
	protected function _markExportedShipping($aIds = null) 
	{
		$oDB = oxDb::getDb();
		
		$sArtView 		= getViewName('oxarticles');
			
		$oSnapshotItem	= $this->getBaseObject();
		
		if ($aIds && sizeof($aIds) > 0) {
			$sIdSQL = "'".implode("','", $aIds)."'";
		}
			
		$aHashFields = $oSnapshotItem->getInventoryHashFields();
		
        $sInventoryHashSQL = "{$sArtView}.".implode(",{$sArtView}.", $aHashFields);
        $aHashSQL[] = "az_amz_snapshots.az_shippinghash = MD5(CONCAT({$sInventoryHashSQL}))";
        
        //timestamp update
		$aHashSQL[] = "az_amz_snapshots.az_shipping_timestamp = $sArtView.oxtimestamp";
		
		$sHashUpdateSQL = implode(',', $aHashSQL);
		
		$sQ = "UPDATE $sArtView, az_amz_snapshots
				SET $sHashUpdateSQL
				WHERE $sArtView.oxid = az_amz_snapshots.az_productid
				 AND az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}'						
				 AND az_amz_snapshots.az_productid IN ($sIdSQL)
				";
		
		if ($oDB->execute($sQ))
			return true;
			
		return false;
	}
	
	/**
	 * Generate Variant relation feed hash
	 * @param array $aIds Array of product ids
	 * 
	 * @return boolean
	 */
	protected function _markExportedVariantRelation($aIds = null) 
	{
		$oDB = oxDb::getDb();
		
		$sArtView 		= getViewName('oxarticles');
		//##TODO: can sku field be changed here? 
		$sSkuField		= $this->_oAZConfig->sSkuField;	
			
		$oSnapshotItem	= $this->getBaseObject();
		
		if ($aIds && sizeof($aIds) > 0) {
			$sIdSQL = "'".implode("','", $aIds)."'";
		}
		
		$sQ = "	UPDATE
					az_amz_snapshots
				SET az_variant_timestamp = CURRENT_TIMESTAMP,
					az_varianthash = (
					  		SELECT MD5(GROUP_CONCAT(variants.$sSkuField ORDER BY variants.$sSkuField))
							  FROM oxarticles as parents, oxarticles as variants
							  WHERE parents.oxid = variants.oxparentid
							  	AND variants.oxactive = '1'
							    AND az_amz_snapshots.az_productid = parents.oxid
							    AND parents.oxparentid = ''
							  GROUP BY parents.oxid
					),
					az_variant_data = (
					  		SELECT GROUP_CONCAT(variants.$sSkuField ORDER BY variants.$sSkuField SEPARATOR '".$this->_sDataSeparator."')
							  FROM oxarticles as parents, oxarticles as variants
							  WHERE parents.oxid = variants.oxparentid
							  	AND variants.oxactive = '1'
							    AND az_amz_snapshots.az_productid = parents.oxid
							    AND parents.oxparentid = ''
							  GROUP BY parents.oxid
					)	
				WHERE az_amz_snapshots.az_productid IN ($sIdSQL)
				";
		
		if ($oDB->execute($sQ))
			return true;
			
		return false;
	}
	
	
	/**
	 * Recalculates hashes
	 * @param array $aIds array of product ids
	 * @param string $sExporType Type of export
	 * 
	 * @return boolean True on success, false on fail
	 */
	public function markExportedItems($aIds = null, $sExportType = null)
	{
		$blRet = false;
		
		switch($sExportType) {
			case Az_Amz_Feed::TYPE_PRODUCT:
				$blRet = $this->_markExportedProducts($aIds);
			break;
			
			case Az_Amz_Feed::TYPE_PRICE:
				$blRet = $this->_markExportedPrices($aIds);
			break;
			
			case Az_Amz_Feed::TYPE_PRODUCT_IMAGES:
				$blRet = $this->_markExportedImages($aIds);
			break;
			
			case Az_Amz_Feed::TYPE_INVENTORY:
				$blRet = $this->_markExportedInventory($aIds);
			break;
			
			case Az_Amz_Feed::TYPE_SHIPPING:
				$blRet = $this->_markExportedShipping($aIds);
			break;
			
			case Az_Amz_Feed::TYPE_RELATION:
				$blRet = $this->_markExportedVariantRelation($aIds);
			break;
			
		}
		
		return $blRet;
	}
	
	/**
	 * Delete snapshot items by article numbers
	 * 
	 * @param array $aArtSKUs Array of article numbers
	 * 
	 * @return boolean 
	 */
	public function deleteItemsByArtNum($aArtSKUs)
	{
		$oDB = oxDb::getDb();
		
		if ($aArtSKUs && sizeof($aArtSKUs) > 0)
		{
			$sQ = " DELETE
						FROM az_amz_snapshots
						WHERE az_amz_snapshots.az_destinationid = '{$this->_sDestinationId}'
						  AND az_amz_snapshots.az_skufield IN('".implode("','", $aArtSKUs)."')
				  ";
			
			$oDB->Execute($sQ);
			
			return true;
		}
		
		return false;
	}
	
	
    
}
?>
