--
-- Table structure for table `users_auth`
--

CREATE TABLE `users_auth` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super','admin','user') DEFAULT NULL,
  `token` varchar(200) DEFAULT NULL,
  `expiration_token` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_auth`
-- password: Cesar203.35
--

INSERT INTO `users_auth` (`id`, `user`, `email`, `email_verified_at`, `password`, `role`, `token`, `expiration_token`, `active`) VALUES
(1, 'cesar', 'perucaos@gmail.com', NULL, 'ab4ec94da2a4e2bf28e254d636c2f83aef9d723b', 'admin', NULL, NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users_auth`
--
ALTER TABLE `users_auth`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `users_auth_email_unique` (`email`) USING BTREE,
  ADD UNIQUE KEY `users_auth_user_unique` (`user`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users_auth`
--
ALTER TABLE `users_auth`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

