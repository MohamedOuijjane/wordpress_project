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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

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
define( 'AUTH_KEY',         '`TXR?<E{|3$e:t.!8,ouz PtBmS:T*nK~uZdxyav7`WgEbv-8u:}O<#.d$qvtkI*' );
define( 'SECURE_AUTH_KEY',  '^_:::*Ca7yMiJn>l52F~m-k|if2da_$II((FbaPOQDVemMnjeK^f-lf1crA]Yr5b' );
define( 'LOGGED_IN_KEY',    ')zl)=.*@[a;H:cVyX71L(~,FG2LdbgXuS`EK/e^,-UW;IcEpVA4Zo7sR{H/d.ZV@' );
define( 'NONCE_KEY',        ';m`f68UFE_ Bn:DUP^*tTz=R*2KbP&|z!PsB`j6fKg(I@Q(632U$<M_+j*KkeNIX' );
define( 'AUTH_SALT',        'l,.fcAUdkEai(G3cst&?*@RDsA~)}nV_Py4?o&N(y5b,:A~-!ziLqq;iBk/1H,xQ' );
define( 'SECURE_AUTH_SALT', 'iNE) E<s<jnnMb,K|BB8tDo$2ho6<ip 8[*[~_wX@12d+z#Tu7Dp#]XSo/aKFmIq' );
define( 'LOGGED_IN_SALT',   'jV%e:vX:Mf>(XAX9[G5Fym9>i^nH[lnn_>=s/4MkgQ!(Z,aiD$B!gdi{,l9dOb{X' );
define( 'NONCE_SALT',       'C--_la,W7,J7cQuwDR8sQ^9}Ym5h?c]@o|%ci/_uw0gy~Qkv(7<<XDy(M2*r:crB' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
