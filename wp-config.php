<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'homestay' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ubsto(O0!![Msdog:4H$ Ht12l#]vGvx5BAWq0S3!3)6w!6aAa`_DJ?( q:a.?MB' );
define( 'SECURE_AUTH_KEY',  'qRQN>_$|LN{Qk5i})%gypV;;1wO.L}3fw!(<:W4uvX?QV;@BKt9GBC Ol:$$Iy+l' );
define( 'LOGGED_IN_KEY',    'H(q!71%{(EK9._*HtP#+XqOK+sqBdl<&[3GDdvjCj4K]XKw0F+GEn#g?=VRXk`+2' );
define( 'NONCE_KEY',        'w,8jpeb{uz5<==[nG;68q)lNub12j1%M{5H,t={xIcKt+ ]A%I)vlMK76c5XF%2W' );
define( 'AUTH_SALT',        'QN~Gt~hrjFr<K?3W2(+G-8o><?UMAzzv2:N`d|(h|@ia(a |]#pvhCw%7-w3CE+6' );
define( 'SECURE_AUTH_SALT', 'td9/?-wmBsAltuUpv?&hx lTR[ui,Q$v>j(u5Z9Q1- [~;*aAtR(G{!s9m>@XO7K' );
define( 'LOGGED_IN_SALT',   ']zYZ&6TxlV(/R9C.w!Q*w9B|V)$7cnVh~1lxwOeI9]GCHc&w+e%QcC{>vytp=[i{' );
define( 'NONCE_SALT',       's#a[iyjxMhh=4:,6!l>;F8y%O5q6%1owdlmx`eE,J0bKWmsrt|5W}<J,QU)PqKlc' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
