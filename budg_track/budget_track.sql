-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 30, 2010 at 09:13 PM
-- Server version: 5.0.75
-- PHP Version: 5.2.6-3ubuntu4.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `budget_track`
--

-- --------------------------------------------------------

--
-- Table structure for table `daysRepeatedAfterFirstOccurenceSchedule`
--

CREATE TABLE IF NOT EXISTS `daysRepeatedAfterFirstOccurenceSchedule` (
  `uid` int(10) unsigned NOT NULL auto_increment,
  `startDate` datetime NOT NULL,
  `daysAfterToRepeat` int(10) unsigned NOT NULL,
  `transaction_uid` int(10) unsigned default NULL,
  PRIMARY KEY  (`uid`),
  KEY `transaction_uid` (`transaction_uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `monthlySchedule`
--

CREATE TABLE IF NOT EXISTS `monthlySchedule` (
  `uid` int(10) unsigned NOT NULL auto_increment,
  `dayOfMonth` int(10) unsigned NOT NULL,
  `transaction_uid` int(10) unsigned default NULL,
  PRIMARY KEY  (`uid`),
  KEY `transaction_uid` (`transaction_uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55 ;

--
-- Dumping data for table `monthlySchedule`
--

-- --------------------------------------------------------

--
-- Table structure for table `oneTimeSchedule`
--

CREATE TABLE IF NOT EXISTS `oneTimeSchedule` (
  `uid` int(10) unsigned NOT NULL auto_increment,
  `dateOfTransaction` datetime NOT NULL,
  `transaction_uid` int(10) unsigned default NULL,
  PRIMARY KEY  (`uid`),
  KEY `transaction_uid` (`transaction_uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;


--
-- Table structure for table `savedBudget`
--

CREATE TABLE IF NOT EXISTS `savedBudget` (
  `uid` int(10) unsigned NOT NULL auto_increment,
  `hashpw` varchar(40) NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=115 ;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE IF NOT EXISTS `transaction` (
  `uid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `description` mediumtext NOT NULL,
  `dollar_amount` decimal(10,2) NOT NULL,
  `budget_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`uid`),
  KEY `name` (`name`),
  KEY `budgetId` (`budget_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=87 ;

