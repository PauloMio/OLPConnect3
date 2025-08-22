OLPConnect3
===================

An offline LAN-based website for librarians to upload PDF eBooks, with a user-friendly interface that allows students to view them on library computers

---------------------------------------------------------
1. PHP Configuration (php.ini) - XAMPP
---------------------------------------------------------
To handle large file uploads, you must increase PHP limits.

Steps:
1. Open php.ini (in XAMPP:  C:\xampp\php\php.ini)
2. Find and update the following values:

    ; Allow up to 3GB uploads
    upload_max_filesize = 3072M
    post_max_size = 3072M

    ; Allow enough time for large uploads
    max_execution_time = 6000
    max_input_time = 6000

    ; Increase PHP memory
    memory_limit = 4096M

3. Save the file.
4. Restart Apache using the XAMPP Control Panel.

---------------------------------------------------------
2. MySQL Database Setup
---------------------------------------------------------
1. Start MySQL from XAMPP Control Panel.
2. Open phpMyAdmin (http://localhost/phpmyadmin).
3. Create the database:

    CREATE DATABASE olpconnect3;

---------------------------------------------------------
3. Import Database Tables (olpconnect3.sql)
---------------------------------------------------------
1. Open phpMyAdmin in your browser:  http://localhost/phpmyadmin
2. In the left sidebar, click on the database "olpconnect3" (that you created earlier).
3. At the top menu, click on the **Import** tab.
4. Click **Choose File** (or **Browse**) and select the file "olpconnect3.sql" from your project folder.
5. Scroll down and click **Go**.
6. phpMyAdmin will run the SQL script and create all required tables in "olpconnect3".
7. Once completed, you should see a success message and the tables will appear under the database.

---------------------------------------------------------
4. Project Structure
---------------------------------------------------------
OLPConnect3/
│
├── index.php
├── css/
├── images/
└── js/
├── tabs/
│   └── admin
|   │   └── ebooks.php
│   └── uploads
|   │   └── ebooks
|   │   └── coverage
|   │   └── announcement
│   └── user
|   │   └── guest
|   │   └── student
|   |   │   └── ebook_collection.php
|   |   │   └── ebook_collection.php
├── database/
│   └── db_connection.php