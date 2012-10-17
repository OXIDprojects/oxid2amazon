CREATE TABLE  `az_amz_config` (
`oxid` CHAR( 32 ) NOT NULL ,
`oxshopid` CHAR( 32 ) NOT NULL ,
`az_varname` VARCHAR( 32 ) NOT NULL ,
`az_varvalue` BLOB NOT NULL ,
PRIMARY KEY (  `oxid` )
) ENGINE = MYISAM;

CREATE TABLE  `az_amz_destinations` (
`oxid` CHAR( 32 ) NOT NULL ,
`oxshopid` CHAR( 32 ) NOT NULL ,
`az_title` VARCHAR( 255 ) NOT NULL,
`az_server` VARCHAR( 255 ) NOT NULL ,
`az_ftppassivemode` TINYINT( 1 ) NOT NULL ,
`az_ftpuser` VARCHAR( 255 ) NOT NULL ,
`az_ftppassword` VARCHAR( 255 ) NOT NULL ,
`az_ftpdirectory` VARCHAR( 255 ) NOT NULL ,
`az_reportsdirectory` VARCHAR( 255 ) NOT NULL ,
`az_currency` VARCHAR( 255 ) NOT NULL ,
`az_parcel` VARCHAR( 255 ) NOT NULL ,
`az_amz_merchantid` VARCHAR( 255 ) NOT NULL ,
`az_amz_shopname` VARCHAR( 255 ) NOT NULL ,
`az_amz_user` VARCHAR( 255 ) NOT NULL ,
`az_amz_password` VARCHAR( 255 ) NOT NULL ,
`az_language` TINYINT NOT NULL,
`az_productselector` BLOB NOT NULL,
PRIMARY KEY (  `oxid` )
) ENGINE = MYISAM;

CREATE TABLE  `az_amz_history` (
`oxid` CHAR( 32 ) NOT NULL ,
`oxshopid` CHAR( 32 ) NOT NULL ,
`az_destinationid` CHAR( 32 ) NOT NULL ,
`az_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`az_action` CHAR( 32 ) NOT NULL ,
`az_statusmsg` TEXT NOT NULL,
`oxuserid` CHAR( 32 ) NOT NULL,
PRIMARY KEY (  `oxid` ) ,
INDEX (  `oxshopid` ,  `az_destinationid`)
) ENGINE = MYISAM;

CREATE TABLE  `az_amz_snapshots` (
`oxid` CHAR( 32 ) NOT NULL ,
`az_destinationid` CHAR( 32 ) NOT NULL ,
`az_productid` CHAR( 32 ) NOT NULL ,
`az_skufield` VARCHAR( 255 ) NOT NULL,
`az_hash` CHAR( 32 ) NOT NULL ,
`az_pricehash` CHAR( 32 ) NOT NULL,
`az_picturehash` CHAR( 32 ) NOT NULL,
`az_inventoryhash` VARCHAR(255) NOT NULL DEFAULT '',
`az_shippinghash` CHAR( 32 ) NOT NULL,
`az_varianthash` CHAR( 32 ) NOT NULL,
`az_timestamp` TIMESTAMP NOT NULL,
`az_price_timestamp` TIMESTAMP NOT NULL,
`az_picture_timestamp` TIMESTAMP NOT NULL,
`az_inventory_timestamp` TIMESTAMP NOT NULL,
`az_shipping_timestamp` TIMESTAMP NOT NULL,
`az_variant_timestamp` TIMESTAMP NOT NULL,
`az_variant_data` TEXT NOT NULL,
PRIMARY KEY (  `oxid` ) ,
INDEX (  `az_destinationid` ,  `az_productid` )
) ENGINE = MYISAM;

CREATE TABLE `az_amz_cronjobs` (
`oxid` CHAR( 32 ) NOT NULL ,
`destinationId` CHAR( 32 ) NOT NULL ,
`feedType` VARCHAR( 32 ) NOT NULL ,
`startDate` DATETIME NOT NULL ,
`endDate` DATETIME NOT NULL ,
`action` VARCHAR( 32 ) NOT NULL ,
`fileName` VARCHAR( 255 ) NOT NULL, 
`uploadDate` DATETIME NOT NULL ,
PRIMARY KEY (  `oxid` )
) ENGINE = MYISAM ;

ALTER TABLE  `oxarticles` 
    ADD  `az_amz_stock_reserve` INT NOT NULL,
    ADD  `az_amz_ship_option` VARCHAR(250) NOT NULL DEFAULT '',
    ADD  `az_amz_ship_type` VARCHAR(10) NOT NULL DEFAULT '',
    ADD  `az_amz_ship_amount` DOUBLE NOT NULL DEFAULT 0 ;
ALTER TABLE  `oxcategories` ADD  `az_amz_stock_reserve` INT NOT NULL;


CREATE TABLE IF NOT EXISTS `az_amz_orders_tmp` (
  `AMZORDERID` varchar(25) NOT NULL,
  `OXSHOPID` varchar(50) NOT NULL,
  `AMZORDERDATE` datetime NOT NULL,
  `BUYEREMAIL` varchar(250) NOT NULL,
  `BUYERNAME` varchar(250) NOT NULL,
  `BUYERCOMPANY` varchar(250) NOT NULL,
  `BUYERPHONE` varchar(150) NOT NULL,
  `BUYERSTREET` varchar(150) NOT NULL,
  `BUYERCITY` varchar(150) NOT NULL,
  `BUYERZIP` varchar(20) NOT NULL,
  `BUYERCOUNTRYCODE` varchar(10) NOT NULL,
  `DELSERVICELEVEL` varchar(50) NOT NULL,
  `DELNAME` varchar(250) NOT NULL,
  `DELCOMPANY` varchar(150) NOT NULL,
  `DELSTREET` varchar(150) NOT NULL,
  `DELCITY` varchar(150) NOT NULL,
  `DELZIP` varchar(10) NOT NULL,
  `DELCOUNTRYCODE` varchar(10) NOT NULL,
  `DELPHONE` varchar(50) NOT NULL,
  `DELSTATE` varchar(150) NOT NULL,
  `AZTIMESTAMP` timestamp NOT NULL,
  `AMZFILENAME` varchar(150) NOT NULL,
  `DATEOFIMPORT` datetime NOT NULL,
  `AZPROCESSED` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`AMZORDERID`),
  KEY `DATEOFIMPORT` (`DATEOFIMPORT`),
  KEY `AZPROCESSED` (`AZPROCESSED`),
  KEY `AMZFILENAME` (`AMZFILENAME`)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS `az_amz_orderitems_tmp` (
  `AMZORDERID` varchar(50) NOT NULL,
  `AMZORDERITEMCODE` varchar(50) NOT NULL,
  `AMZSKU` varchar(150) NOT NULL,
  `AMZTITLE` varchar(250) NOT NULL,
  `AMZQUANTITY` int(11) NOT NULL,
  `AMZTAXCODE` varchar(50) NOT NULL,
  `AMZARTPRICE` double NOT NULL,
  `AMZARTTAX` double NOT NULL,
  `AMZSHIPPRICE` double NOT NULL,
  `AMZSHIPTAX` double NOT NULL,
  `AZTIMESTAMP` timestamp NOT NULL,
  PRIMARY KEY  (`AMZORDERID`,`AMZORDERITEMCODE`)
) TYPE=MyISAM;

ALTER TABLE `oxorder` ADD `AMZORDERID` VARCHAR( 50 ) NOT NULL ,
ADD INDEX ( `AMZORDERID` ) ;
