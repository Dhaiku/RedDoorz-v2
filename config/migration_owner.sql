-- ============================================================
-- RedDoorz Hotel Owner Panel — Database Migration
-- Compatible with: MariaDB 10.4 / MySQL 5.7+
-- Run against the `reddoorz` database
-- ============================================================

USE reddoorz;

-- ============================================================
-- 1. Add Hotel_OwnerId to Hotels (safe — skips if exists)
-- ============================================================
SET @col_exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'Hotels'
      AND COLUMN_NAME  = 'Hotel_OwnerId'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE Hotels ADD COLUMN Hotel_OwnerId INT NULL DEFAULT NULL',
    'SELECT "Hotel_OwnerId already exists, skipping" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================
-- 2. Add Acct_MustChangePassword to Accounts (safe)
-- ============================================================
SET @col_exists = (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'Accounts'
      AND COLUMN_NAME  = 'Acct_MustChangePassword'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE Accounts ADD COLUMN Acct_MustChangePassword TINYINT(1) NOT NULL DEFAULT 0',
    'SELECT "Acct_MustChangePassword already exists, skipping" AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ============================================================
-- 3. HotelStaff
-- ============================================================
CREATE TABLE IF NOT EXISTS HotelStaff (
    Staff_Id        INT AUTO_INCREMENT PRIMARY KEY,
    Staff_HotelId   INT         NOT NULL,
    Staff_OwnerId   INT         NOT NULL,
    Staff_Name      VARCHAR(120) NOT NULL,
    Staff_Role      VARCHAR(60)  NOT NULL DEFAULT 'front_desk',
    Staff_Phone     VARCHAR(20)  NULL,
    Staff_Email     VARCHAR(120) NULL,
    Staff_Status    VARCHAR(20)  NOT NULL DEFAULT 'active',
    Staff_CreatedAt TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Staff_HotelId) REFERENCES Hotels(Hotel_Id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 4. BlockedDates
-- ============================================================
CREATE TABLE IF NOT EXISTS BlockedDates (
    Block_Id        INT AUTO_INCREMENT PRIMARY KEY,
    Block_RoomId    INT  NOT NULL,
    Block_HotelId   INT  NOT NULL,
    Block_DateFrom  DATE NOT NULL,
    Block_DateTo    DATE NOT NULL,
    Block_Reason    VARCHAR(120) NOT NULL DEFAULT 'maintenance',
    Block_CreatedAt TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Block_RoomId)  REFERENCES Rooms(Room_Id)   ON DELETE CASCADE,
    FOREIGN KEY (Block_HotelId) REFERENCES Hotels(Hotel_Id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 5. Earnings  (85% owner / 15% platform)
-- ============================================================
CREATE TABLE IF NOT EXISTS Earnings (
    Earn_Id           INT AUTO_INCREMENT PRIMARY KEY,
    Earn_BookId       INT            NOT NULL,
    Earn_HotelId      INT            NOT NULL,
    Earn_OwnerId      INT            NULL,
    Earn_TotalAmount  DECIMAL(12,2)  NOT NULL,
    Earn_OwnerShare   DECIMAL(12,2)  NOT NULL,
    Earn_PlatformFee  DECIMAL(12,2)  NOT NULL,
    Earn_Status       VARCHAR(20)    NOT NULL DEFAULT 'pending',
    Earn_CreatedAt    TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Earn_BookId)  REFERENCES Bookings(Book_Id)  ON DELETE CASCADE,
    FOREIGN KEY (Earn_HotelId) REFERENCES Hotels(Hotel_Id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 6. PayoutRequests
-- ============================================================
CREATE TABLE IF NOT EXISTS PayoutRequests (
    Payout_Id         INT AUTO_INCREMENT PRIMARY KEY,
    Payout_OwnerId    INT           NOT NULL,
    Payout_HotelId    INT           NOT NULL,
    Payout_Amount     DECIMAL(12,2) NOT NULL,
    Payout_Method     VARCHAR(40)   NOT NULL DEFAULT 'bank_transfer',
    Payout_AccountNo  VARCHAR(60)   NULL,
    Payout_Status     VARCHAR(20)   NOT NULL DEFAULT 'pending',
    Payout_Note       TEXT          NULL,
    Payout_CreatedAt  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    Payout_UpdatedAt  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 7. OwnerApplications
-- ============================================================
CREATE TABLE IF NOT EXISTS OwnerApplications (
    App_Id           INT AUTO_INCREMENT PRIMARY KEY,
    App_FullName     VARCHAR(120) NOT NULL,
    App_Email        VARCHAR(120) NOT NULL,
    App_Phone        VARCHAR(20)  NOT NULL,
    App_HotelName    VARCHAR(120) NOT NULL,
    App_HotelCity    VARCHAR(80)  NOT NULL,
    App_HotelAddress TEXT         NULL,
    App_RoomCount    INT          NOT NULL DEFAULT 1,
    App_Message      TEXT         NULL,
    App_Status       VARCHAR(20)  NOT NULL DEFAULT 'pending',
    App_CreatedAt    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Done! Verify with:
-- SHOW TABLES;
-- ============================================================
