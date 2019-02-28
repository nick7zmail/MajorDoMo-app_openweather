<?php

class app_openweather extends module
{
   /**
    * openweather
    *
    * Module class constructor
    *
    * @access private
    */
   public function __construct()
   {
      $this->name = "app_openweather";
      $this->title = "Погода от OpenWeatherMap";
      $this->module_category = "<#LANG_SECTION_APPLICATIONS#>";
      $this->checkInstalled();
   }
   
   public function saveParams($data = 0)
   {
      $p = array();
      
      if(isset($this->id))
         $p["id"] = $this->id;
      
      if(isset($this->view_mode))
         $p["view_mode"] = $this->view_mode;
      
      if(isset($this->edit_mode))
         $p["edit_mode"] = $this->edit_mode;
      
      if(isset($this->tab))
         $p["tab"] = $this->tab;
      
      return parent::saveParams($p);
   }
   
   public function getParams()
   {
      global $id;
      global $mode;
      global $view_mode;
      global $edit_mode;
      global $tab;
      global $fact;
      global $forecast;
	  
      if (isset($id))
         $this->id=$id;
      
      if (isset($mode))
         $this->mode = $mode;
      
      if (isset($view_mode))
         $this->view_mode = $view_mode;
      
      if (isset($edit_mode))
         $this->edit_mode = $edit_mode;
      
      if (isset($tab))
         $this->tab = $tab;
      
      if (isset($forecast))
         $this->forecast = $forecast;
      
      if (isset($fact))
         $this->fact = $fact;
   }
   
   public function run()
   {
      global $session;
      $out = array();
      
      if ($this->action == 'admin')
         $this->admin($out);
      else
         $this->usual($out);
      
      if (isset($this->owner->action))
         $out['PARENT_ACTION'] = $this->owner->action;
      
      if (isset($this->owner->name))
         $out['PARENT_NAME'] = $this->owner->name;
      
      $out['VIEW_MODE'] = $this->view_mode;
      $out['EDIT_MODE'] = $this->edit_mode;
      $out['MODE']      = $this->mode;
      $out['ACTION']    = $this->action;
      if ($this->single_rec)
         $out['SINGLE_REC'] = 1;
      
      $this->data = $out;

      $p = new parser(DIR_TEMPLATES . $this->name . "/" . $this->name . ".html", $this->data, $this);

      $this->result = $p->result;
   }
   
   /**
    * BackEnd
    * Summary of admin
    * @param mixed $out Out
    */
   public function admin(&$out)
   {
      global $ow_subm;
      
      if($ow_subm == 'setCityId')
      {
         $this->save_cityId();
         $this->view_mode = "setting";
      }
      else if($ow_subm == 'setting')
      {
         $this->save_setting();
         $this->get_weather(gg('ow_city.id'));
         $this->view_mode = "";
      }
	  else if($ow_subm == 'getCityId')
      {
         $this->view_mode = "getCityId";
         $this->get_cityId($out);
      }
      else if($ow_subm == 'getWeather')
      {
         $this->get_weather(gg('ow_city.id'));
      }       
	  else if($ow_subm == 'ws_reg')
      {
         $this->ws_reg();
      }
      
      if($this->view_mode == '')
      {
         $ow_city_id = gg('ow_city.id');
         $ow_city_name = gg('ow_city.name');
         
         if($ow_city_id != '' && $ow_city_name != '')
         {
            $out["ow_city_name"] = $ow_city_name;
            $out["ow_data_update"] = gg('ow_city.data_update');
            $this->view_weather($out);
         }
		 else
         {
            $this->view_mode = "getCityId";
            $this->get_cityId($out);
         }
      }
      else if($this->view_mode == 'setting')
      {
         $this->get_setting($out);
      }
	  else if($this->view_mode == 'getCityId')
      {
		$filePath = ROOT.'cms'. DIRECTORY_SEPARATOR . 'cached' . DIRECTORY_SEPARATOR . 'openweather';
		if(!file_exists($filePath . DIRECTORY_SEPARATOR . 'city_list.txt')) {  
			 if (!is_dir($filePath))
			 {
				@mkdir(ROOT . 'cms'. DIRECTORY_SEPARATOR . 'cached', 0777);
				@mkdir($filePath, 0777);
			 }
			/* SaveFile($fileName, @file_get_contents('http://bulk.openweathermap.org/sample/city.list.json.gz'));
			 $buffer_size = 4096;
			 $out_file_name = str_replace('.gz', '', $fileName); 
			 $file = gzopen($fileName, 'rb');
			 $out_file = fopen($out_file_name, 'wb'); 
			 while (!gzeof($file)) {
				fwrite($out_file, gzread($file, $buffer_size));
			 }
			 fclose($out_file);
			 gzclose($file);*/
		}
         $this->get_cityId($out);
      }
	  else if($this->view_mode == 'wstation')
      {
		 $this->ws_get_info($out);
	  }
	  $out["ow_ws_active"] = gg('ow_setting.ow_ws_active');	
   }
   
