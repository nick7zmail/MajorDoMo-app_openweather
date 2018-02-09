<?php

	/**
	* Get wind direction name by direction in degree 
	* @param mixed $degree Wind degree
	* @return string
	*/
function getWindDirection($degree, $full=false)
   {
	if (SETTINGS_SITE_LANGUAGE && file_exists(ROOT . 'languages/' . 'app_openweather_' .SETTINGS_SITE_LANGUAGE . '.php'))
	{
		include_once (ROOT . 'languages/' . 'app_openweather_' .SETTINGS_SITE_LANGUAGE . '.php');
	} else {
		include_once (ROOT . 'languages/'.'app_openweather_default.php');
	}
	if($full) {
		$windDirection = array(
			LANG_OW_WIND_FULL_N,
			LANG_OW_WIND_FULL_NNE,
			LANG_OW_WIND_FULL_NE,
			LANG_OW_WIND_FULL_ENE,
			LANG_OW_WIND_FULL_E,
			LANG_OW_WIND_FULL_ESE,
			LANG_OW_WIND_FULL_SE,
			LANG_OW_WIND_FULL_SSE,
			LANG_OW_WIND_FULL_S,
			LANG_OW_WIND_FULL_SSW,
			LANG_OW_WIND_FULL_SW,
			LANG_OW_WIND_FULL_WSW,
			LANG_OW_WIND_FULL_W,
			LANG_OW_WIND_FULL_WNW,
			LANG_OW_WIND_FULL_NW,
			LANG_OW_WIND_FULL_NNW,
			LANG_OW_WIND_FULL_N	
		);		
	} else {
		$windDirection = array(
			LANG_N,
			LANG_NNE,
			LANG_NE,
			LANG_ENE,
			LANG_E,
			LANG_ESE,
			LANG_SE,
			LANG_SSE,
			LANG_S,
			LANG_SSW,
			LANG_SW,
			LANG_WSW,
			LANG_W,
			LANG_WNW,
			LANG_NW,
			LANG_NNW,
			LANG_N
		);
	}
    $direction = $windDirection[round(intval($degree) / 22.5)];
    return $direction;
   }
   
   /**
    * Convert Pressure from one system to another. 
    * If error or system not found then function return current pressure.
    * @param $vPressure 
    * @param $vFrom
    * @param $vTo
    * @param $vPrecision
    * @return
    */
function ConvertPressure($pressure, $from, $to, $precision = 2)
   {
      if (empty($from) || empty($to) || empty($pressure))
         return $pressure;
      
      if (!is_numeric($pressure))
         return $pressure;
      
      $pressure = (float) $pressure;
      $from     = strtolower($from);
      $to       = strtolower($to);
      
      if ($from == "hpa" && $to == "mmhg")
         return round($pressure * 0.75006375541921, $precision);
      
      if ($from == "mmhg" && $to == "hpa")
         return round($pressure * 1.33322, $precision);
      
      return $pressure;
   }
   
	/**
	* Get possibility freeze by evening and day temperature
	* @param mixed $tempDay      Temperature at 13:00
	* @param mixed $tempEvening  Termerature at 21:00
	* @return double|int         Freeze possibility %
	*/
function GetFreezePossibility($tempDay, $tempEvening)
	{
	// Температура растет или Температура ниже нуля
	if ( $tempEvening >= $tempDay || $tempEvening < 0)
		return -1;

		$tempDelta = $tempDay - $tempEvening;

		if ( $tempEvening < 11 && $tempDelta < 11 )
		{
		$t_graph = array(0 => array(0.375, 11, 0),
						1 => array(0.391, 8.7, 10),
						2 => array(0.382, 6.7, 20),
						3 => array(0.382, 4.7, 40),
						4 => array(0.391, 2.7, 60),
						5 => array(0.4, 1.6, 80));
		$graphCount = count($t_graph);
		for ($i = 0; $i < $graphCount; $i++)
		{
			$y1 = $t_graph[$i][0] * $tempDelta + $t_graph[$i][1];
			if ( $tempEvening > $y1)
				{
					return (int)$t_graph[$i][2];
				}
			}
			return 100;
		}
      
	return -1;
	}
 
function GetSunInfo($timeStamp = -1)
	{
		$cityLat=gg('ow_city.lat');
		$cityLong=gg('ow_city.lon');
		if($timeStamp == '' or $timeStamp == -1) $timeStamp = time();
		if (!isset($cityLat) || !isset($cityLong)) return FALSE;
		if(empty($cityLat) || empty($cityLong)) {
			DebMes('OpenWeather: '.'CityCoords not found');
			return FALSE;
		}
		$info = date_sun_info($timeStamp, $cityLat, $cityLong);
		return $info;
	}
?>
