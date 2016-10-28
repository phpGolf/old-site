<?php
if(!defined('INDEX')) {
    header('location: /');
}

//DB config
define('DSN','mysql:host=localhost;dbname=phpgolf');
define('DB_NAME','phpgolf');
define('DB_USER','phpgolf');
define('DB_PASS','passwd');

//Memcache config
define('MEM_HOST','localhost');
define('MEM_PORT','11211');

//Domain
define('DOMAIN','www.phpgolf.org');

//Paths
define('BASE_PATH','/var/www/phpgolf/');
define('FUNCTIONS',BASE_PATH.'functions/');
define('CLASSES',BASE_PATH.'classes/');
define('CHALLENGES',BASE_PATH.'challs/');
define('DESIGN','/design/');
define('GFX','/gfx/');
define('SCRIPT','/js/');
define('STAT',BASE_PATH.'static/');

//Skin
define('DEF_SKIN','default');

//Names
define('TITLE','phpGolf');
define('COOKIE','new_auto_login');

//Dates
define('DATE_FORMAT','F d Y');
define('TIME_FORMAT','H:i:s');
date_default_timezone_set('GMT');

//Other
define('DEF_PAGE','main');
define('USER_CHANGE_NAME',FALSE);

$countries = array(
	        "Afghanistan","Albania","Algeria","American Samoa","Andorra","Angola","Anguilla","Antigua and Barbuda","Argentina",
	        "Armenia","Aruba","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium",
	        "Belize","Benin","Bermuda","Bhutan","Bolivia","Bosnia","Botswana","Brazil","British Virgin Islands","Brunei","Bulgaria",
	        "Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Cayman Islands","Central African Republic","Chad",
	        "Chile","China","Christmas Island","Colombia","Comoros","Cook Islands","Costa Rica","Croatia","Cuba","Cyprus","Czech Republic",
	        "CÃ´te d'Ivoire","Democratic Republic of the Congo","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador","Egypt",
	        "El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Falkland Islands","Faroe Islands","Fiji","Finland","France",
	        "French Polynesia","Gabon","Gambia","Georgia","Germany","Ghana","Gibraltar","Greece","Greenland","Grenada","Guam","Guatemala",
	        "Guinea","Guinea Bissau","Guyana","Haiti","Honduras","Hong Kong","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland",
	        "Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho",
	        "Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macao","Macedonia","Madagascar","Malawi","Malaysia","Maldives","Mali",
	        "Malta","Marshall Islands","Martinique","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Montserrat",
	        "Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","Netherlands Antilles","New Zealand","Nicaragua",
	        "Niger","Nigeria","Niue","Norfolk Island","North Korea","Norway","Oman","Pakistan","Palau","Panama","Papua New Guinea","Paraguay",
	        "Peru","Philippines","Pitcairn Islands","Poland","Portugal","Puerto Rico","Qatar","Republic of the Congo","Romania",
	        "Russian Federation","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Pierre","Saint Vicent and the Grenadines","Samoa",
	        "San Marino","Sao TomÃ© and PrÃ­ncipe","Saudi Arabia","Senegal","Serbia and Montenegro","Seychelles","Sierra Leone","Singapore",
	        "Slovakia","Slovenia","Soloman Islands","Somalia","South Africa","South Georgia","South Korea","Soviet Union","Spain",
	        "Sri Lanka","Sudan","Suriname","Swaziland","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand",
	        "Tibet","Timor-Leste","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Turks and Caicos Islands",
	        "Tuvalu","UAE","Uganda","Ukraine","United Kingdom","United States","Uruguay","US Virgin Islands","Uzbekistan",
	        "Vanuatu","Vatican City","Venezuela","Vietnam","Wallis and Futuna","World","Yemen","Zambia","Zimbabwe");
?>
