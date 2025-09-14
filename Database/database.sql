
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cdrdb`
--
USE `cdrdb`;

-- --------------------------------------------------------

--
-- Table structure for table `cdr`
--

CREATE TABLE `cdr` (
  `uniqueId` int(11) NOT NULL,
  `timeReceived` datetime NOT NULL DEFAULT current_timestamp(),
  `softDeleted` tinyint(1) NOT NULL DEFAULT 0,
  `id` int(11) NOT NULL,
  `mnc` int(11) DEFAULT NULL,
  `bytes_used` int(11) NOT NULL,
  `dmcc` varchar(255) DEFAULT NULL,
  `cellid` int(11) DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cdr`
--
ALTER TABLE `cdr`
  ADD PRIMARY KEY (`uniqueId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cdr`
--
ALTER TABLE `cdr`
  MODIFY `uniqueId` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
