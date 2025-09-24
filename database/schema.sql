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
    user_id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- create a table to store accounts
CREATE TABLE IF NOT EXISTS accounts (
    account_id BIGSERIAL PRIMARY KEY,
    parent_id BIGINT REFERENCES accounts(account_id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
    name VARCHAR(255) NOT NULL,
    balance DOUBLE PRECISION NOT NULL DEFAULT 0.0,
    balance_limit DOUBLE PRECISION,
    currency VARCHAR(3) NOT NULL DEFAULT "NGN",
    type VARCHAR(255) NOT NULL DEFAULT "Bank",
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- create a table to store transactions
CREATE TABLE IF NOT EXISTS transactions (
    transaction_id BIGSERIAL PRIMARY KEY,
    account_id BIGINT NOT NULL REFERENCES accounts(account_id) ON DELETE CASCADE,
    description TEXT,
    deposit DOUBLE PRECISION,
    withdrawal DOUBLE PRECISION,
    balance DOUBLE PRECISION NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- create a table to store budgets
CREATE TABLE IF NOT EXISTS budgets (
    budget_id BIGSERIAL PRIMARY KEY,
    account_id BIGINT NOT NULL REFERENCES accounts(account_id) ON DELETE CASCADE,
    name TEXT,
    description TEXT,
    budget DOUBLE PRECISION,
    balance DOUBLE PRECISION,
    entities TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);