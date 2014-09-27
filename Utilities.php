<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio;

use Visio;

/**
 * Package of some useful functions.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Utilities {

    /**
     * Visio\Utilities::__contruct()
     */
    public function __contruct() {
        throw new Visio\Exception\General("Cannot instantiate static class " . get_class($this));
    }

    /**
     * Visio\Utilities::webalize()
     *
     * @param mixed $string
     * @return string
     */
    public static function webalize($string) {
        //Based od code by Jakub Vrána @jakubvrana
        $string = preg_replace('~[^\\pL0-9_]+~u', '-', $string);
        $string = trim($string, "-");
        $string = iconv("utf-8", "us-ascii//TRANSLIT", $string);
        $string = strtolower($string);
        $string = preg_replace('~[^-a-z0-9_]+~', '', $string);

        return Visio\Utilities\String::lower(trim($string));
    }

    /**
     * Visio\Utilities::toUTF8()
     *
     * @param mixed $string
     * @return string
     */
    public static function toUTF8($string) {
        if (preg_match('#[\x80-\x{1FF}\x{2000}-\x{3FFF}]#u', $string)) {
            return $string;
        } elseif (preg_match('#[\x7F-\x9F\xBC]#', $string)) {
            return iconv('WINDOWS-1250', 'UTF-8', $string);
        } else {
            return iconv('ISO-8859-2', 'UTF-8', $string);
        }
    }

    /**
     * Visio\Utilities::createToken()
     *
     * @return string
     */
    public static function createToken() {
        return hash("md5", uniqid(null, true));
    }

    /**
     * Visio\Utilities::encodePhpTags()
     *
     * @param mixed $string
     * @return string
     */
    public static function encodePhpTags($string) {
        return str_replace(array('<?',
                                 '?>'), array('&lt;?',
                                              '?&gt;'), $string);
    }

    /**
     * Visio\Utilities::getMimeContentType()
     *
     * @param mixed $file
     * @return string
     */
    public static function getMimeContentType($file) {
        //List of MIME types is based on list written by svogal
        $mimeTypes = array('txt' => 'text/plain',
                           'htm' => 'text/html',
                           'html' => 'text/html',
                           'php' => 'text/html',
                           'css' => 'text/css',
                           'js' => 'application/javascript',
                           'json' => 'application/json',
                           'xml' => 'application/xml',
                           'swf' => 'application/x-shockwave-flash',
                           'flv' => 'video/x-flv',
            // images
                           'png' => 'image/png',
                           'jpe' => 'image/jpeg',
                           'jpeg' => 'image/jpeg',
                           'jpg' => 'image/jpeg',
                           'gif' => 'image/gif',
                           'bmp' => 'image/bmp',
                           'ico' => 'image/vnd.microsoft.icon',
                           'tiff' => 'image/tiff',
                           'tif' => 'image/tiff',
                           'svg' => 'image/svg+xml',
                           'svgz' => 'image/svg+xml',
            // archives
                           'zip' => 'application/zip',
                           'rar' => 'application/x-rar-compressed',
                           'exe' => 'application/x-msdownload',
                           'msi' => 'application/x-msdownload',
                           'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
                           'mp3' => 'audio/mpeg',
                           'qt' => 'video/quicktime',
                           'mov' => 'video/quicktime',
            // adobe
                           'pdf' => 'application/pdf',
                           'psd' => 'image/vnd.adobe.photoshop',
                           'ai' => 'application/postscript',
                           'eps' => 'application/postscript',
                           'ps' => 'application/postscript',
            // ms office
                           'doc' => 'application/msword',
                           'docx' => 'application/msword',
                           'rtf' => 'application/rtf',
                           'xls' => 'application/vnd.ms-excel',
                           'ppt' => 'application/vnd.ms-powerpoint',
                           'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            // open office
                           'odt' => 'application/vnd.oasis.opendocument.text',
                           'ods' => 'application/vnd.oasis.opendocument.spreadsheet');


        $file = ($file instanceof Visio\FileSystem\File) ? $file->getFullPath() : (string)$file;

        $fileInfo = pathinfo($file);
        $extension = $fileInfo['extension'];
        $extension = Visio\Utilities\String::lower($extension);

        if (isset($mimeTypes[$extension])) {
            return $mimeTypes[$extension];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = @finfo_file($finfo, $file);
            finfo_close($finfo);

            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }

    /**
     * Visio\Utilities::translateCountryCode()
     *
     * @param string $code
     * @return string
     */
    public static function translateCountryCode($code) {
        $countries = array("AF" => "AFGHANISTAN",
                           "AX" => "ALAND ISLANDS",
                           "AL" => "ALBANIA",
                           "DZ" => "ALGERIA",
                           "AS" => "AMERICAN SAMOA",
                           "AD" => "ANDORRA",
                           "AO" => "ANGOLA",
                           "AI" => "ANGUILLA",
                           "AQ" => "ANTARCTICA",
                           "AG" => "ANTIGUA AND BARBUDA",
                           "AR" => "ARGENTINA",
                           "AM" => "ARMENIA",
                           "AW" => "ARUBA",
                           "AU" => "AUSTRALIA",
                           "AT" => "AUSTRIA",
                           "AZ" => "AZERBAIJAN",
                           "BS" => "BAHAMAS",
                           "BH" => "BAHRAIN",
                           "BD" => "BANGLADESH",
                           "BB" => "BARBADOS",
                           "BY" => "BELARUS",
                           "BE" => "BELGIUM",
                           "BZ" => "BELIZE",
                           "BJ" => "BENIN",
                           "BM" => "BERMUDA",
                           "BT" => "BHUTAN",
                           "BO" => "BOLIVIA, PLURINATIONAL STATE OF",
                           "BQ" => "BONAIRE, SAINT EUSTATIUS AND SABA",
                           "BA" => "BOSNIA AND HERZEGOVINA",
                           "BW" => "BOTSWANA",
                           "BV" => "BOUVET ISLAND",
                           "BR" => "BRAZIL",
                           "IO" => "BRITISH INDIAN OCEAN TERRITORY",
                           "BN" => "BRUNEI DARUSSALAM",
                           "BG" => "BULGARIA",
                           "BF" => "BURKINA FASO",
                           "BI" => "BURUNDI",
                           "KH" => "CAMBODIA",
                           "CM" => "CAMEROON",
                           "CA" => "CANADA",
                           "CV" => "CAPE VERDE",
                           "KY" => "CAYMAN ISLANDS",
                           "CF" => "CENTRAL AFRICAN REPUBLIC",
                           "TD" => "CHAD",
                           "CL" => "CHILE",
                           "CN" => "CHINA",
                           "CX" => "CHRISTMAS ISLAND",
                           "CC" => "COCOS (KEELING) ISLANDS",
                           "CO" => "COLOMBIA",
                           "KM" => "COMOROS",
                           "CG" => "CONGO",
                           "CD" => "CONGO, THE DEMOCRATIC REPUBLIC OF THE",
                           "CK" => "COOK ISLANDS",
                           "CR" => "COSTA RICA",
                           "CI" => "COTE D'IVOIRE",
                           "HR" => "CROATIA",
                           "CU" => "CUBA",
                           "CW" => "CURACAO",
                           "CY" => "CYPRUS",
                           "CZ" => "CZECH REPUBLIC",
                           "DK" => "DENMARK",
                           "DJ" => "DJIBOUTI",
                           "DM" => "DOMINICA",
                           "DO" => "DOMINICAN REPUBLIC",
                           "EC" => "ECUADOR",
                           "EG" => "EGYPT",
                           "SV" => "EL SALVADOR",
                           "GQ" => "EQUATORIAL GUINEA",
                           "ER" => "ERITREA",
                           "EE" => "ESTONIA",
                           "ET" => "ETHIOPIA",
                           "FK" => "FALKLAND ISLANDS (MALVINAS)",
                           "FO" => "FAROE ISLANDS",
                           "FJ" => "FIJI",
                           "FI" => "FINLAND",
                           "FR" => "FRANCE",
                           "GF" => "FRENCH GUIANA",
                           "PF" => "FRENCH POLYNESIA",
                           "TF" => "FRENCH SOUTHERN TERRITORIES",
                           "GA" => "GABON",
                           "GM" => "GAMBIA",
                           "GE" => "GEORGIA",
                           "DE" => "GERMANY",
                           "GH" => "GHANA",
                           "GI" => "GIBRALTAR",
                           "GR" => "GREECE",
                           "GL" => "GREENLAND",
                           "GD" => "GRENADA",
                           "GP" => "GUADELOUPE",
                           "GU" => "GUAM",
                           "GT" => "GUATEMALA",
                           "GG" => "GUERNSEY",
                           "GN" => "GUINEA",
                           "GW" => "GUINEA-BISSAU",
                           "GY" => "GUYANA",
                           "HT" => "HAITI",
                           "HM" => "HEARD ISLAND AND MCDONALD ISLANDS",
                           "VA" => "HOLY SEE (VATICAN CITY STATE)",
                           "HN" => "HONDURAS",
                           "HK" => "HONG KONG",
                           "HU" => "HUNGARY",
                           "IS" => "ICELAND",
                           "IN" => "INDIA",
                           "ID" => "INDONESIA",
                           "IR" => "IRAN, ISLAMIC REPUBLIC OF",
                           "IQ" => "IRAQ",
                           "IE" => "IRELAND",
                           "IM" => "ISLE OF MAN",
                           "IL" => "ISRAEL",
                           "IT" => "ITALY",
                           "JM" => "JAMAICA",
                           "JP" => "JAPAN",
                           "JE" => "JERSEY",
                           "JO" => "JORDAN",
                           "KZ" => "KAZAKHSTAN",
                           "KE" => "KENYA",
                           "KI" => "KIRIBATI",
                           "KP" => "KOREA, DEMOCRATIC PEOPLE'S REPUBLIC OF",
                           "KR" => "KOREA, REPUBLIC OF",
                           "KW" => "KUWAIT",
                           "KG" => "KYRGYZSTAN",
                           "LA" => "LAO PEOPLE'S DEMOCRATIC REPUBLIC",
                           "LV" => "LATVIA",
                           "LB" => "LEBANON",
                           "LS" => "LESOTHO",
                           "LR" => "LIBERIA",
                           "LY" => "LIBYAN ARAB JAMAHIRIYA",
                           "LI" => "LIECHTENSTEIN",
                           "LT" => "LITHUANIA",
                           "LU" => "LUXEMBOURG",
                           "MO" => "MACAO",
                           "MK" => "MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF",
                           "MG" => "MADAGASCAR",
                           "MW" => "MALAWI",
                           "MY" => "MALAYSIA",
                           "MV" => "MALDIVES",
                           "ML" => "MALI",
                           "MT" => "MALTA",
                           "MH" => "MARSHALL ISLANDS",
                           "MQ" => "MARTINIQUE",
                           "MR" => "MAURITANIA",
                           "MU" => "MAURITIUS",
                           "YT" => "MAYOTTE",
                           "MX" => "MEXICO",
                           "FM" => "MICRONESIA, FEDERATED STATES OF",
                           "MD" => "MOLDOVA, REPUBLIC OF",
                           "MC" => "MONACO",
                           "MN" => "MONGOLIA",
                           "ME" => "MONTENEGRO",
                           "MS" => "MONTSERRAT",
                           "MA" => "MOROCCO",
                           "MZ" => "MOZAMBIQUE",
                           "MM" => "MYANMAR",
                           "NA" => "NAMIBIA",
                           "NR" => "NAURU",
                           "NP" => "NEPAL",
                           "NL" => "NETHERLANDS",
                           "NC" => "NEW CALEDONIA",
                           "NZ" => "NEW ZEALAND",
                           "NI" => "NICARAGUA",
                           "NE" => "NIGER",
                           "NG" => "NIGERIA",
                           "NU" => "NIUE",
                           "NF" => "NORFOLK ISLAND",
                           "MP" => "NORTHERN MARIANA ISLANDS",
                           "NO" => "NORWAY",
                           "OM" => "OMAN",
                           "PK" => "PAKISTAN",
                           "PW" => "PALAU",
                           "PS" => "PALESTINIAN TERRITORY, OCCUPIED",
                           "PA" => "PANAMA",
                           "PG" => "PAPUA NEW GUINEA",
                           "PY" => "PARAGUAY",
                           "PE" => "PERU",
                           "PH" => "PHILIPPINES",
                           "PN" => "PITCAIRN",
                           "PL" => "POLAND",
                           "PT" => "PORTUGAL",
                           "PR" => "PUERTO RICO",
                           "QA" => "QATAR",
                           "RE" => "REUNION",
                           "RO" => "ROMANIA",
                           "RU" => "RUSSIAN FEDERATION",
                           "RW" => "RWANDA",
                           "BL" => "SAINT BARTHELEMY",
                           "SH" => "SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA",
                           "KN" => "SAINT KITTS AND NEVIS",
                           "LC" => "SAINT LUCIA",
                           "MF" => "SAINT MARTIN (FRENCH PART)",
                           "PM" => "SAINT PIERRE AND MIQUELON",
                           "VC" => "SAINT VINCENT AND THE GRENADINES",
                           "WS" => "SAMOA",
                           "SM" => "SAN MARINO",
                           "ST" => "SAO TOME AND PRINCIPE",
                           "SA" => "SAUDI ARABIA",
                           "SN" => "SENEGAL",
                           "RS" => "SERBIA",
                           "SC" => "SEYCHELLES",
                           "SL" => "SIERRA LEONE",
                           "SG" => "SINGAPORE",
                           "SX" => "SINT MAARTEN (DUTCH PART)",
                           "SK" => "SLOVAKIA",
                           "SI" => "SLOVENIA",
                           "SB" => "SOLOMON ISLANDS",
                           "SO" => "SOMALIA",
                           "ZA" => "SOUTH AFRICA",
                           "GS" => "SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS",
                           "ES" => "SPAIN",
                           "LK" => "SRI LANKA",
                           "SD" => "SUDAN",
                           "SR" => "SURINAME",
                           "SJ" => "SVALBARD AND JAN MAYEN",
                           "SZ" => "SWAZILAND",
                           "SE" => "SWEDEN",
                           "CH" => "SWITZERLAND",
                           "SY" => "SYRIAN ARAB REPUBLIC",
                           "TW" => "TAIWAN, PROVINCE OF CHINA",
                           "TJ" => "TAJIKISTAN",
                           "TZ" => "TANZANIA, UNITED REPUBLIC OF",
                           "TH" => "THAILAND",
                           "TL" => "TIMOR-LESTE",
                           "TG" => "TOGO",
                           "TK" => "TOKELAU",
                           "TO" => "TONGA",
                           "TT" => "TRINIDAD AND TOBAGO",
                           "TN" => "TUNISIA",
                           "TR" => "TURKEY",
                           "TM" => "TURKMENISTAN",
                           "TC" => "TURKS AND CAICOS ISLANDS",
                           "TV" => "TUVALU",
                           "UG" => "UGANDA",
                           "UA" => "UKRAINE",
                           "AE" => "UNITED ARAB EMIRATES",
                           "GB" => "UNITED KINGDOM",
                           "US" => "UNITED STATES",
                           "UM" => "UNITED STATES MINOR OUTLYING ISLANDS",
                           "UY" => "URUGUAY",
                           "UZ" => "UZBEKISTAN",
                           "VU" => "VANUATU",
                           "VA" => "VATICAN CITY STATE HOLY SEE",
                           "VE" => "VENEZUELA, BOLIVARIAN REPUBLIC OF",
                           "VN" => "VIET NAM",
                           "VG" => "VIRGIN ISLANDS, BRITISH",
                           "VI" => "VIRGIN ISLANDS, U.S.",
                           "WF" => "WALLIS AND FUTUNA",
                           "EH" => "WESTERN SAHARA",
                           "YE" => "YEMEN",
                           "ZM" => "ZAMBIA",
                           "ZW" => "ZIMBABWE");

        $code = Visio\Utilities\String::upper($code);

        return (isset($countries[$code]) ? $countries[$code] : $code);
    }

}