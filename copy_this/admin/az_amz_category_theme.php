<?php

class az_amz_category_theme extends oxAdminDetails
{
	
	public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oCategory = oxNew( 'oxcategory' );

        $soxId = oxConfig::getParameter( "oxid");
        // check if we right now saved a new entry
        $sSavedID = oxConfig::getParameter( "saved_oxid");
        if ( ($soxId == "-1" || !isset( $soxId)) && isset( $sSavedID) ) {
            $soxId = $sSavedID;
            oxSession::deleteVar( "saved_oxid");
            $this->_aViewData["oxid"] =  $soxId;
            // for reloading upper frame
            $this->_aViewData["updatelist"] =  "1";
        }

		$oAzConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
		
		$aThemeData = oxConfig::getParameter('aAmazon');
		             
        if (!$aThemeData)            		    	
    		$aThemeData = az_amz_theme::getOxidCategoryMapping($soxId);
        
    	
		$this->_aViewData['aCatThemeData']		= $aThemeData;
		    	    	        
        $this->_aViewData['aAmazonThemes']	= $oAzConfig->getAmazonThemes();
        
        if (isset($aThemeData['theme']) && $aThemeData['theme'] != '')
        {
        	$oTheme = az_amz_theme::newAmzTheme($aThemeData['theme']);
        	
        	$this->_aViewData['aAmazonThemeCategories'] = $oTheme->getCategories();
        	
        	$this->_aViewData['aAmazonThemeSubCategories'] = $oTheme->getSubCategories();
			
			if ($oAzConfig->blAmazonExportVariants) {
				$this->_aViewData['aAmazonVariationThemes']	= $oTheme->getVariationThemes();
			}        	        	
        }
        	
        return 'az_amz_category_theme.tpl';
    }
    
    public function save()
    {
    	$soxId 		= oxConfig::getParameter('oxid');
    	$aThemeData = oxConfig::getParameter('aAmazon');
    	
    	az_amz_theme::setCategoryTheme($soxId, $aThemeData);
    	
    }
    
}
?>
