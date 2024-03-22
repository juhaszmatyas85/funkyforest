<?php
/**
 * A WordPress fő konfigurációs állománya
 *
 * Ebben a fájlban a következő beállításokat lehet megtenni: MySQL beállítások
 * tábla előtagok, titkos kulcsok, a WordPress nyelve, és ABSPATH.
 * További információ a fájl lehetséges opcióiról angolul itt található:
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 *  A MySQL beállításokat a szolgáltatónktól kell kérni.
 *
 * Ebből a fájlból készül el a telepítési folyamat közben a wp-config.php
 * állomány. Nem kötelező a webes telepítés használata, elegendő átnevezni
 * "wp-config.php" névre, és kitölteni az értékeket.
 *
 * @package WordPress
 */

// ** MySQL beállítások - Ezeket a szolgálatótól lehet beszerezni ** //
/** Adatbázis neve */
define( 'DB_NAME', 'wordpress' );

/** MySQL felhasználónév */
define( 'DB_USER', 'wordpress' );

/** MySQL jelszó. */
define( 'DB_PASSWORD', 'wordpress' );

/** MySQL  kiszolgáló neve */
define( 'DB_HOST', 'database' );

/** Az adatbázis karakter kódolása */
define( 'DB_CHARSET', 'utf8mb4' );

/** Az adatbázis egybevetése */
define('DB_COLLATE', '');

/**#@+
 * Bejelentkezést tikosító kulcsok
 *
 * Változtassuk meg a lenti konstansok értékét egy-egy tetszóleges mondatra.
 * Generálhatunk is ilyen kulcsokat a {@link http://api.wordpress.org/secret-key/1.1/ WordPress.org titkos kulcs szolgáltatásával}
 * Ezeknek a kulcsoknak a módosításával bármikor kiléptethető az összes bejelentkezett felhasználó az oldalról.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY', '9AJA!lW^M/ay*NFj6(/+[ZCt~@/^FBPuKsk;:8z-B1T0n/$Bco%zkS+6b&Y/`&7=' );
define( 'SECURE_AUTH_KEY', '*#MbW&EHt;<PNV3vWuw<>dIb9KGI#;&!Utd~C~p#&YzuEyl*`k~_bLnIsXDztLA1' );
define( 'LOGGED_IN_KEY', 'EKi748tE&`=R^Vpso1uLBr!0nXHQIydj&R%:y)x@ k>`I_OICJ3Jzu$C@h}%?YgG' );
define( 'NONCE_KEY', 'e*Pd0Q{h[0(h!5UUCnrTN!#Db0> B2fI9hEs*R0c_+X&vGr3+MEJH#+*O{be0kPe' );
define( 'AUTH_SALT',        'w@NBvzE9F :Q_VT$IF<E<z1+rhBgrPm%wB|WC+_Hts$:jvV+kwQ:D{IZ(KHP?(mV' );
define( 'SECURE_AUTH_SALT', 'f;P8SA+:NG6mf1#G(wnI)Ajb1#H{8L{o;3?8u#DkEx6M/i&vfN}ZV4{yiaq*.f0(' );
define( 'LOGGED_IN_SALT',   'j)ht0h6q9~PplfT.~`Xx*-)XDDVc}?ti;~^:Ft+GkyS,FiTqaDEkc rsG-}Du=7[' );
define( 'NONCE_SALT',       'h;mXa] *9f^a&Y(Knvwmjhv:[5aQfv+F~08C(3rT@U$mugx/$ @4%&&X{`5jpjnv' );

/**#@-*/

/**
 * WordPress-adatbázis tábla előtag.
 *
 * Több blogot is telepíthetünk egy adatbázisba, ha valamennyinek egyedi
 * előtagot adunk. Csak számokat, betűket és alulvonásokat adhatunk meg.
 */
$table_prefix = 'wp_';

/**
 * Fejlesztőknek: WordPress hibakereső mód.
 *
 * Engedélyezzük ezt a megjegyzések megjelenítéséhez a fejlesztés során.
 * Erősen ajánlott, hogy a bővítmény- és sablonfejlesztők használják a WP_DEBUG
 * konstansot.
 */
define('WP_DEBUG', false);

/* Ennyi volt, kellemes blogolást! */
/* That's all, stop editing! Happy publishing. */

/** A WordPress könyvtár abszolút elérési útja. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Betöltjük a WordPress változókat és szükséges fájlokat. */
require_once(ABSPATH . 'wp-settings.php');
