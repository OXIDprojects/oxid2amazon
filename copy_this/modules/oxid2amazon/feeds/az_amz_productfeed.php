<?php
/**
 * Main product feed 
 * writes all information about product
 * 
 */
class Az_Amz_ProductFeed extends Az_Amz_Feed
{	
	/**
	 * Feed name
	 * 
	 * @var string
	 */	
	protected $_sFeedName = 'Product feed';
	
	
	protected $_messageType = 'Product';
	
	/**
	 * Feed Action
	 * 
	 * @var string
	 */	
	protected $_sAction = Az_Amz_Feed::TYPE_PRODUCT;
	
	/**
	 * File name base
	 * @var $_sFileNameBase
	 */
	protected $_sFileNameBase = 'product_feed';
	
	/**
	 * Returns ids of changed products	
     * 
     * @return array $aProductIds Product Ids
     */
	public function getChangedProductIds()
	{	
		$oSnapshot = oxNew('az_amz_snapshot');
		$oSnapshot->setDestination($this->getDestination());		
		// update snapshot if not dryrun
		$dryRun = $this->getDryRun();
			    
		if (!$dryRun)
			$oSnapshot->doSnapshot();
							
		$aIds = $oSnapshot->getChangedProductIds($this->_sAction);
		
		return $aIds;
	}
	
	/**
	 * Returns article numbers of deleted products	
     * 
     * @return array $aArtSKUs Product SKUs
     */
	public function getDeletedProductArtNums()
	{	
		$oSnapshot = oxNew('az_amz_snapshot');
		$oSnapshot->setDestination($this->getDestination());						
		return $oSnapshot->getDeletedProductArtNums();
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
		if ($aArtSKUs && sizeof($aArtSKUs))
		{
			$oSnapshot = oxNew('az_amz_snapshot');
			$oSnapshot->setDestination($this->getDestination());						
			$oSnapshot->deleteItemsByArtNum($aArtSKUs);
			
			return true;
		}
		
		return false;
	}		    	
	
	public function getUpdateXml($id)
	{
		$amzConfig = $this->_getAmzConfig();
		
		$product = $this->_getProduct($id);
		
		$sXml = '<Message>'.$this->nl;
			$sXml .= '<MessageID>' . (++$this->_messageId) . '</MessageID>'.$this->nl;
			$sXml .= '<OperationType>Update</OperationType>'.$this->nl;
			$sXml .= '<Product>'.$this->nl;
			
				$sSkuProp = $this->getSkuProperty();
				$sSkuValue = $product->$sSkuProp->value;
				/*
				if($this->azHasAnyVariant($product)) {
					$sSkuValue = "P".$sSkuValue;
				}
				*/
		        $sXml .= '<SKU>'. $sSkuValue. '</SKU>';
		        
		        
		        if(!$this->azHasAnyVariant($product)) {
			        $sEanField = 'oxarticles__' . $amzConfig->sEanField;
			        $sXml .= '<StandardProductID>'.$this->nl;
			        	$sXml .= '<Type>EAN</Type>'.$this->nl;
			        	$sXml .= '<Value>'.$product->$sEanField->value.'</Value>'.$this->nl;
			        $sXml .= '</StandardProductID>'.$this->nl;
		        }
		        $sXml .= '<LaunchDate>'. date("Y-m-d")."T".date("H:i:s")."+01:00". '</LaunchDate>'.$this->nl;
	           // ProductTaxCode
	           // LaunchDate
               // DiscontinueDate
	           // ReleaseDate
	           // Condition ConditionType = 
	                   //[New, UsedLikeNew, UsedVeryGood, UsedGood, UsedAcceptable, CollectibleLikeNew, 
	                   //CollectibleVeryGood, CollectibleGood, CollectibleAcceptable, Refurbished, Club]
               
	           if(isset($amzConfig->sConditionTypeField)) {
	               $sConditionTypeField = $amzConfig->sConditionTypeField;
	           }
	           $sXml .= '<Condition>'.$this->nl;
	           $possibleConditions = array('New', 'UsedLikeNew', 'UsedVeryGood',
                               'UsedGood', 'UsedAcceptable', 'CollectibleLikeNew',
                               'CollectibleVeryGood', 'CollectibleGood', 'CollectibleAcceptable', 
                               'Refurbished', 'Club');
	           if(!empty($sConditionTypeField) && $sConditionTypeField != 'new' && isset($product->{'oxarticles__'.$sConditionTypeField})) {
	               $sConditionType =  $product->{'oxarticles__'.$sConditionTypeField}->value;
	               if(!in_array($sConditionType, $possibleConditions)) {
	                   unset($sConditionType);
	               }
	           }
	           if(!isset($sConditionType)) {
	               $sConditionType = '<ConditionType>New</ConditionType>'.$this->nl;
	           } 
	           $sXml .= $sConditionType;
	           
	           // Condition ConditionNote
               $sXml .= '</Condition>'.$this->nl;

	           // Rebate (RebateStartDate, RebateEndDate, RebateMessage, RebateName)
	           // ItemPackageQuantity
	           // NumberOfItems
	           
		       $sXml .= $this->_getDescriptionDataXml($product);
	           $sXml .= $this->_getProductDataXml($product);

			$sXml .= '</Product>';
		$sXml .= '</Message>';
		return $sXml;
	}
	
