<?php

class Az_Amz_ProductImagesFeed extends Az_Amz_Feed
{
	/**
	 * Feed name
	 * 
	 * @var string
	 */	
	protected $_sFeedName = 'Product image feed';
	
	protected $_messageType = 'ProductImage';
	
	/**
	 * Feed Action
	 * 
	 * @var string
	 */	
	protected $_sAction = Az_Amz_Feed::TYPE_PRODUCT_IMAGES;
	
	/**
	 * File name base
	 * @var $_sFileNameBase
	 */
	protected $_sFileNameBase = 'productimage_feed';
	    
	
			
		
	
	public function getUpdateXml($id)
	{
		$oAmzConfig = $this->_getAmzConfig();
		
		$product = oxNew('oxarticle');
        $product->load($id);

		for ($i = 1 ; $i < 9 ; $i++)
		{
			$sConfigFieldName = 'sPicField'.$i;
			if (isset($oAmzConfig->$sConfigFieldName))
			{
				$sPicField = "oxarticles__".$oAmzConfig->$sConfigFieldName;
				
				if (isset($product->$sPicField->value) && !preg_match('/nopic.jpg$/', $product->$sPicField->value))
				{
					$sImageType 	= ($i == 1 ? 'Main' : 'PT'.($i-1));
					
					// checking what type of pic: oxpic or oxzoom
					if (stripos($sPicField, 'oxpic'))
					{
						$iImgIndex 		= (int)str_replace('oxarticles__oxpic', '', $sPicField);
						$sImageLocation = $product->getPictureUrl($iImgIndex);					
					}elseif(stripos($sPicField, 'oxzoom')) 
					{
						$iImgIndex 		= (int)str_replace('oxarticles__oxzoom', '', $sPicField);
						$sImageLocation = $product->getZoomPictureUrl($iImgIndex);					
					}
					
					$sSkuProp = $this->getSkuProperty();
					
					$sXml .= '<Message>'.$this->nl;
						$sXml .= '<MessageID>' . (++$this->_messageId) . '</MessageID>'.$this->nl;
						$sXml .= '<OperationType>Update</OperationType>'.$this->nl;
						$sXml .= '<ProductImage>'.$this->nl;				
					        $sXml .= '<SKU>'. $product->$sSkuProp->value. '</SKU>'.$this->nl;
					        $sXml .= '<ImageType>'.$sImageType.'</ImageType>'.$this->nl;					        
					        $sXml .= '<ImageLocation>'.$sImageLocation.'</ImageLocation>'.$this->nl;				           
						$sXml .= '</ProductImage>'.$this->nl;
					$sXml .= '</Message>'.$this->nl;
				}
			}
		}
				
		return $sXml;
	}
}