<?php

namespace Module\Posts;

class PostsSQL {
    function createTable( $name ) { return <<<EOF
CREATE TABLE `{$name}` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `date` date NOT NULL DEFAULT '0000-00-00',
    `text_id` int NOT NULL DEFAULT '0',
    `page_id` int NOT NULL DEFAULT '0',
    `img` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
EOF;
    }

    function dropTable( $name ) { return <<<EOF
DROP TABLE IF EXISTS `{$name}`;
EOF;
    }
}