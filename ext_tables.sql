CREATE TABLE tx_securefelogin_failed_attempts
(
    `attempt_id`    bigint(20)           NOT NULL AUTO_INCREMENT,
    `user_id`       bigint(20)           NOT NULL,
    `tstamp`        int(11)  DEFAULT '0' NOT NULL COMMENT 'failed authentication on time',
    `device_cookie` longtext DEFAULT '' COMMENT 'device cookie (if present).',
    PRIMARY KEY (attempt_id)

) ENGINE = InnoDB;

CREATE TABLE tx_securefelogin_cookie_lockout
(
    `lockout_id`    bigint(20)           NOT NULL AUTO_INCREMENT,
    `device_cookie` longtext DEFAULT NULL COMMENT 'device cookie',
    `lockout_until` int(11)  DEFAULT '0' NOT NULL COMMENT 'date until the cookie is locked out',
    PRIMARY KEY (lockout_id)
) ENGINE = InnoDB;

CREATE TABLE tx_securefelogin_user
(
    `user_id`                         bigint(20)          NOT NULL AUTO_INCREMENT,
    `user_name`                       varchar(255)        NOT NULL,
    `lockout_untrusted_clients_until` int(11) DEFAULT '0' NOT NULL COMMENT 'time until untrusted clients are locked out',
    PRIMARY KEY (user_id)
) ENGINE = InnoDB;
