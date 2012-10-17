<?php

class az_amz_orders extends oxSuperCfg
{
	protected $_sSourceDir = "";
	protected $_aReportFileNames = array();
	protected $_sCurrentFile = null;
	protected $_oAZConfig = null;
	protected $_dMaxVatPercent = 19;
	protected $_dShippingcost = 0;
	protected $_blOrderDelete = false;
	
	public function __construct()
	{
		$this->_oAZConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
		
		return $ret;
	}	
	
	public function setSourceDir($sSourceDir = "")
	{
		$this->_sSourceDir = $sSourceDir;
	}
	
	public function setCurrentFileName($sFileName)
	{
		$this->_sCurrentFile = $sFileName;
	}
	
	public function readFiles()
	{
		$this->_readSourceDir();
		#var_dump($this->_aReportFileNames);
		foreach ($this->_aReportFileNames as $sFileName) {
			$this->setCurrentFileName($sFileName);
			$this->_parseFileContent();
			$this->_moveOrderReport($sFileName);
		}
	}
	
	protected function _moveOrderReport($sFileName)
	{
	    #echo __LINE__;
		if(!is_dir($this->_sSourceDir."/done"))
			mkdir($this->_sSourceDir."/done");
		rename($this->_sSourceDir."/$sFileName", $this->_sSourceDir."/done/$sFileName");
	}
	
