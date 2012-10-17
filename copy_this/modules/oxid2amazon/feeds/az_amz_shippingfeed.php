<?php

class Az_Amz_ShippingFeed extends Az_Amz_Feed
{
    /**
     * Feed name
     * 
     * @var string
     */ 
    protected $_sFeedName = 'Shipping feed';
    
    
    protected $_messageType = 'Override';
    
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
    protected $_sFileNameBase = 'override_feed';
    
    
    
    public function getUpdateXml($id)
    {
        $product = $this->_getProduct($id);
        $sXml = '<Message>';
            $sXml .= '<MessageID>' . (++$this->_messageId) . '</MessageID>';
            $sXml .= '<Override>'.$this->nl;
            	
            	$sSkuProp = $this->getSkuProperty();
                $sXml .= '<SKU>'. $product->$sSkuProp->value. '</SKU>';
                $sXml .= '<ShippingOverride>';
                    // ShipOption - string 
                    $sXml .= $this->_getXmlIfExists('ShipOption', $product->oxarticles__az_amz_ship_option->value);
                    // Type ("Additive"|"Exclusive")
                    $sXml .= $this->_getXmlIfExists('Type', $product->oxarticles__az_amz_ship_type->value);
                    // IsShippingRestricted
                    // TODO
                    // ShipAmount (currencyAmount)
                    $aCur = oxConfig::getInstance()->getCurrencyArray($this->getDestination()->az_amz_destinations__az_currency->value);
                    foreach($aCur as $oCur) {
                        if($oCur->selected == 1) {
                            break;
                        }
                    }
                    $sXml .= $this->_getXmlIfExists('ShipAmount', number_format($product->oxarticles__az_amz_ship_amount->value * $oCur->rate, 2, '.', ''), array('currency' =>  $oCur->name));
                $sXml .= '</ShippingOverride>';
            $sXml .= '</Override>';
        $sXml .= '</Message>';
        
        return $sXml;
    
    }

}