<?php
class Az_Amz_InventoryFeed extends Az_Amz_Feed
{
    protected $_messageType = 'Inventory'; 
	
	/**
	 * Feed name
	 * 
	 * @var string
	 */	
	protected $_sFeedName = 'Pricing feed';
	
	/**
	 * Feed Action
	 * 
	 * @var string
	 */	
	protected $_sAction = Az_Amz_Feed::TYPE_INVENTORY;
	
	/**
	 * File name base
	 * @var $_sFileNameBase
	 */
	protected $_sFileNameBase = 'inventory_feed';
        
    
    public function getUpdateXml($id)
    {
        $product = $this->_getProduct($id);
        $iStockReserve = $product->oxarticles__az_amz_stock_reserve->value;
        if($iStockReserve <= 0) {
            $iStockReserve = $product->getCategory()->oxcategories__az_amz_stock_reserve->value;
            if($iStockReserve <= 0) {
                $iStockReserve = (int)$this->_getAmzConfig()->iDefaultStockReserve;
            }
        }
        $iStock = $product->oxarticles__oxstock->value - $iStockReserve;
        if($iStock < 0) {
            $iStock = 0;
        }
        
        $sSkuProp = $this->getSkuProperty();
        
        $sXml = '<Message>'.$this->nl;
            $sXml .= '<MessageID>' . (++$this->_messageId) . '</MessageID>'.$this->nl;
            $sXml .= '<Inventory>'.$this->nl;
                $sXml .= '<SKU>'. $product->$sSkuProp->value. '</SKU>'.$this->nl;
                $sXml .= $this->_getXmlIfExists('Quantity', $iStock);
            $sXml .= '</Inventory>'.$this->nl;
        $sXml .= '</Message>'.$this->nl;
                
        return $sXml;
    }
}