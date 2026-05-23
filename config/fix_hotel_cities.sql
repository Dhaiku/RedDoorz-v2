-- =====================================================
-- RedDoorz — Fix Hotel City Names
-- Run this in phpMyAdmin SQL tab on the reddoorz DB.
-- Fixes all city mismatches so search filters work.
-- =====================================================

USE reddoorz;

-- Fix Bali (was 'Badung' or 'Gianyar')
UPDATE Hotels SET Hotel_City = 'Bali'
WHERE Hotel_City IN ('Badung', 'Gianyar')
  AND (Hotel_Address LIKE '%Bali%' OR Hotel_Name LIKE '%Bali%'
       OR Hotel_Name LIKE '%Kuta%' OR Hotel_Name LIKE '%Seminyak%'
       OR Hotel_Name LIKE '%Ubud%' OR Hotel_Name LIKE '%Canggu%'
       OR Hotel_Name LIKE '%Sanur%' OR Hotel_Name LIKE '%Legian%'
       OR Hotel_Name LIKE '%Tegallalang%');

-- Fix Boracay (was 'Malay')
UPDATE Hotels SET Hotel_City = 'Boracay'
WHERE Hotel_City = 'Malay'
  AND (Hotel_Name LIKE '%Boracay%' OR Hotel_Address LIKE '%Boracay%');

-- Fix Lombok (keep as 'Lombok' — already correct, just verify)
-- No change needed

-- Fix Ho Chi Minh City — normalize abbreviations if any
UPDATE Hotels SET Hotel_City = 'Ho Chi Minh City'
WHERE Hotel_City IN ('HCMC', 'Ho Chi Minh', 'Saigon')
   OR (Hotel_Name LIKE '%HCMC%' AND Hotel_City != 'Ho Chi Minh City');

-- Fix Lapu-Lapu (Mactan) — make searchable as Cebu
UPDATE Hotels SET Hotel_City = 'Cebu City'
WHERE Hotel_City = 'Lapu-Lapu'
  AND Hotel_Name LIKE '%Mactan%';

-- Verify — show all distinct city values after fix
SELECT DISTINCT Hotel_City, COUNT(*) AS HotelCount
FROM Hotels
WHERE Hotel_Status = 'active'
GROUP BY Hotel_City
ORDER BY Hotel_City;
