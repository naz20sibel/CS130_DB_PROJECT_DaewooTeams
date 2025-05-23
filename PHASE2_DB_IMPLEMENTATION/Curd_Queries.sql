
--Curd operations queries demonstration for few tables. NOTE: BELOW QUERIES ARE NOT EXECUTED BECAUSE MY DATA IS ALREADY CORRECT AND USEFUL.

-- BUSES table CRUD operations

-- Read
SELECT * FROM BUSES WHERE status = 'active';

-- Update
UPDATE BUSES SET capacity = 55 WHERE bus_id = 1;

-- Delete
DELETE FROM BUSES WHERE bus_id = 5 AND status = 'inactive';

-- BUS_AMENITIES table CRUD operations

-- Read
SELECT * FROM BUS_AMENITIES WHERE bus_id = 1;

-- Update
UPDATE BUS_AMENITIES SET is_available = 'N' WHERE amenity_id = 1;

-- Delete
DELETE FROM BUS_AMENITIES WHERE amenity_id = 3 AND bus_id = 3;

-- STAFF table CRUD operations

-- Read
SELECT * FROM STAFF WHERE role = 'Driver';

-- Update
UPDATE STAFF SET phone = '03001234568' WHERE staff_id = 1;

-- Delete
DELETE FROM STAFF WHERE staff_id = 3 AND status != 'Active';

-- CITIES table CRUD operations

-- Read
SELECT * FROM CITIES ORDER BY city_name;

-- Update
UPDATE CITIES SET city_name = 'Islamabad Capital' WHERE city_id = 4;

-- Delete
DELETE FROM CITIES WHERE city_id = 11 AND city_name = 'Sialkot';

-- STATIONS table CRUD operations

-- Read
SELECT * FROM STATIONS WHERE city_id = 6;

-- Update
UPDATE STATIONS SET contact_number = '042-111-123457' WHERE station_id = 1;

-- Delete
DELETE FROM STATIONS WHERE station_id = 10 AND city_id = 11;

-- ROUTES table CRUD operations


-- Read
SELECT * FROM ROUTES WHERE distance_km > 200;

-- Update
UPDATE ROUTES SET distance_km = 155 WHERE route_id = 1;

-- Delete
DELETE FROM ROUTES WHERE route_id = 26;

-- BUS_SCHEDULES table CRUD operations

-- Read
SELECT * FROM BUS_SCHEDULES WHERE available_seats > 40;

-- Update
UPDATE BUS_SCHEDULES SET fare = 850.00 WHERE schedule_id = 1;

-- Delete
DELETE FROM BUS_SCHEDULES WHERE schedule_id = 5;

-- PASSENGERS table CRUD operations

-- Read
SELECT * FROM PASSENGERS WHERE email LIKE '%@example.com';

-- Update
UPDATE PASSENGERS SET mobile = '03451234568' WHERE passenger_id = 1;

-- Delete
DELETE FROM PASSENGERS WHERE passenger_id = 1;

-- ADMINS table CRUD operations
-- Create
INSERT INTO ADMINS (email, password, full_name) 
VALUES ('admin3@example.com', 'password789', 'Admin Three');

-- Read
SELECT * FROM ADMINS WHERE email = 'admin1@example.com';

-- Update
UPDATE ADMINS SET password = 'newpassword123' WHERE admin_id = 1;

-- Delete
DELETE FROM ADMINS WHERE admin_id = 2;

-- SIGNUP table CRUD operations
-- Create
INSERT INTO SIGNUP (first_name, last_name, email, password, phone) 
VALUES ('Usman', 'Ali', 'usman.ali@example.com', 'mypassword', '03331234567');

-- Read
SELECT * FROM SIGNUP WHERE email = 'usman.ali@example.com';

-- Update
UPDATE SIGNUP SET phone = '03331234568' WHERE user_id = 1;

-- Delete
DELETE FROM SIGNUP WHERE user_id = 1;