   /**
    * FrontEnd
    * Summary of usual
    * @access public
    *
    * @param mixed $out 
    */
   public function usual(&$out)
   {
      $ow_city_id   = gg('ow_city.id');
      $ow_city_name = gg('ow_city.name');
      
      if ($ow_city_id != '' && $ow_city_name != '')
      {
         $out["ow_city_name"]     = $ow_city_name;
         $out["data_update"] = gg('ow_city.data_update');

         $this->view_weather($out);
      }
      else
      {
         $out["fact_weather"] = constant('LANG_OW_CHOOSE_CITY');
      }
   }

   /**
    * Вывод данных о прогнозе погоды
    * @param array $out Массив с сформированными данными о прогнозе погоды
    * @return void
    */
   public function view_weather(&$out)
   {
      $fact     = $this->fact;
      $forecast = $this->forecast;
	  
      if (is_null($forecast))
         $forecast = gg('ow_setting.forecast_interval');
      
      if($fact != 'off')
      {
         $temp = gg('ow_fact.temperature');

         if($temp > 0) $temp = "+" . $temp;

         $out["FACT"]["temperature"]   = $temp;
         $out["FACT"]["weatherIcon"]   = app_openweather::getWeatherIcon(gg('ow_fact.image'));
         $windDirection                = gg('ow_fact.wind_direction');
         $out["FACT"]["windDirection"] = getWindDirection($windDirection) . " (" . $windDirection . "&deg;)";
         $out["FACT"]["windSpeed"]     = gg('ow_fact.wind_speed');
         $out["FACT"]["humidity"]      = gg('ow_fact.humidity');
         $out["FACT"]["clouds"]        = gg('ow_fact.clouds');
         $out["FACT"]["weatherType"]   = gg('ow_fact.weather_type');
         $out["FACT"]["pressure"]      = gg('ow_fact.pressure');
         $out["FACT"]["pressure_mmhg"] = ConvertPressure(gg('ow_fact.pressure'),"hpa", "mmhg");
         $out["FACT"]["data_update"]   = gg('ow_city.data_update');
         
         $out["FACT"]["sunrise"]       = date("H:i:s", gg('ow_fact.sunrise'));
         $out["FACT"]["sunset"]        = date("H:i:s", gg('ow_fact.sunset'));
         $out["FACT"]["day_length"]    = gmdate("H:i", gg('ow_fact.day_length'));
      }
      
      if ($forecast > 0)
      {
		 $api_method =gg('ow_setting.api_method'); 
         $forecastOnLabel = constant('LANG_OW_FORECAST_ON');
		 if($api_method=='5d3h') $forecast=$forecast*8-1; else $forecast=$forecast-1;
         for ($i = 0; $i <= $forecast; $i++)
         {
            $curDate = gg('ow_day' . $i . '.date');

            if ($i == 0)
            {
               $out["FORECAST"][$i]["date"] = constant('LANG_OW_WEATHER_TODAY') . ' ' . $curDate;
            }
            else
            {
               $out["FORECAST"][$i]["date"] = $forecastOnLabel . ' ' . $curDate;
            }
            
            $temp = gg('ow_day' . $i . '.temperature');

            if($temp > 0) $temp = "+" . $temp;
            
            if($api_method=='5d3h') $dayTemp=gg('ow_day'.$i.'.temp_max'); else $dayTemp = gg('ow_day'.$i.'.temp_day');
			if($dayTemp > 0) $dayTemp = "+" . $dayTemp;
            $eveTemp = gg('ow_day'.$i.'.temp_eve');
			if($eveTemp > 0) $eveTemp = "+" . $eveTemp;
			if($api_method=='5d3h') $nTemp=gg('ow_day'.$i.'.temp_min'); else $nTemp=gg('ow_day'.$i.'.temp_night');
			if($nTemp > 0) $nTemp = "+" . $nTemp;
			
            $out["FORECAST"][$i]["temperature"] = $temp;
            $out["FORECAST"][$i]["temp_morn"]   = gg('ow_day'.$i.'.temp_morn');
			$out["FORECAST"][$i]["temp_day"] = $dayTemp;
            $out["FORECAST"][$i]["temp_eve"]    = $eveTemp;
			$out["FORECAST"][$i]["temp_night"] = $nTemp;
            $out["FORECAST"][$i]["temp_min"]    = gg('ow_day'.$i.'.temp_min');
            $out["FORECAST"][$i]["temp_max"]    = gg('ow_day'.$i.'.temp_max');
            
            $out["FORECAST"][$i]["weatherIcon"]   = app_openweather::getWeatherIcon(gg('ow_day' . $i . '.image'));
            $windDirection                        = gg('ow_day' . $i . '.wind_direction');
            $out["FORECAST"][$i]["windDirection"] = getWindDirection($windDirection) . " (" . $windDirection . "&deg;)";
            $out["FORECAST"][$i]["windSpeed"]     = gg('ow_day'.$i.'.wind_speed');
            $out["FORECAST"][$i]["humidity"]      = gg('ow_day'.$i.'.humidity');
            $out["FORECAST"][$i]["weatherType"]   = gg('ow_day'.$i.'.weather_type');
            $out["FORECAST"][$i]["pressure"]      = gg('ow_day'.$i.'.pressure');
            $out["FORECAST"][$i]["pressure_mmhg"] = gg('ow_day'.$i.'.pressure_mmhg');
            $out["FORECAST"][$i]["clouds"]        = gg('ow_day'.$i.'.clouds');
            $out["FORECAST"][$i]["rain"]          = gg('ow_day'.$i.'.rain');
            $out["FORECAST"][$i]["snow"]          = gg('ow_day'.$i.'.snow');
            $out["FORECAST"][$i]["freeze"]        = GetFreezePossibility($dayTemp, $eveTemp);
			
            $out["FORECAST"][$i]["sunrise"]    = date("H:i:s", (int)gg('ow_day'.$i.'.sunrise'));
            $out["FORECAST"][$i]["sunset"]     = date("H:i:s", (int)gg('ow_day'.$i.'.sunset'));
            $out["FORECAST"][$i]["day_length"] = gmdate("H:i", (int)gg('ow_day'.$i.'.day_length')); 
         }
      }
   }
	function processSubscription($event_name, $details='') {
		if ($event_name=='HOURLY') {
			$updateTime = gg('ow_setting.updateTime');
			if($updateTime > 0) {
				$count = gg('ow_setting.countTime'); 
				if($count >= $updateTime){
					$this->get_weather(gg('ow_city.id'));
					if( gg('ow_setting.ow_ws_active')==1) $this->ws_send_data($out, true);
					sg('ow_setting.countTime', 1);
				} else {
					$count++;
					sg('ow_setting.countTime', $count);
				}
			}
		}
	}
   /**
    * Получение погоды по ID города
    * @param int $cityID ID города  
    */
   public function get_weather($cityID)
   {
	require(DIR_MODULES.$this->name.'/get_weather.inc.php');
    runScript(gg('ow_setting.updScript'));
   }

