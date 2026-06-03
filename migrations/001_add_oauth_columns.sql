-- Migration: add oauth columns for external providers
-- Run this against your `ooplogin` database (or the DB used by this app)

ALTER TABLE users
  ADD COLUMN oauth_provider VARCHAR(50) NULL,
  ADD COLUMN oauth_uid VARCHAR(255) NULL;

-- Optional: index for quick lookup
CREATE INDEX idx_users_oauth ON users (oauth_provider, oauth_uid);
