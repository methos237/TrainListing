CREATE TABLE `trains` (
    `id` int NOT NULL AUTO_INCREMENT,
    `line` varchar(45) DEFAULT NULL,
    `route` varchar(45) DEFAULT NULL,
    `run_number` varchar(45) DEFAULT NULL,
    `operator_id` varchar(45) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id_UNIQUE` (`id`),
    UNIQUE KEY `trains_UNIQUE` (`line`,`route`,`run_number`,`operator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;