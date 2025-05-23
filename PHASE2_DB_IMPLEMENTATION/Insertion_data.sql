INSERT INTO BUSES (BUS_NUMBER, CAPACITY, TYPE, STATUS)
VALUES ('PKR-1001', 45, 'standard', 'active'),
       ('PKR-1002', 50, 'premium', 'maintenance'),
       ('PKR-1003', 40, 'luxury', 'inactive'),
       ('PKR-1004', 55, 'standard', 'active'),
       ('PKR-1005', 35, 'luxury', 'active');

INSERT INTO BUS_AMENITIES (bus_id, amenity_name, amenity_description, is_available)
VALUES (1, 'WiFi', 'High-speed wireless internet available', 'Y'),
       (2, 'Recliner Seats', 'Comfortable seats that recline for long journeys', 'N'),
       (3, 'Air Conditioning', 'Automatic climate control system', 'Y');


INSERT INTO STAFF (first_name, last_name, email, phone, role, status)
VALUES ('Ali', 'Khan', 'ali.khan@example.com', '03001234567', 'Driver', 'Active'),
       ('Sara', 'Ahmed', 'sara.ahmed@example.com', '03111234567', 'Conductor', 'On Leave'),
       ('Usman', 'Raza', 'usman.raza@example.com', '03211234567', 'Mechanic', 'Active');


INSERT INTO CITIES (city_name) 
VALUES ('Faisalabad'), ('Gujranwala'), ('Hyderabad'), ('Islamabad'), ('Karachi'),
       ('Lahore'), ('Multan'), ('Peshawar'), ('Quetta'), ('Rawalpindi'), ('Sialkot');


INSERT INTO STATIONS (station_name, location, city_id, contact_number)
VALUES ('Lahore General Bus Stand', 'Badami Bagh', 6, '042-111-123456'),
       ('Karachi Bus Terminal', 'Sohrab Goth', 5, '021-111-654321'),
       ('Rawalpindi Faizabad Terminal', 'Faizabad', 10, '051-111-111111'),
       ('Peshawar General Bus Stand', 'GT Road', 7, '091-111-222333'),
       ('Multan Bus Stand', 'Vehari Chowk', 4, '061-111-333444'),
       ('Faisalabad City Terminal', 'Jhang Road', 1, '041-111-555666'),
       ('Quetta Bus Terminal', 'Sariab Road', 8, '081-111-777888'),
       ('Hyderabad Bus Terminal', 'Auto Bhan Road', 3, '022-111-888999'),
       ('Gujranwala General Bus Stand', 'GT Road', 2, '055-111-000111'),
       ('Sialkot Bus Terminal', 'Daska Road', 11, '052-111-222000');



INSERT INTO ROUTES (from_station_id, to_station_id, distance_km)
VALUES 
(1, 2, 150),    -- Route 2
(2, 3, 200),    -- Route 3
(3, 4, 180),    -- Route 4
(4, 5, 170),    -- Route 5
(5, 6, 160),    -- Route 6
(6, 7, 140),    -- Route 7
(7, 8, 130),    -- Route 8
(8, 9, 120),    -- Route 9
(9, 10, 110),   -- Route 10
(10, 1, 300),   -- Route 11
(2, 1, 150),    -- Route 12
(3, 2, 200),    -- Route 13
(4, 3, 180),    -- Route 14
(5, 4, 170),    -- Route 15
(6, 5, 160),    -- Route 16
(7, 6, 140),    -- Route 17
(8, 7, 130),    -- Route 18
(9, 8, 120),    -- Route 19
(10, 9, 110),   -- Route 20
(1, 3, 350),    -- Route 22
(2, 8, 150),    -- Route 23
(3, 6, 280),    -- Route 24
(7, 2, 700),    -- Route 25
(10, 9, 50);    -- Route 26



INSERT INTO FARE_RULES (route_id, bus_type, fare)
VALUES 
(2, 'standard', 500),
(3, 'standard', 600),
(4, 'premium', 550),
(5, 'premium', 650),
(6, 'premium', 700),
(7, 'premium', 720),
(8, 'luxury', 600),
(9, 'luxury', 650),
(10, 'luxury', 750),
(11, 'luxury', 800),
(12, 'standard', 1200);





INSERT INTO BUS_SCHEDULES (bus_id, route_id, available_seats) VALUES
(1, 2, 40),
(1, 3, 42),
(2, 4, 45),
(2, 5, 48),
(3, 6, 38),
(3, 7,  36),
(4, 8,  50),
(4, 9,  53),
(5, 10,  30),
(5, 11,  32),
(1, 12, 40);




INSERT INTO ADMINS (email, password, full_name)
VALUES ('admin1@example.com', 'password123', 'Admin One'),
       ('admin2@example.com', 'password456', 'Admin Two');



INSERT INTO PAYMENT_METHODS (method_id, method_name, method_type, is_active, account_number, bank_name) VALUES
(1, 'Credit Card', 'Card', 1, NULL, NULL),
(2, 'PayPal', 'Online', 1, 'paypal@example.com', NULL),
(3, 'Bank Transfer', 'Banking', 1, '1234567890', 'ABC Bank'),
(4, 'Cash on Delivery', 'Cash', 1, NULL, NULL);




INSERT INTO VEHICLE_MAINTENANCE (bus_id, maint_type, cost, notes)
VALUES 
(1, 'Engine Repair', 1500.00, 'Replaced engine parts due to wear and tear.'),
(2, 'Tire Replacement', 800.00, 'Replaced two tires with new ones.'),
(3, 'Air Conditioning Service', 500.00, 'Serviced AC unit for improved cooling performance.'),
(4, 'Brake System Overhaul', 1200.00, 'Complete overhaul of the brake system for safety.'),
(5, 'Oil Change', 200.00, 'Changed engine oil and filter.');



