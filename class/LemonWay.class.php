<?php

//ini_set('soap.wsdl_cache_enabled', 0); // only for developpement

class LemonWay
{
	//const URL = 'https://ws.lemonway.fr/mb/demo/dev/directkitxml/service.asmx?wsdl';
	const URL = 'https://ws.lemonway.fr/mb/clubdelok/prod/directkitxml/service.asmx?wsdl';
	const LOGIN = 'adminmb';
	const PASS = 'LemonWay1234';
	const VERSION = '1.9';
	const LANGUAGE = 'fr';
	const PRIX_MIN = 1;
	const PRIX_MAX = 2500;
	const LEMON_DISABLED = false;
	const WALLET_PREFIX = 'w_';
	
	public static function __callStatic($name, $arguments)
	{
		if(self::LEMON_DISABLED)
			return array('error' => false, 'result' => array());
		
		$header_arguments = array('wlLogin' => self::LOGIN,
								  'wlPass' => self::PASS,
								  'language' => self::LANGUAGE,
								  'version' => self::VERSION,
								  'walletIp' => $_SERVER['REMOTE_ADDR'],
								  'walletUa' => $_SERVER['HTTP_USER_AGENT']);
		
		if(empty($GLOBALS['LemonWayClient']))
			$GLOBALS['LemonWayClient'] = new SoapClient(self::URL);
		
		if(isset($arguments[0]['wallet']))
			$arguments[0]['wallet'] = self::WALLET_PREFIX . $arguments[0]['wallet'];
		
		if(isset($arguments[0]['creditWallet']))
			$arguments[0]['creditWallet'] = self::WALLET_PREFIX . $arguments[0]['creditWallet'];
		
		if(isset($arguments[0]['debitWallet']))
			$arguments[0]['debitWallet'] = self::WALLET_PREFIX . $arguments[0]['debitWallet'];
		
		$method = $name . 'Result';
		$req = $GLOBALS['LemonWayClient']->$name(array_merge($header_arguments, $arguments[0]));
		$method = key($req);
		$result = get_object_vars($req->$method);
		$key = key($result);
		$res = get_object_vars($result[$key]);
		
		return array('error' => ($key == 'E') ? true : false, 'result' => $res);
	}
	
	public static function displayErrorMessage($erreur, $title = '')
	{
		$chaine = '<div class="alert alert-danger text-center" role="alert">';
		
		if(!empty($title))
			$chaine .= '<h4>' . $title . '</h4>';
		
		$chaine .= '<strong><span class="glyphicon glyphicon-info-sign"></span> Erreur n°' . htmlspecialchars($erreur['Code']) . ' : </strong> ' . htmlspecialchars($erreur['Msg']) . '.<br /><em>Si vous ne pensez pas être à l\'origine de cette erreur, veuillez <a href="documentation.php#contact" onclick="window.open(this.href); return false;">nous contacter</a>.</em></div>';
		
		return $chaine;
	}
	
