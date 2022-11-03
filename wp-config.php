<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'testo' );

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
define( 'AUTH_KEY',         '.~DN(w.;.uL!8PxO:^1wC(v(2N,(SD#K8@6<nMePMf];9:68?#t{G.p/1)~ps!Mx' );
define( 'SECURE_AUTH_KEY',  'Zsf3EnffRSMkM/H@KvdgJ^u:)omXkP*2v/CH*~=jkJD0Gmro{U3Y<;FRkaFa]ApT' );
define( 'LOGGED_IN_KEY',    'i1FR7+q~As@U1;*&yZgcujG}vg<XIXFWn+,nd+oAFE.j@HK}VsT;&VMwU@>z]<}@' );
define( 'NONCE_KEY',        'PBA8Q4HoeYp-lswfpvg0Z<VTs;T-G`2Jx!=xq)1&sjm#)+ ^OD573|2P6gev/7)n' );
define( 'AUTH_SALT',        'rc[A/@NS`;rtK,JI4]5lQ4S6b~:>z~u-R=q+$g{6XPI_][f)oHc1%Os.SF5oq&.*' );
define( 'SECURE_AUTH_SALT', 'GYkam}LW3tHW+_8xH0k{LyKbTbE(IlFaMH5U-I~my&wxngAP?ircTfPJDf)UFU,r' );
define( 'LOGGED_IN_SALT',   '5Gc$ccd`4<iTM1A[sQSGxBGr_$}wONONl GJ#%j4)(hxY!kDa[b]?5&7k`SZ1U5F' );
define( 'NONCE_SALT',       'sdVeLQ^#p<f4[h$oq#y]CO/r54b+$&!Yv8dqb`g(@eVeec:(]qDp~uyD7/56[M#.' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
