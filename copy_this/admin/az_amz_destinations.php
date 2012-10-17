<?php
class az_amz_destinations extends oxAdminView 
{
	protected $_sThisTemplate = 'az_amz_destinations.tpl';
	
	public function render()
	{
		parent::render();
		return $this->_sThisTemplate;
	}
}