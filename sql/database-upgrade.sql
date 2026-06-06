-- Add email verification support and 15-minute expiry for unverified accounts.
ALTER TABLE users
  ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN verification_code VARCHAR(64) NULL,
  ADD COLUMN verification_expires DATETIME NULL;

-- The application will automatically delete unverified accounts when the expiry time is reached.
