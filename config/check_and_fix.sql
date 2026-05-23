-- =====================================================
-- RedDoorz — Step 1: Check what's in your database
-- Run this first to see current state
-- =====================================================
USE reddoorz;

SELECT
    Hotel_City,
    COUNT(*) AS HotelCount
FROM Hotels
WHERE Hotel_Status = 'active'
GROUP BY Hotel_City
ORDER BY Hotel_City;
