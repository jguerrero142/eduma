<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'lostres_edutores' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'cb~B{(hfb`A*A8O2,*VCx4x?QAe*!q]=7KS6OLgyBm7EK!!<MQY oiJqL^7=;$fA' );
define( 'SECURE_AUTH_KEY',  'Zi79S:>zc4APcih*0qw<=6I0R*gw$b,{ZkAB-Zj$~Qen@wL1=Bdc/?~P,WS$ !Z&' );
define( 'LOGGED_IN_KEY',    'X+5#5L`lo:x1(adURS,Yt)%I16wJ61Zdn ]0|f^VPJEI5 eB>IG[WmMyM:mT18SJ' );
define( 'NONCE_KEY',        'kR/Uo~kNdYx~(U4_eehcCp@8{&&j87 ~wum<rvPc;7GWFB9I|?xdm++h5IB?vtK3' );
define( 'AUTH_SALT',        '1r-[XJ@+G=Z+4PJC1MRrl[${?(iuAvT1by@wRlY=E7`%>; eWj|7S:UjVv/B<8oK' );
define( 'SECURE_AUTH_SALT', 'Tjx&qPo)XwRBrw<-6B-NB p,4X%8O7+)[.NhS(mWZ0a)h{gpL=A),8N8 ]jY$[Uc' );
define( 'LOGGED_IN_SALT',   'T(l(m^`-T@+wOh-WO+p]2Ud?Cs1 T.4H[$4C-RLJ^A4&WOG;N=5;jNd~tek[EHd`' );
define( 'NONCE_SALT',       ';yQsM>K;b_#=s04~JXltMX#BF1cFr}LNVVA}|1jl5I9(o&`A*yRcP#,wJs2&nHir' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'xzr_';

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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
