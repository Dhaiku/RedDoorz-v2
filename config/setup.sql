-- RedDoorz Hotel Booking System
-- Run this script to set up the database

CREATE DATABASE IF NOT EXISTS reddoorz;
USE reddoorz;

-- =====================
-- ACCOUNTS
-- =====================
CREATE TABLE IF NOT EXISTS Accounts (
    Acct_Id              INT AUTO_INCREMENT PRIMARY KEY,
    Acct_Email           VARCHAR(100) UNIQUE NOT NULL,
    Acct_Password        VARCHAR(255) NOT NULL,
    Acct_Role            ENUM('customer','admin') DEFAULT 'customer',
    Acct_Status          ENUM('active','inactive') DEFAULT 'active',
    Acct_MustChangePassword TINYINT(1) DEFAULT 0,
    Acct_CreatedAt       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- CUSTOMERS
-- =====================
CREATE TABLE IF NOT EXISTS Customers (
    Cust_Id     INT AUTO_INCREMENT PRIMARY KEY,
    Cust_AcctId INT NOT NULL,
    Cust_FName  VARCHAR(50) NOT NULL,
    Cust_LName  VARCHAR(50) NOT NULL,
    Cust_Phone  VARCHAR(20),
    FOREIGN KEY (Cust_AcctId) REFERENCES Accounts(Acct_Id) ON DELETE CASCADE
);

-- =====================
-- HOTELS
-- =====================
CREATE TABLE IF NOT EXISTS Hotels (
    Hotel_Id          INT AUTO_INCREMENT PRIMARY KEY,
    Hotel_Name        VARCHAR(150) NOT NULL,
    Hotel_City        VARCHAR(100) NOT NULL,
    Hotel_Address     TEXT,
    Hotel_Description TEXT,
    Hotel_Image       VARCHAR(255) DEFAULT 'assets/images/hotel_default.jpg',
    Hotel_Rating      DECIMAL(2,1) DEFAULT 4.0,
    Hotel_Status      ENUM('active','inactive') DEFAULT 'active',
    Hotel_CreatedAt   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================
-- ROOMS
-- =====================
CREATE TABLE IF NOT EXISTS Rooms (
    Room_Id          INT AUTO_INCREMENT PRIMARY KEY,
    Room_HotelId     INT NOT NULL,
    Room_Type        VARCHAR(50) NOT NULL,
    Room_Price       DECIMAL(10,2) NOT NULL,
    Room_Capacity    INT DEFAULT 2,
    Room_Description TEXT,
    Room_Status      ENUM('available','unavailable') DEFAULT 'available',
    FOREIGN KEY (Room_HotelId) REFERENCES Hotels(Hotel_Id) ON DELETE CASCADE
);

-- =====================
-- BOOKINGS
-- =====================
CREATE TABLE IF NOT EXISTS Bookings (
    Book_Id         INT AUTO_INCREMENT PRIMARY KEY,
    Book_CustId     INT NOT NULL,
    Book_HotelId    INT NOT NULL,
    Book_RoomId     INT NOT NULL,
    Book_CheckIn    DATE NOT NULL,
    Book_CheckOut   DATE NOT NULL,
    Book_Guests     INT DEFAULT 1,
    Book_TotalPrice DECIMAL(10,2) NOT NULL,
    Book_RefCode    VARCHAR(20) UNIQUE DEFAULT NULL,
    Book_Status     ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
    Book_CreatedAt  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Book_CustId)  REFERENCES Customers(Cust_Id),
    FOREIGN KEY (Book_HotelId) REFERENCES Hotels(Hotel_Id),
    FOREIGN KEY (Book_RoomId)  REFERENCES Rooms(Room_Id)
);

-- =====================
-- PAYMENTS
-- =====================
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

-- =====================
-- SEED: Admin account
-- password: admin123
-- =====================
INSERT INTO Accounts (Acct_Email, Acct_Password, Acct_Role) VALUES
('admin@reddoorz.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- =====================
-- SEED: Sample hotels
-- =====================
INSERT INTO Hotels (Hotel_Name, Hotel_City, Hotel_Address, Hotel_Description, Hotel_Rating) VALUES
('RedDoorz @ Makati CBD', 'Makati', 'Ayala Avenue, Makati City, Metro Manila', 'A cozy and affordable hotel in the heart of Makati CBD. Walking distance to major malls and business centers.', 4.3),
('RedDoorz Plus @ BGC', 'Taguig', 'Bonifacio Global City, Taguig, Metro Manila', 'Modern rooms with city views. Located in the vibrant BGC district with easy access to restaurants and nightlife.', 4.5),
('RedDoorz @ Cebu City Center', 'Cebu City', 'Colon Street, Cebu City', 'Centrally located hotel perfect for exploring Cebu. Clean, comfortable rooms at budget-friendly prices.', 4.1),
('RedDoorz @ Davao City', 'Davao', 'Rizal Street, Davao City', 'Welcoming hotel near Davao City landmarks. Enjoy fresh durian nearby and explore the southern gem of the Philippines.', 4.2),
('RedDoorz @ Boracay Station 1', 'Malay', 'Station 1, White Beach, Boracay Island', 'Steps away from the world-famous White Beach. Perfect base for your Boracay adventure.', 4.6),
('RedDoorz Premium @ Quezon City', 'Quezon City', 'Timog Avenue, Quezon City, Metro Manila', 'Premium rooms with enhanced amenities. Ideal for both business and leisure travelers in QC.', 4.4);

-- =====================
-- SEED: Sample rooms
-- =====================
INSERT INTO Rooms (Room_HotelId, Room_Type, Room_Price, Room_Capacity, Room_Description) VALUES
-- Makati
(1, 'Standard Room',    899.00,  2, 'Comfortable room with double bed, free WiFi, and cable TV.'),
(1, 'Deluxe Room',     1299.00,  2, 'Spacious room with queen bed, mini-fridge, and city view.'),
(1, 'Triple Room',     1499.00,  3, 'Room with three single beds. Perfect for small groups.'),
-- BGC
(2, 'Standard Room',   1199.00,  2, 'Modern room with premium bedding and high-speed WiFi.'),
(2, 'Deluxe Room',     1699.00,  2, 'Spacious deluxe room with BGC skyline view and work desk.'),
(2, 'Suite',           2499.00,  2, 'Executive suite with separate living area and premium amenities.'),
-- Cebu
(3, 'Standard Room',    799.00,  2, 'Clean and cozy room with essential amenities and free WiFi.'),
(3, 'Deluxe Room',     1099.00,  2, 'Upgraded room with larger bed and modern furnishings.'),
-- Davao
(4, 'Standard Room',    749.00,  2, 'Comfortable room with free WiFi and air conditioning.'),
(4, 'Deluxe Room',     1049.00,  2, 'Deluxe room with enhanced furnishings and city view.'),
(4, 'Family Room',     1399.00,  4, 'Spacious family room with two double beds.'),
-- Boracay
(5, 'Standard Room',   1599.00,  2, 'Beachside room with tropical vibe and sea breeze.'),
(5, 'Deluxe Room',     2199.00,  2, 'Superior room with partial sea view and premium bedding.'),
(5, 'Beach Suite',     3499.00,  2, 'Stunning suite with direct beach view and private balcony.'),
-- Quezon City
(6, 'Standard Room',    999.00,  2, 'Premium standard room with plush bedding and smart TV.'),
(6, 'Deluxe Room',     1499.00,  2, 'Spacious deluxe room with work area and enhanced bathroom.'),
(6, 'Suite',           2299.00,  2, 'Luxurious suite with separate lounge and premium amenities.');
