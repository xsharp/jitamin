<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Enable/Disable debug
define('DEBUG', $config['debug']);

// Jitamin directory
define('JITAMIN_DIR', __DIR__.DIRECTORY_SEPARATOR.'..');

// Data directory (must be writeable by the web server user)
define('DATA_DIR', JITAMIN_DIR.DIRECTORY_SEPARATOR.'storage');

// Log filename if the log driver is "file"
define('LOG_FILE', DATA_DIR.DIRECTORY_SEPARATOR.'debug.log');

// Cache directory to use if cache driver is "file" (must be writeable by the web server user)
define('CACHE_DIR', DATA_DIR.DIRECTORY_SEPARATOR.'cache');

// Folder for uploaded files (must be writeable by the web server user)
define('FILES_DIR', DATA_DIR.DIRECTORY_SEPARATOR.'files');

// Plugins directory
define('PLUGINS_DIR', JITAMIN_DIR.DIRECTORY_SEPARATOR.'plugins');

// Plugins directory URL
define('PLUGIN_API_URL', 'https://jitamin.com/plugins.json');

// Enable/Disable plugin installer
define('PLUGIN_INSTALLER', true);

// Available log drivers: syslog, stderr, stdout or file
define('LOG_DRIVER', $config['log_driver']);

// Available cache drivers are "file", "memory" and "memcached"
define('CACHE_DRIVER', $config['cache_driver']);

// Cache prefix
define('CACHE_PREFIX', '');

// E-mail address for the "From" header (notifications)
define('MAIL_FROM', 'notifications@jitamin.local');

// Mail transport available: "smtp", "sendmail", "mail" (PHP mail function), "postmark", "mailgun", "sendgrid"
define('MAIL_TRANSPORT', 'mail');

// SMTP configuration to use when the "smtp" transport is chosen
define('MAIL_SMTP_HOSTNAME', '');
define('MAIL_SMTP_PORT', 25);
define('MAIL_SMTP_USERNAME', '');
define('MAIL_SMTP_PASSWORD', '');
define('MAIL_SMTP_ENCRYPTION', null); // Valid values are "null", "ssl" or "tls"

// Sendmail command to use when the transport is "sendmail"
define('MAIL_SENDMAIL_COMMAND', '/usr/sbin/sendmail -bs');

// Application version
defined('APP_VERSION') or define('APP_VERSION', trim(file_get_contents(__DIR__.'/../VERSION')));

// Database driver: sqlite, mysql or pgsql (sqlite by default)
defined('DB_DRIVER') or define('DB_DRIVER', $config['db_driver']);

if (DB_DRIVER !== 'sqlite') {
    // Mysql/Postgres username
    defined('DB_USERNAME') or define('DB_USERNAME', $config['db_connections'][$config['db_driver']]['username']);

    // Mysql/Postgres password
    defined('DB_PASSWORD') or define('DB_PASSWORD', $config['db_connections'][$config['db_driver']]['password']);

    // Mysql/Postgres hostname
    defined('DB_HOSTNAME') or define('DB_HOSTNAME', $config['db_connections'][$config['db_driver']]['host']);

    // Mysql/Postgres database name
    defined('DB_NAME') or define('DB_NAME', $config['db_connections'][$config['db_driver']]['database']);

    // Mysql/Postgres custom port (null = default port)
    defined('DB_PORT') or define('DB_PORT', $config['db_connections'][$config['db_driver']]['port']);
} else {
    // Sqlite configuration
    defined('DB_FILENAME') or define('DB_FILENAME', DATA_DIR.DIRECTORY_SEPARATOR.$config['db_connections'][$config['db_driver']]['database'].'.sqlite');
}

// Mysql SSL key
defined('DB_SSL_KEY') or define('DB_SSL_KEY', null);

// Mysql SSL certificate
defined('DB_SSL_CERT') or define('DB_SSL_CERT', null);

// Mysql SSL CA
defined('DB_SSL_CA') or define('DB_SSL_CA', null);

// Database backend group provider
defined('DB_GROUP_PROVIDER') or define('DB_GROUP_PROVIDER', true);

// Enable LDAP authentication (false by default)
define('LDAP_AUTH', false);

// LDAP server hostname
define('LDAP_SERVER', '');

// LDAP server port (389 by default)
define('LDAP_PORT', 389);

// By default, require certificate to be verified for ldaps:// style URL. Set to false to skip the verification
define('LDAP_SSL_VERIFY', true);

// Enable LDAP START_TLS
define('LDAP_START_TLS', false);

// By default Jitamin lowercase the ldap username to avoid duplicate users (the database is case sensitive)
// Set to true if you want to preserve the case
define('LDAP_USERNAME_CASE_SENSITIVE', false);

// LDAP bind type: "anonymous", "user" or "proxy"
define('LDAP_BIND_TYPE', 'anonymous');

// LDAP username to use with proxy mode
// LDAP username pattern to use with user mode
define('LDAP_USERNAME', null);

