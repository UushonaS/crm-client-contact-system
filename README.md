CRM Client-Contact System
Overview
This is a simple CRM system built with PHP and MySQL to manage clients and their contacts. It allows users to add clients, add contacts, link and unlink contacts to clients, and view relevant data.

Features
Add, view, and manage clients.

Add, view, and manage contacts.

Link and unlink contacts with clients.

Auto-generated unique client codes (3 uppercase letters + 3 digits).

User-friendly UI with input validation.

Admin access control.

Technologies Used
PHP (without frameworks)

MySQL (via mysqli)

HTML/CSS/JavaScript for frontend validation

Apache (XAMPP environment)

Installation
Clone the repository:

bash
Copy
Edit
git clone https://github.com/UushonaS/crm-client-contact-system.git
Import the SQL database from database.sql file (or create tables according to the provided schema).

Configure the database connection in db.php with your MySQL credentials.

Place the project in your web server’s root folder (e.g., htdocs for XAMPP).

Access the app via http://localhost/crm-client-contact-system/ in your browser.

Usage
Register or login as an admin.

Add clients and contacts via the respective forms.

Link contacts to clients and vice versa.

View lists of clients and contacts with their linked counterparts.

Notes
Client codes are auto-generated using the first three letters of the client name and a numeric suffix.

Input validation is performed on both client-side (JavaScript) and server-side (PHP).

Author
Selma Uushona
