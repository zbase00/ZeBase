CREATE TABLE IF NOT EXISTS `fish` (
  `batch_ID` int(11) NOT NULL AUTO_INCREMENT,
  `room` text,
  `gender` text,
  `name` text,
  `status` text,
  `birthday` text,
  `death_date` text,
  `mother_ID` text,
  `mother_other` text,
  `father_ID` text,
  `father_other` text,
  `tank_ID` text,
  `user_ID` text,
  `comments` text,
  `strain_ID` text,
  `generation` text,
  `current_nursery` text,
  `current_adults` text,
  `starting_adults` text,
  `starting_nursery` text NOT NULL,
  PRIMARY KEY (`batch_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
^
CREATE TABLE IF NOT EXISTS `labs` (
  `lab_ID` int(11) NOT NULL AUTO_INCREMENT,
  `lab_name` varchar(100) NOT NULL,
  PRIMARY KEY (`lab_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
^
CREATE TABLE IF NOT EXISTS `mutant` (
  `mutant_ID` int(11) NOT NULL AUTO_INCREMENT,
  `mutant` text NOT NULL,
  `allele` text NOT NULL,
  `reference` text NOT NULL,
  `strain` text NOT NULL,
  `cross_ref` text NOT NULL,
  `batch_name` text NOT NULL,
  `gene` text NOT NULL,
  PRIMARY KEY (`mutant_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
^
CREATE TABLE IF NOT EXISTS `mutant_assoc` (
  `mutant_assoc_ID` int(11) NOT NULL AUTO_INCREMENT,
  `mutant_ID` int(11) NOT NULL,
  `batch_ID` int(11) NOT NULL,
  `mutant_genotype_wildtype` text,
  `mutant_genotype_heterzygous` text,
  `mutant_genotype_homozygous` text,
  PRIMARY KEY (`mutant_assoc_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
^
CREATE TABLE IF NOT EXISTS `report_recipients` (
  `recipient_ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_ID` int(11) NOT NULL,
  `report_ID` int(11) NOT NULL,
  PRIMARY KEY (`recipient_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
^
CREATE TABLE IF NOT EXISTS `saved_searches` (
  `search_ID` int(11) NOT NULL AUTO_INCREMENT,
  `search_name` text NOT NULL,
  `batch_ID` text,
  `mylab` text,
  `gender` text,
  `name` text,
  `status` text,
  `birthday` text,
  `mother_ID` text,
  `mother_other` text,
  `father_ID` text,
  `father_other` text,
  `tank_ID` text,
  `user_ID` text,
  `comments` text,
  `strain_ID` text,
  `mutant_ID` text,
  `other_mutant` text,
  `generation` text,
  `mutant_genotype_wildtype` varchar(15) DEFAULT NULL,
  `mutant_genotype_heterzygous` varchar(15) DEFAULT NULL,
  `mutant_genotype_homozygous` varchar(15) DEFAULT NULL,
  `transgene_ID` text,
  `transgene_genotype_wildtype` varchar(10) DEFAULT NULL,
  `transgene_genotype_heterzygous` varchar(10) DEFAULT NULL,
  `transgene_genotype_homozygous` varchar(10) DEFAULT NULL,
  `lab` text,
  `mutant_allele` text,
  `transgene_allele` text,
  PRIMARY KEY (`search_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
^
CREATE TABLE IF NOT EXISTS `stat_survival_track` (
  `track_ID` int(11) NOT NULL AUTO_INCREMENT,
  `batch_ID` int(11) NOT NULL,
  `starting_adults` int(11) NOT NULL,
  `current_adults` int(11) NOT NULL,
  `status` text NOT NULL,
  `survival_percent` text NOT NULL,
  `birthday` text NOT NULL,
  `date_taken` text NOT NULL,
  `starting_nursery` int(11) NOT NULL,
  PRIMARY KEY (`track_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7473 ;
^
CREATE TABLE IF NOT EXISTS `strain` (
  `strain_ID` int(11) NOT NULL AUTO_INCREMENT,
  `strain` text NOT NULL,
  `source` text NOT NULL,
  `source_contact_info` text NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`strain_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;
^
CREATE TABLE IF NOT EXISTS `tank` (
  `tank_ID` int(11) NOT NULL AUTO_INCREMENT,
  `size` text,
  `location` text,
  `room` text NOT NULL,
  `comments` text,
  PRIMARY KEY (`tank_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1192 ;
^
CREATE TABLE IF NOT EXISTS `tank_assoc` (
  `batch_ID` int(11) NOT NULL,
  `tank_ID` varchar(30) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`batch_ID`,`tank_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
^
CREATE TABLE IF NOT EXISTS `transgene` (
  `transgene_ID` int(11) NOT NULL AUTO_INCREMENT,
  `transgene` text NOT NULL,
  `promoter` text NOT NULL,
  `gene` text NOT NULL,
  `reference` text NOT NULL,
  `strain` text NOT NULL,
  `comment` text NOT NULL,
  `allele` text,
  PRIMARY KEY (`transgene_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
^
CREATE TABLE IF NOT EXISTS `transgene_assoc` (
  `transgene_assoc_ID` int(11) NOT NULL AUTO_INCREMENT,
  `transgene_ID` int(11) NOT NULL,
  `batch_ID` int(11) NOT NULL,
  `transgene_genotype_wildtype` text,
  `transgene_genotype_heterzygous` text,
  `transgene_genotype_homozygous` text,
  PRIMARY KEY (`transgene_assoc_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
^
CREATE TABLE IF NOT EXISTS `users` (
  `user_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_pass` varchar(60) NOT NULL DEFAULT '',
  `user_date` varchar(100) NOT NULL,
  `user_modified` varchar(100) NOT NULL,
  `user_last_login` datetime DEFAULT NULL,
  `db_reference_name` varchar(100) NOT NULL,
  `lab` varchar(100) NOT NULL,
  `office_location` text NOT NULL,
  `lab_location` text NOT NULL,
  `lab_phone` varchar(80) NOT NULL,
  `emergency_phone` varchar(80) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(255) NOT NULL,
  `admin_access` varchar(100) DEFAULT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `middle_name` text NOT NULL,
  PRIMARY KEY (`user_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;
^
CREATE TABLE IF NOT EXISTS `water_quality` (
  `entry_ID` int(11) NOT NULL AUTO_INCREMENT,
  `system_name` text,
  `location` text,
  `nitrate` text,
  `nitrite` text,
  `ph` text,
  `conductivity` text,
  `do` text,
  `temperature` text,
  `record_date` text,
  PRIMARY KEY (`entry_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
^
