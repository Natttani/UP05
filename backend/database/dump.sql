CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    login TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    full_name TEXT NOT NULL,
    phone TEXT NOT NULL,
    email TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL
);

CREATE TABLE payment_methods (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL
);

CREATE TABLE statuses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL
);

CREATE TABLE applications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    address TEXT NOT NULL,
    phone TEXT NOT NULL,
    date TEXT NOT NULL,
    time TEXT NOT NULL,
    service_id INTEGER NOT NULL,
    payment_id INTEGER NOT NULL,
    status_id INTEGER NOT NULL DEFAULT 1,
    custom_service_desc TEXT NULL,
    cancel_reason TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (payment_id) REFERENCES payment_methods(id),
    FOREIGN KEY (status_id) REFERENCES statuses(id)
);

CREATE TABLE reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    application_id INTEGER NOT NULL UNIQUE,
    user_id INTEGER NOT NULL,
    text TEXT NOT NULL,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


INSERT INTO statuses (id, name) VALUES
    (1, 'Новая'),
    (2, 'В работе'),
    (3, 'Выполнено'),
    (4, 'Отменено');


INSERT INTO services (id, name) VALUES
    (1, 'Общий клининг'),
    (2, 'Генеральная уборка'),
    (3, 'Послестроительная уборка'),
    (4, 'Химчистка ковров и мебели'),
    (5, 'Иная услуга');

INSERT INTO payment_methods (id, name) VALUES
    (1, 'Наличными'),
    (2, 'Банковской картой');

-- Пароль: Chisto2026 (bcrypt cost 12, проверен password_verify).
INSERT INTO users (login, password, full_name, phone, email, role) VALUES (
    'Admin',
    '$2y$12$/6Jbj4bAeZ.8SKHGDsQBuuDPqoN/YUCr89FSgF7XnMnaDCfJAkyAG',
    'Администратор',
    '8(999)999-99-99',
    'admin@blesk.ru',
    'admin'
);