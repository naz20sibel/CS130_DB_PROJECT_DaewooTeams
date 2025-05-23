CREATE TABLE BUSES (
    bus_id INT IDENTITY(1,1) PRIMARY KEY,
    bus_number VARCHAR(20) NOT NULL UNIQUE,
    capacity INT NOT NULL,
    type VARCHAR(20) NOT NULL CHECK (type IN ('standard', 'premium', 'luxury')),
    status VARCHAR(20) NOT NULL CHECK (status IN ('active', 'maintenance', 'inactive'))
);



CREATE TABLE BUS_AMENITIES (
    amenity_id INT IDENTITY(1,1) PRIMARY KEY,
    bus_id INT NOT NULL,
    amenity_name VARCHAR(50) NOT NULL,
    amenity_description VARCHAR(200),
    is_available CHAR(1) DEFAULT 'Y' CHECK (is_available IN ('Y', 'N')),
    FOREIGN KEY (bus_id) REFERENCES BUSES(bus_id)
);



CREATE TABLE STAFF (
    staff_id INT IDENTITY(1,1) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role VARCHAR(20) NOT NULL,
    status VARCHAR(20) NOT NULL
);



CREATE TABLE CITIES (
    city_id INT IDENTITY(1,1) PRIMARY KEY,
    city_name VARCHAR(100) NOT NULL UNIQUE
);




CREATE TABLE STATIONS (
    station_id INT IDENTITY(1,1) PRIMARY KEY,
    station_name VARCHAR(100),
    location VARCHAR(100),
    city_id INT,
    contact_number VARCHAR(20),
    FOREIGN KEY (city_id) REFERENCES CITIES(city_id)
);




CREATE TABLE ROUTES (
    route_id INT IDENTITY(1,1) PRIMARY KEY,
    from_station_id INT NOT NULL,
    to_station_id INT NOT NULL,
    distance_km FLOAT,
    FOREIGN KEY (from_station_id) REFERENCES STATIONS(station_id),
    FOREIGN KEY (to_station_id) REFERENCES STATIONS(station_id)
);

CREATE TABLE FARE_RULES (
    rule_id INT IDENTITY(1,1) PRIMARY KEY,
    route_id INT NOT NULL,
    bus_type VARCHAR(20) NOT NULL CHECK (bus_type IN ('standard', 'premium', 'luxury')),
    fare DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (route_id) REFERENCES ROUTES(route_id)
);






CREATE TABLE BUS_SCHEDULES (
    schedule_id INT IDENTITY(1,1) PRIMARY KEY,
    bus_id INT NOT NULL,
    route_id INT NOT NULL,
    available_seats INT NOT NULL,
    FOREIGN KEY (bus_id) REFERENCES BUSES(bus_id),
    FOREIGN KEY (route_id) REFERENCES ROUTES(route_id)
);


CREATE TABLE PASSENGERS (
    passenger_id INT IDENTITY(1,1) PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    cnic VARCHAR(15) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL
);


CREATE TABLE ADMINS (
    admin_id INT IDENTITY(1,1) PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    full_name VARCHAR(100)
);



CREATE TABLE SIGNUP (
    user_id INT IDENTITY(1,1) PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL
);


CREATE TABLE PAYMENT_METHODS (
    method_id INT PRIMARY KEY,
    method_name VARCHAR(50),
    method_type VARCHAR(50),
    is_active BIT,
    account_number VARCHAR(100),
    bank_name VARCHAR(100)
);



CREATE TABLE FEEDBACK (
    feedback_id INT IDENTITY(1,1) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    message VARCHAR(500) NOT NULL
);

CREATE TABLE VEHICLE_MAINTENANCE (
    maintenance_id INT IDENTITY(1,1) PRIMARY KEY,
    bus_id INT,
    maint_type VARCHAR(50) NOT NULL,
    cost DECIMAL(10,2),
    notes VARCHAR(500),
    FOREIGN KEY (bus_id) REFERENCES BUSES(bus_id)
);




CREATE TABLE NOTICEBOARD (
    notice_id INT IDENTITY(1,1) PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    message VARCHAR(500) NOT NULL,
    posted_by INT NOT NULL,
    FOREIGN KEY (posted_by) REFERENCES ADMINS(admin_id)
);
