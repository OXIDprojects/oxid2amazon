<?php

class az_amz_destinations_prodselector extends oxAdminDetails
{
	protected $_sThisTemplate = 'az_amz_destinations_prodselector.tpl';
	
	protected $_aOperators	= null;
	
	protected $_aFieldTranslations = null;
	
	protected $_oAzConfig	= null;
									
	protected $_iMaxFields	= 10;	
	
	
	public function init() {
		
		parent::init();
		
		$oAzConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
		
		$this->_oAzConfig  = $oAzConfig;
		
		$this->_aOperators 			= $oAzConfig->aOperators;
		$this->_aFieldTranslations	= $oAzConfig->aFilterFieldTranslations;
	}	
	/**
     * Executes parent method parent::render()
     *
     * @return string
     */
	public function render() {
		parent::render();
		
		$myConfig = $this->getConfig();
    	
    	$soxId = $myConfig->getParameter( "oxid");
    	
        // check if we right now saved a new entry
        $sSavedID = $myConfig->getParameter( "saved_oxid");
        if ( ($soxId == "-1" || !isset( $soxId)) && isset( $sSavedID) ) {
            $soxId = $sSavedID;
            oxSession::deleteVar( "saved_oxid");
            $this->_aViewData["oxid"] =  $soxId;
            // for reloading upper frame
            $this->_aViewData["updatelist"] =  "1";
        }

        if ( $soxId != "-1" && isset( $soxId)) {
            // loading destination 
            $oAZDestination = oxNew( "az_amz_destination" );
            $oAZDestination->load( $soxId);
            $this->_aViewData["edit"] =  $oAZDestination;
            
            if ($oAZDestination->az_amz_destinations__az_productselector->value)
            	$aFilter = unserialize($oAZDestination->az_amz_destinations__az_productselector->getRawValue());
                        
           	if (!isset($aFilter['categories'])){
           		           	
           		$aFilter['categories'] = array();
           	}
           	           	           		
           	$aFilter = $this->_processFilter($aFilter);	
           	           	
            $this->_aViewData["aFilter"] 	= $aFilter;
            $this->_aViewData["iMaxFields"]	= $this->_iMaxFields;
        
        }   
                
        // parent categorie tree
        $oCatTree = oxNew( "oxCategoryList" );
        $oCatTree->buildList($myConfig->getConfigParam( 'bl_perfLoadCatTree' ));        
        $this->_aViewData["cattree"] =  $oCatTree;
        
        // oxarticles fields
        $oArticle = oxNew('oxarticle');
    	$aFields = explode(', ', $oArticle->getSelectFields());
    
    	$this->_aViewData['aArticleFields'] = $aFields;
    	
    	// operations
    	$this->_aViewData['aOperators'] = $this->_aOperators;
    	
    	// field translations    	
    	$this->_aViewData['aFieldTranslations']	= $this->_aFieldTranslations;
    	
    	// preview mode
    	$blPreview = oxConfig::getParameter("preview");
    	if ( $blPreview == 1 ) {

			$oSnapShot = oxNew('az_amz_snapshot');
			$oSnapShot->setDestinationId($soxId);
			
			$aArticles = $oSnapShot->getPreviewArticles();			
			$this->_aViewData['aArticles']	= $aArticles;
			$this->_aViewData['blExportVariants'] = ($this->_oAzConfig->blAmazonExportVariants == '1' ? true : false);						            
            return "popups/az_amz_products_preview.tpl";
        } 
    	
    	    	
                
        return $this->_sThisTemplate;
	}
	
	/**
     * Saves changed az_amz_destination parameters.
     *
     * @return mixed
     */
	public function save()
	{
		$myConfig = $this->getConfig();
		$aFilter = $myConfig->getParameter('aFilter');
		   
		// correction for first element
		if (is_array($aFilter['fields']) && isset($aFilter['fields']['operator']))
			$aFilter['fields'] = array($aFilter['fields']);
			
		$aFilter = $this->_cleanFilter($aFilter);
			
		$soxId      = oxConfig::getParameter( "oxid");
        $aParams    = oxConfig::getParameter( "editval");
       
        $oAZDestination = oxNew( "az_amz_destination" );
        if ( $soxId != "-1")
            $oAZDestination->load( $soxId);
        else
            $aParams['az_amz_destinations__oxid'] = null;
                
        $oAZDestination->assign( $aParams);
        $oAZDestination->az_amz_destinations__az_productselector->setValue(serialize($aFilter));
        $oAZDestination->save();
        
        $oHistory = oxNew('az_amz_history');			
        $sHistoryMsg = 'Changes in "Product selector" tab';
		$oHistory->addRecord($oAZDestination, 'save_selector', $sHistoryMsg);
       
        // set oxid if inserted
        if ( $soxId == "-1")
            oxSession::setVar( "saved_oxid", $oAZDestination->az_amz_destinations__oxid->value);
	
	}
	
	/**
	 * Do snapshot
	 */
	public function doSnapshot()
	{
		$soxId      = oxConfig::getParameter( "oxid");
		
		$oSnapshot = oxNew('az_amz_snapshot');
		
		
		$oSnapshot->setDestinationId($soxId);
		$oSnapshot->doSnapshot();
	}
	
	/**
	 * Clenas empty filter values
	 * @param array $aFilter Filter
	 * 
	 * @return array
	 */
	protected function _cleanFilter($aFilter)
	{
		$oAzConfig = $this->_oAzConfig;
		// fields
		$aCleanFields = array();
		foreach($aFilter['fields'] as $aField) {
			
			if ($aField['delete'] == '1') {
				continue;
			}
			
			// we don`t need that field anymore
			unset($aField['delete']);
			
			$blRequired = $oAzConfig->isRequiredOperatorValue($aField['operator']);
			 			
			if ($aField['value'] != '' && $blRequired) {
				
				$aCleanFields[] = $aField;
				
			} elseif (!$blRequired) {
				
				unset($aField['value']);
				$aCleanFields[] = $aField;
			}
		}
						
		$aFilter['fields'] = $aCleanFields;
				
		return $aFilter;
	}
	
	/**
	 * Makes changes/additions to filter
	 * @param array $aFilter
	 * 
	 * @return array
	 */
	protected function _processFilter($aFilter) {
		
		// sets which fields has required value
		if (isset($aFilter['fields']) && is_array($aFilter['fields'])) {
			
			foreach($aFilter['fields'] as $sKey => $aField) {
				
				$blRequired = false;
				foreach($this->_aOperators as $oper) {
					
					if($oper['operator'] == $aField['operator']) {
						$blRequired = $oper['value_required'];
						break;
					}
				}
				$aFilter['fields'][$sKey]['req'] = $blRequired;
			}
		}
		
		return $aFilter;
	}
	
	
	
}