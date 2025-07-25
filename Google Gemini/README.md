## Security Measures Documentation
This section outlines the security measures implemented in the provided code and crucial considerations for building a truly secure authorization system.

# 1. Secure Password Handling
- Password Hashing:
    - Implementation: Passwords are never stored in plain text. Instead, password_hash() is used with PASSWORD_ARGON2ID (recommended as it's resistant to brute-force and rainbow table attacks, and generally stronger than bcrypt for new applications) or PASSWORD_BCRYPT (a strong fallback).
    - Verification: password_verify() is used to compare the user-provided password with the stored hash, preventing timing attacks.

# 2. Prepared Statements (Prevention of SQL Injection - SQLi & Blind SQLi)
- Implementation: All database interactions that involve user input (e.g., login, registration) use PHP's mysqli::prepare() and bind_param() methods.
- Mechanism: Prepared statements separate the SQL query logic from the user-supplied data. This ensures that user input is treated as data, not as executable code, effectively preventing SQL injection attacks, including blind SQLi, by design.

# 3. Secure Session Management
- Session Cookie Parameters (session_set_cookie_params()):
    - lifetime: Sets an appropriate session expiry time.
    - path: Ensures the cookie is valid for the entire application.
    - domain: Binds the cookie to your specific domain.
    - secure = true: CRITICAL. Ensures the session cookie is only sent over HTTPS connections, protecting it from eavesdropping. (Requires your server to be configured for HTTPS).
    - httponly = true: CRITICAL. Prevents client-side JavaScript from accessing the session cookie, mitigating XSS attacks that try to steal session tokens.
    - samesite = 'Lax' (or 'Strict'): Provides protection against Cross-Site Request Forgery (CSRF) attacks by controlling when cookies are sent with cross-site requests. 'Lax' is a good balance for general websites, while 'Strict' offers stronger protection but might interfere with legitimate cross-site links.
- Session Fixation Prevention (session_regenerate_id(true)):
    - Implementation: The session ID is regenerated after successful login and on every request (start_secure_session()), invalidating the old session ID.
    - Mechanism: This prevents an attacker from pre-setting a user's session ID and then tricking the user into logging in with that ID, thereby taking over their session.
- Session Timeout:
    - Implementation: A last_activity timestamp is stored in the session, and if the user is inactive for a predefined period (e.g., 30 minutes), their session is destroyed, and they are forced to log in again.
    - Mechanism: Reduces the window of opportunity for attackers to exploit hijacked or abandoned sessions.

# 4. Input Validation and Sanitization (Prevention of XSS & SSTI)
- Implementation:
    - Input Validation: User inputs (username, email, password) are checked for length, format (e.g., filter_var for email, preg_match for username patterns), and emptiness. This is done on the server-side (process_register.php, process_login.php).
    - Output Encoding (htmlspecialchars()): Any user-supplied data that is echoed back to the HTML output (e.g., the username on the landing page) is escaped using htmlspecialchars().
- Mechanism:
    - Input Validation: Ensures data conforms to expected formats, reducing the attack surface.
    - Output Encoding: Converts special characters (<, >, &, ", ') into their HTML entities (<, >, &, ", '). This prevents the browser from interpreting malicious scripts or HTML tags injected by an attacker (Cross-Site Scripting - XSS) and mitigates Server-Side Template Injection (SSTI) by ensuring user input cannot escape template rendering logic.

# 5. Cross-Site Request Forgery (CSRF) Protection
- Current Implementation: The samesite=Lax cookie attribute in session_set_cookie_params() offers a good baseline defense.
- Further Recommendation (for robust protection): Synchronizer Token Pattern:
    - Mechanism: On every form submission, a unique, cryptographically strong token is generated and stored in the user's session. This token is also embedded as a hidden field in the HTML form. When the form is submitted, the server compares the token from the form with the one in the session. If they don't match, the request is rejected. This prevents an attacker from forging requests that originate from another site, as they cannot guess or obtain the valid token.

# 6. Prevention of Insecure Direct Object Reference (IDOR)
- Principle: User IDs (or any direct object identifiers) passed in URLs or form data should never be directly trusted. Access to resources should always be validated against the logged-in user's permissions.
- Implementation in this example: The landing page checks $_SESSION['loggedin'] and $_SESSION['user_id']. If you were to add a "view profile" page (e.g., profile.php?id=123), you would need to:
Get user_id from $_SESSION['user_id'].
If the user is trying to access profile.php?id=X, check if X matches $_SESSION['user_id'] or if the logged-in user has administrative privileges to view other profiles.
- Mechanism: Ensures that a user can only access resources they are authorized for, even if they manipulate identifiers in requests.

# 7. Broken Access Control
- Principle: Ensure that authenticated users can only perform actions and access resources that are explicitly allowed by their role and permissions.
- Implementation in this example: The landing.php checks if $_SESSION['loggedin'] is true before displaying content, preventing unauthenticated access.
- Further Recommendation: For more complex applications, implement a robust role-based access control (RBAC) system. Every sensitive action or resource access point should have a server-side check against the user's assigned roles and permissions.

# 8. HTTPS for Secure Data Transmission
- Recommendation: CRITICAL. All traffic, especially authentication credentials, must be encrypted in transit using HTTPS (HTTP Secure).
- Implementation: This is typically configured at the web server level (e.g., Apache, Nginx) using SSL/TLS certificates. PHP code only plays a role by setting secure=true for cookies, instructing browsers to only send them over HTTPS. Without HTTPS, session cookies and user credentials can be easily intercepted.

# 9. Rate Limiting and Account Lockout (Counteracting Brute-Force Attacks)
- Recommendation:
    - Rate Limiting: Implement server-side logic to limit the number of login attempts from a single IP address or username within a specific time frame. If the limit is exceeded, temporarily block the IP or require a CAPTCHA.
    - Account Lockout: After a certain number of failed login attempts for a specific username, temporarily lock that account (e.g., for 15-30 minutes).
- Implementation: These features are not directly included in the provided simple PHP scripts as they require more sophisticated state management (e.g., storing failed attempt counts in the database, using a caching system like Redis, or leveraging web server/WAF capabilities).

# 10. Server-Side Request Forgery (SSRF) Prevention
- Principle: If your application makes requests to other internal or external resources based on user-supplied URLs (e.g., image fetching from a URL provided by a user), it can be vulnerable to SSRF. An attacker could force your server to make requests to internal network services or other external services.
- Relevance to this example: The provided code does not inherently include SSRF vulnerabilities as it doesn't make server-side requests based on user input.
- Recommendation: If your application does this:
    - Whitelist: Only allow requests to a strict whitelist of approved domains/IPs.
    - Blacklist: Block requests to private IP ranges (e.g., 127.0.0.1, 10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16).
    - Validate URL scheme: Only allow http or https.
    - Disable redirects.

# 11. Error Handling and Logging
- Implementation: Basic try-catch and die() are used for critical errors (like database connection). error_log() is used to log errors securely.
- Recommendation:
    - Secure Logging: Log detailed error messages to a secure, non-web-accessible file. Never display raw error messages (especially database errors) to the end-user.
    - Generic Error Messages: Provide generic error messages to the user (e.g., "An unexpected error occurred. Please try again later."), rather than revealing internal system details.

# 12. php.ini Hardening
- Recommendation: Configure your php.ini file for production:
    - display_errors = Off
    - log_errors = On
    - error_log = /path/to/your/secure/php-error.log
    - expose_php = Off
    - allow_url_fopen = Off (unless explicitly needed and handled securely)
    - session.use_strict_mode = 1 (prevents PHP from accepting uninitialized session IDs)
    - session.cookie_httponly = 1
    - session.cookie_secure = 1
    - session.cookie_samesite = "Lax" (or "Strict")

Disclaimer: This documentation and the provided code are intended for educational purposes to demonstrate security principles. A production-ready authorization system requires rigorous testing, continuous security auditing, and often the use of well-maintained security libraries and frameworks (e.g., for PHP, Symfony Security, Laravel Passport, etc.) which handle many of these complex security considerations out-of-the-box. Never deploy custom authentication systems to production without expert security review.
