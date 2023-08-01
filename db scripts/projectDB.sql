-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2021 m. Geg 27 d. 22:27
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projektas`
--

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `history`
--

CREATE TABLE `history` (
  `History_id` int(11) NOT NULL,
  `Ivykio_tipas` text COLLATE utf32_lithuanian_ci NOT NULL,
  `Ivykio_vardas` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL,
  `Pakeitimo_data` datetime NOT NULL,
  `Vartotojo_id` int(11) NOT NULL,
  `Vardas` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_lithuanian_ci;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `komandos`
--

CREATE TABLE `komandos` (
  `Projekto_id` int(11) NOT NULL,
  `Role` int(11) NOT NULL,
  `Vartotojas` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_lithuanian_ci;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `projektai`
--

CREATE TABLE `projektai` (
  `Projekto_id` int(11) NOT NULL,
  `Pavadinimas` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL,
  `Aprasymas` text COLLATE utf32_lithuanian_ci NOT NULL,
  `Busena` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL,
  `Sukurimo_data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_lithuanian_ci;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `projektu_uzduotys`
--

CREATE TABLE `projektu_uzduotys` (
  `Projekto_id` int(11) NOT NULL,
  `Uzduoties_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_lithuanian_ci;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `roles`
--

CREATE TABLE `roles` (
  `Roles_id` int(11) NOT NULL,
  `Pavadinimas` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL,
  `Aprasymas` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_lithuanian_ci;

--
-- Sukurta duomenų kopija lentelei `roles`
--

INSERT INTO `roles` (`Roles_id`, `Pavadinimas`, `Aprasymas`) VALUES
(1, 'Administratorius', 'Gali keisti projektus ir uzduotis'),
(2, 'Komandos narys', 'Negali keisti projektu');

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `uzduotys`
--

CREATE TABLE `uzduotys` (
  `Uzduoties_id` int(11) NOT NULL,
  `Pavadinimas` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL,
  `Aprasymas` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL,
  `Prioritetas` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL,
  `Busena` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL,
  `Sukurimo_data` date NOT NULL,
  `Naujinimo_data` date NOT NULL,
  `Eiles_nr` bigint(8) NOT NULL,
  `Projekto_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_lithuanian_ci;

-- --------------------------------------------------------

--
-- Sukurta duomenų struktūra lentelei `vartotojai`
--

CREATE TABLE `vartotojai` (
  `El_pastas` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL,
  `Slaptazodis` varchar(64) COLLATE utf32_lithuanian_ci NOT NULL,
  `Vardas` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL,
  `Pavarde` varchar(255) COLLATE utf32_lithuanian_ci NOT NULL,
  `Vartotojo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_lithuanian_ci;

--
-- Sukurta duomenų kopija lentelei `vartotojai`
--

INSERT INTO `vartotojai` (`El_pastas`, `Slaptazodis`, `Vardas`, `Pavarde`, `Vartotojo_id`) VALUES
('labas@labas.lt', 'Labas1234', 'labius', 'methlabius', 1),
('gmail@gmail.com', 'Gmail333', 'Simonas', 'Donskovas', 22);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`History_id`);

--
-- Indexes for table `komandos`
--
ALTER TABLE `komandos`
  ADD KEY `Komandos_fk0` (`Projekto_id`),
  ADD KEY `Komandos_fk1` (`Role`),
  ADD KEY `Komandos_fk2` (`Vartotojas`);

--
-- Indexes for table `projektai`
--
ALTER TABLE `projektai`
  ADD PRIMARY KEY (`Projekto_id`);

--
-- Indexes for table `projektu_uzduotys`
--
ALTER TABLE `projektu_uzduotys`
  ADD KEY `Projektu_uzduotys_fk0` (`Projekto_id`),
  ADD KEY `Projektu_uzduotys_fk1` (`Uzduoties_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`Roles_id`);

--
-- Indexes for table `uzduotys`
--
ALTER TABLE `uzduotys`
  ADD PRIMARY KEY (`Uzduoties_id`),
  ADD UNIQUE KEY `Eiles_nr` (`Eiles_nr`);

--
-- Indexes for table `vartotojai`
--
ALTER TABLE `vartotojai`
  ADD PRIMARY KEY (`Vartotojo_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `History_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=360;

--
-- AUTO_INCREMENT for table `uzduotys`
--
ALTER TABLE `uzduotys`
  MODIFY `Eiles_nr` bigint(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- Apribojimai eksportuotom lentelėm
--

--
-- Apribojimai lentelei `komandos`
--
ALTER TABLE `komandos`
  ADD CONSTRAINT `Komandos_fk0` FOREIGN KEY (`Projekto_id`) REFERENCES `projektai` (`Projekto_id`),
  ADD CONSTRAINT `Komandos_fk1` FOREIGN KEY (`Role`) REFERENCES `roles` (`Roles_id`),
  ADD CONSTRAINT `Komandos_fk2` FOREIGN KEY (`Vartotojas`) REFERENCES `vartotojai` (`Vartotojo_id`);

--
-- Apribojimai lentelei `projektu_uzduotys`
--
ALTER TABLE `projektu_uzduotys`
  ADD CONSTRAINT `Projektu_uzduotys_fk0` FOREIGN KEY (`Projekto_id`) REFERENCES `projektai` (`Projekto_id`),
  ADD CONSTRAINT `Projektu_uzduotys_fk1` FOREIGN KEY (`Uzduoties_id`) REFERENCES `uzduotys` (`Uzduoties_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
