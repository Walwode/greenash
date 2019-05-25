-- --------------------------------------------------------

--
-- Table structure for table `GreenAsh_Device`
--

CREATE TABLE IF NOT EXISTS `GreenAsh_Device` (
  `chipId` varchar(12) NOT NULL,
  `name` varchar(80) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `diameter` int(11) NOT NULL COMMENT '[mm]',
  `faintInterval` int(11) NOT NULL COMMENT '[ms]',
  `pushInterval` int(11) NOT NULL COMMENT '[ms]',
  `hasOled` tinyint(1) NOT NULL,
  `colorCode` varchar(23) NOT NULL DEFAULT 'rgb(125, 255, 000, 1)',
  PRIMARY KEY (`chipId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `GreenAsh_Device`
--

INSERT INTO `GreenAsh_Device` (`chipId`, `name`, `active`, `diameter`, `faintInterval`, `pushInterval`, `hasOled`, `colorCode`) VALUES
('3628105', 'Johanniter Unfallhilfe e.V. Regionalverband Dresden', 1, 80, 30, 1000, 0, '#12130F'),
('6917762', 'USV TU Dresden, Abteilung Schwimmen', 1, 80, 30, 1000, 0, '#A51F15'),
('11188782', 'Tierschutzverein "Hoffnung für Tiere" e.V.', 1, 80, 30, 1000, 0, '#39965E'),
('11189915', 'Eisenbahnersportverein e.V., Abt. Hockey', 1, 80, 30, 1000, 0, '#A0AAAA'),
('11177482', 'ColumbaPalumbus e.V.', 1, 80, 30, 1000, 0, '#0A4766'),
('11190082', 'Förderung Leichtathletik Dresden e.V.', 1, 80, 30, 1000, 0, '#DDE54E'),
('3628584', 'Laufband 7', 1, 80, 30, 1000, 0, 'rgb(125, 255, 000, 1)'),
('11190629', 'Laufband 8', 1, 80, 30, 1000, 0, 'rgb(125, 255, 000, 1)');

-- --------------------------------------------------------

--
-- Table structure for table `GreenAsh_Log`
--

CREATE TABLE IF NOT EXISTS `GreenAsh_Log` (
  `entryNo` bigint(20) NOT NULL AUTO_INCREMENT,
  `chipId` varchar(12) NOT NULL,
  `dateTime` double NOT NULL,
  `distance` double NOT NULL COMMENT '[m]',
  `speed` double NOT NULL COMMENT '[km/h]',
  `cumulatedDistance` double NOT NULL,
  PRIMARY KEY (`entryNo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2129 ;


-- --------------------------------------------------------

--
-- Table structure for table `GreenAsh_Setup`
--

CREATE TABLE IF NOT EXISTS `GreenAsh_Setup` (
  `pk` int(11) NOT NULL,
  `timeOffset` double NOT NULL,
  `title` varchar(80) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `GreenAsh_Setup`
--

INSERT INTO `GreenAsh_Setup` (`pk`, `timeOffset`, `title`) VALUES
(0, 1558773023, 'LAUFSZENE EVENTS');
