<?php

class az_amz_destinations_main extends oxAdminDetails
{
	protected $_sThisTemplate = 'az_amz_destinations_main.tpl';
	
	/**
     * Executes parent method parent::render() and creates az_amz_destination
     * object, which is passed to Smarty engine.
     *
     * @return string
     */
    public function render()
    {
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
            $sUrl = $myConfig->getConfigParam( 'sShopURL' );
            $aCrons = array(
            	'AZ_AMZ_EXPORT_PRODUCTS_CRON' 	=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=exportProducts&amp;destinationid='.$soxId,
                'AZ_AMZ_UPLOAD_PRODUCTS_CRON' 	=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=uploadProducts&amp;destinationid='.$soxId,
                'AZ_AMZ_EXPORT_IMAGES_CRON'		=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=exportProductImages&amp;destinationid='.$soxId,
                'AZ_AMZ_UPLOAD_IMAGES_CRON'		=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=uploadProductImages&amp;destinationid='.$soxId,
                'AZ_AMZ_EXPORT_PRICES_CRON'		=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=exportProductPrices&amp;destinationid='.$soxId,
                'AZ_AMZ_UPLOAD_PRICES_CRON'		=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=uploadProductPrices&amp;destinationid='.$soxId,
                'AZ_AMZ_EXPORT_INVENTORY_CRON'	=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=exportInventory&amp;destinationid='.$soxId,
                'AZ_AMZ_UPLOAD_INVENTORY_CRON'	=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=uploadInventory&amp;destinationid='.$soxId,
                'AZ_AMZ_EXPORT_SHIPPING_CRON'	=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=exportShipping&amp;destinationid='.$soxId,
                'AZ_AMZ_UPLOAD_SHIPPING_CRON'	=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=uploadShipping&amp;destinationid='.$soxId,
                'AZ_AMZ_EXPORT_RELATIONS_CRON'	=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=exportRelations&amp;destinationid='.$soxId,
                'AZ_AMZ_UPLOAD_RELATIONS_CRON'	=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=uploadRelations&amp;destinationid='.$soxId,
				'AZ_AMZ_DOWNLOAD_REPORTS_CRON'	=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=downloadOrderReports&amp;destinationid='.$soxId,
                'AZ_AMZ_IMPORT_ORDERS_CRON'		=> $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=importOrders&amp;destinationid='.$soxId,                
            );
            
            $this->_aViewData['aCrons'] = $aCrons;
            $this->_aViewData['sRemoveAllUrl'] = $sUrl . 'index.php?cl=az_amz_cron&amp;fnc=exportRemoveAll&amp;destinationid='.$soxId;                        
        }
        else {
            $this->_aViewData['aCrons'] = array();
        }
        
        // loading currencies            
        $this->_aViewData["aCurrencies"] 	= $myConfig->getCurrencyArray();
        $this->_aViewData["aLanguages"]		= oxLang::getInstance()->getLanguageNames();
        
        // fake parcels - later we will get it from amazon
        $oParcel1 = new stdClass();
        $oParcel1->id 	= 0;
        $oParcel1->name	= 'UPS';
        
        $oParcel2 = new stdClass();
        $oParcel2->id 	= 1;
        $oParcel2->name	= 'DHL';
        
        $aParcels = array('0'	=> $oParcel1,
        				  '1'	=> $oParcel2);
        				  
        $this->_aViewData["aParcels"]		= $aParcels;
        
        return $this->_sThisTemplate;
    }
    
     /**
     * Saves changed az_amz_destination parameters.
     *
     * @return mixed
     */
    public function save()
    {

        $soxId      = oxConfig::getParameter( "oxid");
        $aParams    = oxConfig::getParameter( "editval");
       
        $oAZDestination = oxNew( "az_amz_destination" );
        if ( $soxId != "-1")
            $oAZDestination->load( $soxId);
        else
            $aParams['az_amz_destinations__oxid'] = null;
        
        if ($aParams['az_amz_destinations__az_ftpdirectory'] == '')
        	$aParams['az_amz_destinations__az_ftpdirectory'] = '/';
        
        $oAZDestination->assign( $aParams);
        $oAZDestination->save();
        
        $oHistory = oxNew('az_amz_history');			
        $sHistoryMsg = 'Changes in "Destination" tab';
		$oHistory->addRecord($oAZDestination, 'save_main', $sHistoryMsg);
		
        // set oxid if inserted
        if ( $soxId == "-1")
            oxSession::setVar( "saved_oxid", $oAZDestination->az_amz_destinations__oxid->value);
    }
    
}