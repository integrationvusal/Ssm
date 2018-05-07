<?php
    define('app_root', realpath('.'));
    define('ds', DIRECTORY_SEPARATOR);
    define('core_path', app_root . ds . 'core');

    function getConfig($key, $default = null){
        try{
            $host = $_SERVER['HTTP_HOST'];
            if(!is_file(app_root . "/" . $host .".ini")) $host = "local";
            if(!is_file(app_root . "/" . $host .".ini")){
                echo "<h1>Couldn't handle .ini file</h1>";
                exit;
            }
            $ini = parse_ini_file(app_root . "/" . $host . ".ini");
            if(array_key_exists($key, $ini)) return $ini[$key];
            return $default;
        } catch (Exception $e) {
            echo "<h1>Couldn't handle .ini file</h1>";
            exit;
        }
    }

    if(getConfig("mode", "production") == "develop"){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

	$__db = Array(
        'prefix' => 'vl1',
        'default' => Array(
            'user' => getConfig('db_username'),
            'password' => getConfig('db_password'),
            'name' => getConfig('db_name'),
            'port' => getConfig('db_port'),
            'host' => getConfig('db_host'),
        )
	);

	date_default_timezone_set('Asia/Baku');

    $appUrl = getConfig("app_host");
    $port = getConfig('app_port');
    if(!empty($port)) $appUrl = $appUrl . ":" . $port;
    $_adminName = 'admin';
	
	$__app = Array(
		'url' => $appUrl,
		'debug' => false,
		'url_converter_enabled' => false,
		'use_session' => true,
		'controllers_folder' => app_root . ds . 'controllers',
		'controllers_ext' => '.controller.php',
		'middleware_folder' => app_root . ds . 'middleware',
		'middleware_ext' => '.middleware.php',
		'models_folder' => app_root . ds . 'models',
		'templates_folder' => app_root . ds . 'views',
		'static_url' => $appUrl . '/static',
		'static_files_ext' => Array('css', 'js'),
		'image_extensions' => Array('jpg', 'png', 'bmp'),
		'static_files' => Array(
			'jquery.js',
			'json.js',
			'template.js',
			'actions.js',
		),
		'static_folder' => app_root . ds . 'static',
		'libs_folder' => app_root . ds . 'libs',
		'middleware_list' => Array(
			Array('Middleware','initializeApp'),
			Array('Middleware','forSmarty'),
			Array('Middleware','getLangsUrl'),
			Array('Middleware','getMenu')
		),
		'smtp' => Array(
			'host' => 'mail.ssm.az',
			'user' => 'info@ssm.az',
			'password' => 'Integ2016',
			'port' => '25',
			'security' => null
		),
		'lpw' => Array(
			'from' => 'notreply@meqa.az',
			'fromName' => 'agclub.az',
		),
		'public_folder' => app_root . ds . 'public',
		'public_url' => $appUrl . '/public',
		'languages' => Array(
			'az' => 'Azərbaycan',
			'ru' => 'Русский',
			'en' => 'English',
		),
		'language_file_folder' => app_root . ds . 'messages',
		'page_count' => 10,
		'default_language' => 'az',
		'autoload_folders' => Array(

			// interfaces
			Array(app_root . ds . 'interfaces' . 'DAO', 'interface'),

			// models
			Array(app_root . ds . 'models','model'),
			Array(app_root . ds . 'models' . ds . 'content','model'),
			Array(app_root . ds . 'models' . ds . 'menu','model'),
			Array(app_root . ds . 'models' . ds . 'filemanager','model'),
			Array(app_root . ds . 'models' . ds . 'admin-users','model'),
			Array(app_root . ds . 'models' . ds . 'search','model'),
			Array(app_root . ds . 'models' . ds . 'get-link','model'),
			Array(app_root . ds . 'models' . ds . 'goods','model'),
			Array(app_root . ds . 'models' . ds . 'user','model'),

			Array(app_root . ds . 'models' . ds . 'contragent','model'),
			Array(app_root . ds . 'models' . ds . 'client','model'),

			Array(app_root . ds . 'models' . ds . 'clothes','model'),
			Array(app_root . ds . 'models' . ds . 'phones','model'),
			Array(app_root . ds . 'models' . ds . 'jewelry','model'),
			Array(app_root . ds . 'models' . ds . 'shoes','model'),
			Array(app_root . ds . 'models' . ds . 'computer','model'),
			Array(app_root . ds . 'models' . ds . 'book','model'),
			Array(app_root . ds . 'models' . ds . 'perfume','model'),

			Array(app_root . ds . 'models' . ds . 'object','model'),
			Array(app_root . ds . 'models' . ds . 'invoice','model'),
			Array(app_root . ds . 'models' . ds . 'invoicegoods','model'),
			Array(app_root . ds . 'models' . ds . 'subject','model'),
			Array(app_root . ds . 'models' . ds . 'category','model'),
			Array(app_root . ds . 'models' . ds . 'store','model'),
			Array(app_root . ds . 'models' . ds . 'invoicedetail','model'),
			Array(app_root . ds . 'models' . ds . 'sell','model'),
			Array(app_root . ds . 'models' . ds . 'cashbox','model'),
			Array(app_root . ds . 'models' . ds . 'chashboxhistory','model'),
			Array(app_root . ds . 'models' . ds . 'operator','model'),
			Array(app_root . ds . 'models' . ds . 'permission','model'),
			Array(app_root . ds . 'models' . ds . 'userpermission','model'),
			Array(app_root . ds . 'models' . ds . 'manager','model'),
			Array(app_root . ds . 'models' . ds . 'report','model'),
			Array(app_root . ds . 'models' . ds . 'statistics','model'),
			Array(app_root . ds . 'models' . ds . 'service','model'),
			Array(app_root . ds . 'models' . ds . 'expense','model'),
			Array(app_root . ds . 'models' . ds . 'notice','model'),
			Array(app_root . ds . 'models' . ds . 'currency','model'),
			Array(app_root . ds . 'models' . ds . 'viewsetting','model'),
			Array(app_root . ds . 'models' . ds . 'discountcard','model'),
			Array(app_root . ds . 'models' . ds . 'discountcardrule','model'),
			Array(app_root . ds . 'models' . ds . 'discountcardhistory','model'),
			Array(app_root . ds . 'models' . ds . 'receiptattributes','model'),
			Array(app_root . ds . 'models' . ds . 'receipt','model'),

			// controllers
			Array(app_root . ds . 'controllers','controller'),
			Array(app_root . ds . 'modules' . ds . $_adminName . ds . 'controllers', 'controller'),
			Array(app_root . ds . 'controllers' . ds . 'utils', 'controller'),
			Array(app_root . ds . 'controllers' . ds . 'app', 'controller'),
			Array(app_root . ds . 'controllers' . ds . 'content', 'controller'),
			Array(app_root . ds . 'controllers' . ds . 'auth', 'controller'),
			Array(app_root . ds . 'controllers' . ds . 'search', 'controller'),

			Array(app_root . ds . 'controllers' . ds . 'rbac', 'controller'),
			Array(app_root . ds . 'controllers' . ds . 'contragent', 'controller'),
			Array(app_root . ds . 'controllers' . ds . 'client', 'controller'),
			Array(app_root . ds . 'controllers' . ds . 'subject', 'controller'),
			Array(app_root . ds . 'controllers' . ds . 'permission', 'controller'),
			Array(app_root . ds . 'controllers' . ds . 'manager', 'controller'),

			// middleware
			Array(app_root . ds . 'middleware','middleware'),

			// forms
			Array(app_root . ds . 'forms','form'),
			Array(app_root . ds . 'modules' . ds . $_adminName . ds . 'forms','form'),
		),
		'permissions_set' => [
			'subject' => 'Obyektlər',
			'change_subject' => 'Obyekti seç',
			'category_read' => 'Kategoriya bax',
			'category_create' => 'Kategoriya yarat',
			'category_update' => 'Kategoriya dəyiş',
			'category_delete' => 'Kategoriya sil',
			'catalog_read' => 'Katalog bax',
			'catalog_create' => 'Katalog yarat',
			'catalog_update' => 'Katalog dəyiş',
			'catalog_delete' => 'Katalog sil',
			'contragent_read' => 'Kontragent bax',
			'contragent_create' => 'Kontragent yarat',
			'contragent_update' => 'Kontragent dəyiş',
			'contragent_delete' => 'Kontragent sil',
			'contragent_pay' => 'Kontragentə ödə',
			'contragent_return' => 'Kontragentə geri',
			'client_read' => 'Müştəri bax',
			'client_create' => 'Müştəri yarat',
			'client_update' => 'Müştəri dəyiş',
			'client_delete' => 'Müştəri sil',
			'client_pay' => 'Müştəri ödə',
			'store_read' => 'Dükandaki mallar bax',
			'store_create' => 'Dükandaki mallar əlavə et',
			'store_transfer' => 'Mağazadan mağazaya mal transferi',
			'sell_read' => 'Satış',
			'buy_price' => 'Alış qiyməti',
			'currency' => 'Valyuta',
			'service_read' => 'Xidmətlər bax',
			'service_create' => 'Xidmət yarat',
			'service_update' => 'Xidmət dəyiş',
			'service_delete' => 'Xidmət sil',
			'service_sell' => 'Xidmət sat',
            'expense_read' => 'Xərclər bax',
			'expense_create' => 'Xərclər yarat',
			'expense_update' => 'Xərclər dəyiş',
			'expense_delete' => 'Xərclər sil',
			'expense_sell' => 'Xərclər sat',
			'cashbox_read' => 'Kassa bax',
			'cashbox_income' => 'Kassa mədaxil',
			'cashbox_outgoing' => 'Kassa məxaric',
			'cashbox_transfer' => 'Kassa transfer',
			'invoice_read' => 'Qaimələr bax',
			'operator_read' => 'İstifadəçilər bax',
			'operator_create' => 'İstifadəçilər yarat',
			'operator_update' => 'İstifadəçilər dəyiş',
			'operator_delete' => 'İstifadəçilər sil',
			'operator_grant' => 'İstifadəçilər modullar təyin et',
			'report_read' => 'Hesabatlar bax',
			'setting' => 'Sazlamalar',
			'discount_rule' => 'Bonus kartlar qaydalar',
			'discount_card' => 'Bonus kartlar',
			'discount_sell' => 'Bonus kart ilə satış',
			'receipt_attributes' => 'Qəbz atributları',
			'receipt_read' => 'Qəbzlərə bax',
			'contact' => 'Əlaqə'
		],
		'user_types' => [
			0 => "User",
			1 => "Operator"
		],
		'yesno_trigger' => [
			0 => 'Xeyr',
			1 => 'Bəli'
		],
		'subject_types' => Array(
			1 => "Anbar",
			2 => "Mağaza",
		),
		'item_status' => Array(
			0 => "Inactive",
			1 => "Active",
			2 => "Pending",
			3 => "Mixed"
		),
		'discount_card_types' => [
			'bonus' => 'Bonus',
			'discount' => 'Endirim',
		],
		'goods_types' => Array(
			0 => [
				'title' => 'Paltar',
				'model_name' => 'ClothesModel',
				'id' => 0
			],
			1 => [
				'title' => 'Telefon',
				'model_name' => 'PhonesModel',
				'id' => 1
			],
			2 => [
				'title' => 'Zərgərlik',
				'model_name' => 'JewelryModel',
				'id' => 2
			],
			3 => [
				'title' => 'Ayaqqabı',
				'model_name' => 'ShoesModel',
				'id' => 3
			],
			4 => [
				'title' => 'Kompüter',
				'model_name' => 'ComputerModel',
				'id' => 4
			],
			5 => [
				'title' => 'Kitab',
				'model_name' => 'BookModel',
				'id' => 5
			],
			6 => [
				'title' => 'Parfum',
				'model_name' => 'PerfumeModel',
				'id' => 6
			]
		),
		'invoice_types' => Array(
			0 => [
				'title' => 'Mağazaya malların qəbulu',
				'code' => 'GTS'
			],
			1 => [
				'title' => 'Mağazadan satış',
				'code' => 'SFS'
			],
			2 => [
				'title' => 'Kassaya mədaxil',
				'code' => 'CBI'
			],
			3 => [
				'title' => 'Kassadan məxaric',
				'code' => 'CBO'
			],
			4 => [
				'title' => 'Kassalar arası transfer',
				'code' => 'CBT'
			],
			5 => [
				'title' => 'Kontragentə ödəniş',
				'code' => 'PTC'
			],
			6 => [
				'title' => 'Müştəridən ödənişin qəbulu',
				'code' => 'PFC'
			],
			7 => [
				'title' => 'Müştəridən geri',
				'code' => 'BFC'
			],
			8 => [
				'title' => 'Anbardan transfer',
				'code' => 'TFA'
			],
			9 => [
				'title' => 'Anbara malların qəbulu',
				'code' => 'GTA'
			],
			10 => [
				'title' => 'Kontragentə geri',
				'code' => 'BTC'
			],
			11 => [
				'title' => 'Xidmət satışı',
				'code' => 'SSL'
			],
			12 => [
				'title' => 'Xərclər',
				'code' => 'ESL'
			]
		),
		'context_types' => Array(
			0 => 'StatusMessage',
			1 => 'Content'
		),
		'colors' => Array(
			'black' => ['title' => 'Qara', 'code' => '#000000'],
			'white' => ['title' => 'Ağ', 'code' => '#FFFFFF'],
			'green' => ['title' => 'Yaşıl', 'code' => '#008000'],
			'red' => ['title' => 'Qırmızı', 'code' => '#FF0000'],
			'blue' => ['title' => 'Mavi', 'code' => '#0000FF'],
			'yellow' => ['title' => 'Sarı', 'code' => '#FFFF00'],
			'lime' => ['title' => 'Əhəng', 'code' => '#00FF00'],
			'silver' => ['title' => 'Gümüş', 'code' => '#C0C0C0'],
			'gray' => ['title' => 'Boz', 'code' => '#808080'],
			'maroon' => ['title' => 'Tünd qırmızı', 'code' => '#800000'],
			'teal' => ['title' => 'Albalı', 'code' => '#008080'],
			'open_blue' => ['title' =>'Açıq mavi', 'code' => '#00BFFF'],
			'orange' => ['title' =>'narıncı', 'code' => '#FFA500'],
			'dark_blue' => ['title' =>'göy', 'code' => '#0000ff'],
			'violet' => ['title' =>'bənövşəyi', 'code' => '#8B00FF'],
			'turquoise' => ['title' =>'firuzəyi', 'code' => '#30D5C8'],
			'pink' => ['title' =>'çəhrayı', 'code' => '#FFC0CB'],
			'lime1' => ['title' =>'Laym', 'code' => '#99FF99'],
			'khaki' => ['title' =>'xaki', 'code' => '#C3B091'],
			'bronze' => ['title' =>'bürünc', 'code' => '#CD7F32'],
			'gold' => ['title' =>'qızılı', 'code' => '#FFD700'],
			'emerald' => ['title' =>'zümrüd', 'code' => '#009B77'],
			'lilac' => ['title' =>'yasəmən', 'code' => '#C8A2C8'],
			'dark_red' => ['title' =>'Al qırmızı', 'code' => '#FF2400'],
			'beige' => ['title' =>'bej', 'code' => '#F5F5DC'],
			'vanilla' => ['title' =>'vanil', 'code' => '#D5713F'],
			'brown' => ['title' =>'palıdı', 'code' => '#964B00'],
			'teal1' => ['title' =>'Açıq albalı', 'code' => '#911E42'],
			'indigo' => ['title' =>'indigo', 'code' => '#4B0082'],
			'cream' => ['title' =>'krem', 'code' => '#FDF4E3'],
			'dark_lime' => ['title' =>'Tünd laym', 'code' => '#00ff00'],
			'lemon' => ['title' =>'limon', 'code' => '#FDE910'],
			'raspberry' => ['title' =>'moruq', 'code' => '#DC143C'],
			'olive' => ['title' =>'zeytun', 'code' => '#808000'],
			'pistachio' => ['title' =>'püstə', 'code' => '#BEF574'],
			'amber' => ['title' =>'kəhrəba', 'code' => '#FFBF00'],
		),
		'countries' => Array(
			'Azərbaycan',
			'ABŞ',
			'Albaniya',
			'Almaniya',
			'Andorra',
			'Angola',
			'Antiqua və Barbuda',
			'Argentina',
			'Aruba',
			'Avstraliya',
			'Avstriya',
			'Bahamas',
			'Bahrain',
			'Banqladeş',
			'Barbados',
			'Belarus',
			'Belçika',
			'Belize',
			'Benin',
			'Birləşmiş Ərəb Əmirlikləri',
			'Birləşmiş Krallıq',
			'Boliviya',
			'Bosniya və Herseqovina',
			'Botswana',
			'Braziliya',
			'Brunei',
			'Bulgaria',
			'Burkina Faso',
			'Burma',
			'Burundi',
			'Butan',
			'Cape Verde',
			'Cezayir',
			'Cənubi Afrika',
			'Cənubi Koreya',
			'Cənubi Koreya',
			'Cənubi Sudan',
			'Chad',
			'Chile',
			'Cibuti',
			'Costa Rica',
			'Cote d`Ivoire',
			'Cuba',
			'Curacao',
			'Çexiya Respublikası',
			'Çin',
			'Danimarka',
			'Dominica',
			'Dominikan respublikası',
			'East Timor (Timor-Leste bax)',
			'Efiopiya',
			'Ekvador',
			'Ekvatorial Qvineya',
			'El Salvador',
			'Eritreya',
			'Ermənistan',
			'Estoniya',
			'Əfqanıstan',
			'Fələstin əraziləri',
			'Fici',
			'Filippin',
			'Finlandiya',
			'Fransa',
			'Grenada',
			'Guinea-Bissau',
			'Guyana',
			'Gürcüstan',
			'Haiti',
			'Hindistan',
			'Hollandiya',
			'Hollandiya Antilləri',
			'Holy See',
			'Honduras',
			'Honq Konq',
			'Xorvatiya',
			'İndoneziya',
			'İraq',
			'İran',
			'İrlandiya',
			'İslandiya',
			'İspaniya',
			'İsrail',
			'İsveç',
			'İsveçrə',
			'İtaliya',
			'Jamaica',
			'Jordan',
			'Kamboca',
			'Kamerun',
			'Kanada',
			'Kenya',
			'Kipr',
			'Kiribati',
			'Kolumbiya',
			'Komor',
			'Konqo Demokratik Respublikası',
			'Konqo Respublikası',
			'Koreya, Şimali',
			'Kosovo',
			'Kuwait',
			'Qabon',
			'Qambiya',
			'Qana',
			'Qatar',
			'Qazaxıstan',
			'Qırğızıstan',
			'Qvatemala',
			'Qvineya',
			'Laos',
			'Latviya',
			'Lebanon',
			'Lesoto',
			'Liberiya',
			'Libya',
			'Lixtenşteyn',
			'Litva',
			'Lüksemburq',
			'Macarıstan',
			'Macau',
			'Madagascar',
			'Makedoniya',
			'Malaysia',
			'Malawi',
			'Maldives',
			'Mali',
			'Malta',
			'Marshall Islands',
			'Mauritius',
			'Mavritaniya',
			'Meksika',
			'Mərakeş',
			'Mərkəzi Afrika Respublikası',
			'Mikronesiya',
			'Misir',
			'Moldaviya',
			'Monaco',
			'Monqolustan',
			'Monteneqro',
			'Mozambique',
			'Namibiya',
			'Nauru',
			'Nepal',
			'Niger',
			'Nigeria',
			'Nikaraqua',
			'Norveç',
			'Oman',
			'Özbəkistan',
			'Pakistan',
			'Palau',
			'Panama',
			'Papua Yeni Qvineya',
			'Paraqvay',
			'Peru',
			'Polşa',
			'Portuqaliya',
			'Rumıniya',
			'Rusiya',
			'Rwanda',
			'Saint Kitts və Nevis',
			'Saint Lucia',
			'Samoa',
			'San Marino',
			'Sao Tome və Principe',
			'Seneqal',
			'Serbiya',
			'Seychelles',
			'Seynt Vinsent və Qrenada',
			'Səudiyyə Ərəbistanı',
			'Sierra Leone',
			'Simali Koreya',
			'Sinqapur',
			'Sint Maarten',
			'Slovakiya',
			'Sloveniya',
			'Solomon Islands',
			'Somali',
			'Sudan',
			'Suriname',
			'Suriya',
			'Swaziland',
			'Şri Lanka',
			'Tacikistan',
			'Tanzania',
			'Tayvan',
			'Thailand',
			'Timor-Leste',
			'Togo',
			'Tonga',
			'Trinidad və Tobaqo',
			'Tunis',
			'Tuvalu',
			'Türkiyə',
			'Türkmənistan',
			'Ukrayna',
			'Uqanda',
			'Uruqvay',
			'Vanuatu',
			'Venesuela',
			'Vyetnam',
			'Yaponiya',
			'Yemen',
			'Yeni Zelandiya',
			'Yunanıstan',
			'Zambia',
			'Zimbabve',
		),
		'image_extensions' => Array('jpg', 'jpeg', 'png', 'gif', 'bmp'),
		'allowed_extensions' => Array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'txt', 'doc', 'docx', 'xls', 'xlsx', 'pdf'),

	);
        
		$_developFolder = 'developer';
        
        $__modules = Array(
            'admin' => Array(
				'version' => '8.0',
                'folder' => app_root . ds . 'modules' . ds . $_adminName,
                'static_url' => $appUrl . '/modules/' . $_adminName . '/static',
				'static_folder' => app_root . ds . 'modules' . ds . $_adminName . ds . 'static',
				'messages_folder' => app_root . ds . 'modules' . ds . $_adminName . ds . 'messages',
				'static_files' => Array(
					'jquery.js',
					'lang.az.js',
					'date-controller.js',
					'json.js',
					'template.js',
					'taskbar.js',
					'tab-controller.js',
					'checkbox-controller.js',
					'select-controller.js',
					'scrollbar-controller.js',
					'scrollbar-dragger.js',
					'desktop.js',
					'dragger.js',
					'resizer.js',
					'tree-dragger.js',
					'window.js',
					'tree.js',
					'filemanager.js',
					'utils.js',
					'main.js',
				),
                'name' => $_adminName,
                'middleware_list' => Array(
					Array('AdminMiddleware','initializeAdmin'),
					Array('AdminMiddleware','checkLoggedIn'),
					Array('AdminMiddleware','authorized'),
                ),
                'middleware_folder' => app_root . ds . 'modules' . ds . $_adminName . ds . 'middleware',
                'controllers_folder' => app_root . ds . 'modules' . ds . $_adminName . ds . 'controllers',
                'models' => Array(
					'MenuRelationsModel',
					'FileManager',
					'AdminUsersModel',
					'AdminUsersGroupModel',
                ),
				'tree_models' => Array(
					0 => Array(
						'MenuRelationsModel',
						'MenuModel',
						'0'
					),
				),
            )
        );

	$__template = Array(
            'debug' => $__app['debug'],
	);
	
	require core_path . ds . 'main.php';
	
?>