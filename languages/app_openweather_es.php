<?php
/**
 * Archivo de idioma español para el módulo OpenWeatherMap
 */

$dictionary = array(
/* general */
'OW_SCRIPT_NAME'=>'Nombre del Script',
'OW_EXECUTE_AFTER_UPDATE'=>'Ejecutar script después de la actualización',
'OW_UPDATE_PERIOD'=>'Periodo de actualización',
'OW_UPDATE_PERIOD_1HOUR' => '1 hora',
'OW_UPDATE_PERIOD_2HOUR' => '2 hora',
'OW_UPDATE_PERIOD_3HOUR' => '3 hora',
'OW_UPDATE_PERIOD_4HOUR' => '4 hora',
'OW_UPDATE_PERIOD_5HOUR' => '5 hora',
'OW_FORECAST_PERIOD_TITLE' => 'Periodo de previsión',
'OW_FORECAST_PERIOD_1DAY' => '1 día',
'OW_FORECAST_PERIOD_2DAY' => '2 día',
'OW_FORECAST_PERIOD_3DAY' => '3 día',
'OW_FORECAST_PERIOD_4DAY' => '4 día',
'OW_FORECAST_PERIOD_5DAY' => '5 día',
'OW_FORECAST_PERIOD_6DAY' => '6 día',
'OW_FORECAST_PERIOD_7DAY' => '7 día',
'OW_FORECAST_PERIOD_8DAY' => '8 día',
'OW_FORECAST_PERIOD_9DAY' => '9 día',
'OW_FORECAST_PERIOD_10DAY' => '10 día',
'OW_FORECAST_PERIOD_11DAY' => '11 día',
'OW_FORECAST_PERIOD_12DAY' => '12 día',
'OW_FORECAST_PERIOD_13DAY' => '13 día',
'OW_FORECAST_PERIOD_14DAY' => '14 día',
'OW_FORECAST_PERIOD_15DAY' => '15 día',
'OW_FORECAST_PERIOD_16DAY' => '16 día',
'OW_FLAG_USE_IMAGE_CACHE' => 'Utilizar cahé para las imágenes',
'OW_CITY_TITLE' => 'Ciudad',
'OW_CHANGE_CITY' => 'Elije otra ciudad',
'OW_CHANGE' => 'Cambiar',
'OW_TAB_WEATHER' => 'Clima',
'OW_TAB_SETTINGS' => 'Ajustes',
'OW_TAB_HELP' => 'Ayuda',
'OW_APP_NAME' => 'Previsión desde OpenWeatherMap.org',
'OW_CHOOSE_COUNTRY' => 'Elije país',
'OW_CHOOSE_CITY' => 'Elije ciudad',
'OW_WEATHER_IN_CITY' => 'Clima en',
'OW_WEATHER_ON_DATE' => 'on',
'OW_WEATHER_REFRESH' => 'refrescar',
'OW_WEATHER_TODAY' => 'AHORA',
'OW_WEATHER_OVERCAST' => 'Nublado',
'OW_WEATHER_RAIN' => 'Volumen de precipitaciones (lluvia)',
'OW_WEATHER_SNOW' => 'Volumen de precipitaciones (nieve)',
'OW_WEATHER_FREEZE' => 'Posibilidad de helada',
'OW_WEATHER_TODAY' => 'Hoy',
'OW_FORECAST_ON' => 'Pronóstico en',
'OW_FORECAST_FOR_SEVERAL_DAYS' => 'Pronóstico del tiempo para varios días.',
'OW_HELP_RUNSCRIPT_VAL' => 'Para los datos de actualización "manual" en sus scripts/métodos se puede utilizar este código:',
'OW_HELP_RUNSCRIPT_TITLE' => 'Para los datos de actualización en scripts/métodos.',
'OW_HELP_CALL_MODULE_MENU_TITLE' => 'Llame al módulo en el menú',
'OW_HELP_DISPLAY_INFO_CUR_WEATHER' => 'Muestra información sobre el clima actual',
'OW_HELP_DISPLAY_INFO_CUR_WEATHER_FORECAST' => 'Muestra información sobre el clima actual y el pronóstico para hoy',
'OW_HELP_DISPLAY_INFO_FORECAST_0DAY' => 'Muestra el pronóstico para hoy',
'OW_HELP_DISPLAY_INFO_FORECAST_1DAY' => 'Muestra el pronóstico para hoy y mañana',
'OW_HELP_DISPLAY_INFO_FORECAST_2DAY' => 'Muestra el pronóstico para hoy y los próximos 2 días',

'OW_SUNINFO_SUNRISE' => 'Amanecer',
'OW_SUNINFO_SUNSET' => 'Anochecer',
'OW_SUNINFO_DAY_LENGTH' => 'Day Length',
'OW_SUNINFO_DAY_SUNRISE_SUNSET' => 'Amanecer/Anochecer',
'OW_API_KEY' => 'API Key',
'OW_API_METHOD' => 'API Method',
'OW_ROUND' => 'Round',
'OW_UNTIL_WHOLE' => 'Until whole',
'OW_WEATHER_STATION_MODE' => 'Weather station mode',
'OW_CITYNAME' => 'Nombre de la ciudad',
'OW_16D' => '16 días/pronóstico diario API',
'OW_5D3H' => '5 días/3 horas pronóstico API',

//wind full
'OW_WIND_FULL_N' => 'Norte',
'OW_WIND_FULL_NNE' => 'Norte-Noreste',
'OW_WIND_FULL_NE' => 'Noreste',
'OW_WIND_FULL_ENE' => 'Este-Noreste',
'OW_WIND_FULL_E' => 'Este',
'OW_WIND_FULL_ESE' => 'Este-Sureste',
'OW_WIND_FULL_SE' => 'Sureste',
'OW_WIND_FULL_SSE' => 'Sur-Sureste',
'OW_WIND_FULL_S' => 'Sur',
'OW_WIND_FULL_SSW' => 'sur-Suroeste',
'OW_WIND_FULL_SW' => 'Suroeste',
'OW_WIND_FULL_WSW' => 'Oeste-Suroeste',
'OW_WIND_FULL_W' => 'Oeste',
'OW_WIND_FULL_WNW' => 'Oeste-Noroeste',
'OW_WIND_FULL_NW' => 'Noroeste',
'OW_WIND_FULL_NNW' => 'Norte-Noroeste',

/* end module names */
);

foreach ($dictionary as $k=>$v)
{
   if (!defined('LANG_' . $k))
   {
      define('LANG_' . $k, $v);
   }
}
