-- create user
CREATE USER budget WITH PASSWORD '<MY_PASSWORD>';

-- create database
CREATE DATABASE budget;

--  give our user budget all database privileges
GRANT ALL PRIVILEGES ON DATABASE budget TO budget;

-- make the role the owner of the database
ALTER DATABASE budget OWNER TO budget;

-- connect to our database
\c budget;

-- enable the pgcrypto extension in the database so we can use cryptographic encryption and hashing
CREATE EXTENSION pgcrypto;

-- create function to update time on updated_at column of any row
CREATE OR REPLACE FUNCTION set_update_at()
RETURNS TRIGGER AS $$
BEGIN
	NEW.updated_at := NOW();
	RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- create a table to store users
CREATE TABLE IF NOT EXISTS users (
	user_id BIGSERIAL PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	telegram_username VARCHAR(255),
	created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
);

-- create a table to store api keys
CREATE TABLE IF NOT EXISTS api_keys (
	key_id BIGSERIAL PRIMARY KEY,
	user_id BIGINT NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
	name VARCHAR(255),
	api_key VARCHAR(255) NOT NULL,
	created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- create a table to store accounts
CREATE TABLE IF NOT EXISTS accounts (
	account_id BIGSERIAL PRIMARY KEY,
	parent_id BIGINT REFERENCES accounts(account_id) ON DELETE CASCADE,
	user_id BIGINT NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
	name VARCHAR(255) NOT NULL,
	balance DOUBLE PRECISION NOT NULL DEFAULT 0.0,
	balance_limit DOUBLE PRECISION,
	currency VARCHAR(3) NOT NULL DEFAULT "USD",
	type VARCHAR(255) NOT NULL DEFAULT "Bank",
	description TEXT,
	created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- create to update the updated_at time when an account has been updated
CREATE OR REPLACE TRIGGER trigger_set_update_at AFTER UPDATE ON accounts FOR EACH ROW EXECUTE PROCEDURE set_update_at();

-- create a table to store transactions
CREATE TABLE IF NOT EXISTS transactions (
	transaction_id BIGSERIAL PRIMARY KEY,
	account_id BIGINT NOT NULL REFERENCES accounts(account_id) ON DELETE CASCADE,
	description TEXT,
	deposit DOUBLE PRECISION,
	withdrawal DOUBLE PRECISION,
	balance DOUBLE PRECISION NOT NULL,
	created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
);

-- create a table to store budgets
CREATE TABLE IF NOT EXISTS budgets (
	budget_id BIGSERIAL PRIMARY KEY,
	account_id BIGINT NOT NULL REFERENCES accounts(account_id) ON DELETE CASCADE,
	name TEXT,
	description TEXT,
	budget_limit DOUBLE PRECISION,
	entities TEXT,
	created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
);

-- create a table to store notifications
CREATE TABLE IF NOT EXISTS notifications (
	notification_id BIGSERIAL PRIMARY KEY,
	user_id BIGINT NOT NULL REFERENCES users(user_id) ON DELETE CASCADE,
	source VARCHAR(255) NOT NULL,
	source_id BIGINT NOT NULL,
	content TEXT NOT NULL,
	created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);