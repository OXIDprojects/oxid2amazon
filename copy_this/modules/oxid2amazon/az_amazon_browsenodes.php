<?php

class az_amazon_browsenodes extends az_amazon_browsenodes_parent
{
	protected function _getBrowseNodes($product)
	{
		// instead of the fixed value here this should be something like: $product->oxarticles__browsenodefield->value)
		// you can put as much lines here as you need
		$sRet = $this->_getXmlIfExists('RecommendedBrowseNode', "361177011");
		
		return $sRet;
	}
}