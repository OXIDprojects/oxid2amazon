<?php
class Az_Amz_RelationshipFeed extends Az_Amz_Feed
{
    /**
     * Feed name
     * 
     * @var string
     */ 
    protected $_sFeedName = 'Relationship feed';
    
    
    protected $_messageType = 'Relationship';
    
    /**
     * Feed Action
     * 
     * @var string
     */ 
    protected $_sAction = Az_Amz_Feed::TYPE_RELATION;
    
    /**
     * File name base
     * @var $_sFileNameBase
     */
    protected $_sFileNameBase = 'relationship_feed';
    
    /**
     * Return the message of the update operation to specified product id
     * @param string $id product id
     * @return string xml
     */
  	public function getUpdateXml($id)
	{
		$amzConfig 	= $this->_getAmzConfig();
		
		$product 	= $this->_getProduct($id);
		$aVariants 	= $this->_getVariants($id);
		$sSkuProp	= $this->getSkuProperty();
		
		if ($aVariants && sizeof($aVariants) > 0) {
			$sXml = '<Message>'.$this->nl;
				$sXml .= '<MessageID>' . (++$this->_messageId) . '</MessageID>'.$this->nl;			
				$sXml .= '<Relationship>'.$this->nl;
				$sXml .= '<ParentSKU>'.$product->$sSkuProp->value.'</ParentSKU>'.$this->nl;
										
				foreach($aVariants as $sVariantSKU) {
					
					$sXml .= '<Relation>'.$this->nl;
						$sXml .= '<SKU>'.$sVariantSKU.'</SKU>'.$this->nl;
						$sXml .= '<Type>Variation</Type>'.$this->nl;
					$sXml .= '</Relation>'.$this->nl;
				}
				$sXml .= '</Relationship>'.$this->nl;
			$sXml .= '</Message>'.$this->nl;
		}
		
		return $sXml;		
	}
	
	/**
	 * Returns list of variant ids
	 * @param string $sParentId Parent product Id
	 * 
	 * @return array
	 */
	protected function _getVariants($sParentId) 
	{
		$oSnapshot = oxNew('az_amz_snapshot');
		$oSnapshot->setDestinationId($this->getDestinationId());
		$aArtSKUs = $oSnapshot->getProductRelations($sParentId); 
		    	
    	return $aArtSKUs;		
	}
	
	public function getDeletedProductArtNums() 
	{
		$oSnapshot = oxNew('az_amz_snapshot');
		$oSnapshot->setDestinationId($this->getDestinationId());
		$aDeletedRelations = $oSnapshot->getDeletedVariantRelations();
		
		return $aDeletedRelations;				
	}
	
	/**
	 * Return delete message xml
	 * 
	 * @param string $id of the product
	 * @return string xml
	 */
	public function getDeleteXml($id)
	{	
		$sXml = '';
		if ($id && sizeof($id) == 2) {
			$sXml = '<Message>'.$this->nl;
				$sXml .= '<MessageID>' . (++$this->_messageId) . '</MessageID>'.$this->nl;
				$sXml .= '<OperationType>Delete</OperationType>'.$this->nl;
				$sXml .= '<Relationship>'.$this->nl;
					$sXml .= '<ParentSKU>'.$id[1].'</ParentSKU>'.$this->nl;
					$sXml .= '<Relation>'.$this->nl;
						$sXml .= '<SKU>'.$id[0].'</SKU>'.$this->nl;
						$sXml .= '<Type>Variation</Type>'.$this->nl;
					$sXml .= '</Relation>'.$this->nl;
				$sXml .= '</Relationship>'.$this->nl;
			$sXml .= '</Message>'.$this->nl;
		}
		
		return $sXml;
	}
}
?>
