<?php

	$this->aOperators	= array(
		'op1'	=> array(
					'operator'			=> 'LIKE',											
					'enabled'			=> true,
					'value_required'	=> true
					),
		'op2'	=> array(
					'operator'			=> 'NOT LIKE',
					'enabled'			=> true,
					'value_required'	=> true
					),
		'op3'	=> array(
					'operator'			=> '=',
					'enabled'			=> true,
					'value_required'	=> true
					),
		'op4'	=> array(
					'operator'			=> '!=',
					'enabled'			=> true,
					'value_required'	=> true
					),
		'op5'	=> array(
					'operator'			=> 'REGEXP',
					'enabled'			=> true,
					'value_required'	=> true
					),
		'op6'	=> array(
					'operator'			=> 'NOT REGEXP',
					'enabled'			=> true,
					'value_required'	=> true
					),
		'op7'	=> array(	
					'operator'			=> 'IS NULL',
					'enabled'			=> true,
					'value_required'	=> false
					),
		'op8'	=> array(
					'operator'			=> 'IS NOT NULL',
					'enabled'			=> true,
					'value_required'	=> false
					),
		'op9'	=> array(
					'operator'			=> '<',
					'enabled'			=> true,
					'value_required'	=> true											
					),
		'op10'	=> array(
					'operator'			=> '<=',
					'enabled'			=> true,
					'value_required'	=> true
					),
		'op11'	=> array(
					'operator'			=> '>',
					'enabled'			=> true,
					'value_required'	=> true
					),
		'op12'	=> array(
					'operator'			=> '>=',
					'enabled'			=> true,
					'value_required'	=> true
					)									
	);
	
	$this->aFilterFieldTranslations = array(
		'oxarticles.oxid'			=> 'Product id',
		'oxarticles.oxshopid'		=> 'Shop Id',
		'oxarticles.oxparentid'		=> 'Parent product id',
		'oxarticles.oxactive'		=> 'Active',
		'oxarticles.oxactivefrom'	=> 'Active from',
		'oxarticles.oxactiveto'		=> 'Active to',
		'oxarticles.oxartnum'		=> 'Product num.',
		'oxarticles.oxean'			=> 'Product EAN',
		'oxarticles.oxdistean'		=> 'Distributor EAN',
		
		
	);
?>
