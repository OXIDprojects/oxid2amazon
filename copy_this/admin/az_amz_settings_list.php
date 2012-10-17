<?php

class az_amz_settings_list extends oxAdminList 
{
	protected $_blUpdateMain = false;
	
	/**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'az_amz_settings_list.tpl';
    
    public function render()
    {
    	$sRet = parent::render();
    	
    	// default page number 1
        $this->_aViewData['default_edit'] = 'az_amz_settings_main';
        $this->_aViewData['updatemain']   = $this->_blUpdateMain;
        
        if ( $this->_aViewData['updatenav'] ) {
            //skipping requirements checking when reloading nav frame
            oxSession::setVar( "navReload", true );
        }
    	$this->_aViewData['oxid'] = 'blabla';    
    	//$this->_aViewData['noOXIDCheck'] = true;
    
    	return $sRet;
    }
	
}