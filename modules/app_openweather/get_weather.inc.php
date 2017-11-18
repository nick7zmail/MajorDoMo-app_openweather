<?php
		if (!isset($cityID)) return null;
		$apiKey = gg('ow_setting.api_key');
		$unit = 'metric';
		
		$query = "http://api.openweathermap.org/data/2.5/weather?id=" . $cityID . "&mode=json&units=" . $unit . "&lang=ru" . "&appid=" . $apiKey;
		$data =  getURL($query);		
		$curWeather = json_decode($data);
		if ($curWeather->cod == "404")
		  {
			 DebMes('OpenWeather: '.$weather->message);
			 return;
		  }
		 
		if($curWeather!=false && !empty($curWeather)) {
		  $fact = $curWeather->main;
		  
		  $date = date("d.m.Y G:i:s T Y", $curWeather->dt);
		 
		  sg('ow_fact.temperature', round ($fact->temp,1));
		  sg('ow_fact.weather_type', $curWeather->weather[0]->description);
		  sg('ow_fact.wind_direction', $curWeather->wind->deg);
		  sg('ow_fact.wind_speed',$curWeather->wind->speed);
		  sg('ow_fact.humidity', $fact->humidity);
		  sg('ow_fact.pressure', $fact->pressure);
		  sg('ow_fact.pressure_mmhg', app_openweather::ConvertPressure($fact->pressure, "hpa", "mmhg", 2));
		  sg('ow_fact.image', $curWeather->weather[0]->icon);
		  sg('ow_fact.clouds', $curWeather->clouds->all);
		  sg('ow_fact.rain', isset($fact->rain) ? $fact->rain : '');
		  sg('ow_fact.condCode', $curWeather->weather[0]->id);
		  sg('ow_city.data_update', $date);

		  
		  $sunInfo = $this->GetSunInfo();
		  if ($sunInfo)
		  {
			 $sunRise = $sunInfo["sunrise"];
			 $sunSet = $sunInfo["sunset"];
			 $dayLength = $sunSet - $sunRise;

			 sg('ow_fact.sunrise', $sunRise);
			 sg('ow_fact.sunset', $sunSet);
			 sg('ow_fact.day_length', $dayLength);
			 sg('ow_fact.transit', $sunInfo["transit"]);
			 sg('ow_fact.civil_twilight_begin', $sunInfo["civil_twilight_begin"]);
			 sg('ow_fact.civil_twilight_end', $sunInfo["civil_twilight_end"]);
		  }
		}
		
		
		
	if(gg('ow_setting.forecast_interval')>2) {
		$query= "http://api.openweathermap.org/data/2.5/forecast/daily?id=" . $cityID . "&mode=json&units=" . $unit . "&cnt=16&lang=ru" . "&appid=" . $apiKey;
		$data = getURL($query);
		$weather = json_decode($data);
		
      if ($weather->cod == "404")
      {
         DebMes('OpenWeather: '.$weather->message);
         return;
      }
	  if($weather!=false && !empty($weather)) {
		  $i = 0;
		  foreach($weather->list as $day)
		  {
			 $date = date("d.m.Y", $day->dt);
			 sg('ow_day'.$i.'.date', $date);
			 
			 sg('ow_day'.$i.'.temperature', round (app_openweather::GetCurrTemp($day->temp),1));
			 sg('ow_day'.$i.'.temp_morn', round ($day->temp->morn,1));
			 sg('ow_day'.$i.'.temp_day', round ($day->temp->day,1));
			 sg('ow_day'.$i.'.temp_eve', round ($day->temp->eve,1));
			 sg('ow_day'.$i.'.temp_night', round ($day->temp->night,1));
			 sg('ow_day'.$i.'.temp_min', round ($day->temp->min,1));
			 sg('ow_day'.$i.'.temp_max', round ($day->temp->max,1));
			 
			 sg('ow_day'.$i.'.weather_type', $day->weather[0]->description);
			 sg('ow_day'.$i.'.wind_direction', $day->deg);
			 sg('ow_day'.$i.'.wind_speed', $day->speed);
			 if($day->humidity) sg('ow_day'.$i.'.humidity', $day->humidity);
			 sg('ow_day'.$i.'.pressure', $day->pressure);
			 sg('ow_day'.$i.'.pressure_mmhg', app_openweather::ConvertPressure($day->pressure, "hpa", "mmhg", 2));
			 sg('ow_day'.$i.'.image', $day->weather[0]->icon);
			 sg('ow_day'.$i.'.clouds', $day->clouds);
			 sg('ow_day'.$i.'.rain', isset($day->rain) ? $day->rain : 0);
			 sg('ow_day'.$i.'.snow', isset($day->snow) ? $day->snow : 0);
			 sg('ow_day'.$i.'.condCode', $day->weather[0]->id);
			 
			 $curTimeStamp = strtotime('+' . $i . ' day', time());
			 $sunInfo = $this->GetSunInfo($curTimeStamp);
			 if ($sunInfo)
			 {
				$sunRise = $sunInfo["sunrise"];
				$sunSet = $sunInfo["sunset"];
				$dayLength = $sunSet - $sunRise;
				
				sg('ow_day'.$i.'.sunrise', $sunRise);
				sg('ow_day'.$i.'.sunset', $sunSet);
				sg('ow_day'.$i.'.day_length', $dayLength);
				sg('ow_day'.$i.'.transit', $sunInfo["transit"]);
				sg('ow_day'.$i.'.civil_twilight_begin', $sunInfo["civil_twilight_begin"]);
				sg('ow_day'.$i.'.civil_twilight_end', $sunInfo["civil_twilight_end"]);
			 }
			 
			 $i++;
		  }
	  }
	} else {
			$query= "http://api.openweathermap.org/data/2.5/forecast?id=" . $cityID . "&mode=json&units=" . $unit . "&cnt=16&lang=ru" . "&appid=" . $apiKey;
			$data = getURL($query);
			$weather = json_decode($data);
			if ($weather->cod == "404")
			  {
				 DebMes('OpenWeather: '.$weather->message);
				 return;
			  }
			  debmes('OpenWeather: '.$query);
	  if($weather!=false && !empty($weather)) {
		  $i = 0;
		  foreach($weather->list as $day)
		  {
			 $date = date("d.m.Y (H:i)", $day->dt);
			 sg('ow_day'.$i.'.date', $date);
			 
			 sg('ow_day'.$i.'.temperature', round (app_openweather::GetCurrTemp($day->main->temp),1));
			 sg('ow_day'.$i.'.temp_morn', 'na');
			 sg('ow_day'.$i.'.temp_day', 'na');
			 sg('ow_day'.$i.'.temp_eve', 'na');
			 sg('ow_day'.$i.'.temp_night', 'na');
			 sg('ow_day'.$i.'.temp_min', round ($day->main->temp_min,1));
			 sg('ow_day'.$i.'.temp_max', round ($day->main->temp_max,1));
			 
			 sg('ow_day'.$i.'.weather_type', $day->weather[0]->description);
			 sg('ow_day'.$i.'.wind_direction', $day->wind->deg);
			 sg('ow_day'.$i.'.wind_speed', $day->wind->speed);
			 if($day->main->humidity) sg('ow_day'.$i.'.humidity', $day->main->humidity);
			 sg('ow_day'.$i.'.pressure', $day->main->pressure);
			 sg('ow_day'.$i.'.pressure_mmhg', app_openweather::ConvertPressure($day->main->pressure, "hpa", "mmhg", 2));
			 sg('ow_day'.$i.'.image', $day->weather[0]->icon);
			 sg('ow_day'.$i.'.clouds', $day->clouds->all);
			 //sg('ow_day'.$i.'.rain', isset($day->rain->3h) ? $day->rain->3h : 0);
			 //sg('ow_day'.$i.'.snow', isset($day->snow->3h) ? $day->snow->3h : 0);
			 sg('ow_day'.$i.'.condCode', $day->weather[0]->id);
			 
			 $curTimeStamp = $day->dt;
			 $sunInfo = $this->GetSunInfo($curTimeStamp);
			 if ($sunInfo)
			 {
				$sunRise = $sunInfo["sunrise"];
				$sunSet = $sunInfo["sunset"];
				$dayLength = $sunSet - $sunRise;
				
				sg('ow_day'.$i.'.sunrise', $sunRise);
				sg('ow_day'.$i.'.sunset', $sunSet);
				sg('ow_day'.$i.'.day_length', $dayLength);
				sg('ow_day'.$i.'.transit', $sunInfo["transit"]);
				sg('ow_day'.$i.'.civil_twilight_begin', $sunInfo["civil_twilight_begin"]);
				sg('ow_day'.$i.'.civil_twilight_end', $sunInfo["civil_twilight_end"]);
			 }
			 
			 $i++;
		  }
	  }
	}
?>