	protected function _readSourceDir()
	{
		if ($handle = opendir($this->_sSourceDir)) {
		    while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != ".." && substr($file, 0, 1) != "." && !is_dir($this->_sSourceDir."/".$file)) {
		            $this->_aReportFileNames[] = $file;
		            //echo $file."<br>";
		        }
		    }
		    closedir($handle);
		}
	}
	
	protected function _parseFileContent()
	{
		$aValues = array();
		$xml = simplexml_load_file($this->_sSourceDir."/".$this->_sCurrentFile);
		
		
		//dumpVar($xml->Message[0]);
		
		foreach ($xml->Message as $oAmzOrder) {
			$this->_collectSingleOrderData($oAmzOrder);
		}
	}
	
	protected function _collectSingleOrderData($oAmzSingleOrderData)
	{
		//$oAmzOrderData		= $xml->Message->OrderReport;
		$oAmzOrderData		= $oAmzSingleOrderData->OrderReport; 
		$oAmzBillingData	= $oAmzOrderData->BillingData;
		$oAmzDeliveryData	= $oAmzOrderData->FulfillmentData;
		$oAmzItemData		= $oAmzOrderData->Item;
		
		if(empty($oAmzOrderData->AmazonOrderID))
			return false;
			
		$aValues = array();
		
		$aValues['AMZORDERID']		= $oAmzOrderData->AmazonOrderID;
		##TODO: shop-id handling in EE
		$aValues['OXSHOPID']		= oxConfig::getInstance()->getShopId();
		$aValues['AMZORDERDATE']	= $oAmzOrderData->OrderDate;
		$aValues['BUYEREMAIL']		= $oAmzBillingData->BuyerEmailAddress;
		$aValues['BUYERNAME']		= $oAmzBillingData->BuyerName;
		$aValues['BUYERPHONE']		= $oAmzBillingData->BuyerPhoneNumber;
		##TODO: what happens if there is a "company" name? probably then company is in field one and street in field two?
		if(empty($oAmzBillingData->Address->AddressFieldTwo))
			$aValues['BUYERSTREET']		= $oAmzBillingData->Address->AddressFieldOne;
		else {
			$aValues['BUYERCOMPANY']	= $oAmzBillingData->Address->AddressFieldOne;
			$aValues['BUYERSTREET']		= $oAmzBillingData->Address->AddressFieldTwo;
		}
		$aValues['BUYERCITY']		= $oAmzBillingData->Address->City;
		$aValues['BUYERZIP']		= $oAmzBillingData->Address->PostalCode;
		$aValues['BUYERCOUNTRYCODE']= $oAmzBillingData->Address->CountryCode;
		$aValues['DELSERVICELEVEL']	= $oAmzDeliveryData->FulfillmentServiceLevel;
		$aValues['DELNAME']			= $oAmzDeliveryData->Address->Name;
		
		if(empty($oAmzDeliveryData->Address->AddressFieldTwo))
			$aValues['DELSTREET']		= $oAmzDeliveryData->Address->AddressFieldOne;
		else {
			$aValues['DELCOMPANY']		= $oAmzDeliveryData->Address->AddressFieldOne;
			$aValues['DELSTREET']		= $oAmzDeliveryData->Address->AddressFieldTwo;
		}
		
		$aValues['DELCITY']			= $oAmzDeliveryData->Address->City;
		$aValues['DELZIP']			= $oAmzDeliveryData->Address->PostalCode;
		$aValues['DELCOUNTRYCODE']	= $oAmzDeliveryData->Address->CountryCode;
		$aValues['DELPHONE']		= $oAmzDeliveryData->Address->PhoneNumber;
		$aValues['DELSTATE']		= $oAmzDeliveryData->Address->StateOrRegion;
		$aValues['AMZFILENAME']		= $this->_sCurrentFile;
		
		$this->_saveTmpData($aValues, "az_amz_orders_tmp");
		
		//echo "ID: ".$oAmzOrderData->AmazonOrderID."<br>";
		//dumpVar($oAmzItemData);
		//echo "<br><br>---------------<br>";
		
		$this->_processItems($oAmzOrderData->AmazonOrderID, $oAmzItemData);
	}
	
	protected function _processItems($sAmzOrderId, $oAmazonItemData)
	{
		foreach ($oAmazonItemData as $oItem) {
			$this->_processSingleItem($sAmzOrderId, $oItem);
		}
	}
	
	protected function _processSingleItem($sAmzOrderId, $oItemData)
	{
		$aItemValues = array();
		$aItemValues['AMZORDERID']			= $sAmzOrderId;
		$aItemValues['AMZORDERITEMCODE']	= $oItemData->AmazonOrderItemCode;
		$aItemValues['AMZSKU']				= $oItemData->SKU;
		$aItemValues['AMZTITLE']			= $oItemData->Title;
		$aItemValues['AMZQUANTITY']			= $oItemData->Quantity;
		$aItemValues['AMZTAXCODE']			= $oItemData->ProductTaxCode;
		
		$aItemValues = $this->_getComponentValues($aItemValues, $oItemData->ItemPrice->Component);
		
		$this->_saveTmpData($aItemValues, "az_amz_orderitems_tmp");
	}
	
	protected function _getComponentValues($aItemValues, $aComponent)
	{
		foreach ($aComponent as $oPrice) {
			switch ($oPrice->Type) {
				case "Principal":
					$aItemValues['AMZARTPRICE'] = $oPrice->Amount;
					break;
				case "Shipping":
					$aItemValues['AMZSHIPPRICE'] = $oPrice->Amount;
					break;
				case "Tax":
					$aItemValues['AMZARTTAX'] = $oPrice->Amount;
					break;
				case "ShippingTax":
					$aItemValues['AMZSHIPTAX'] = $oPrice->Amount;
					break;
			
				default:
					break;
			}
		}
		return $aItemValues;
	}
	
	protected function _saveTmpData($aValues, $sTable)
	{
		$sInsert = "insert into $sTable set ";
		
		foreach ($aValues as $sField=>$sValue) {
			$aPart[] = "$sField = '".utf8_decode($sValue)."'";
		}
		$sInsert .= implode(", ", $aPart);
		$sInsert .= ", AZTIMESTAMP = '".date("Y-m-d H:i:s")."'";
		//echo $sInsert."<br>";
		$rs = oxDb::getDb()->Execute($sInsert);
	}
	
	public function importOrders()
	{
		$aOrders = $this->_getOrders();
		foreach ($aOrders as $aOrder) {
			$oUser = $this->_getOrderUser($aOrder);
			$oAdress = $this->_getDelAdresse($aOrder);
			$oOrder = oxNew("oxorder");
			$oOrder->oxorder__amzorderid->value		= $aOrder['AMZORDERID'];
			$oOrder->oxorder__oxshopid->value		= $aOrder['OXSHOPID'];
			$oOrder->oxorder__oxuserid->value		= $oUser->getId();
			$oOrder->oxorder__oxbillemail->value	= $aOrder['BUYEREMAIL'];
			$oOrder->oxorder__oxbilllname->value	= $aOrder['BUYERNAME'];
			$oOrder->oxorder__oxbillstreet->value	= $aOrder['BUYERSTREET'];
			$oOrder->oxorder__oxbillcity->value		= $aOrder['BUYERCITY'];
			$oOrder->oxorder__oxbillzip->value		= $aOrder['BUYERZIP'];
			$oOrder->oxorder__oxbillcountryid->value= $this->_getUserCountry($aOrder['BUYERCOUNTRYCODE']);
			$oOrder->oxorder__oxbillfon->value		= $aOrder['BUYERPHONE'];
			$oOrder->oxorder__oxdellname->value		= $aOrder['DELNAME'];
			$oOrder->oxorder__oxdelcompany->value	= $aOrder['DELCOMPANY'];
			$oOrder->oxorder__oxdelstreet->value	= $aOrder['DELSTREET'];
			$oOrder->oxorder__oxdelcity->value		= $aOrder['DELCITY'];
			$oOrder->oxorder__oxdelzip->value		= $aOrder['DELZIP'];
			$oOrder->oxorder__oxdelcountryid->value	= $this->_getUserCountry($aOrder['DELCOUNTRYCODE']);
			$oOrder->oxorder__oxdelfon->value		= $aOrder['DELPHONE'];
			$oOrder->oxorder__oxfolder->value		= "ORDERFOLDER_NEW";
			$oOrder->oxorder__oxdeltype->value		= $this->_getAmazonShipSet($aOrder['DELSERVICELEVEL']);
			$oOrder->oxorder__oxpaymenttype->value	= $this->_getAmazonPayment();
			$oOrder->oxorder__oxcurrency->value		= "EUR";
			$oOrder->oxorder__oxcurrate->value		= 1;
			$oOrder->oxorder__oxtransstatus->value	= "OK";
			$oOrder->save();
			
			$sOrderId = $oOrder->getId();
			
			$dArticleSum = $this->_saveOrderArticles($aOrder['AMZORDERID'], $sOrderId);
			
			// if there are any errors on inserting order articles $this->_blOrderDelete will be set to true
			// this means: delete order and return immediately, do not set order sum - cause order is saved again there
			// DOC: such deleted orders stay in tmp tables with azprocessed flag = 0. they could be imported later.
			// TODO: clean up tmp tables
			if($this->_blOrderDelete) {
				$this->_deleteOrder($sOrderId);
				return ;
			}
			$this->_setOrderSum($oOrder, $dArticleSum);
			
			$this->_setOrderDone($aOrder['AMZORDERID']);
		}
	}
	
	protected function _deleteOrder($sOrderId)
	{
		oxDb::getDb()->Execute("delete from oxorder where oxid = '$sOrderId'");
		oxDb::getDb()->Execute("delete from oxorderarticles where oxorderid = '$sOrderId'");
		$this->_blOrderDelete = false;
	}
	
	protected function _setOrderDone($sAmzOrderId)
	{
		$rs = oxDb::getDb()->Execute("update az_amz_orders_tmp set dateofimport = '".date("Y-m-d H:i:s")."', azprocessed = '1' where amzorderid = '$sAmzOrderId'");
	}
	
	// TODO: check if this works in EE 2.7
	protected function _setOrderSum($oOrder, $dArticleSum)
	{
		$oOrder->oxorder__oxtotalbrutsum->value	= $dArticleSum;
		$aVatValues								= $this->_getNetPrice($dArticleSum, $this->_dMaxVatPercent);
		$oOrder->oxorder__oxtotalnetsum->value	= $aVatValues['dNetPrice'];
		$oOrder->oxorder__oxtotalordersum->value= $dArticleSum + $this->_dShippingcost;
		$oOrder->oxorder__oxdelcost->value		= $this->_dShippingcost;
		$oOrder->save();
	}
	
	protected function _getAmazonPayment()
	{
		return $this->_oAZConfig->sAmazonPayment;
	}
	
	protected function _getAmazonShipSet($sAmzServiceLevel)
	{
		switch ($sAmzServiceLevel) {
			case "Standard":
				return $this->_oAZConfig->sAmazonShippingStandard;
				break;
			case "Expedited":
				return $this->_oAZConfig->sAmazonShippingExpress;
				break;
			default:
				return $this->_oAZConfig->sAmazonShippingStandard;
				break;
		}
	}
	
	protected function _saveOrderArticles($sAmzOrderId, $sOrderId)
	{
		$dArticleSum = 0;
		$this->_dShippingcost = 0;
		$aVatPercent = array();
		
		$aOrderItems = $this->_getOrderArticles($sAmzOrderId);
		//dumpVar($aOrderItems);
		foreach ($aOrderItems as $aOrderItem) {
			$oOrderArticle = oxNew("oxorderarticle");
			$oArticle = $this->_getOrderArticle($aOrderItem['AMZSKU']);
			
			if(!$oArticle) {
				//die('article with SKU ' . $aOrderItem['AMZSKU'] . ' not found');
				$this->_oAZConfig->logError("order import error: article with SKU " . $aOrderItem['AMZSKU'] . " not found in article table\n");
				$this->_blOrderDelete = true;
				continue;
			}
			
			if($oArticle->oxarticles__oxvat->value > 0)
				$dVatPercent = $oArticle->oxarticles__oxvat->value;
			else 
				$dVatPercent = $this->getConfig()->getShopConfVar('dDefaultVAT');
				
			$aNetValues = $this->_getNetPrice($aOrderItem['AMZARTPRICE'], $dVatPercent);
			if(!in_array($dVatPercent, $aVatPercent))
				$aVatPercent[] = $dVatPercent;
				
			$oOrderArticle->oxorderarticles__oxartid->value		= $oArticle->getId();
			$oOrderArticle->oxorderarticles__oxartnum->value	= $oArticle->oxarticles__oxartnum->value;
			$oOrderArticle->oxorderarticles__oxtitle->value		= $oArticle->oxarticles__oxtitle->value;
			$oOrderArticle->oxorderarticles__oxshortdesc->value	= $oArticle->oxarticles__oxshortdesc->value;
			$oOrderArticle->oxorderarticles__oxselvariant->value= $oArticle->oxarticles__oxvarselect->value;
			
			$oOrderArticle->oxorderarticles__oxorderid->value	= $sOrderId;
			$oOrderArticle->oxorderarticles__oxamount->value	= $aOrderItem['AMZQUANTITY'];
			$oOrderArticle->oxorderarticles__oxnetprice->value	= $aNetValues['dNetPrice'];
			$oOrderArticle->oxorderarticles__oxbrutprice->value	= $aOrderItem['AMZARTPRICE'];
			$dArticleSum += $aOrderItem['AMZARTPRICE'];
			$oOrderArticle->oxorderarticles__oxvatprice->value	= $aNetValues['dVatPrice'];
			$this->_dShippingcost += $aOrderItem['AMZSHIPPRICE'];
			
/** Add D3 MG/TD START 2011_05_30  **/
/* Artikel in oxorderarticles werden sonst "sporadisch ohne Vat und oxprice gespeichert"*/
			$oOrderArticle->oxorderarticles__oxvat->value		= $dVatPercent;			
			$oOrderArticle->oxorderarticles__oxprice->value		= $aOrderItem['AMZARTPRICE'] / $aOrderItem['AMZQUANTITY'];			
			$oOrderArticle->oxorderarticles__oxbprice->value	= $aOrderItem['AMZARTPRICE'] / $aOrderItem['AMZQUANTITY'];		
			$oOrderArticle->oxorderarticles__oxnprice->value	= $aNetValues['dNetPrice'] / $aOrderItem['AMZQUANTITY'];
/** Add D3 MG/TD  **/				
			
			$oOrderArticle->save();
			
			// TODO for EE 2.7: write alternative function for updateArticleStock
			$oOrderArticle->updateArticleStock( $oOrderArticle->oxorderarticles__oxamount->value * (-1), $this->getConfig()->getConfigParam( 'blAllowNegativeStock' ) );
		}
		
/* ADD D3 MG 2011_11_28 bricht sonst ab */
        if($this->_blOrderDelete == true)			    
            return 0;
		
		$this->_dMaxVatPercent = max($aVatPercent);
		
		return $dArticleSum;
	}
	
	protected function _getNetPrice($dBrutPrice, $dVatPercent)
	{
		$dVat = round($dBrutPrice - ($dBrutPrice * 100 / ($dVatPercent + 100)), 12);
		$dNetPrice = $dBrutPrice - $dVat;
		$aNetValues['dVatPrice']	= $dVat;
		$aNetValues['dNetPrice']	= $dNetPrice;
		
		return $aNetValues;
	}
	
	
	
	protected function _getOrderArticle($sAmzSku)
	{
		$sSkuField = $this->_oAZConfig->sSkuField;
		
		$sArtOxid = oxDb::getDb()->getOne("select oxid from oxarticles where $sSkuField = '$sAmzSku'");
		if(!empty($sArtOxid)) {
			$oArticle = oxNew("oxarticle");
			$oArticle->load($sArtOxid);
			return $oArticle;
		} else {
			return null;
		}
		
	}
	
	protected function _getOrderArticles($sAmzOrderId)
	{
		$sSelect = "select * from az_amz_orderitems_tmp where amzorderid = '$sAmzOrderId'";
		$aOrderItems = oxDb::getDb(true)->getAll($sSelect);
		return $aOrderItems;
	}
	
	protected function _getOrderUser($aAmzOrder)
	{
		$sUserId = oxDb::getDb()->getOne("select oxid from oxuser where oxusername = '".$aAmzOrder['BUYEREMAIL']."'");
		if(empty($sUserId)) {
			$oUser = oxNew("oxuser");
			// TODO EE 2.7: oxactive -> oxactiv
			$oUser->oxuser__oxactive->value		= '1';
			$oUser->oxuser__oxusername->value	= $aAmzOrder['BUYEREMAIL'];
			$oUser->oxuser__oxlname->value		= $aAmzOrder['BUYERNAME'];
			$oUser->oxuser__oxfon->value		= $aAmzOrder['BUYERPHONE'];
			$oUser->oxuser__oxstreet->value		= $aAmzOrder['BUYERSTREET'];
			$oUser->oxuser__oxcity->value		= $aAmzOrder['BUYERCITY'];
			$oUser->oxuser__oxzip->value		= $aAmzOrder['BUYERZIP'];
			$oUser->oxuser__oxcountryid->value	= $this->_getUserCountry($aAmzOrder['BUYERCOUNTRYCODE']);
			$oUser->save();
		} else {
			$oUser = oxNew("oxuser");
			$oUser->load($sUserId);
		}
		return $oUser;
	}
	
	/**
	 * Add item to oxadresse
	 *
	 * @param array $aAmzOrder
	 * @return object
	 */
	protected function _getDelAdresse($aAmzOrder)
	{           
		$sUserId = oxDb::getDb()->getOne("select oxid from oxuser where oxusername = '".$aAmzOrder['BUYEREMAIL']."'");
		
		$oAddress = oxNew("oxaddress");		
		$sAdressOxid = $this->_GetAdressOxid($sUserId);
		
		if(!$oAddress->load($sAdressOxid))
		{			
			$oAddress->oxaddress__oxfname	=	oxnew('oxfield', '');
			$oAddress->oxaddress__oxlname	=	oxnew('oxfield', $aAmzOrder['DELNAME']);
			$oAddress->oxaddress__oxsal		=	oxnew('oxfield','');
			$oAddress->oxaddress__oxfon		=	oxnew('oxfield',$aAmzOrder['DELPHONE']);
			$oAddress->oxaddress__oxcompany	=	oxnew('oxfield',$aAmzOrder['DELCOMPANY']);
			$oAddress->oxaddress__oxstreet	=	oxnew('oxfield',$aAmzOrder['DELSTREET']);
			$oAddress->oxaddress__oxcity	=	oxnew('oxfield',$aAmzOrder['DELCITY']);
			$oAddress->oxaddress__oxzip		=	oxnew('oxfield',$aAmzOrder['DELZIP']);
			$oAddress->oxaddress__oxaddinfo	=	oxnew('oxfield','');
			$oAddress->oxaddress__oxstateid	=	oxnew('oxfield','');
			$oAddress->oxaddress__oxcountryid	=	oxnew('oxfield',$this->_getUserCountry($aAmzOrder['DELCOUNTRYCODE']));					
			$oAddress->save();
			
		} else 
		{			
			$oAddress->load($sAdressOxid);
		}
		return $oAddress;           
	}
	
	protected function _GetAdressOxid($sUserId)
	{
		$oDB = oxDb::getDb();
		$sTableoxaddress =getViewName('oxaddress');
		$sQuery ="SELECT oxid FROM ".$sTableoxaddress." WHERE oxuserid =".$oDB->quote($sUserId);		
		return $oDB->getOne( $sQuery );
	}		
	
	protected function _getUserCountry($sCountryCode)
	{
		$sCountryId = oxDb::getDb()->GetOne("select oxid from oxcountry where oxisoalpha2 = '$sCountryCode'");
    	return $sCountryId;
	}
	
	protected function _getOrders($blDone = false)
	{
		$sSelect = "select * from az_amz_orders_tmp where azprocessed = '0'";
		if($blDone)
			$sSelect .= " and dateofimport > 0";
			
		$aOrders = oxDb::getDb(true)->GetAll($sSelect);
		return $aOrders;
	}
	
	public function deleteFilesFromAMTU($sDestinationId)
	{
		$oDestination = & oxNew('az_amz_destination');
		$oDestination->load($sDestinationId);
		
		$oFtp = oxNew('az_amz_ftp');
		$blSuccess = $oFtp->connect($oDestination->az_amz_destinations__az_server->value,
									$oDestination->az_amz_destinations__az_ftpuser->value,
									$oDestination->az_amz_destinations__az_ftppassword->value,
									$oDestination->az_amz_destinations__az_ftppassivemode->value
									);
		if($blSuccess) {
			$aOrdersDone = $this->_getOrders(true);
			dumpVar($aOrdersDone);
			foreach ($aOrdersDone as $aOrder) {
				$blOk = $oFtp->deleteFile($aOrder['AMZFILENAME'], $oDestination->az_amz_destinations__az_reportsdirectory->value);
				if($blOk) {
				    echo __LINE__;
					$rs = oxDb::getDb()->Execute("update az_amz_orders_tmp set azprocessed = '1' where amzfilename = '".$aOrder['AMZFILENAME']."'");
				}
			}
		}
	}
}