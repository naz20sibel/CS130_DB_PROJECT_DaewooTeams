## 👥 Group Members:
Saba Naz (ID:241095)

## 📋 Description:
This folder contains the frontend implementation of my Daewoo bus seat reservation and management system, built using PHP, HTML, CSS, and SQL Server. It includes dynamic forms for seat booking, admin panel, bus scheduling, and more.

## 📂 Folder Structure:

CS130_DB_PROJECT_DaewooTeams/
├── PHASE1_DB_DESIGN/ ← Database design documents
│   ├── ERD.png ← Entity Relationship Diagram
│   ├── Normalized_Tables.docx ← Normalized table structures
│   └── Schema_Design_Document.docx ← Detailed schema documentation

├── PHASE2_DB_IMPLEMENTATION/ ← SQL implementation files
│   ├── Create_Schema.sql ← SQL script to create schema
│   ├── Curd_Queries.sql ← CRUD operation queries
│   ├── DB_Backup.bak ← SQL Server backup file
│   └── Insertion_data.sql ← Data insertion script

Phase3_Bonus_Frontend/
├── php_forms/ ← All PHP forms
├── assets/ ← CSS/JS/image files
├── Screenshots/ ← Few Screenshots of frontend
├── dbconnect.php ← DB connection file


## ⚙️ How to Run the Forms:
1. Install **XAMPP** to host locally.
2. Copy `Phase3_Bonus_Frontend` to your `htdocs` or `www` directory.
3. Start Apache + SQL Server in XAMPP.
4. Import the provided database `.bak` file or SQL script into SQL Server.
5. Open browser and go to:  
http://localhost/Phase3_Bonus_Frontend/php_forms/index.php

6. Ensure `dbconnect.php` has correct server credentials.

## 📸 Screenshots:
Screenshots are inside the `Screenshots/` folder and demonstrate:
- Homepage
- Seat booking
- Admin dashboard
- Form submissions etc


## ✅ Key Features:
-  Admin Login/Logout + Dashboard
-  Bus Add/Edit/Delete Forms
-  Passenger Signup and Booking
-  Seat Reservation System
-  Staff and Vehicle Maintenance Modules
-  Payment System (Mock)
-  Noticeboard Management
-  CSS Styling and UI Forms

## 💡 Notes:
- All modules perform **CRUD operations** using SQL Server.
- PHP forms are modular and reusable.
- Bonus frontend is integrated with backend logic for real-time data operations.


🔗 **Git Repo**: https://github.com/naz20sibel/CS130_DB_PROJECT_DaewooTeams/

