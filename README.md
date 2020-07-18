# Secure FE Logins
this typo3 extension secures fe_login against brute force attacks as described in
OWASP https://owasp.org/www-community/Slow_Down_Online_Guessing_Attacks_with_Device_Cookies

Current State - Alpha!
dont use in production yet.

## Installation

This TYPO3 extension is available via packagist:

```composer require n3amil/secure_fe_login```

Alternatively, you can install the extension from TER:

[TER: secure_fe_login](https://typo3.org/extensions/repository/view/secure_fe_login)

After that, proceed with [Getting Started](#getting-started)

## Getting Started
- install via TER or composer
- configure needed extension settings

   - Timeout = time in seconds how long the lock-out for untrusted users / device cookies
   - MaxAttempts = how many attempts for untrusted user or attempts with a single device cookie can be made until the untrusted users for the username, or the device cookie gets locked out
   - DeviceCookieName = the name of the device cookie which is set for the client, choose something unique e.g containing the website name
   - DeviceCookieExpireInDays = count of days until the device cookie expires
   - Secret = secret cryptographic key used for hash_hmac. Use a key with at least 512 bit entropy, generate it with the key/password generator of your choice. Dont use it anywhere else and keep it safe!

# FAQ
## don't we already have extensions which protect from brute force attacks?
there are several extensions e.g. login_limit, secure_login or felogin_bruteforce_protection.
Those provide a simple time/ip ban for login attempts, with downsides for a lot of use cases:
### simple time lockout after n attempts
- DoS for user account
### time logout for ip after n attempts (that's what most of the named extensions do)
- not suitable versus large distribution attacks (bot networks etc.)
- not friendly for users behind NAT
- DoS still possible in many cases

inspiration and notes taken from this german talk MRMCD2019 https://media.ccc.de/v/2019-220-ber-bruteforce-protection-und-warum-das-gar-nicht-so-leicht-ist
