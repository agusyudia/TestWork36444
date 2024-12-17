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
define('FS_METHOD', 'direct');
define('DISABLE_WP_CRON', true);

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'db_testwork36444');
/** Database username */
define('DB_USER', 'root');
/** Database password */
define('DB_PASSWORD', '');
/** Database hostname */
define('DB_HOST', 'localhost');
/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');
/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define('AUTH_KEY',         ']:(Ibl**}1fY#|:dgMGnOw<x&SY:fU}W0t8IBuRTm[b8[gLaJ S]m=)>E3Y0vn,q');
define('SECURE_AUTH_KEY',  'Z!&3@?ZkEw5y#W4MQkUVn`V<sxY)hA*4,J?Z[q<PC]QxS}PtVJ*9P1V%Vp5 09Ek');
define('LOGGED_IN_KEY',    ',Iy]ez/XIdWbha]!yL,T(,]>1bCyQ~xi6Lm}yGBX/A)81eDh}ixj`<#j6O!.JEgJ');
define('NONCE_KEY',        'GqSmk}W)xZCmkx5wniGhrCUfBs{=j<L=kt5lJ+k|yz[J;@v3DS~>oOu~-jCy+hQ+');
define('AUTH_SALT',        'Q~!F(SVQpo9!U$}1]D-#eazf^z-5WoW6Zq!V;*74Ay+fz=^f{N>)O<~ojkn_^Qco');
define('SECURE_AUTH_SALT', 'nRIHW?L&FI:]8_GxA.P5trTL2>D6MUUjW13D]X{UN4S*raEP0Pxmq1O8=i9BL9Ul');
define('LOGGED_IN_SALT',   '&g#A9Gpr]Fv}2T>x<$Q,`ILjdAA:9h~p>zX2rm[{B/wF<W-fAHADaHv-cxF=hZqb');
define('NONCE_SALT',       '}[omo- /,hs6dJj`j=L=Hz})u|bNLo@gO*uH8]A@e+2zz)?WIC@_]44kSrD.{+{L');

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
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);


/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (! defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
