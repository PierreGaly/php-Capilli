<?php

// Site
define('SITE_PROTOCOLE', 'https');
define('SITE_EMAIL', 'clubdelok@gmail.com');
define('SITE_FROMEMAIL', 'admin@pigsie.com');
define('SITE_URL', 'pigsie.com');
define('SITE_ADDR', SITE_PROTOCOLE . '://' . SITE_URL . '/');
define('SITE_NOM', 'Capilli-home');
define('SITE_SLOGAN', 'La coiffure à domicile réinventé');
define('SITE_TEL_FORMATED', '07.68.47.21.37');
define('SITE_TEL', str_replace('.', '', SITE_TEL_FORMATED));
define('SITE_ADRESSE', '10 Place Puy Paulin<br />33000 Bordeaux<br />France');
define('SITE_COLOR', '#056FCB');

// BDD
define('SITE_SQL_SERVER', 'localhost');
define('SITE_SQL_DBNAME', 'pigsie');
define('SITE_SQL_USER', 'pigsie');
define('SITE_SQL_MDP', 'cdl@33');

// IMAGES_DIR
define('IMAGES_BIENS', 'images_biens/');
define('IMAGES_COMMUNAUTES', 'images_communautes/');
define('IMAGES_SOUS_CAT', 'images_sous_cat/');

// URL
define('JQUERY_URL', SITE_PROTOCOLE . '://code.jquery.com/jquery.min.js');
define('API_KEY_MAP', 'AIzaSyDNH3rJXyscv-ZJWdG1k_D1g4uXaxfOme4');
//define('API_KEY_PAYPLUG', 'sk_test_6ggbtKyj7cXSvvv3pHTSxh');
define('LINK_FACEBOOK', 'https://www.facebook.com/pigsiebdx/');
define('LINK_TWITTER', 'https://twitter.com/PigsieBdx');
define('LINK_GOOGLE', 'https://plus.google.com/111285225895973398430/about?hl=fr');
define('INSCRIPTION_CLOSED', false);

// Params
define('NBR_JOURS_STAY_DEMANDE_LOCATION', 3);
define('NBR_JOURS_PAIEMENT_AUTO', 7);
define('NOMBRE_CLUBS_ACCUEIL', 4);
define('NOMBRE_ANNONCES_ACCUEIL', 8);
define('MONTANT_TIRELIRE_MOYEN', 0.01);
define('MONTANT_TIRELIRE_GROS', 50);
define('ID_MEMBRE_ENTREPRISE', 131);