   /**
    * Get weather icon
    * @param string $image weather icon name
    * @return string
    */
   private static function getWeatherIcon($image)
   {
      if ($image == '') retrun;
      
      $fileName = $image . '.png';
      $urlIcon = "http://openweathermap.org/img/w/" . $fileName;
      
      if(gg('ow_setting.ow_imagecache') == 'on')
      {
         $filePath = ROOT.'cms'. DIRECTORY_SEPARATOR . 'cached' . DIRECTORY_SEPARATOR . 'openweather' . DIRECTORY_SEPARATOR . 'image';
         
         if (!is_dir($filePath))
         {
            @mkdir(ROOT . 'cms'. DIRECTORY_SEPARATOR . 'cached', 0777);
            @mkdir(ROOT . 'cms'. DIRECTORY_SEPARATOR . 'cached' . DIRECTORY_SEPARATOR . 'openweather', 0777);
            @mkdir($filePath, 0777);
         }
         
         if (!file_exists($filePath . DIRECTORY_SEPARATOR . $fileName))
         {
            $contents = @file_get_contents($urlIcon);
            if ($contents)
            {
               SaveFile($filePath . DIRECTORY_SEPARATOR . $fileName, $contents);
            }
         }
         
         $urlIcon = ROOTHTML . "cms/cached/openweather/image/" . $fileName;
      }
      return $urlIcon;
   }
   

public function save_setting()
	{
		global $ow_forecast_interval;
		global $ow_imagecache;
		global $ow_update_interval;
		global $ow_script;
		global $ow_api_key;
		global $ow_city_id;
		global $ow_city_name;
		global $ow_city_lat;
		global $ow_city_lon;
		global $api_method;
		global $ow_round;	  
		global $ow_ws_active;
	  
		if(!isset($ow_imagecache)) $ow_imagecache = 'off';
		if(isset($ow_script)) sg('ow_setting.updScript', $ow_script);
		if(isset($ow_api_key)) sg('ow_setting.api_key', $ow_api_key);
		if(isset($api_method)) sg('ow_setting.api_method', $api_method);
		if(isset($ow_round)) sg('ow_setting.ow_round', $ow_round);
		if(isset($ow_ws_active)) $ow_ws_active = 1; else $ow_ws_active=0; sg('ow_setting.ow_ws_active', $ow_ws_active);

		sg('ow_setting.ow_imagecache', $ow_imagecache);
		sg('ow_setting.updatetime',$ow_update_interval);
		sg('ow_setting.forecast_interval', $ow_forecast_interval);
		sg('ow_setting.countTime', 1);
		
		$class = SQLSelectOne("SELECT ID FROM classes WHERE TITLE = 'openweather'");
		if ($api_method=='5d3h') $ow_forecast_interval=$ow_forecast_interval*8;
		if ($class['ID']) 
		{
			SQLExec("DELETE FROM pvalues WHERE object_id IN (SELECT ID FROM objects WHERE CLASS_ID='" . $class['ID'] . "' AND TITLE LIKE 'ow_day%')");
			SQLExec("DELETE FROM properties WHERE object_id IN (SELECT ID FROM objects WHERE CLASS_ID='" . $class['ID'] . "' AND TITLE LIKE 'ow_day%')");
			SQLExec("DELETE FROM objects WHERE CLASS_ID='" . $class['ID'] . "' AND TITLE LIKE 'ow_day%'");

			for ($i = 0; $i < $ow_forecast_interval; $i++)
			{

			$obj_rec = array();
			$obj_rec['CLASS_ID'] = $class['ID'];
			$obj_rec['TITLE'] = "ow_day" . $i;
			$obj_rec['DESCRIPTION'] = "Forecast on ".($i+1)." period(s)";
			$obj_rec['ID'] = SQLInsert('objects', $obj_rec);
			}
		}


	}

public function get_setting(&$out)
	{
		$out["ow_city_name"] = gg('ow_city.name');
		$out["ow_city_id"] = gg('ow_city.id');
		$out["ow_city_lat"] = gg('ow_city.lat');
		$out["ow_city_lon"] = gg('ow_city.lon');
		$out["ow_imagecache"] = gg('ow_setting.ow_imagecache');
		$out["updatetime"] = gg('ow_setting.updatetime');
		$out["script"] = gg('ow_setting.updScript');
		$out["forecast_interval"] = gg('ow_setting.forecast_interval');
		$out["ow_api_key"] = gg('ow_setting.api_key');
		$out["api_method"] = gg('ow_setting.api_method');	
		$out["ow_round"] = gg('ow_setting.ow_round');	
		$out["ow_ws_active"] = gg('ow_setting.ow_ws_active');	
	}

public function get_cityId(&$out)
   {
      global $country;
      if (!isset($country)) $country = '';
	  $data = @file_get_contents(ROOT.'cms/cached/openweather/city_list.txt');
	  $out["country"]=$country;
      $dataArray = explode("\n", $data);
		  foreach($dataArray as $row) 
		  {
			 $city = explode("\t", $row); 
			 if ($country==$city[4]) {
				 if ($city[0] == "id" || ($city[0] == "")) continue;
				 $arr["CITY_ID"] = $city[0];
				 $arr["CITY_NAME"] = $city[1];
				 $arr["CITY_LAT"] = $city[2];
				 $arr["CITY_LNG"] = $city[3];
				 $out["ow_city"] .= '<option value = "' .  $arr["CITY_ID"] . '">' . $arr["CITY_NAME"] .' ('.$arr["CITY_LAT"].'|'.$arr["CITY_LNG"].')</option>';
			 }
		  }
		$out["ow_city"] .= '<option value="0" [#if city_id="none"#] selected[#endif#]>--'. constant('LANG_OW_CHOOSE_CITY') . '--</option>';
		$out["city_id"]="none";
   }
   
public function save_cityId()
   {
      global $ow_city_id;
     
      if(isset($ow_city_id) && $ow_city_id != 0)
      {
		$data = @file_get_contents(ROOT.'cms/cached/openweather/city_list.txt');
		if (count($data) <= 0) return;
		$dataArray = explode("\n", $data);	
		  foreach($dataArray as $row) 
		  {
			 $city = explode("\t", $row); 
			 if ($ow_city_id==$city[0]) {
				if ($city[0] == "id" || ($city[0] == "")) continue;
				sg('ow_city.id', $city[0]);
				sg('ow_city.name', $city[1]);
				sg('ow_city.lat', $city[2]);
				sg('ow_city.lon', $city[3]); 
			 }
		  }
      }
   }  

   
private static function GetCurrTemp($temp)
   {
      $time = date("H");
      
      if ($time >= 4 && $time < 13)
      {
         return $temp->morn;
      }
      else if ($time >= 12 && $time < 18) 
      {
         return $temp->day;
      }
      else if($time >= 18 && $time < 24) 
      {
         return $temp->eve;
      }
      else
      {
         return $temp->night;
      }
   }   

private function ws_reg() 
{
	global $external_id;
	global $name;
	global $latitude;
	global $longitude;
	global $altitude;
	
	$apiKey = gg('ow_setting.api_key');
	
	$data['external_id']=$external_id;
	$data['name']=$name;
	$data['latitude']=(float)$latitude;
	$data['longitude']=(float)$longitude;
	$data['altitude']=(float)$altitude;
	$json_data=json_encode($data, JSON_UNESCAPED_UNICODE);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'http://api.openweathermap.org/data/3.0/stations?appid='.$apiKey);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
	$response = curl_exec($ch);
	curl_close($ch);
	$data=json_decode($response, TRUE);
	if ($data['code'] != '')
      {
         DebMes('OpenWeather: Error '.$data['code'].' '.$data['message']);
         return;
      }
	debmes($response);
	sg('ow_ws.id', $data['ID']);
	sg('ow_ws.user_id', $data['user_id']);
	sg('ow_ws.name', $data['name']);
}
private function ws_get_info(&$out) {
	$out["ow_ws_id"] = gg('ow_ws.id');
	$out["ow_ws_name"] = gg('ow_ws.name');
	If ($out["ow_ws_id"]!='' || !isset($out["ow_ws_id"]) ) $out["NEED_REG"]=false; else $out["NEED_REG"]=true;
	$this->ws_send_data($out, false);
}
private function ws_send_data(&$out, $send=false) {
	if (gg('ow_ws.id')!='') 				$data['station_id']=gg('ow_ws.id');
	if (gg('ow_ws.dt')!='') 				$data['dt']=gg('ow_ws.dt'); if($data['station_id']='') $data['station_id']=time();
	if (gg('ow_ws.temperature')!='') 		$data['temperature']=gg('ow_ws.temperature');
	if (gg('ow_ws.wind_speed')!='') 		$data['wind_speed']=gg('ow_ws.wind_speed');
	if (gg('ow_ws.wind_gust')!='') 			$data['wind_gust']=gg('ow_ws.wind_gust');
	if (gg('ow_ws.wind_deg')!='') 			$data['wind_deg']=gg('ow_ws.wind_deg');
	if (gg('ow_ws.pressure')!='') 			$data['pressure']=gg('ow_ws.pressure');
	if (gg('ow_ws.humidity')!='') 			$data['humidity']=gg('ow_ws.humidity');
	if (gg('ow_ws.rain_1h')!='') 			$data['rain_1h']=gg('ow_ws.rain_1h');
	if (gg('ow_ws.rain_6h')!='') 			$data['rain_6h']=gg('ow_ws.rain_6h');
	if (gg('ow_ws.rain_24h')!='') 			$data['rain_24h']=gg('ow_ws.rain_24h');
	if (gg('ow_ws.snow_1h')!='') 			$data['snow_1h']=gg('ow_ws.snow_1h');
	if (gg('ow_ws.snow_6h')!='') 			$data['snow_6h']=gg('ow_ws.snow_6h');
	if (gg('ow_ws.snow_24h')!='') 			$data['snow_24h']=gg('ow_ws.snow_24h');
	if (gg('ow_ws.dew_point')!='') 			$data['dew_point']=gg('ow_ws.dew_point');
	if (gg('ow_ws.humidex')!='') 			$data['humidex']=gg('ow_ws.humidex');
	if (gg('ow_ws.heat_index')!='') 		$data['heat_index']=gg('ow_ws.heat_index');
	if (gg('ow_ws.visibility_distance')!='') $data['visibility_distance']=gg('ow_ws.visibility_distance');
	if (gg('ow_ws.visibility_prefix')!='') 	$data['visibility_prefix']=gg('ow_ws.visibility_prefix');
	if (gg('ow_ws.clouds_distance')!='') 	$data['clouds']['distance']=gg('ow_ws.clouds_distance');
	if (gg('ow_ws.clouds_condition')!='') 	$data['clouds']['condition']=gg('ow_ws.clouds_condition');
	if (gg('ow_ws.clouds_cumulus')!='') 	$data['clouds']['cumulus']=gg('ow_ws.clouds_cumulus');
	if (gg('ow_ws.weather_precipitation')!='') $data['weather']['precipitation']=gg('ow_ws.weather_precipitation');
	if (gg('ow_ws.weather_descriptor')!='') $data['weather']['descriptor']=gg('ow_ws.weather_descriptor');
	if (gg('ow_ws.weather_intensity')!='') 	$data['weather']['intensity']=gg('ow_ws.weather_intensity');
	if (gg('ow_ws.weather_proximity')!='') 	$data['weather']['proximity']=gg('ow_ws.weather_proximity');
	if (gg('ow_ws.weather_obsruration')!='') $data['weather']['obsruration']=gg('ow_ws.weather_obsruration');
	if (gg('ow_ws.weather_other')!='') 		$data['weather']['other']=gg('ow_ws.weather_other');
	$i=1;
	$apiKey = gg('ow_setting.api_key');
	$stationID = $data['station_id'];
	if ($stationID!='') {
		foreach ($data as $k=>$v) {
			$out['data'][$i]['name']=$k;
			$out['data'][$i]['val']=$v;
			$i++;
		}
	}
	if($send==true) {
		$json_data=json_encode($data, JSON_UNESCAPED_UNICODE);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.openweathermap.org/data/3.0/stations?appid='.$apiKey.'&type=h&limit=100&station_id='.$stationID);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
		$response = curl_exec($ch);
		curl_close($ch);
		if ($data['code'] != '')
		  {
			 DebMes('OpenWeather: Error '.$data['code'].' '.$data['message']);
			 return;
		  }
		}
}   
   /**
    * Install
    * Module installation routine
    * @access private
    */
   public function install($data = '')
   {
	  subscribeToEvent($this->name, 'HOURLY');
      $className = 'openweather';
      $objectName = array('ow_city', 'ow_setting', 'ow_ws', 'ow_fact', 'ow_day0', 'ow_day1', 'ow_day2');
      $objDescription = array('Местоположение', 'Настройки', 'Погодная станция (экспорт)', 'Текущая температура', 'Прогноз погоды на день', 'Прогноз погоды на завтра', 'Прогноз погоды на послезавтра');

      $rec = SQLSelectOne("SELECT ID FROM classes WHERE TITLE LIKE '" . DBSafe($className) . "'");
      
      if (!$rec['ID'])
      {
         $rec = array();
         $rec['TITLE'] = $className;
         $rec['DESCRIPTION'] = 'Погода Open Weather Map';
         $rec['ID'] = SQLInsert('classes', $rec);
      }
      
      for ($i = 0; $i < count($objectName); $i++)
      {
         $obj_rec = SQLSelectOne("SELECT ID FROM objects WHERE CLASS_ID='" . $rec['ID'] . "' AND TITLE LIKE '" . DBSafe($objectName[$i]) . "'");
         
         if (!$obj_rec['ID'])
         {
            $obj_rec = array();
            $obj_rec['CLASS_ID'] = $rec['ID'];
            $obj_rec['TITLE'] = $objectName[$i];
            $obj_rec['DESCRIPTION'] = $objDescription[$i];
            $obj_rec['ID'] = SQLInsert('objects', $obj_rec);
         }
      }
      parent::install();
   }
   
   public function uninstall()
   {
	  unsubscribeFromEvent($this->name, 'HOURLY');
      SQLExec("delete from pvalues where property_id in (select id FROM properties where object_id in (select id from objects where class_id = (select id from classes where title = 'openweather')))");
      SQLExec("delete from properties where object_id in (select id from objects where class_id = (select id from classes where title = 'openweather'))");
      SQLExec("delete from objects where class_id = (select id from classes where title = 'openweather')");
      SQLExec("delete from classes where title = 'openweather'");
      parent::uninstall();
   }
}
?>
