<?php
class az_amz_settings_categories extends oxAdminDetails
{
	/**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'az_amz_settings_categories.tpl';
    
    public function render()
    {    	    	
    	$sRet = parent::render();
    	
    	$myConfig = $this->getConfig();
    	
    	$oAzConfig = oxNew('az_amz_config', oxConfig::getInstance()->getShopId());
    	
    	
    	$aThemeMappings = unserialize($oAzConfig->aThemeMappings);
    	
    	$aCatTree = $this->_buildCategoryTree();
                
        foreach($aCatTree as $sOXID => $sCatPath)    	
    		if (isset($aThemeMappings[$sOXID]))
    			$aCatTree[$sOXID]['map'] = $aThemeMappings[$sOXID];	
    	
        $this->_aViewData['aCategoryMappings'] = $aCatTree;        
                
    	$this->_aViewData["menustructure"] = $this->getNavigation()->getDomXml()->documentElement->childNodes;
    	
    	
    	return $sRet;
    }
     
    
    protected function _buildCategoryTree()
    {    	     		
		$oDB =getDb();
		$sCatView = getViewName('oxcategories');
		
		$sQ = "SELECT SiteTree.oxid AS oxid, GROUP_CONCAT(PathTree.oxtitle ORDER BY PathTree.oxleft SEPARATOR ' > ') as Path
				  FROM $sCatView AS SiteTree, $sCatView AS PathTree
				  WHERE PathTree.oxleft <= SiteTree.oxleft
				    AND PathTree.oxright >= SiteTree.oxright
				    AND PathTree.oxrootid = SiteTree.oxrootid
				  GROUP BY SiteTree.oxid
				  ORDER BY SiteTree.oxleft
				 ";
		
		$rs = $oDB->execute($sQ);
		
		if($rs != false && $rs->recordCount() > 0) 
    	{
            while (!$rs->EOF) 
            {            	
            	$aCatTree[$rs->fields['oxid']]['path']	= $rs->fields['Path'];
            	$aCatTree[$rs->fields['oxid']]['map']  	= array('theme' 		=> '-', 
																'category'		=> '-',
																'subcategory'	=> '-'
																);
            	
            	$rs->moveNext();
            }
    	}
    	
    	return $aCatTree;
    }   
}
?>
