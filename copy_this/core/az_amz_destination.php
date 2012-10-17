<?php
/*
 * Created on Jul 13, 2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class az_amz_destination extends oxBase
 {
 	/**
     * Core database table name. $sCoreTbl could be only original data table name and not view name.
     * @var string
     */
    protected $_sCoreTbl   = 'az_amz_destinations';

    /**
     * Name of current class
     * @var string
     */
    protected $_sClassName = 'az_amz_destination';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'az_amz_destinations' );
    }
 }
?>
