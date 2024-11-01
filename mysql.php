<?php

$sql = "-- Create syntax for TABLE '".$wpdb->prefix."vcos_courses'
CREATE TABLE `".$wpdb->prefix."vcos_courses` (
  `courseid` int(11) NOT NULL AUTO_INCREMENT,
  `ownerid` bigint(15) NOT NULL DEFAULT '0',
  `course` varchar(40) NOT NULL DEFAULT '',
  `theme` varchar(40) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `preface` mediumtext NOT NULL,
  `credits` char(2) NOT NULL DEFAULT '0',
  `level` int(2) NOT NULL DEFAULT '1',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `prerequisite` varchar(40) DEFAULT NULL,
  `proid` varchar(40) DEFAULT 'ND',
  `objectives` mediumtext NOT NULL,
  `enabled` int(1) NOT NULL DEFAULT '0',
  `type` varchar(20) NOT NULL DEFAULT '',
  `cost` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`courseid`)
) ENGINE=MyISAM AUTO_INCREMENT=1576 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE '".$wpdb->prefix."vcos_fexams'
CREATE TABLE `".$wpdb->prefix."vcos_fexams` (
  `courseid` int(11) NOT NULL,
  `exam` mediumtext NOT NULL,
  UNIQUE KEY `courseid` (`courseid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Create syntax for TABLE '".$wpdb->prefix."vcos_homework'
CREATE TABLE `".$wpdb->prefix."vcos_homework` (
  `courseid` int(11) unsigned NOT NULL,
  `postid` int(11) NOT NULL DEFAULT '0',
  `homework` mediumtext,
  `grade` int(3) DEFAULT NULL,
  `enabled` int(2) DEFAULT NULL,
  PRIMARY KEY (`courseid`,`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE '".$wpdb->prefix."vcos_locale'
CREATE TABLE `wp_vcos_locale` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(30) DEFAULT NULL,
  `file` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE '".$wpdb->prefix."vcos_settings'
CREATE TABLE `".$wpdb->prefix."vcos_settings` (
  `slug_name` varchar(50) NOT NULL DEFAULT '',
  `welcome_txt` mediumtext,
  `grade` int(3) DEFAULT NULL,
  PRIMARY KEY (`slug_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create syntax for TABLE '".$wpdb->prefix."vcos_studentanswers'
CREATE TABLE `".$wpdb->prefix."vcos_studentanswers` (
  `userid` bigint(15) NOT NULL DEFAULT '0',
  `questionid` int(10) DEFAULT '0',
  `courseid` int(11) DEFAULT '0',
  `note` decimal(7,0) DEFAULT '0',
  `date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Create syntax for TABLE '".$wpdb->prefix."vcos_studentcourses'
CREATE TABLE `".$wpdb->prefix."vcos_studentcourses` (
  `IDmateria` varchar(10) NOT NULL DEFAULT '',
  `IDestudiante` bigint(15) NOT NULL DEFAULT '0',
  `done` int(1) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`IDmateria`,`IDestudiante`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Create syntax for TABLE '".$wpdb->prefix."vcos_studenthomework'
CREATE TABLE `".$wpdb->prefix."vcos_studenthomework` (
  `userid` int(15) DEFAULT NULL,
  `courseid` int(11) unsigned NOT NULL,
  `postid` int(4) DEFAULT NULL,
  `homework` mediumtext,
  `grade` int(3) DEFAULT NULL,
  UNIQUE KEY `userid` (`userid`,`courseid`,`postid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `".$wpdb->prefix."vcos_studentfexams` (
  `userid` bigint(15) unsigned NOT NULL,
  `courseid` int(11) NOT NULL DEFAULT '0',
  `answers` mediumtext,
  `grade` int(3) DEFAULT NULL,
  PRIMARY KEY (`userid`,`courseid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



-- Create syntax for TABLE '".$wpdb->prefix."vcos_user_locales'
CREATE TABLE `".$wpdb->prefix."vcos_user_locales` (
  `userid` bigint(20) unsigned NOT NULL,
  `langid` int(10) DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    
INSERT INTO `".$wpdb->prefix."vcos_settings` (`slug_name`, `welcome_txt`, `grade`)
VALUES
	('materias','<h1>Bienvenido a vcOS - <em>Plataforma de Enseñanza Virtual</em></h1>
<h1><span><img alt= src=http://vcos.info/wp-content/plugins/vcos/img/vcos_large.png width=200 /> </span></h1>',80);";
?>