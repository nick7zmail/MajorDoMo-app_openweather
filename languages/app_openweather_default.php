<?php
/**
 * Russian language file for OpenWeatherMap module
 */

$dictionary = array(
/* general */
'OW_SCRIPT_NAME'=>'Script name',
'OW_EXECUTE_AFTER_UPDATE'=>'Execute script after update',
'OW_UPDATE_PERIOD'=>'Update period',
'OW_UPDATE_PERIOD_1HOUR' => '1 hour',
'OW_UPDATE_PERIOD_2HOUR' => '2 hour',
'OW_UPDATE_PERIOD_3HOUR' => '3 hour',
'OW_UPDATE_PERIOD_4HOUR' => '4 hour',
'OW_UPDATE_PERIOD_5HOUR' => '5 hour',
'OW_FORECAST_PERIOD_TITLE' => 'Forecast period',
'OW_FORECAST_PERIOD_1DAY' => '1 day',
'OW_FORECAST_PERIOD_2DAY' => '2 days',
'OW_FORECAST_PERIOD_3DAY' => '3 days',
'OW_FORECAST_PERIOD_4DAY' => '4 days',
'OW_FORECAST_PERIOD_5DAY' => '5 days',
'OW_FORECAST_PERIOD_6DAY' => '6 days',
'OW_FORECAST_PERIOD_7DAY' => '7 days',
'OW_FORECAST_PERIOD_8DAY' => '8 days',
'OW_FORECAST_PERIOD_9DAY' => '9 days',
'OW_FORECAST_PERIOD_10DAY' => '10 days',
'OW_FORECAST_PERIOD_11DAY' => '11 days',
'OW_FORECAST_PERIOD_12DAY' => '12 days',
'OW_FORECAST_PERIOD_13DAY' => '13 days',
'OW_FORECAST_PERIOD_14DAY' => '14 days',
'OW_FORECAST_PERIOD_15DAY' => '15 days',
'OW_FORECAST_PERIOD_16DAY' => '16 days',
'OW_FLAG_USE_IMAGE_CACHE' => 'Use cache for images',
'OW_CITY_TITLE' => 'City',
'OW_CHANGE_CITY' => 'Choose another city',
'OW_CHANGE' => 'Change',
'OW_TAB_WEATHER' => 'Weather',
'OW_TAB_SETTINGS' => 'Settings',
'OW_TAB_HELP' => 'Help',
'OW_APP_NAME' => 'Forecast from OpenWeatherMap.org',
'OW_CHOOSE_COUNTRY' => 'Choose country',
'OW_CHOOSE_CITY' => 'Choose city',
'OW_WEATHER_IN_CITY' => 'Weather in',
'OW_WEATHER_ON_DATE' => 'on',
'OW_WEATHER_REFRESH' => 'refresh',
'OW_WEATHER_TODAY' => 'NOW',
'OW_WEATHER_OVERCAST' => 'Overcast',
'OW_WEATHER_RAIN' => 'Precipitation volume (rain)',
'OW_WEATHER_SNOW' => 'Precipitation volume (snow)',
'OW_WEATHER_FREEZE' => 'Freeze possibility',
'OW_WEATHER_TODAY' => 'Today',
'OW_FORECAST_ON' => 'Forecast on',
'OW_FORECAST_FOR_SEVERAL_DAYS' => 'Weather forecast for several days',
'OW_HELP_RUNSCRIPT_VAL' => 'For the "manual" update data in your scripts/methods can be used this code:',
'OW_HELP_RUNSCRIPT_TITLE' => 'For the update data in scripts/methods',
'OW_HELP_CALL_MODULE_MENU_TITLE' => 'Call module in menu',
'OW_HELP_DISPLAY_INFO_CUR_WEATHER' => 'Displays information about the current weather',
'OW_HELP_DISPLAY_INFO_CUR_WEATHER_FORECAST' => 'Displays information about the current weather and the forecast for today',
'OW_HELP_DISPLAY_INFO_FORECAST_0DAY' => 'Displays forecast for today',
'OW_HELP_DISPLAY_INFO_FORECAST_1DAY' => 'Displays forecast for today and next 1 day',
'OW_HELP_DISPLAY_INFO_FORECAST_2DAY' => 'Displays forecast for today and next 2 day',

'OW_SUNINFO_SUNRISE' => 'Sunrise',
'OW_SUNINFO_SUNSET' => 'Sunset',
'OW_SUNINFO_DAY_LENGTH' => 'Day Length',
'OW_SUNINFO_DAY_SUNRISE_SUNSET' => 'Sunrise/Sunset',
'OW_API_KEY' => 'API Key',
'OW_API_METHOD' => 'API Method',
'OW_ROUND' => 'Round',
'OW_UNTIL_WHOLE' => 'Until whole',
'OW_WEATHER_STATION_MODE' => 'Weather station mode',
'OW_CITYNAME' => 'City name',
'OW_16D' => '16 days/daily forecast API',
'OW_5D3H' => '5 days/3 hour forecast API',

//wind full
'OW_WIND_FULL_N' => 'North',
'OW_WIND_FULL_NNE' => 'North-North-East',
'OW_WIND_FULL_NE' => 'North-East',
'OW_WIND_FULL_ENE' => 'East-North-East',
'OW_WIND_FULL_E' => 'East',
'OW_WIND_FULL_ESE' => 'East-South-East',
'OW_WIND_FULL_SE' => 'South-East',
'OW_WIND_FULL_SSE' => 'South-South-East',
'OW_WIND_FULL_S' => 'South',
'OW_WIND_FULL_SSW' => 'South-South-West',
'OW_WIND_FULL_SW' => 'South-West',
'OW_WIND_FULL_WSW' => 'West-South-West',
'OW_WIND_FULL_W' => 'West',
'OW_WIND_FULL_WNW' => 'West-North-West',
'OW_WIND_FULL_NW' => 'North-West',
'OW_WIND_FULL_NNW' => 'North-North-West',

/* end module names */
);

foreach ($dictionary as $k=>$v)
{
   if (!defined('LANG_' . $k))
   {
      define('LANG_' . $k, $v);
   }
}

?>
