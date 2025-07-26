# Security Measures Documentation
### Prepared Statements
All database interactions use prepared statements to prevent SQL injection attacks.

### Password Hashing
Passwords are hashed using password_hash() to securely store user passwords.

### Session Management:
Sessions are managed securely with session_start() and session variables. Session cookies are set to be secure and HTTP-only.

### Input Validation:
User inputs are trimmed and validated to prevent XSS and SSTI attacks.

### HTTPS
Ensure that the application is served over HTTPS to secure data transmission.

### Rate Limiting and Account Lockout
Implement rate limiting on login attempts and lock accounts after a certain number of failed attempts to prevent brute-force attacks.

### CSRF Protection
Implement CSRF tokens in forms to prevent CSRF attacks (not shown in the code above but should be added).

### IDOR Prevention
Validate user permissions for accessing resources based on session data.