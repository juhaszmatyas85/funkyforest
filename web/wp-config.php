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
define( 'AUTH_KEY', 'kyh?C(R0tG5%bGb6PZc&I5>n85QF=h,Qm~ mn>aFWt D1@YD{MOqV]/Q:I#gp#74' );
define( 'SECURE_AUTH_KEY', ']}Aa69%8[SCt8C.<?!Ke^S%/=X-VLO/fzCeOE[t-(xg/ZmGELFC%4pK UgFjVbCA' );
define( 'LOGGED_IN_KEY', ')X7:ce}g)zFd/%H7Ma6)I*P^=Yn9NLFb*(D%mR,c#RE8ug9Pu*.RZ{pJyh4P[W@9' );
define( 'NONCE_KEY', '{#7tytP5zd~4Z*z^/Qn/[.n_d*3e]E.bWQ?~=6N1cs]:Z]dMN6*Ley`IaLW4vk85' );
define( 'AUTH_SALT',        'w6V~lEQ<BBCoef<@*_X)wufUi[?Ka^y|@C^==O~?*iYkrOwuBW|sT8YU_>]+Gxo2' );
define( 'SECURE_AUTH_SALT', '`eaw/^YMhgU&M*lofCN@~enWUsU2CZOJX75?zg Kpx~+lmA{txa+]&:X%&q`EU}b' );
define( 'LOGGED_IN_SALT',   '*V[6905sdB^Hul+YWm6,y+U,fWVO`PFbC|2f(pl(mO7,{#,=3E~TZQzw*glRkVQ4' );
define( 'NONCE_SALT',       'cq @]TO[6Ob|t1k]UIWKy<zu{f|OVD8Yh4):HTFB,7=u.E$`@I?Wf+@-s@YC]*w,' );

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
