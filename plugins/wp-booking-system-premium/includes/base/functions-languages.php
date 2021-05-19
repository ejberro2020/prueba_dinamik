<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Returns an array with supported languages
 *
 * @return array
 *
 */
function wpbs_get_languages() {

	$languages = array( 
		'en' => 'English', 
		'bg' => 'Bulgarian',
		'ca' => 'Catalan',
		'zh' => 'Chinese',
		'hr' => 'Croatian',
		'cs' => 'Czech',
		'da' => 'Danish',
		'nl' => 'Dutch',
		'et' => 'Estonian',
		'fi' => 'Finnish',
		'fr' => 'French',
		'gl' => 'Galician',
		'de' => 'German',
		'el' => 'Greek',
		'hu' => 'Hungarian',
		'is' => 'Icelandic',
		'it' => 'Italian',
		'id' => 'Indonesian',
		'jp' => 'Japanese',
		'ko' => 'Korean',
		'lv' => 'Latvian',
		'lt' => 'Lithuanian',
		'no' => 'Norwegian',
		'pl' => 'Polish',
		'pt' => 'Portugese',
		'ro' => 'Romanian',
		'ru' => 'Russian',
		'sr' => 'Serbian',
		'sk' => 'Slovak',
		'sl' => 'Slovenian',
		'es' => 'Spanish',
		'sv' => 'Swedish',
		'tr' => 'Turkish',
		'uk' => 'Ukrainian',
		'cy' => 'Welsh'
	);

	/**
	 * Filter to modify the languages array just before returning
	 *
	 * @param array $languages
	 *
	 */
	$languages = apply_filters( 'wpbs_get_languages', $languages );

	return $languages;

}


/**
 * Returns an array with the first letter of each day of the week in order, starting
 * from Monday until Sunday
 *
 * @param string $language_code
 *
 * @return array
 *
 */
function wpbs_get_days_first_letters( $language_code = 'en' ) {

    $days_first_letters = array(
        'af' => array( 'M','D','W','D','V','S','S' ), /* South Africa */
		'bg' => array( 'П','В','С','Ч','П','С','Н' ), /* Bulgarian */
        'ca' => array( 'D','D','D','D','D','D','D' ), /* Catalan */
		'hr' => array( 'P','U','S','Č','P','S','N' ), /* Croatian */
		'cs' => array( 'P','Ú','S','Č','P','S','N' ), /* Czech */
		'zh' => array( '一','二','三','四','五','六','日' ), /* Chinese */
		'da' => array( 'M','T','O','T','F','L','S' ), /* Danish */
		'nl' => array( 'M','D','W','D','V','Z','Z' ), /* Dutch */
        'en' => array( 'M','T','W','T','F','S','S' ), /* English */
		'et' => array( 'E','T','K','N','R','L','P' ), /* Estonian */
		'fi' => array( 'M','T','K','T','P','L','S' ), /* Finnish */
		'fr' => array( 'L','M','M','J','V','S','D' ), /* French */
		'gl' => array( 'L','M','M','X','V','S','D' ), /* Galician */
		'de' => array( 'M','D','M','D','F','S','S' ), /* German */
		'el' => array( 'Δ','Τ','Τ','Π','Π','Σ','Κ' ), /* Greek */
		'hu' => array( 'H','K','S','C','P','S','V' ), /* Hungarian */
		'is' => array( 'M','Þ','M','F','F','L','S' ), /* Icelandic */
		'it' => array( 'L','M','M','G','V','S','D' ), /* Italian */
		'id' => array( 'S','S','R','K','J','S','M' ), /* Indonesian */
        'jp' => array( '月','火','水','木','金','土','日' ), /* Japanese */
        'ja' => array( '月','火','水','木','金','土','日' ), /* Japanese */
		'ko' => array( '월','화','수','목','금','토','일' ), /* Korean */
		'no' => array( 'M','T','O','T','F','L','S' ), /* Norwegian */
        'lv' => array( 'P','O','T','C','P','S','S' ), /* Latvian */
        'lt' => array( 'P','A','T','K','P','Š','S' ), /* Lithuanian */
		'nb' => array( 'M','T','O','T','F','L','S' ), /* Norwegian */
		'pl' => array( 'P','W','S','C','P','S','N' ), /* Polish */
		'pt' => array( 'S','T','Q','Q','S','S','D' ), /* Portugese */
        'ro' => array( 'L','M','M','J','V','S','D' ), /* Romanian */
		'ru' => array( 'П','В','С','Ч','П','С','В' ), /* Russian */
		'sr' => array( 'P','U','S','Č','P','S','N' ), /* Serbian */
		'sk' => array( 'P','U','S','Š','P','S','N' ), /* Slovak */
		'sl' => array( 'P','T','S','Č','P','S','N' ), /* Slovenian */
		'es' => array( 'L','M','M','J','V','S','D' ), /* Spanish */
		'sv' => array( 'M','T','O','T','F','L','S' ), /* Swedish */
		'tr' => array( 'P','S','Ç','P','C','C','P' ), /* Turkish */
		'uk' => array( 'П','В','С','Ч','П','С','Н' ),  /* Ukrainian */
		'cy' => array( 'Ll','M','M','I','G','S','S' )  /* Welsh */
    );

    return ( ! empty( $days_first_letters[$language_code] ) ? $days_first_letters[$language_code] : $days_first_letters['en'] );

}


