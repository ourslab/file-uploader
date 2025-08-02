CREATE TABLE `ArchivedData` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `channel` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `time` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7737 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Backgrounds` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=341 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Birthday` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `year` int DEFAULT NULL,
  `month` int DEFAULT NULL,
  `day` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `IP` (
  `id` int NOT NULL AUTO_INCREMENT,
  `addr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `user_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Links` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `SentFiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `code` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tag` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `stt` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9053 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ServerConfig` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
INSERT INTO `ServerConfig` VALUES (1,'debug','0'),(2,'https_only','1'),(3,'domain_name','www.momiji.ip-ddns.com'),(4,'domain_only','1'),(5,'ip_address','172.23.72.58'),(6,'ip_only','0'),(7,'public_mode','0'),(8,'default_background_public','/public-background.webp'),(9,'generate_enabled','1'),(10,'files_per_page','25'),(11,'image_extensions','png:jpg:jpeg:tif:jfif:heic:bmp:webp:eps:gif'),(12,'reload_time','10000');

CREATE TABLE `SlackUsers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `user_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

