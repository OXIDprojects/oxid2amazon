<?php
  
 abstract class az_amz_theme
 {
 	/**
 	 * Root theme tag
 	 * @var string
 	 */
 	protected $_sRootTag		= null;
	
	/**
	 * Category tag
	 * @var string
	 */
	protected $_sCategoryTag 	= null;
	
	/**
	 * Describes type of values:
	 *  choice - <CategoryTag><ItemFromCategories></ItemFromCategories></CategoryTag> 
	 *  string - <CategoryTag>ItemFromCategories</CategoryTag>
	 * 
     * @var string type of category values
     */
	protected $_sCatType = null;
	
	/**
	 * List of categories
	 * 
	 * @var array
	 */
	protected $_aCategories = null;
	
	/**
	 * Sub-category tag
	 * 
	 * @var string
	 */	
	protected $_sSubCategoryTag = null;
	
	/**
	 * List of sub-categories
	 * 
	 * @var array
	 */
	protected $_aSubCategories = null;
	
	/**
	 * Theme category assigned to oxid category
	 */
	protected $_sMappedCategory = null;
	
	/**
	 * Theme sub category assigned to oxid category
	 */
	protected $_sMappedSubCategory = null;
	
	/**
	 * Theme variation assigned to oxid category
	 */
	protected $_sMappedVariation = null;
	
	protected $_sNewLine = "\r\n";
	
	
 	/**
 	 * Factory for themees by theme name
 	 * @param string $sTheme
 	 * @return az_amz_theme
 	 */
 	static function newAmzTheme($sTheme)
 	{
	 	$sThemeClass 	= 'amz_'.strtolower($sTheme).'_theme';
		$sThemeFile 	= $sThemeClass.'.php';
	
		// TODO: path to module has to be dynamic, put in config
		require_once(getShopBasePath()."/modules/oxid2amazon/themes/". $sThemeFile);
		
		if (class_exists($sThemeClass))
		{
			$oTheme = new $sThemeClass;
			
			return $oTheme;
		}
		
		return false;
	}
 	
 	/**
 	 * Returns root start tag
 	 * 
 	 * @return string
 	 */
 	protected function _getRootStartTag()
 	{ 		
 		if (!$this->_sRootTag)
 			return '';
 			
 		return '<'.$this->_sRootTag.'>'.$this->_sNewLine;
 	}
 	
 	/**
 	 * Returns root end tag
 	 * 
 	 * @return string
 	 */
 	protected function _getRootEndTag()
 	{ 		 	
 		if (!$this->_sRootTag)
 			return '';
 				
 		return '</'.$this->_sRootTag.'>'.$this->_sNewLine;
 	}
 	
 	protected function _getVariationXml($oProduct) {
 		
 		$oAZConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
 		$sXML = '';
 		if ($oAZConfig->blAmazonExportVariants == '1' && $this->_sMappedVariation != '') {
			$sXML .= '<VariationData>'.$this->_sNewLine;
			$sParentage = ($oProduct->oxarticles__oxparentid->value != '' ? 'child' : 'parent');
			$sXML .= '<Parentage>'.$sParentage.'</Parentage>'.$this->_sNewLine;
			$sXML .= '<VariationTheme>'.$this->_sMappedVariation.'</VariationTheme>'.$this->_sNewLine;
			if ($sParentage == 'child') {
				$sVariationValue = $oProduct->oxarticles__oxvarselect->value;
				if ($sVariationValue == '') {
					$sVariationValue = 'undefined';
				}
				$sXML .= '<'.$this->_sMappedVariation.'>'.$sVariationValue.'</'.$this->_sMappedVariation.'>'.$this->_sNewLine;
			}
			$sXML .= '</VariationData>'.$this->_sNewLine;
		}
		
		return $sXML;
 		
 	}
 	
 	/**
 	 * Returns body of theme
 	 * @param oxarticle $oProduct Product object
 	 * 
 	 * @return string
 	 */
 	protected function _getXmlBody($oProduct)
 	{
 		$sXML = '';
 		$oAZConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
 		// category
 		if ($this->_sMappedCategory && is_array($this->_aCategories) && in_array($this->_sMappedCategory, $this->_aCategories))
 		{
 			$sXML .= '<'.$this->_sCategoryTag.'>'.($this->_sCatType == 'choice' ? $this->_sNewLine : '');
 			
 			if ($this->_sCatType == 'choice')
 			{
 				$sXML .= '<'.$this->_sMappedCategory . '>';
 				$sXML .= '</'.$this->_sMappedCategory . '>'.$this->_sNewLine;
 			}elseif ($this->_sCatType == 'string')
 			{
 				$sXML .= $this->_sMappedCategory;
 			} 			
 			$sXML .= '</'.$this->_sCategoryTag.'>'.$this->_sNewLine;
 			
 			$sXML .= $this->_getVariationXml($oProduct); 			
 		} 	
 		
 		//subcategory
 		if ($this->_sMappedSubCategory && is_array($this->_aSubCategories) && in_array($this->_sMappedSubCategory, $this->_aSubCategories))
 		{
 			$sXML .= '<'.$this->_sSubCategoryTag.'>'.($this->_sSubCatType == 'choice' ? $this->_sNewLine : '');
 			
 			if ($this->_sSubCatType == 'choice')
 			{
 				$sXML .= '<'.$this->_sMappedSubCategory . '>'; 				 				 				
 				 				
 				$sXML .= '</'.$this->_sMappedSubCategory . '>'.$this->_sNewLine;
 			}elseif ($this->_sSubCatType == 'string')
 			{
 				$sXML .= $this->_sMappedSubCategory;
 			}
 			
 			$sXML .= '</'.$this->_sSubCategoryTag.'>'.$this->_sNewLine;
 			
 		}
 			 	
 		return $sXML;
 	}
 	 	
 	
 	/**
 	 * Returns xml of <ProductData>
 	 * @param oxarticle $oProduct Product object
 	 * 
 	 * @return string
 	 */
 	public function getProductDataXml($oProduct)
 	{
 		$sXML  = $this->_getRootStartTag();
 		
 		$sXML .= $this->_getXmlBody($oProduct);
 		
 		$sXML .= $this->_getRootEndTag();
 		
 		return $sXML;
 	}
 	
 	/**
 	 * Returns list of categories
 	 * 
 	 * @return array
 	 */
 	 public function getCategories()
 	 {
 	 	return $this->_aCategories;
 	 }
 	 
 	 /**
 	  * Returns list of sub-categories
 	  * 
 	  * @return array
 	  */
 	 public function getSubCategories()
 	 {
 	 	return $this->_aSubCategories;	
 	 } 	 	 
 	 
 	 /**
 	  * Returns variation themes
 	  * 
 	  * @return array
 	  */
 	 public function getVariationThemes() 
 	 {
		return $this->_aVariationThemes; 	 	
 	 }
 	 
 	 /**
 	  * Returns category theme, category and sub-category
 	  * 
 	  * @param string $sCatId Category Id
 	  * 
 	  * @return az_amz_theme | bool false if thre is no mapping
 	  */
 	 static function getCategoryTheme($sCatId)
 	 {
 	 	$oAzConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
 	 	
 	 	$aThemeMappings = unserialize($oAzConfig->aThemeMappings);

 	 	if (isset($aThemeMappings[$sCatId]) && $aThemeMappings[$sCatId]['theme'] != '') {
 	 		$sThemeName =  $aThemeMappings[$sCatId]['theme'];
 	 	} else {
 	 		$sThemeName = $oAzConfig->sDefaultTheme;
 	 	}
 	 	
 		$oTheme = az_amz_theme::newAmzTheme($sThemeName);
 		
 		$oTheme->setOxidMapping($aThemeMappings[$sCatId]);
 		
 		return $oTheme; 	 	
 	 }
 	 
 	 /**
 	  * Returns category theme, category and sub-category
 	  * 
 	  * @param string $sCatId Category Id
 	  * 
 	  * @return array
 	  */
 	 static function getOxidCategoryMapping($sCatId)
 	 {
 	 	$oAzConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
 	 	
 	 	$aThemeMappings = unserialize($oAzConfig->aThemeMappings);
 	 	 	 	
 	 	
 	 	if (isset($aThemeMappings[$sCatId])) 	 	 	 		 	 	
 	 		return $aThemeMappings[$sCatId];
 	 	
 	 	
 	 	
 	 	return false;
 	 }
 	 
 	 /**
 	  * Sets category theme data
 	  * @param string $sCatId Category Id
 	  * @param array $aThemeData Theme, category and sub-category
 	  * 
 	  * @return boolean
 	  */
 	 static function setCategoryTheme($sCatId, $aThemeData)
 	 {
 	 	$oAzConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
 	 	
 	 	$aThemeMappings = unserialize($oAzConfig->aThemeMappings);
 	 	 	 	
 	 	$aThemeMappings[$sCatId] = $aThemeData;
 	 	
 	 	$oAzConfig->aThemeMappings = serialize($aThemeMappings);
 	 	
 	 	$oAzConfig->saveToDatabase();
 	 	
 	 	return true;
 	 } 	  	 
 	 
 	 public function setOxidMapping($aTheme)
 	 {
 	 	if (isset($aTheme['category']) && $aTheme['category'] != '') {
 	 		$this->_sMappedCategory = $aTheme['category'];
 	 	}
 	 	
 	 	if (isset($aTheme['subcategory']) && $aTheme['subcategory'] != '') {
 	 		$this->_sMappedSubCategory = $aTheme['subcategory'];
 	 	}
 	 	
 	 	if (isset($aTheme['variation']) && $aTheme['variation'] != '') {
 	 		$this->_sMappedVariation = $aTheme['variation'];
 	 	}
 	 }
 }