	public static function country2ISO3($country)
	{
		$iso_array = array(
			'Afghanistan' => 'AFG',
			'Afrique du Sud' => 'ZAF',
			'Albanie' => 'ALB',
			'Algerie' => 'DZA',
			'Allemagne' => 'DEU',
			'Andorre' => 'AND',
			'Angola' => 'AGO',
			'Anguilla' => 'AIA',
			'Antarctique' => 'ATA',
			'Antigua et Barbuda' => 'ATG',
			'Antilles Néerlandaises' => 'ANT',
			'Arabie Saoudite' => 'SAU',
			'Argentine' => 'ARG',
			'Arménie' => 'ARM',
			'Aruba' => 'ABW',
			'Australie' => 'AUS',
			'Autriche' => 'AUT',
			'Azerbaidjan' => 'AZE',
			'Bahamas' => 'BHS',
			'Bahrein' => 'BHR',
			'Bangladesh' => 'BGD',
			'Barbade' => 'BRB',
			'Belgique' => 'BEL',
			'Belize' => 'BLZ',
			'Bermudes' => 'BMU',
			'Bhoutan' => 'BTN',
			'Bolivie' => 'BOL',
			'Bosnie et Herzégovine' => 'BIH',
			'Botswana' => 'BWA',
			'Bouvet Island' => 'BVT',
			'Brunei' => 'BRN',
			'Brésil' => 'BRA',
			'Bulgarie' => 'BGR',
			'Burkina Faso' => 'BFA',
			'Burundi' => 'BDI',
			'Bélarus (Biélorussie)' => 'BLR',
			'Bénin' => 'BEN',
			'Cambodge' => 'KHM',
			'Cameroun' => 'CMR',
			'Canada' => 'CAN',
			'Cap Vert' => 'CPV',
			'Chili' => 'CHL',
			'Chine' => 'CHN',
			'Chypre' => 'CYP',
			'Cité du Vatican (Holy See)' => 'VAT',
			'Colombie' => 'COL',
			'Comores' => 'COM',
			'Congo, République' => 'COG',
			'Congo, République Démocratique du' => 'COD',
			'Corée du Nord' => 'PRK',
			'Corée du Sud' => 'KOR',
			'Costa Rica' => 'CRI',
			'Croatie' => 'HRV',
			'Cuba' => 'CUB',
			'Curacao' => 'CUW',
			'Côte d\'Ivoire' => 'CIV',
			'Danemark' => 'DNK',
			'Djibouti' => 'DJI',
			'Dominique' => 'DMA',
			'Egypte' => 'EGY',
			'Emirats Arabes Unis' => 'ARE',
			'Equateur' => 'ECU',
			'Erythrée' => 'ERI',
			'Espagne' => 'ESP',
			'Estonie' => 'EST',
			'Etats-Unis' => 'USA',
			'Ethiopie' => 'ETH',
			'Fidji' => 'FJI',
			'Finlande' => 'FIN',
			'France' => 'FRA',
			'France, Métropolitaine' => 'FXX',
			'Gabon' => 'GAB',
			'Gambie' => 'GMB',
			'Gaza' => 'PSE',
			'Ghana' => 'GHA',
			'Gibraltar' => 'GIB',
			'Grenade' => 'GRD',
			'Greoenland' => 'GRL',
			'Grèce' => 'GRC',
			'Guadeloupe' => 'GLP',
			'Guam' => 'GUM',
			'Guatemala' => 'GTM',
			'Guinée' => 'GIN',
			'Guinée Bissau' => 'GNB',
			'Guinée Equatoriale' => 'GNQ',
			'Guyane' => 'GUY',
			'Guyane Française' => 'GUF',
			'Géorgie' => 'GEO',
			'Géorgie du Sud et les îles Sandwich du Sud' => 'SGS',
			'Haïti' => 'HTI',
			'Honduras' => 'HND',
			'Hong Kong' => 'HKG',
			'Hongrie' => 'HUN',
			'Ile de Man' => 'IMN',
			'Iles Caïman' => 'CYM',
			'Iles Christmas' => 'CXR',
			'Iles Cocos' => 'CCK',
			'Iles Cook' => 'COK',
			'Iles Féroé' => 'FRO',
			'Iles Guernesey' => 'GGY',
			'Iles Heardet McDonald' => 'HMD',
			'Iles Malouines' => 'FLK',
			'Iles Mariannes du Nord' => 'MNP',
			'Iles Marshall' => 'MHL',
			'Iles Maurice' => 'MUS',
			'Iles mineures éloignées des Etats-Unis' => 'UMI',
			'Iles Norfolk' => 'NFK',
			'Iles Salomon' => 'SLB',
			'Iles Turques et Caïque' => 'TCA',
			'Iles Vierges des Etats-Unis' => 'VIR',
			'Iles Vierges du Royaume-Uni' => 'VGB',
			'Inde' => 'IND',
			'Indonésie' => 'IDN',
			'Iran' => 'IRN',
			'Iraq' => 'IRQ',
			'Irlande' => 'IRL',
			'Islande' => 'ISL',
			'Israël' => 'ISR',
			'Italie' => 'ITA',
			'Jamaique' => 'JAM',
			'Japon' => 'JPN',
			'Jersey' => 'JEY',
			'Jordanie' => 'JOR',
			'Kazakhstan' => 'KAZ',
			'Kenya' => 'KEN',
			'Kirghizistan' => 'KGZ',
			'Kiribati' => 'KIR',
			'Kosovo' => 'XKO',
			'Koweit' => 'KWT',
			'Laos' => 'LAO',
			'Lesotho' => 'LSO',
			'Lettonie' => 'LVA',
			'Liban' => 'LBN',
			'Libye' => 'LBY',
			'Libéria' => 'LBR',
			'Liechtenstein' => 'LIE',
			'Lituanie' => 'LTU',
			'Luxembourg' => 'LUX',
			'Macao' => 'MAC',
			'Macédoine' => 'MKD',
			'Madagascar' => 'MDG',
			'Malaisie' => 'MYS',
			'Malawi' => 'MWI',
			'Maldives' => 'MDV',
			'Mali' => 'MLI',
			'Malte' => 'MLT',
			'Maroc' => 'MAR',
			'Martinique' => 'MTQ',
			'Mauritanie' => 'MRT',
			'Mayotte' => 'MYT',
			'Mexique' => 'MEX',
			'Micronésie' => 'FSM',
			'Moldavie' => 'MDA',
			'Monaco' => 'MCO',
			'Mongolie' => 'MNG',
			'Montserrat' => 'MSR',
			'Monténégro' => 'MNE',
			'Mozambique' => 'MOZ',
			'Myanmar (Birmanie)' => 'MMR',
			'Namibie' => 'NAM',
			'Nauru' => 'NRU',
			'Nicaragua' => 'NIC',
			'Niger' => 'NER',
			'Nigeria' => 'NGA',
			'Niue' => 'NIU',
			'Norvège' => 'NOR',
			'Nouvelle Calédonie' => 'NCL',
			'Nouvelle Zélande' => 'NZL',
			'Népal' => 'NPL',
			'Oman' => 'OMN',
			'Ouganda' => 'UGA',
			'Ouzbékistan' => 'UZB',
			'Pakistan' => 'PAK',
			'Palau' => 'PLW',
			'Panama' => 'PAN',
			'Papouasie Nouvelle Guinée' => 'PNG',
			'Paraguay' => 'PRY',
			'Pays-Bas' => 'NLD',
			'Philippines' => 'PHL',
			'Pitcairn' => 'PCN',
			'Pologne' => 'POL',
			'Polynésie Française' => 'PYF',
			'Porto Rico' => 'PRI',
			'Portugal' => 'PRT',
			'Pérou' => 'PER',
			'Qatar' => 'QAT',
			'Roumanie' => 'ROU',
			'Royaume Uni' => 'GBR',
			'Russie' => 'RUS',
			'Rwanda' => 'RWA',
			'République Centraficaine' => 'CAF',
			'République Dominicaine' => 'DOM',
			'République Tchèque' => 'CZE',
			'Réunion' => 'REU',
			'Sahara Occidental' => 'ESH',
			'Saint Barthelemy' => 'BLM',
			'Saint Hélène' => 'SHN',
			'Saint Kitts et Nevis' => 'KNA',
			'Saint Martin' => 'MAF',
			'Saint Martin' => 'SXM',
			'Saint Pierre et Miquelon' => 'SPM',
			'Saint Vincent et les Grenadines' => 'VCT',
			'Sainte Lucie' => 'LCA',
			'Salvador' => 'SLV',
			'Samoa Americaines' => 'ASM',
			'Samoa Occidentales' => 'WSM',
			'San Marin' => 'SMR',
			'Sao Tomé et Principe' => 'STP',
			'Serbie' => 'SRB',
			'Seychelles' => 'SYC',
			'Sierra Léone' => 'SLE',
			'Singapour' => 'SGP',
			'Slovaquie' => 'SVK',
			'Slovénie' => 'SVN',
			'Somalie' => 'SOM',
			'Soudan' => 'SDN',
			'Sri Lanka' => 'LKA',
			'Sud Soudan' => 'SSD',
			'Suisse' => 'CHE',
			'Surinam' => 'SUR',
			'Suède' => 'SWE',
			'Svalbard et Jan Mayen' => 'SJM',
			'Swaziland' => 'SWZ',
			'Syrie' => 'SYR',
			'Sénégal' => 'SEN',
			'Tadjikistan' => 'TJK',
			'Taiwan' => 'TWN',
			'Tanzanie' => 'TZA',
			'Tchad' => 'TCD',
			'Terres Australes et Antarctique Françaises' => 'ATF',
			'Territoires Palestiniens occupés (Gaza)' => 'PSE',
			'Thaïlande' => 'THA',
			'Timor-Leste' => 'TLS',
			'Togo' => 'TGO',
			'Tokelau' => 'TKL',
			'Tonga' => 'TON',
			'Trinité et Tobago' => 'TTO',
			'Tunisie' => 'TUN',
			'Turkmenistan' => 'TKM',
			'Turquie' => 'TUR',
			'Tuvalu' => 'TUV',
			'Térritoire Britannique de l\'Océan Indien' => 'IOT',
			'Ukraine' => 'UKR',
			'Uruguay' => 'URY',
			'Vanuatu' => 'VUT',
			'Venezuela' => 'VEN',
			'Vietnam' => 'VNM',
			'Wallis et Futuna' => 'WLF',
			'Yemen' => 'YEM',
			'Zambie' => 'ZMB',
			'Zimbabwe' => 'ZWE'
			);
		
		return isset($iso_array[$country]) ? $iso_array[$country] : 'FRA';
	}
}