/**
 * Returns an array with the names of the months
 *
 * @param int 	 $month
 * @param string $language_code
 *
 * @return array
 *
 */
function wpbs_get_month_name( $month, $language_code = 'en' ) {

    $month_names = array(

    	/* South Africa */
	    'af'  => array( 'Januarie', 'Februarie', 'Maart', 'April', 'Mei', 'Junie', 'Julie', 'Augustus', 'September', 'Oktober', 'November', 'Desember' ),

	    /* Bulgarian */
		'bg' => array( 'Януари', 'Февруари', 'Март', 'Април', 'Май', 'Юни', 'Юли', 'Август', 'Септември', 'Октомври', 'Ноември', 'Декември' ),

		/* Catalan */
        'ca' => array( 'Gener', 'Febrer', 'Març', 'Abril', 'Maig', 'Juny', 'Juliol', 'Agost', 'Setembre', 'Octubre', 'Novembre', 'Desembre' ),

        /* Croatian */
		'hr' => array( 'Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj', 'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac' ),

		/* Czech */
		'cs' => array( 'Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec' ),

		/* Chinese */
		'zh' => array( '一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月' ),
	
		/* Danish */
		'da' => array( 'Januar', 'Februar', 'Marts', 'April', 'Maj', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'December' ),

		/* Dutch */
		'nl' => array( 'Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December' ),

		/* English */
        'en' => array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ),

        /* Estonian */
		'et' => array( 'Jaanuar', 'Veebruar', 'Märts', 'Aprill', 'Mai', 'Juuni', 'Juuli', 'August', 'September', 'Oktoober', 'November', 'Detsember' ),

		/* Finnish */
		'fi' => array( 'Tammikuu', 'Helmikuu', 'Maaliskuu', 'Huhtikuu', 'Toukokuu', 'Kesäkuu', 'Heinäkuu', 'Elokuu', 'Syyskuu', 'Lokakuu', 'Marraskuu', 'Joulukuu' ),

		/* French */
		'fr' => array( 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre' ),
		
		/* Galician */
		'gl' => array( 'Xaneiro', 'Febreiro', 'Marzo', 'Abril', 'Maio', 'Xuño', 'Xullo', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Decembro' ),

		/* German */
		'de' => array( 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember' ),		
		
		/* Greek */
		'el' => array( 'Ιανουάριος', 'Φεβρουάριος', 'Μάρτιος', 'Απρίλιος', 'Μάιος', 'Ιούνιος', 'Ιούλιος', 'Αύγουστος', 'Σεπτέμβριος', 'Οκτώβριος', 'Νοέμβριος', 'Δεκέμβριος' ),		
        
        /* Hungarian */
        'hu' => array( 'Január', 'Február', 'Március', 'Április', 'Május', 'Június', 'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December' ),
		
		/* Icelandic */
		'is' => array( 'Janúar', 'Febrúar', 'Mars', 'Apríl', 'Maí', 'Júní', 'Júlí', 'Ágúst', 'September', 'Oktober', 'Nóvember', 'Desember' ),

		/* Italian */
		'it' => array( 'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre' ),

		/* Indonesian */
		'id' => array( 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember' ),
        
        /* Japanese */
        'jp' => array( '1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月' ),
        'ja' => array( '1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月' ),

		/* Korean */
		'ko' => array( '1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월' ),
		
		/* Latvian */
		'lv' => array( 'Janvāris', 'Februāris', 'Marts', 'Aprīlis', 'Maijs', 'Jūnijs', 'Jūlijs', 'Augusts', 'Septembris', 'Oktobris', 'Novembris', 'Decembris' ),

		/* Lithuanian */
		'lt' => array( 'Sausis', 'Vasaris', 'Kovas', 'Balandis', 'Gegužis', 'Birželis', 'Liepa', 'Rugpjūtis', 'Rugsėjis', 'Spalis', 'Lapkritis', 'Gruodis' ),
        
		/* Norwegian */
        'no' => array( 'Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember' ),
		
		/* Norwegian */
		'nb' => array( 'Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember' ),
		
		/* Polish */
		'pl' => array( 'Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień' ),
		
		/* Portugese */
		'pt' => array( 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro' ),
        
        /* Romanian */
        'ro' => array( 'Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie' ),
		
		/* Russian */
		'ru' => array( 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь' ),
		
		/* Serbian */
		'sr' => array( 'Januar', 'Februar', 'Mart', 'April', 'Maj', 'Jun', 'Jul', 'Avgust', 'Septembar', 'Oktobar', 'Novembar', 'Decembar' ),

		/* Slovak */
		'sk' => array( 'Január', 'Február', 'Marec', 'Apríl', 'Máj', 'Jún', 'Júl', 'August', 'September', 'Október', 'November', 'December' ),	
		
		/* Slovenian */
		'sl' => array( 'Januar', 'Februar', 'Marec', 'April', 'Maj', 'Junij', 'Julij', 'Avgust', 'September', 'Oktober', 'November', 'December' ),
		
		/* Spanish */
		'es' => array( 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' ),
		
		/* Swedish */
		'sv' => array( 'Januari', 'Februari', 'Mars', 'April', 'Maj', 'Juni', 'Juli', 'Augusti', 'September', 'Oktober', 'November', 'December' ),
		
		/* Turkish */
		'tr' => array( 'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık' ),
		
		/* Ukrainian */
		'uk' => array( 'Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень' ),

		/* Welsh */
		'cy' => array( 'Ionawr', 'Chwefror', 'Mawrth', 'Ebrill', 'Mai', 'Mehefin', 'Gorffennaf', 'Awst', 'Medi', 'Hydref', 'Tachwedd', 'Rhagfyr' ),

    );

    return ( ! empty( $month_names[$language_code][$month - 1] ) ? $month_names[$language_code][$month - 1] : $month_names['en'][$month - 1] );

}

function wpbs_language_to_locale($language){
	
	$locales = array('af' => 'af', 'ak' => 'ak', 'sq' => 'sq', 'am' => 'am', 'ar' => 'ar', 'hy' => 'hy', 'rup' => 'rup_MK', 'as' => 'as', 'az' => 'az', 'az-tr' => 'az_TR', 'ba' => 'ba', 'eu' => 'eu', 'bel' => 'bel', 'bn' => 'bn_BD', 'bs' => 'bs_BA', 'bg' => 'bg_BG', 'mya' => 'my_MM', 'ca' => 'ca', 'bal' => 'bal', 'zh-cn' => 'zh_CN', 'zh-hk' => 'zh_HK', 'zh-tw' => 'zh_TW', 'co' => 'co', 'hr' => 'hr', 'cs' => 'cs_CZ', 'da' => 'da_DK', 'dv' => 'dv', 'nl' => 'nl_NL', 'nl-be' => 'nl_BE', 'en' => 'en_US', 'en-au' => 'en_AU', 'en-ca' => 'en_CA', 'en-gb' => 'en_GB', 'eo' => 'eo', 'et' => 'et', 'fo' => 'fo', 'fi' => 'fi', 'fr-be' => 'fr_BE', 'fr' => 'fr_FR', 'fy' => 'fy', 'fuc' => 'fuc', 'gl' => 'gl_ES', 'ka' => 'ka_GE', 'de' => 'de_DE', 'de-ch' => 'de_CH', 'el' => 'el', 'gn' => 'gn', 'gu' => 'gu_IN', 'haw' => 'haw_US', 'haz' => 'haz', 'he' => 'he_IL', 'hi' => 'hi_IN', 'hu' => 'hu_HU', 'is' => 'is_IS', 'ido' => 'ido', 'id' => 'id_ID', 'ga' => 'ga', 'it' => 'it_IT', 'ja' => 'ja', 'jv' => 'jv_ID', 'kn' => 'kn', 'kk' => 'kk', 'km' => 'km', 'kin' => 'kin', 'ky' => 'ky_KY', 'ko' => 'ko_KR', 'ckb' => 'ckb', 'lo' => 'lo', 'lv' => 'lv', 'li' => 'li', 'lin' => 'lin', 'lt' => 'lt_LT', 'lb' => 'lb_LU', 'mk' => 'mk_MK', 'mg' => 'mg_MG', 'ms' => 'ms_MY', 'ml' => 'ml_IN', 'mr' => 'mr', 'xmf' => 'xmf', 'mn' => 'mn', 'me' => 'me_ME', 'ne' => 'ne_NP', 'nb' => 'nb_NO', 'nn' => 'nn_NO', 'ory' => 'ory', 'os' => 'os', 'ps' => 'ps', 'fa' => 'fa_IR', 'fa-af' => 'fa_AF', 'pl' => 'pl_PL', 'pt-br' => 'pt_BR', 'pt' => 'pt_PT', 'pa' => 'pa_IN', 'rhg' => 'rhg', 'ro' => 'ro_RO', 'ru' => 'ru_RU', 'ru-ua' => 'ru_UA', 'rue' => 'rue', 'sah' => 'sah', 'srd' => 'srd', 'gd' => 'gd', 'sr' => 'sr_RS', 'sd' => 'sd_PK', 'si' => 'si_LK', 'sk' => 'sk_SK', 'sl' => 'sl_SI', 'so' => 'so_SO', 'azb' => 'azb', 'es-ar' => 'es_AR', 'es-cl' => 'es_CL', 'es-co' => 'es_CO', 'es-mx' => 'es_MX', 'es-pe' => 'es_PE', 'es-pr' => 'es_PR', 'es' => 'es_ES', 'es-ve' => 'es_VE', 'su' => 'su_ID', 'sw' => 'sw', 'sv' => 'sv_SE', 'gsw' => 'gsw', 'tl' => 'tl', 'tg' => 'tg', 'tzm' => 'tzm', 'ta' => 'ta_IN', 'ta-lk' => 'ta_LK', 'tt' => 'tt_RU', 'te' => 'te', 'th' => 'th', 'bo' => 'bo', 'tir' => 'tir', 'tr' => 'tr_TR', 'tuk' => 'tuk', 'ug' => 'ug_CN', 'uk' => 'uk', 'ur' => 'ur', 'uz' => 'uz_UZ', 'vi' => 'vi', 'wa' => 'wa', 'cy' => 'cy', 'yor' => 'yor');

	if(isset($locales[$language]))
		return $locales[$language];

	return 'en_US';
	
}