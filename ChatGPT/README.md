# Security Measures

| Threat                      | Mitigation                                                               |
| --------------------------- | ------------------------------------------------------------------------ |
| SQL Injection               | PDO with prepared statements only                                        |
| CSRF                        | Tokens validated via `includes/csrf.php`                                 |
| XSS                         | Output encoded with `htmlspecialchars()`                                 |
| IDOR                        | Access controls check user-specific `$_SESSION['user_id']` before action |
| Session Injection           | `session_regenerate_id()` after login                                    |
| Broken Access Control       | Pages require `require_login()`                                          |
| SSTI                        | PHP natively avoids SSTI risks unless templates are misused              |
| SSRF                        | No user-controlled URLs sent to servers                                  |
| Brute Force / Rate Limiting | To be enhanced with CAPTCHA, lockouts (not shown due to brevity)         |
| HTTPS                       | Enforced via server config, not in PHP (must be enabled on deployment)   |