// LDAP password to use for proxy mode
define('LDAP_PASSWORD', null);

// LDAP DN for users
// Example for ActiveDirectory: CN=Users,DC=jitamin,DC=local
// Example for OpenLDAP: ou=People,dc=example,dc=com
define('LDAP_USER_BASE_DN', '');

// LDAP pattern to use when searching for a user account
// Example for ActiveDirectory: '(&(objectClass=user)(sAMAccountName=%s))'
// Example for OpenLDAP: 'uid=%s'
define('LDAP_USER_FILTER', '');

// LDAP attribute for username
// Example for ActiveDirectory: 'samaccountname'
// Example for OpenLDAP: 'uid'
define('LDAP_USER_ATTRIBUTE_USERNAME', 'uid');

// LDAP attribute for user full name
// Example for ActiveDirectory: 'displayname'
// Example for OpenLDAP: 'cn'
define('LDAP_USER_ATTRIBUTE_FULLNAME', 'cn');

// LDAP attribute for user email
define('LDAP_USER_ATTRIBUTE_EMAIL', 'mail');

// LDAP attribute to find groups in user profile
define('LDAP_USER_ATTRIBUTE_GROUPS', 'memberof');

// LDAP attribute for user avatar image: thumbnailPhoto or jpegPhoto
define('LDAP_USER_ATTRIBUTE_PHOTO', '');

// LDAP attribute for user language, example: 'preferredlanguage'
// Put an empty string to disable language sync
define('LDAP_USER_ATTRIBUTE_LANGUAGE', '');

// Allow automatic LDAP user creation
define('LDAP_USER_CREATION', true);

// LDAP DN for administrators
// Example: CN=Jitamin-Admins,CN=Users,DC=jitamin,DC=local
define('LDAP_GROUP_ADMIN_DN', '');

// LDAP DN for managers
// Example: CN=Jitamin Managers,CN=Users,DC=jitamin,DC=local
define('LDAP_GROUP_MANAGER_DN', '');

// Enable LDAP group provider for project permissions
// The end-user will be able to browse LDAP groups from the user interface and allow access to specified projects
define('LDAP_GROUP_PROVIDER', false);

// LDAP Base DN for groups
define('LDAP_GROUP_BASE_DN', '');

// LDAP group filter
// Example for ActiveDirectory: (&(objectClass=group)(sAMAccountName=%s*))
define('LDAP_GROUP_FILTER', '');

// LDAP user group filter
// If this filter is configured, Jitamin will search user groups in LDAP_GROUP_BASE_DN with this filter
// Example for OpenLDAP: (&(objectClass=posixGroup)(memberUid=%s))
define('LDAP_GROUP_USER_FILTER', '');

// LDAP attribute for the group name
define('LDAP_GROUP_ATTRIBUTE_NAME', 'cn');

// Enable/disable the reverse proxy authentication
define('REVERSE_PROXY_AUTH', false);

// Header name to use for the username
define('REVERSE_PROXY_USER_HEADER', 'REMOTE_USER');

// Username of the admin, by default blank
define('REVERSE_PROXY_DEFAULT_ADMIN', '');

// Default domain to use for setting the email address
define('REVERSE_PROXY_DEFAULT_DOMAIN', '');

// Enable/disable remember me authentication
define('REMEMBER_ME_AUTH', true);

// Enable or disable "Strict-Transport-Security" HTTP header
define('ENABLE_HSTS', true);

// Enable or disable "X-Frame-Options: DENY" HTTP header
define('ENABLE_XFRAME', true);

// Escape html inside markdown text
define('MARKDOWN_ESCAPE_HTML', true);

// API alternative authentication header, the default is HTTP Basic Authentication defined in RFC2617
define('API_AUTHENTICATION_HEADER', '');

// Enable/disable url rewrite
define('ENABLE_URL_REWRITE', $config['enable_url_rewrite']);

// Hide login form, useful if all your users use Google/Github/ReverseProxy authentication
define('HIDE_LOGIN_FORM', $config['hide_login_form']);

// Disabling logout (for external SSO authentication)
define('DISABLE_LOGOUT', false);

// Enable captcha after 3 authentication failure
define('BRUTEFORCE_CAPTCHA', 3);

// Lock the account after 6 authentication failure
define('BRUTEFORCE_LOCKDOWN', 6);

// Lock account duration in minute
define('BRUTEFORCE_LOCKDOWN_DURATION', 15);

// Session duration in second (0 = until the browser is closed)
// See http://php.net/manual/en/session.configuration.php#ini.session.cookie-lifetime
define('SESSION_DURATION', 0);

// HTTP client proxy
define('HTTP_PROXY_HOSTNAME', '');
define('HTTP_PROXY_PORT', '3128');
define('HTTP_PROXY_USERNAME', '');
define('HTTP_PROXY_PASSWORD', '');

// Set to false to allow self-signed certificates
define('HTTP_VERIFY_SSL_CERTIFICATE', true);

defined('TOTP_ISSUER') or define('TOTP_ISSUER', 'Jitamin');
