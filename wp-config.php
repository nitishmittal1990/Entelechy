<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'DPK~Q9PbbCzW~HqtG7b:y$j4gw,^TM-( 60*{lyhCVwLh:6R/~BhmAAynW{~6h,|');
define('SECURE_AUTH_KEY',  'O69|rr_v]uOmYvC),Oo?[q=UXXknhNaPSyG~,s3rV]?jQFqVX`IrGW8#&HH)20|k');
define('LOGGED_IN_KEY',    'IUtYEH0]x+XB+ifW2jyv%Oa~]C^`iPR0z``jH%j/oZX8.jC22uQB]fiSNhyQ|;&O');
define('NONCE_KEY',        'cr`&o2yG.D@BY(RYLzT|YX:JZp}c.5b_p IWsYW7#+7xgEfn7VEKFi]{jaOKN1Jh');
define('AUTH_SALT',        'g7~zt<@dII)e*r/A*02B`k1;ggrC%9=P,N0^{5qa!2bh!Yl&je.z=cm[AyGIu~l,');
define('SECURE_AUTH_SALT', 'zzrCffQhpwQfyzchg%3=#rsk]c0kg?Y]f5;aG^0fdAt@Z9}<C~P<>n^^ys:kybrt');
define('LOGGED_IN_SALT',   'E|CC.v,)uC1akpbx6{xBNXh[5&Z47>,wTT;tb<$d3I6svCUM5:&H2`%:/P~*h/61');
define('NONCE_SALT',       ' qfM.9w}=A&hA6y++kc`hMkYd2;DRiR5lXpsIcYthU@2bQKtGYQ}H@gP{2)(r (k');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
