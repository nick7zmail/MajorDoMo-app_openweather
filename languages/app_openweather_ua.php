<?php
/**
 * Russian language file for OpenWeather module
 */

$dictionary = array(
/* general */
'OW_SCRIPT_NAME'=>'Назва сценарію',
'OW_EXECUTE_AFTER_UPDATE'=>'Після оновлення виконати сценарій',
'OW_UPDATE_PERIOD'=>'Період оновлення',
'OW_UPDATE_PERIOD_1HOUR' => 'кожну годину',
'OW_UPDATE_PERIOD_2HOUR' => 'кожні дві години',
'OW_UPDATE_PERIOD_3HOUR' => 'кожні три години',
'OW_UPDATE_PERIOD_4HOUR' => 'кожні чотири години',
'OW_UPDATE_PERIOD_5HOUR' => 'кожні п`ять години',
'OW_FORECAST_PERIOD_TITLE' => 'Прогноз погоди',
'OW_FORECAST_PERIOD_1DAY' => 'на 1 день',
'OW_FORECAST_PERIOD_2DAY' => 'на 2 дні',
'OW_FORECAST_PERIOD_3DAY' => 'на 3 дні',
'OW_FORECAST_PERIOD_4DAY' => 'на 4 дні',
'OW_FORECAST_PERIOD_5DAY' => 'на 5 днів',
'OW_FORECAST_PERIOD_6DAY' => 'на 6 днів',
'OW_FORECAST_PERIOD_7DAY' => 'на 7 днів',
'OW_FORECAST_PERIOD_8DAY' => 'на 8 днів',
'OW_FORECAST_PERIOD_9DAY' => 'на 9 днів',
'OW_FORECAST_PERIOD_10DAY' => 'на 10 днів',
'OW_FORECAST_PERIOD_11DAY' => 'на 11 днів',
'OW_FORECAST_PERIOD_12DAY' => 'на 12 днів',
'OW_FORECAST_PERIOD_13DAY' => 'на 13 днів',
'OW_FORECAST_PERIOD_14DAY' => 'на 14 днів',
'OW_FORECAST_PERIOD_15DAY' => 'на 15 днів',
'OW_FORECAST_PERIOD_16DAY' => 'на 16 днів',
'OW_FLAG_USE_IMAGE_CACHE' => 'Використовувати збережені іконки',
'OW_CITY_TITLE' => 'Місто',
'OW_CHANGE_CITY' => 'Обрати місто',
'OW_CHANGE' => 'Змінити',
'OW_TAB_WEATHER' => 'Погода',
'OW_TAB_SETTINGS' => 'Налаштування',
'OW_TAB_HELP' => 'Допомога',
'OW_APP_NAME' => 'Погода від OpenWeatherMap.org',
'OW_CHOOSE_COUNTRY' => 'Обрати країну',
'OW_CHOOSE_CITY' => 'Обрати місто',
'OW_WEATHER_IN_CITY' => 'Погода в м.',
'OW_WEATHER_ON_DATE' => 'станом на',
'OW_WEATHER_REFRESH' => 'оновити',
'OW_WEATHER_NOW' => 'Зараз',
'OW_WEATHER_OVERCAST' => 'Хмарність',
'OW_WEATHER_RAIN' => 'Об`єм осадів (дождю)',
'OW_WEATHER_SNOW' => 'Об`єм осадів (снігу)',
'OW_WEATHER_FREEZE' => 'Ймовірність заморозків',
'OW_WEATHER_TODAY' => 'Сьогодні',
'OW_FORECAST_ON' => 'Прогноз на',
'OW_FORECAST_FOR_SEVERAL_DAYS' => 'Прогноз на декілька днів',
'OW_HELP_RUNSCRIPT_VAL' => 'Для "ручного" оновлення даних, в своїх скриптах/методах можно використати:',
'OW_HELP_RUNSCRIPT_TITLE' => 'Виклик модуля в своїх скриптах/методах',
'OW_HELP_CALL_MODULE_MENU_TITLE' => 'Виклик модуля в меню',
'OW_HELP_DISPLAY_INFO_CUR_WEATHER' => 'вивід інформації про теперішню погоду',
'OW_HELP_DISPLAY_INFO_CUR_WEATHER_FORECAST' => 'вивід інформації про теперішню погоду та прогнозу на сьогодні',
'OW_HELP_DISPLAY_INFO_FORECAST_0DAY' => 'прогноз погоди на сьогодні ',
'OW_HELP_DISPLAY_INFO_FORECAST_1DAY' => 'прогноз погоди на сьогодні та завтра',
'OW_HELP_DISPLAY_INFO_FORECAST_2DAY' => 'прогноз погоди на сьогодні, завтра та післязавтра',

'OW_SUNINFO_SUNRISE' => 'Схід',
'OW_SUNINFO_SUNSET' => 'Захід',
'OW_SUNINFO_DAY_LENGTH' => 'Довжина дня',
'OW_SUNINFO_DAY_SUNRISE_SUNSET' => 'Схід/захід сонця',
'OW_API_KEY' => 'Ключ API',
'OW_API_METHOD' => 'API Method',
'OW_ROUND' => 'Round',
'OW_UNTIL_WHOLE' => 'Until whole',
'OW_WEATHER_STATION_MODE' => 'Weather station mode',
'OW_CITYNAME' => 'Назва міста',
'OW_16D' => '16 денний/на кожен день',
'OW_5D3H' => '5 денний/3 годинний прогноз',

//wind full
'OW_WIND_FULL_N' => 'Північ',
'OW_WIND_FULL_NNE' => 'Північно-Північно-Схід',
'OW_WIND_FULL_NE' => 'Північно-Схід',
'OW_WIND_FULL_ENE' => 'Схід-Північно-Схід',
'OW_WIND_FULL_E' => 'Схід',
'OW_WIND_FULL_ESE' => 'Схід-Південний-Схід',
'OW_WIND_FULL_SE' => 'Південно-Схід',
'OW_WIND_FULL_SSE' => 'Південно-Південно-Схід',
'OW_WIND_FULL_S' => 'Південь',
'OW_WIND_FULL_SSW' => 'Південно-Південно-Захід',
'OW_WIND_FULL_SW' => 'Південно-Захід',
'OW_WIND_FULL_WSW' => 'Захід-Південь-Захід',
'OW_WIND_FULL_W' => 'Захід',
'OW_WIND_FULL_WNW' => 'Захід-Північ-Захід',
'OW_WIND_FULL_NW' => 'Північ-Захід',
'OW_WIND_FULL_NNW' => 'Північ-Північ-Захід',

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
