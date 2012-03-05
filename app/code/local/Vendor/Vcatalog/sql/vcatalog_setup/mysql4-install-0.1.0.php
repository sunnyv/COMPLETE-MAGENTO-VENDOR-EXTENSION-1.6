<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('vcatalog')};
CREATE TABLE {$this->getTable('vcatalog')} (
  `vcatalog_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`vcatalog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('vinfo')};
CREATE TABLE IF NOT EXISTS `vinfo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_user` int(11) NOT NULL,
  `business_category` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `business_phone_number` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `eaddr` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL,
  `payment_status` tinyint(1) NOT NULL DEFAULT '0',
  `profile_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=72 ;

CREATE TABLE IF NOT EXISTS `vprelation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendorid` int(11) NOT NULL,
  `profileid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;


-- DROP TABLE IF EXISTS {$this->getTable('vsubscription')};
CREATE TABLE IF NOT EXISTS `vsubscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subsname` varchar(255) NOT NULL,
  `duration` int(11) NOT NULL,
  `amout` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- DROP TABLE IF EXISTS {$this->getTable('vpaymentinfo')};
CREATE TABLE IF NOT EXISTS `vpaymentinfo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) DEFAULT NULL,
  `subscription_years` varchar(255) DEFAULT NULL,
  `amount` varchar(255) DEFAULT NULL,
  `payment_methoed` varchar(255) DEFAULT NULL,
  `payment_status` tinyint(1) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

    ");
$installer->endSetup(); 