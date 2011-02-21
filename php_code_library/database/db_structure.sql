-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 24, 2008 at 10:41 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.4-2ubuntu5.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `php_code_library_unittest`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_phonenum`
--

CREATE TABLE IF NOT EXISTS `t_phonenum` (
  `c_uid` int(10) unsigned NOT NULL auto_increment,
  `c_user` bigint(20) unsigned NOT NULL,
  `c_phonenum` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`c_uid`),
  KEY `c_user` (`c_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=149 ;

-- --------------------------------------------------------

--
-- Table structure for table `t_user`
--

CREATE TABLE IF NOT EXISTS `t_user` (
    `c_uid` int(10) unsigned NOT NULL auto_increment,
    `c_username` varchar(100) NOT NULL,
    `c_password` varchar(100) NOT NULL,
    PRIMARY KEY  (`c_uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

