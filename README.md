# Secure-Website-AI-Battle
How secure is a website that created by most famous AIs?

# Prompt
Create a fully functional authorization page system that includes login, registration, and a landing page. The system must prioritize high-level security to prevent vulnerabilities such as Insecure Direct Object Reference (IDOR), Session Injection, Broken Access Control, SQL Injection (SQLi), Blind SQL Injection, Server-Side Template Injection (SSTI), Cross-Site Scripting (XSS),  Cross-Site Request Forgery (CSRF), and Server-Side Request Forgery (SSRF). 

Requirements:

1. Front-End:
   - Use HTML and Tailwind CSS to design the user interface for the authorization pages.
   - The login page should include fields for username and password, and a submit button.
   - The registration page should include fields for username, email, password, and confirmation of the password, along with a submit button.
   - The landing page should display a welcome message and provide options to log out or navigate to other features.

2. Back-End:
   - Implement the back-end using native PHP.
   - Ensure secure handling of user sessions and cookies.
   - Use prepared statements for all database interactions to prevent SQL injection attacks.

3. Database:
   - Use MariaDB for the database.
   - Create necessary tables for users, including fields for user ID, username, email, password hash, and timestamps for created and updated records.
   - Provide the SQL queries needed to create these tables, ensuring proper data types and constraints are applied.

4. Security Measures:
   - Implement mechanisms to prevent IDOR by validating user permissions for accessing resources.
   - Use HTTPS for secure data transmission.
   - Ensure session management is robust, with secure session cookies and timeout mechanisms.
   - Implement input validation and sanitization to prevent XSS and SSTI attacks.
   - Introduce rate limiting and account lockout mechanisms to counteract brute-force attacks.

Deliverables:
- Complete HTML and Tailwind CSS code for the login, registration, and landing pages.
- PHP scripts for handling login, registration, and session management.
- SQL queries for creating the user table in MariaDB.
- Documentation outlining the security measures implemented within the code.
