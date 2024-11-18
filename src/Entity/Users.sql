INSERT INTO users (email, roles, password, first_name, name)
    VALUES (
        "zoo.arcadia.martinon@gmail.com",
        '["ROLE_ADMIN"]',
        "$2y$13$sDIPXa2YIgWCBeEhBJniXOgYDin2fIVQZOcoDwcA5Z5WLe7WHKbIW",
        "José",
        "Martinon",
        true
    );

    SHOW COLUMNS FROM users LIKE 'roles';

    SHOW CREATE TABLE users;