	protected function _getDescriptionDataXml($product) 
	{
	    $amzConfig = $this->_getAmzConfig();
	    
	    $sXml = '<DescriptionData>'.$this->nl;
        // DescriptionData -> Title
        $sXml .= $this->_getXmlIfExists('Title', $product->oxarticles__oxtitle->value).$this->nl;
        
        // DescriptionData -> Brand
        if(isset($amzConfig->sBrandField)) {
            if($amzConfig->sBrandField == 'oxvendorid' && ($oVendor = $product->getVendor())) {
                $brand = $oVendor->oxvendor__oxtitle->value;
            }
            elseif($amzConfig->sBrandField == 'oxmanufacturerid' && ($oManufacturer = $product->getManufacturer())) {
                $brand = $oManufacturer->oxmanufacturers__oxtitle->value;  
            }
            else {
                $brand = '';
            }
            $sXml .= $this->_getXmlIfExists('Brand', $brand).$this->nl;
        }
        // DescriptionData -> Designer
        // DescriptionData -> Description
        $description = $this->_getXmlIfExists('Description', strip_tags($product->oxarticles__oxlongdesc->value));
        if(strlen($description) == 0) {
            $description = $this->_getXmlIfExists('Description', strip_tags($product->oxarticles__oxshortdesc->value));
        }
        $sXml .= $description.$this->nl;
           
        // DescriptionData -> BulletPoint (max 5)
        $aBuletPoints = explode(',', $product->oxarticles__oxshortdesc->value, 6);
        if(isset($aBuletPoints[5])) {
           unset($aBuletPoints[5]);
        }
        foreach($aBuletPoints as $buletPoint) {
           $sXml .= $this->_getXmlIfExists('BulletPoint', $buletPoint).$this->nl;
        }
        // DescriptionData -> ItemDimensions (Length, Width, Height, Weight)
        if(
            $product->oxarticles__oxweight->value > 0
            || $product->oxarticles__oxlength->value > 0
            || $product->oxarticles__oxheight->value > 0
            || $product->oxarticles__oxwidth->value > 0
        ) {
            $sXml .= '<ItemDimensions>'.$this->nl;
            $sXml .= $this->_getXmlIfExists('Length', $product->oxarticles__oxlength->value, array('unitOfMeasure' => 'M')).$this->nl;
            $sXml .= $this->_getXmlIfExists('Width', $product->oxarticles__oxwidth->value,  array('unitOfMeasure' => 'M')).$this->nl;
            $sXml .= $this->_getXmlIfExists('Height', $product->oxarticles__oxheight->value,  array('unitOfMeasure' => 'M')).$this->nl;
            $sXml .= $this->_getXmlIfExists('Weight', $product->oxarticles__oxweight->value,  array('unitOfMeasure' => 'KG')).$this->nl;
            $sXml .= '</ItemDimensions>'.$this->nl;
            
        }
        // DescriptionData -> PackageDimensions (Length, Width, Height)
        // DescriptionData -> <PackageWeight unitOfMeasure="{GR|KG|OZ|LB}"></PackageWeight>
        // DescriptionData -> <ShippingWeight unitOfMeasure="{GR|KG|OZ|LB}"></PackageWeight>
        // DescriptionData -> MerchantCatalogNumber
        // DescriptionData -> <MSRP currency="{USD|GBP|EUR|JPY|CAD}"></MSRP>
        if(isset($product->oxarticles__oxtprice->value) && $product->oxarticles__oxtprice->value > 0) {
            $aCur = oxConfig::getInstance()->getCurrencyArray($this->getDestination()->az_amz_destinations__az_currency->value);
            foreach($aCur as $oCur) {
                if($oCur->selected == 1) {
                    break;
                }
            }
            $sXml .= $this->_getXmlIfExists('MSRP', number_format($product->oxarticles__oxtprice->value * $oCur->rate, 2, '.', ''), array('currency' =>  $oCur->name)).$this->nl;
        }
        
//        if(isset($product->oxarticles__oxtprice->value) && ) {
//            
//        }
        // DescriptionData -> MaxOrderQuantity 
        // DescriptionData -> SerialNumberRequired 
        // DescriptionData -> Prop65
        // DescriptionData -> <CPSIAWarning>choking_hazard_balloon|choking_hazard_contains_a_marble|choking_hazard_contains_small_ball|choking_hazard_is_a_marble|choking_hazard_is_a_small_ball|choking_hazard_small_parts|no_warning_applicable
        // DescriptionData -> CPSIAWarningDescription
        // DescriptionData -> LegalDisclaimer
        // DescriptionData -> Manufacturer

        if($amzConfig->sManufacturerField == 'oxvendorid' && ($oVendor = $product->getVendor())) {
            $manufacturer = $oVendor->oxvendor__oxtitle->value;
        }
        elseif($amzConfig->sManufacturerField == 'oxmanufacturerid' && ($oManufacturer = $product->getManufacturer())) {
            $manufacturer = $oManufacturer->oxmanufacturers__oxtitle->value;  
        }
        else {
            $manufacturer = '';
        }
        //$sXml .= $this->_getXmlIfExists('Brand', $manufacturer).$this->nl;
        $sXml .= $this->_getXmlIfExists('Manufacturer', $manufacturer).$this->nl;
        
        
        
        // BrowseNodes - at the moment not implemented, therefore dummy-function which can be overloaded by module
        $sXml .= $this->_getBrowseNodes($product).$this->nl;
        
           
        // DescriptionData -> MfrPartNumber
        // DescriptionData -> SearchTerms maxOccurs="5"
        $searchKeys = explode(' ', $product->oxarticles__oxsearchkeys->value, 6);
        
        for($i=0; $i<5 && isset($searchKeys[$i]); ++$i) {
            $sXml .= $this->_getXmlIfExists('SearchTerms', $searchKeys[$i]).$this->nl;
        }
        
        
        // DescriptionData -> PlatinumKeywords maxOccurs="20"
        // DescriptionData -> Memorabilia bool
        // DescriptionData -> Autographed bool
        // DescriptionData -> UsedFor maxOccurs="5"
        // DescriptionData -> ItemType
        // DescriptionData -> OtherItemAttributes maxOccurs="5"
        // DescriptionData -> TargetAudience maxOccurs="3"
        // DescriptionData -> SubjectContent maxOccurs="5"
        // DescriptionData -> IsGiftWrapAvailable bool
        // DescriptionData -> IsGiftMessageAvailable bool
        // DescriptionData -> PromotionKeywords maxOccurs="10"
        // DescriptionData -> IsDiscontinuedByManufacturer bool
        // DescriptionData -> DeliveryChannel= in_store|direct_ship
        // DescriptionData -> MaxAggregateShipQuantity
        // DescriptionData -> RecommendedBrowseNode integer
        // DescriptionData -> FEDAS_ID
        $sXml .= '</DescriptionData>'.$this->nl;
        
        return $sXml;
	}
	
	protected function _getBrowseNodes($product)
	{
		return "";
	}
		
	
	protected function _getProductDataXml(oxarticle $product)
	{
	    //TODO check this 
	    // az, 20100124: does not work with hidden categories
	    //$catId = $product->getCategory()->getId();
	    
	    ##TODO: what if article belongs to more then ONE category?
	    $aCatIds = $product->getCategoryIds();
	    $catId = $aCatIds[0];
	    
	    if (false !== ($theme = az_amz_theme::getCategoryTheme($catId))) {
	        return '<ProductData>'.$theme->getProductDataXml($product).'</ProductData>';
	    }
	    return '';
	}
	
//	/**
//	 * Recalculates hashes
//	 * 
//	 * @param array $aIds array of product ids to be updated
//	 * 
//	 * @return boolean 
//	 */
//	public function updateItems($aIds)
//	{
//		if ($aIds)
//		{
//			$oSnapshot = oxNew('az_amz_snapshot');
//			$oSnapshot->setDestination($this->getDestination());						
//			//$oSnapshot->markExportedItems($aIds, true, false, false);
//			$oSnapshot->markExportedItems($aIds, $this->_sAction);
//			return true;
		
//		}
//		
}