
# usage

Install frontend dependencies:

    npm install

Build frontend assets:

    npm run build

Run the PHP website locally from the project root:

    php -S localhost:8000
    cd D:\KULDEEP\pro\school-website-master
    C:\xampp\php\php.exe -S localhost:8000
Then open:

    http://localhost:8000/index.html

Dynamic pages:

    http://localhost:8000/events.php
    http://localhost:8000/gallery.php
    http://localhost:8000/results.php
    http://localhost:8000/teachers.php
    http://localhost:8000/dashboard.php
    http://localhost:8000/backend/health.php

# PHP backend

The backend is in `backend/`.

- Contact form posts to `backend/contact.php`
- Admission form posts to `backend/admission.php`
- Admin login posts to `backend/login.php`
- Admin dashboard is `dashboard.php`
- Submissions are saved in MySQL tables
- Student results can be added/updated from `dashboard.php`
- Public result lookup is `results.php`
- Events and gallery can be added from `dashboard.php`
- Public events and gallery pages are `events.php` and `gallery.php`
- Teachers can be added, edited, hidden, or removed from `dashboard.php`
- Public teachers page is `teachers.php`
- Admin dashboard includes CSV exports for admissions, contact messages, and results
- Hosting Health in the dashboard checks PHP version, MySQL, writable upload folders, setup lock, and admin password hash

Default local admin login:

    username: admin
    password: admin123

Before hosting, change the admin password. Prefer a password hash instead of a plain password:

    C:\xampp\php\php.exe -r "echo password_hash('your-strong-password', PASSWORD_DEFAULT), PHP_EOL;"

Then put the generated value in `.env`:

    MBVM_ADMIN_PASSWORD_HASH=generated-hash-here

# MySQL setup with XAMPP

Start both services in XAMPP Control Panel:

    Apache
    MySQL

Create the database and tables with either option:

Option 1, browser:

Temporarily set this in `.env`:

    MBVM_ALLOW_SETUP=1

    http://localhost:8000/backend/setup_database.php

After setup, set `MBVM_ALLOW_SETUP=0` or remove it.

Option 2, phpMyAdmin:

    http://localhost/phpmyadmin

Import or run:

    backend/schema.sql

Default database settings:

    host: 127.0.0.1
    database: mbvm_school
    user: root
    password:

For local development, copy `.env.example` to `.env` and update the database/admin values:

    MBVM_DB_HOST=127.0.0.1
    MBVM_DB_NAME=mbvm_school
    MBVM_DB_USER=root
    MBVM_DB_PASS=
    MBVM_ADMIN_USERNAME=yourname
    MBVM_ADMIN_PASSWORD_HASH=your-generated-password-hash
    MBVM_ALLOW_SETUP=0
    MBVM_SHEETS_WEBHOOK_URL=
    MBVM_SHEETS_WEBHOOK_SECRET=

# Excel and Google Sheets

From `dashboard.php`, use:

    Export Admissions CSV
    Export Contacts CSV
    Export Results CSV

These CSV files open directly in Excel and can be imported into Google Sheets.

For automatic Google Sheets sync, use the Apps Script template in:

    docs/google-sheets-webhook.gs

Setup steps:

1. Create a new Google Sheet.
2. Open `Extensions > Apps Script`.
3. Paste the code from `docs/google-sheets-webhook.gs`.
4. Change `WEBHOOK_SECRET` in the script to the same strong random value you use in `.env`.
5. Click `Deploy > New deployment`.
6. Select type `Web app`.
7. Set `Execute as` to `Me`.
8. Set `Who has access` to `Anyone`.
9. Deploy and copy the web app URL.


Put the web app URL and the same secret in `.env`:

    MBVM_SHEETS_WEBHOOK_URL=https://script.google.com/macros/s/your-web-app-id/exec
    MBVM_SHEETS_WEBHOOK_SECRET=your-strong-random-secret

When this value is set, new admission, contact, and result records are posted to that webhook after they are saved in MySQL.

For admission enquiries, the uploaded ID card file is also sent to the Apps Script webhook. The script saves it in a Google Drive folder named:

    MBVM Admission Uploads

The Google Drive file URL is stored in the `id_card` column of the `Admissions` sheet. If other staff members need to open those files, share this Drive folder with them from Google Drive.

If you change `docs/google-sheets-webhook.gs` after deploying, open Apps Script and deploy a new web app version:

    Deploy > Manage deployments > Edit > Version > New version > Deploy

If the dashboard test returns HTTP `401` or a Google Drive "unable to open the file" page, create/update the Apps Script deployment again and confirm:

    Execute as: Me
    Who has access: Anyone
    URL ends with: /exec

Then paste the new `/exec` URL into `.env` as `MBVM_SHEETS_WEBHOOK_URL`.

# Hosting checklist

Use PHP hosting with MySQL, such as shared hosting/cPanel or a VPS.

1. Upload project files to the website root.
2. Create a MySQL database and user in hosting panel.
3. Import `backend/schema.sql` in phpMyAdmin.
4. Copy `.env.example` to `.env` on the server and fill real database credentials.
5. Make sure these folders are writable by PHP:

       backend/storage
       backend/storage/uploads/admissions
       backend/storage/sessions
       frontend/uploads/admin

6. Open `/login.html`, sign in, and add results/events/gallery content.
7. Open `/backend/health.php` while signed in as admin and fix any failed checks.
8. After testing, keep `.env` private and use a strong admin password.

Apache `.htaccess` is included for clean URLs:

    /events
    /gallery
    /results
    /admin
    /health

# screenshots

![](src/screenshots/1.png)
