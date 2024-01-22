-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Gegenereerd op: 19 jan 2024 om 15:59
-- Serverversie: 10.6.12-MariaDB-1:10.6.12+maria~ubu2004
-- PHP-versie: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `artlab`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `artpieces`
--

CREATE SCHEMA IF NOT EXISTS `artlab` DEFAULT CHARACTER SET utf8 ;
USE `artlab` ;

CREATE TABLE `artpieces` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` varchar(255) NOT NULL,
  `width_in_cm` int(11) NOT NULL,
  `height_in_cm` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `type` enum('auction','buynow') NOT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `category` enum('pictures','sculptures','paintings','drawings') NOT NULL,
  `auction_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `artpieces`
--

INSERT INTO `artpieces` (`id`, `name`, `description`, `width_in_cm`, `height_in_cm`, `price`, `type`, `likes`, `user_id`, `created_at`, `category`, `auction_id`) VALUES
(1, 'Test', 'hello', 50, 50, 50, 'auction', 0, 2, '2024-01-18', 'pictures', NULL);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `auctions`
--

CREATE TABLE `auctions` (
  `id` int(11) NOT NULL,
  `end_date` date NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `bids`
--

CREATE TABLE `bids` (
  `id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `content` varchar(45) NOT NULL,
  `likes` int(11) NOT NULL,
  `artpiece_id` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `url` varchar(45) NOT NULL,
  `artpiece_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `images`
--

INSERT INTO `images` (`id`, `url`, `artpiece_id`) VALUES
(1, '1.jpg', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `likes`
--

CREATE TABLE `likes` (
  `user_id` int(11) NOT NULL,
  `artpiece_id` int(11) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `status` varchar(45) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_adress_id` int(11) NOT NULL,
  `created_at` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `orders_adresses`
--

CREATE TABLE `orders_adresses` (
  `id` int(11) NOT NULL,
  `street_no` varchar(45) NOT NULL,
  `zip` varchar(45) NOT NULL,
  `city` varchar(45) NOT NULL,
  `country` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `order_has_artpiece`
--

CREATE TABLE `order_has_artpiece` (
  `order_id` int(11) NOT NULL,
  `artpiece_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `refresh_tokens`
--

CREATE TABLE `refresh_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phonenumber` varchar(45) NOT NULL,
  `date_of_birth` date NOT NULL,
  `profile_picture` varchar(45) NOT NULL,
  `bio` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` date NOT NULL,
  `users_adresses_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `phonenumber`, `date_of_birth`, `profile_picture`, `bio`, `role`, `created_at`, `users_adresses_id`) VALUES
(2, 'example_username', 'example@email.com', '$2y$10$IRn2auju6r/Zwr.Q8jxrJ.UH0WLN6lD1wy.prBDo/hi41mOK4IYuC', '1234567890', '1990-01-01', 'profile_pic.jpg', 'This is a sample bio.', 'admin', '2024-01-16', 4);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `users_adresses`
--

CREATE TABLE `users_adresses` (
  `id` int(11) NOT NULL,
  `street_no` varchar(45) NOT NULL,
  `zip` varchar(45) NOT NULL,
  `city` varchar(45) NOT NULL,
  `country` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `users_adresses`
--

INSERT INTO `users_adresses` (`id`, `street_no`, `zip`, `city`, `country`) VALUES
(3, '123', '12345', 'Sample City', 'Sample Country'),
(4, '123', '12345', 'Sample City', 'Sample Country');

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `artpieces`
--
ALTER TABLE `artpieces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_artpiece` (`user_id`);

--
-- Indexen voor tabel `auctions`
--
ALTER TABLE `auctions`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`id`,`auction_id`,`user_id`),
  ADD KEY `fk_bids_auctions1_idx` (`auction_id`),
  ADD KEY `fk_bids_users1_idx` (`user_id`);

--
-- Indexen voor tabel `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`,`artpiece_id`,`user_id`),
  ADD KEY `fk_comments_artpieces1_idx` (`artpiece_id`),
  ADD KEY `fk_comments_users1_idx` (`user_id`);

--
-- Indexen voor tabel `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`,`artpiece_id`),
  ADD KEY `fk_images_artpieces1_idx` (`artpiece_id`);

--
-- Indexen voor tabel `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`,`user_id`,`order_adress_id`),
  ADD KEY `fk_orders_users1_idx` (`user_id`),
  ADD KEY `fk_orders_orders_adresses1_idx` (`order_adress_id`);

--
-- Indexen voor tabel `orders_adresses`
--
ALTER TABLE `orders_adresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexen voor tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_users_adresses1_idx` (`users_adresses_id`);

--
-- Indexen voor tabel `users_adresses`
--
ALTER TABLE `users_adresses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `artpieces`
--
ALTER TABLE `artpieces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT voor een tabel `auctions`
--
ALTER TABLE `auctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `bids`
--
ALTER TABLE `bids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT voor een tabel `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT voor een tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `orders_adresses`
--
ALTER TABLE `orders_adresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT voor een tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT voor een tabel `users_adresses`
--
ALTER TABLE `users_adresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `artpieces`
--
ALTER TABLE `artpieces`
  ADD CONSTRAINT `fk_user_artpiece` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Beperkingen voor tabel `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `fk_bids_auctions1` FOREIGN KEY (`auction_id`) REFERENCES `auctions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_bids_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Beperkingen voor tabel `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Beperkingen voor tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_orders_adresses1` FOREIGN KEY (`order_adress_id`) REFERENCES `orders_adresses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_orders_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Beperkingen voor tabel `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD CONSTRAINT `refresh_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Beperkingen voor tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_users_adresses1` FOREIGN KEY (`users_adresses_id`) REFERENCES `users_adresses` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
