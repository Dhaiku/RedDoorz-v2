-- Migration: Add Hotel_Amenities column
-- Run once. Safe to re-run — skips if column already exists.

SET @hasCol = (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'Hotels'
      AND COLUMN_NAME  = 'Hotel_Amenities'
);

SET @sql = IF(@hasCol = 0,
    'ALTER TABLE Hotels ADD COLUMN Hotel_Amenities VARCHAR(500) NULL DEFAULT NULL AFTER Hotel_Description',
    'SELECT "Hotel_Amenities already exists, skipping" AS info'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
