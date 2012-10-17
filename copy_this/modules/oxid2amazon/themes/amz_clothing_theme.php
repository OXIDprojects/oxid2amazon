<?php
/**
 * Clothing theme
 * @link http://g-ecx.images-amazon.com/images/G/01/rainier/help/xsd/release_1_9/ProductClothing.xsd
 */
class amz_clothing_theme extends az_amz_theme
{
	protected $_sRootTag		= 'Clothing';
	
	protected $_sCategoryTag 	= 'ClassificationData';
	
	protected $_sCatType 		= 'string';
	
	protected $_aCategories = array(
		'Shirt',
		'Sweater',
		'Pants',
		'Shorts',
		'Skirt',
		'Dress',
		'Suit',
		'Blazer',
		'Outerwear',
		'SocksHosiery',
		'Underwear',
		'Bra',
		'Shoes',
		'Hat',
		'Bag',
		'Accessory',
		'Jewelry',
		'Sleepwear',
		'Swimwear',
		'PeronalBodyCare',
		'HomeAccessory',
		'NonApparelMisc',
		'Kimono',
		'Obi',
		'Chanchanko',
		'Jinbei',
		'Yukata'								
	);
	
	protected $_aVariationThemes = array(
		'Size',
		'Color',
		'SizeColor', 
	);
	
	
	protected $_sSubCategoryTag = null;
	
	protected $_aSubCategories = null;
	
	
	/**
 	 * Returns body of theme
 	 * @param oxarticle $oProduct Product object
 	 * 
 	 * @return string
 	 */
	protected function _getXmlBody($oProduct)
 	{ 		
 		$sXML .= $this->_getVariationXml($oProduct);
 		
 		$sXML .= '<'.$this->_sCategoryTag.'>'.$this->_sNewLine;
 			
 		$sXML .= '<ClothingType>'.$this->_sMappedCategory.'</ClothingType>'.$this->_sNewLine;
 		
 		//TODO: StyleKeywords could perhaps be filled more sensefull! 
 		$sXML .= $this->_getDepartment($oProduct); 		
 		$sXML .= '<StyleKeywords>'.$this->_sMappedCategory.'</StyleKeywords>'.$this->_sNewLine; 			
 			
 		$sXML .= '</'.$this->_sCategoryTag.'>'.$this->_sNewLine;
 		
 		return $sXML; 		
 	}
 	
 	/**
 	 * get department string
 	 *
 	 * @param obj $oProduct
 	 * @return str $sDepartmentContainer
 	 */
 	protected function _getDepartment($oProduct)
 	{
 		//TODO: Department could perhaps be filled more sensefull!
 		$sDepartment = 'DepartmentNotNull'; 		
 		$sDepartmentContainer = "<Department>$sDepartment</Department>".$this->_sNewLine;
 		
 		return $sDepartmentContainer;
 	}
 	
 	protected function _getVariationXml($oProduct) 
 	{ 		
 		$oAZConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
 		$sXML = '';
 		 
 		if ($oAZConfig->blAmazonExportVariants == '1' && $this->_sMappedVariation != '') {
			$sXML .= '<VariationData>'.$this->_sNewLine;
			$sParentage = ($oProduct->oxarticles__oxparentid->value != '' ? 'child' : 'parent');
			$sXML .= '<Parentage>'.$sParentage.'</Parentage>'.$this->_sNewLine;
			if ($sParentage == 'child') {
				$sVariationValue = $oProduct->oxarticles__oxvarselect->value;
				if ($sVariationValue == '') {
					$sVariationValue = 'undefined';
				}
				$sXML .= '<'.$this->_sMappedVariation.'>'.$sVariationValue.'</'.$this->_sMappedVariation.'>'.$this->_sNewLine;
			}
			$sXML .= '<VariationTheme>'.$this->_sMappedVariation.'</VariationTheme>'.$this->_sNewLine;			
			$sXML .= '</VariationData>'.$this->_sNewLine;
		}
		
		return $sXML;
 		
 	}
	
}
?>
