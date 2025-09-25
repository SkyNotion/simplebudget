-- Postgresql syntax

-- create user
CREATE USER budget WITH PASSWORD '<MY_PASSWORD>';

-- create database
CREATE DATABASE budget;

-- give our user budget all database privileges
GRANT ALL PRIVILEGES ON DATABASE budget TO budget;

-- make the role the owner of the database
ALTER DATABASE budget OWNER TO budget;

-- create a table to store users
CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT NULL
);

-- create a table to store accounts
CREATE TABLE IF NOT EXISTS accounts (
    id BIGSERIAL PRIMARY KEY,
    parent_id BIGINT DEFAULT NULL REFERENCES accounts(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    balance DOUBLE PRECISION NOT NULL DEFAULT 0.0,
    balance_limit DOUBLE PRECISION DEFAULT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT "NGN",
    type VARCHAR(255) NOT NULL DEFAULT "Bank",
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT NULL
);

-- create a table to store transactions
CREATE TABLE IF NOT EXISTS transactions (
    id BIGSERIAL PRIMARY KEY,
    account_id BIGINT NOT NULL REFERENCES accounts(id) ON DELETE CASCADE,
    description TEXT DEFAULT NULL,
    amount DOUBLE PRECISION DEFAULT NULL,
    type VARCHAR(1) DEFAULT NULL,
    balance DOUBLE PRECISION DEFAULT NULL,
    created_at TIMESTAMP DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT NULL
);

-- create a table to store budgets
CREATE TABLE IF NOT EXISTS budgets (
    id BIGSERIAL PRIMARY KEY,
    account_id BIGINT NOT NULL REFERENCES accounts(id) ON DELETE CASCADE,
    name VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    budget DOUBLE PRECISION NOT NULL,
    balance DOUBLE PRECISION NOT NULL,
    entities TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT NULL
);