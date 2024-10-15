INSERT INTO users (email, roles, password, first_name, name, is_verified)
    VALUES (
        "zoo.arcadia.martinon@gmail.com",
        '["ROLE_ADMIN"]',
        "$vU9o4JNRxksgclc7XltUduZInECHQFqGPnHkHELt7GXaYbyQisgaK",
        "José",
        "Martinon",
        true
    );

    SHOW COLUMNS FROM users LIKE 'roles';

    SHOW CREATE TABLE users;