# Society Complaint Management System

A PHP/MySQL society complaint management system built for residents, staff, and administrators.

## Features

- Role-based login and dashboards
- Resident complaint submission
- Staff assignment and status updates
- Admin reports and complaint management
- Resident feedback capture
- Shared header/footer layout with Bootstrap styling
- Database seeding for default admin and staff users

## Installation

1. Copy the project into your XAMPP `htdocs` folder (e.g. `C:\xampp\htdocs\Society`).
2. Start Apache and MySQL from the XAMPP control panel.
3. Open your browser and go to `http://localhost/Society`.
4. The app automatically creates the database and tables on first load.

## Default Accounts

- Admin: `admin@domain.com` / `admin123`
- Staff: `staff@domain.com` / `staff123`

## Database

The project auto-creates the database schema in `db.php`.

For manual database import, use the included SQL file:

- `society_schema.sql`

## Files of Note

- `db.php` - database connection, schema creation, helper functions
- `header.php` / `footer.php` - shared layout components
- `index.php` - landing page
- `login.php` / `register.php` - authentication pages
- `admin_dashboard.php`, `staff_dashboard.php`, `resident_dashboard.php` - role dashboards
- `complaints.php`, `assign_complaint.php`, `update_status.php`, `feedback.php`, `reports.php` - workflow pages

## Branding

A professional SVG logo was added as `logo.svg` and is used in the shared header.

## Notes

- This app uses `mysqli` and PHP sessions.
- Make sure `display_errors` is enabled in PHP if you want debugging output during development.


