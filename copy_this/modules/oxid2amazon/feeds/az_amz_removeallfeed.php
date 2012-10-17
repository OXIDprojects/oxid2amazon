<?php


require_once dirname(__FILE__).DIRECTORY_SEPARATOR. 'az_amz_inventoryfeed.php';
class Az_Amz_RemoveAllFeed extends Az_Amz_InventoryFeed
{
    protected $_sFeedName = 'Remove all';
    /**
     * File name base
     * @var $_sFileNameBase
     */
    protected $_sFileNameBase = 'removeall_feed';
    /**
     * Feed Action
     * 
     * @var string
     */ 
    protected $_sAction = Az_Amz_Feed::TYPE_REMOVE_ALL;
    
    public function getChangedProductIds()
    {
        $oSnapshot = oxNew('az_amz_snapshot');
        $oSnapshot->setDestination($this->getDestination());
        return $oSnapshot->getAllProductArtNums();
        
    }
    
    public function updateItems()
    {
        return true;
    }
    
    public function _getProduct($sId)
    {
        $product = new stdClass;
        //##TODO - has oxartnum field to be changed here as well?
        $product->oxarticles__oxartnum = new stdClass;
        $product->oxarticles__oxartnum->value = $sId;
        $product->oxarticles__az_amz_stock_reserve = new stdClass;
        $product->oxarticles__az_amz_stock_reserve->value = 1;
        $product->oxarticles__oxstock = new stdClass;
        $product->oxarticles__oxstock->value = 0;
        return $product;
    }
}