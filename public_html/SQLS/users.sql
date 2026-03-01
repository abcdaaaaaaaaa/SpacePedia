CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,

    username VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,

    email_verified TINYINT(1) NOT NULL DEFAULT 0,
    email_token VARCHAR(64),
    email_token_expires DATETIME,
    last_activation_sent DATETIME,

    reset_token VARCHAR(64),
    reset_token_expires DATETIME,
    last_reset_sent DATETIME,

    email_change_token VARCHAR(64),
    email_change_expires DATETIME,
    pending_email VARCHAR(100),
    last_email_change_sent DATETIME,

    profile_info TEXT,
    profile_image VARCHAR(255) DEFAULT 'profile_images/uzaylogo.png',
    mode ENUM('light','dark') NOT NULL DEFAULT 'light',

    verified TINYINT(1) NOT NULL DEFAULT 0,
    supporter TINYINT UNSIGNED NOT NULL DEFAULT 0,
    support_start DATETIME,
    support_end DATETIME,

    account_closed TINYINT(1) NOT NULL DEFAULT 0,
    account_closed_at DATETIME,
    account_close_count INT NOT NULL DEFAULT 0,
    account_close_reason VARCHAR(255),
    account_reopen_token VARCHAR(64),
    last_reopen_sent DATETIME,

    last_online DATETIME,

    register_ip VARCHAR(45),
    last_login_ip VARCHAR(45),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
