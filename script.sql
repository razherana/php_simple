-- Active: 1714003570230@@127.0.0.1@3306@framework2
use framework2;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50)
);

CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_sender INT,
    id_receiver INT,
    message TEXT,
    created_at DATETIME DEFAULT(NOW())
);

INSERT INTO users VALUE (DEFAULT, "herana");

INSERT INTO users VALUE (DEFAULT, "fanilo");

INSERT INTO messages VALUE ( DEFAULT, 1, 2, "Message 1", DEFAULT );

INSERT INTO messages VALUE ( DEFAULT, 1, 2, "Message 2", DEFAULT );

INSERT INTO messages VALUE ( DEFAULT, 2, 1, "Message 3", DEFAULT );

INSERT INTO messages VALUE ( DEFAULT, 2, 1, "Message 4", DEFAULT );

SELECT * FROM messages JOIN users ON users.id = id_sender;

SELECT
    users.id,
    users.name,
    r66c8dae342880_0_id,
    r66c8dae342880_0_id_sender,
    r66c8dae342880_0_id_receiver,
    r66c8dae342880_0_message,
    r66c8dae342880_0_created_at,
    r66c8dae348ebc_0_id,
    r66c8dae348ebc_0_id_message,
    r66c8dae348ebc_0_id_user,
    r66c8dae348ebc_0_type,
    r66c8dae348ebc_0_created_at
FROM users
    LEFT JOIN (
        SELECT
            r66c8dae342880.id AS r66c8dae342880_0_id, r66c8dae342880.id_sender AS r66c8dae342880_0_id_sender, r66c8dae342880.id_receiver AS r66c8dae342880_0_id_receiver, r66c8dae342880.message AS r66c8dae342880_0_message, r66c8dae342880.created_at AS r66c8dae342880_0_created_at, r66c8dae348ebc_0_id, r66c8dae348ebc_0_id_message, r66c8dae348ebc_0_id_user, r66c8dae348ebc_0_type, r66c8dae348ebc_0_created_at
        FROM
            messages AS r66c8dae342880
            LEFT JOIN (
                SELECT
                    r66c8dae348ebc.id AS r66c8dae348ebc_0_id, r66c8dae348ebc.id_message AS r66c8dae348ebc_0_id_message, r66c8dae348ebc.id_user AS r66c8dae348ebc_0_id_user, r66c8dae348ebc.type AS r66c8dae348ebc_0_type, r66c8dae348ebc.created_at AS r66c8dae348ebc_0_created_at
                FROM reactions AS r66c8dae348ebc
            ) AS r66c8dae348ebc ON (
                r66c8dae342880.id = r66c8dae348ebc_0_id_message
            )
        WHERE
            r66c8dae342880.id_sender > 5
    ) AS r66c8dae342880 ON (
        users.id = r66c8dae342880_0_id_sender
    );