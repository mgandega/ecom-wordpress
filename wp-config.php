<?php

/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'wordpress_ifocop' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', 'root' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Wj6}0~o+`3)6jrKzaHT7)a,WbxaT|4gNkN-~*.=:`1If;K]-H[qa20/x||Xaz)T}' );
define( 'SECURE_AUTH_KEY',  'F?7jBMem*/ZoC+IhI^IRs{yL{nO$|xKR=TR@]jV~6VVm7K|jkj+!o&@SGdzJ[GY]' );
define( 'LOGGED_IN_KEY',    'm-vh]uA5R&K!t:16#$kF8^l25!>9%gY*`w](lFjf)hBPjKt$p*Nue}Ss0[9xEEjM' );
define( 'NONCE_KEY',        'z7iPV5.Mz2pdP&`M6@1$!aPpGK4B(-q94-nSBG$pJ7=s4~bx&R93GZSB^f}8^nvL' );
define( 'AUTH_SALT',        'X.9<-uZpKnoWN$suxp%+Y)5w/-6@:g)@CfQv?IY+)Tg8gSr~hHv7):cvXp=bf*l{' );
define( 'SECURE_AUTH_SALT', '(kVJSeXc{Qg,A>u?$&Dd4:Gn?{fMPAc3_*%VlJsiLM|;8ZVqO<F9|n%*rxBte(m.' );
define( 'LOGGED_IN_SALT',   'O6{azk:gXvqfT.gUX|w|@co:>1ZF1&1j |VG!(AZP+#oxSvGnE7;u-B^{V`3UqEe' );
define( 'NONCE_SALT',       '6<2VZ;uFVnPCWf5WhJhfS!MIYjtV0mvE2G2%M5{,hVU]9JEg%;4^lB2HD7jp`!I[' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortement recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
