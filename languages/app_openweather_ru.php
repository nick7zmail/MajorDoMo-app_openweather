<?php
/**
 * Russian language file for OpenWeather module
 */

$dictionary = array(
/* general */
'OW_SCRIPT_NAME'=>'Имя сценария',
'OW_EXECUTE_AFTER_UPDATE'=>'После обновления выполнить сценарий',
'OW_UPDATE_PERIOD'=>'Период обновления',
'OW_UPDATE_PERIOD_1HOUR' => 'каждый час',
'OW_UPDATE_PERIOD_2HOUR' => 'каждые два часа',
'OW_UPDATE_PERIOD_3HOUR' => 'каждые три часа',
'OW_UPDATE_PERIOD_4HOUR' => 'каждые четыре часа',
'OW_UPDATE_PERIOD_5HOUR' => 'каждые пять часов',
'OW_FORECAST_PERIOD_TITLE' => 'Прогноз погоды',
'OW_FORECAST_PERIOD_1DAY' => 'на 1 день',
'OW_FORECAST_PERIOD_2DAY' => 'на 2 дня',
'OW_FORECAST_PERIOD_3DAY' => 'на 3 дня',
'OW_FORECAST_PERIOD_4DAY' => 'на 4 дня',
'OW_FORECAST_PERIOD_5DAY' => 'на 5 дней',
'OW_FORECAST_PERIOD_6DAY' => 'на 6 дней',
'OW_FORECAST_PERIOD_7DAY' => 'на 7 дней',
'OW_FORECAST_PERIOD_8DAY' => 'на 8 дней',
'OW_FORECAST_PERIOD_9DAY' => 'на 9 дней',
'OW_FORECAST_PERIOD_10DAY' => 'на 10 дней',
'OW_FORECAST_PERIOD_11DAY' => 'на 11 дней',
'OW_FORECAST_PERIOD_12DAY' => 'на 12 дней',
'OW_FORECAST_PERIOD_13DAY' => 'на 13 дней',
'OW_FORECAST_PERIOD_14DAY' => 'на 14 дней',
'OW_FORECAST_PERIOD_15DAY' => 'на 15 дней',
'OW_FORECAST_PERIOD_16DAY' => 'на 16 дней',
'OW_FLAG_USE_IMAGE_CACHE' => 'Использовать кешированные иконки',
'OW_CITY_TITLE' => 'Город',
'OW_CHANGE_CITY' => 'Изменить город',
'OW_CHANGE' => 'Изменить',
'OW_TAB_WEATHER' => 'Погода',
'OW_TAB_SETTINGS' => 'Настройки',
'OW_TAB_HELP' => 'Помощь',
'OW_APP_NAME' => 'Погода от OpenWeatherMap.org',
'OW_CHOOSE_COUNTRY' => 'Выбрать страну',
'OW_CHOOSE_CITY' => 'Выбрать город',
'OW_WEATHER_IN_CITY' => 'Погода в г.',
'OW_WEATHER_ON_DATE' => 'по состоянию на',
'OW_WEATHER_REFRESH' => 'обновить',
'OW_WEATHER_NOW' => 'Сейчас',
'OW_WEATHER_WIND' => 'Ветер',
'OW_WEATHER_OVERCAST' => 'Облачность',
'OW_PRESSURE' => 'Давление',
'OW_PRESSURE_MMHG' => 'мм рт. ст',
'OW_PRESSURE_HPA' => 'гПА',
'OW_WEATHER_HUMIDITY' => 'Влажность',
'OW_WEATHER_RAIN' => 'Объем осадков (дождь)',
'OW_WEATHER_SNOW' => 'Объем осадков (снег)',
'OW_WEATHER_FREEZE' => 'Вероятность заморозка',
'OW_WEATHER_TODAY' => 'Сегодня',
'OW_FORECAST_ON' => 'Прогноз на',
'OW_FORECAST_FOR_SEVERAL_DAYS' => 'Прогноз на несколько дней',
'OW_HELP_RUNSCRIPT_VAL' => 'Для "ручного" обновления данных, в своих скриптах/методах можно использовать:',
'OW_HELP_RUNSCRIPT_TITLE' => 'Вызов модуля в своих скриптах/методах',
'OW_HELP_CALL_MODULE_MENU_TITLE' => 'Вызов модуля в меню',
'OW_HELP_DISPLAY_INFO_CUR_WEATHER' => 'вывод сведений о текущей погоде',
'OW_HELP_DISPLAY_INFO_CUR_WEATHER_FORECAST' => 'вывод сведений о текущей погоде и прогнозе на сегодня',
'OW_HELP_DISPLAY_INFO_FORECAST_0DAY' => 'прогноз погоды на сегодня ',
'OW_HELP_DISPLAY_INFO_FORECAST_1DAY' => 'прогноз погоды на сегодня и завтра',
'OW_HELP_DISPLAY_INFO_FORECAST_2DAY' => 'прогноз погоды на сегодня, завтра и послезавтра',

'OW_SUNINFO_SUNRISE' => 'Восход',
'OW_SUNINFO_SUNSET' => 'Заход',
'OW_SUNINFO_DAY_LENGTH' => 'Долгота дня',
'OW_SUNINFO_DAY_SUNRISE_SUNSET' => 'Восход/заход солнца',
'OW_API_KEY' => 'Ключ API',
'OW_CITYNAME' => 'Имя города'

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
