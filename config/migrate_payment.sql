-- =====================================================
-- RedDoorz Payment System Migration
-- Run this on your existing reddoorz database.
-- Safe to run multiple times (uses IF NOT EXISTS).
-- =====================================================

USE reddoorz;

-- Add booking reference code column to Bookings
ALTER TABLE Bookings
    ADD COLUMN IF NOT EXISTS Book_RefCode VARCHAR(20) UNIQUE DEFAULT NULL;

-- Create Payments table
CREATE TABLE IF NOT EXISTS Payments (
    Paymt_Id       INT AUTO_INCREMENT PRIMARY KEY,
    Paymt_BookId   INT NOT NULL UNIQUE,
    Paymt_Amount   DECIMAL(10,2) NOT NULL,
    Paymt_Method   ENUM('gcash','maya','credit_card','pay_at_hotel') NOT NULL,
    Paymt_Status   ENUM('paid','pending_collection') DEFAULT 'paid',
    Paymt_RefCode  VARCHAR(100) DEFAULT NULL,
    Paymt_Date     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Paymt_BookId) REFERENCES Bookings(Book_Id) ON DELETE CASCADE
);
