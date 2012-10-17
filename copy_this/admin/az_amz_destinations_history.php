<?php
class az_amz_destinations_history extends oxAdminDetails
{
	protected $_sThisTemplate = 'az_amz_destinations_history.tpl';
	
	protected $iHistoryRowsPerPage = 12;
	
	/**
     * Executes parent method parent::render() and creates az_amz_destination
     * object, which is passed to Smarty engine.
     *
     * @return string
     */
    public function render()
    {
    	$sDestinationId = oxConfig::getParameter( "oxid");
    	
    	$sRet = parent::render();
    	
    	$oHistory = oxNew('az_amz_history');
    	
    	$iPage = (int)oxConfig::getParameter("page");
    	
    	$this->_aViewData['iPage'] 	= $iPage;
    	
    	if ($iPage > 0) {
    		$iPage = $iPage - 1;
    	}
    	$aHistoryList 	= $oHistory->getDestinationHistory($sDestinationId, $this->iHistoryRowsPerPage, $iPage);
    	$iHistoryCount 	= $oHistory->getDestinationHistoryCount($sDestinationId);
    	$this->_aViewData['aHistory'] = $aHistoryList;
    	
    	
    	$iTotalPages 			= (int)ceil($iHistoryCount / $this->iHistoryRowsPerPage);
    	
    	if ($iTotalPages = 1) {
    		$iTotalPages = 0;  
    	}  	
    	
    	$this->_aViewData['iTotalPages'] = $iTotalPages;    	
    	
    	return $this->_sThisTemplate;
    }
}
?>
