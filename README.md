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
├── css/
├── database/
│   └── db_connection.php
├── images/
│   └── icons/
└── js/
├── tabs/
│   └── admin
|   │   └── accounts.php
|   │   └── admin.php
|   │   └── announcement.php
|   │   └── dashboard.php
|   │   └── department.php
|   │   └── ebook_categories.php
|   │   └── ebook_location.php
|   │   └── ebooks.php
|   │   └── footer.php
|   │   └── generate_pdf.php
|   │   └── guest_record.php
|   │   └── login_admin.php
|   │   └── program_user.php
|   │   └── research_category.php
|   │   └── research.php
|   │   └── sidebar.php
│   └── uploads
|   │   └── ebooks
|   │   └── coverage
|   │   └── announcement
│   └── user
|   │   └── guest
|   |   │   └── ebook_collection.php
|   |   │   └── ebook_details.php
|   |   │   └── research_list_guest.php
|   │   └── student
|   |   │   └── ebook_collection.php
|   |   │   └── ebook_details.php
|   |   │   └── favorites_collection.php
|   |   │   └── logIn.php
|   |   │   └── research_list.php
|   |   │   └── sidebar.php
|   |   │   └── toggle_favorites.php
├── .gitignore
├── index.php
├── olpconnect3.sql
├── README.md
├── sidebar.php

---------------------------------------------------------
4. Add folders
---------------------------------------------------------
Inside the "tabs" folder make a new folder named:
uploads

Then inside "uploads" folder make three folders:
1. ebooks
2. coverage
3. announcement

---------------------------------------------------------
6. Network set up
---------------------------------------------------------
1. Edit httpd.conf

File location:

C:/xampp/apache/conf/httpd.conf


Find this section:

<Directory "C:/xampp/htdocs">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride All
    Require all granted
</Directory>


Replace it with this:

<Directory "C:/xampp/htdocs">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride All
    Require all denied
    LimitRequestBody 2147483647
</Directory>


This denies access to htdocs globally, but later you’ll open access for your project via a VirtualHost.

LimitRequestBody sets a large upload limit (~2GB).

Still in httpd.conf, find:

Listen 80


and add a new line below it:

Listen 8001


This makes Apache listen on port 8001 in addition to port 80.

2. Edit httpd-vhosts.conf

File location:

C:/xampp/apache/conf/extra/httpd-vhosts.conf


Add this block at the bottom of the file:

<VirtualHost *:8001>
    ServerAdmin webmaster@localhost
    DocumentRoot "C:/xampp/htdocs/OLPConnect3"
    ServerName olpconnect.test

    <Directory "C:/xampp/htdocs/OLPConnect3">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>


🔎 Explanation:

DocumentRoot "C:/xampp/htdocs/OLPConnect3"
→ This points Apache to the folder where your index.php is located.

<Directory "C:/xampp/htdocs/OLPConnect3">
→ Grants access to everything inside your project folder.

Require all granted
→ Makes the site accessible from other PCs in the network.

3. Edit hosts file (Windows)

File location:

C:/Windows/System32/drivers/etc/hosts


Add a new line:

10.0.0.43    olpconnect.test


Replace 10.0.0.43 with your actual IPv4 address.

To check your IPv4 address:
Open Command Prompt and run:

ipconfig

4. Restart Apache

Open XAMPP Control Panel.

Stop and Start Apache.

🚀 How to Access

On your PC:

http://olpconnect.test:8001/


On another PC on the same network:

http://10.0.0.43:8001/


(or http://olpconnect.test:8001/ if they also update their hosts file).

---------------------------------------------------------
8. Routes
---------------------------------------------------------
"Admin Log In":
http://olpconnect.test:8001/tabs/admin/login_admin.php

"Home Screen":
http://olpconnect.test:8001/

---------------------------------------------------------
9. Default Users
---------------------------------------------------------
Username: admin
Email: admin@gmail.com
Password: admin123

Username: boyzmaker
Email: zyril.evangelista@gmail.com
Password: password

Username: pjem
Email: pjem@gmail.com
Password: password

Username: Nik
Email: defendingdemigod1975@gmail.com
Password: password

Username: Olpcc College Library
Email: olpcccollegelibrary@gmail.com
Password: Olpcc1949

---------------------------------------------------------
8. Routes
---------------------------------------------------------
"Admin Log In":
http://olpconnect.test:8001/tabs/admin/login_admin.php

"Home Screen":
http://olpconnect.test:8001/