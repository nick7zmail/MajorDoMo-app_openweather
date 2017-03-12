<?php

/**
 * OpenWeather Application
 *
 * module for MajorDoMo project
 * @author Lutsenko Denis <palacex@gmail.com>
 * @copyright Lutsenko D.V.
 * @version 0.1 December 2014
 */
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
   
   /**
    * saveParams
    *
    * Saving module parameters
    * @access public
    * @param mixed $data Data (default 0)
    * @return void
    */
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
   
   /**
    * getParams
    * Getting module parameters from query string
    * @access public
    * @return void
    */
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
   
   /**
    * Run
    *
    * Description
    * @access public
    *
    * @return void
    */
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
      
      if($this->view_mode == '')
      {
         $ow_city_id = gg('ow_city.id');
         $ow_city_name = gg('ow_city.name');
         
         if($ow_city_id != '' && $ow_city_name != '')
         {
            $out["ow_city"] = $ow_city_name;
            $out["ow_data_update"] = gg('ow_city.data_update');
            //$this->forecast = 0;
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
         $this->get_cityId($out);
      }
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
         $out["ow_city"]     = $ow_city_name;
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
         $out["FACT"]["windDirection"] = app_openweather::getWindDirection($windDirection) . "(" . $windDirection . "&deg;)";
         $out["FACT"]["windSpeed"]     = gg('ow_fact.wind_speed');
         $out["FACT"]["humidity"]      = gg('ow_fact.humidity');
         $out["FACT"]["clouds"]        = gg('ow_fact.clouds');
         $out["FACT"]["weatherType"]   = gg('ow_fact.weather_type');
         $out["FACT"]["pressure"]      = gg('ow_fact.pressure');
         $out["FACT"]["pressure_mmhg"] = app_openweather::ConvertPressure(gg('ow_fact.pressure'),"hpa", "mmhg");
         $out["FACT"]["data_update"]   = gg('ow_city.data_update');
         
         $out["FACT"]["sunrise"]       = date("H:i:s", gg('ow_fact.sunrise'));
         $out["FACT"]["sunset"]        = date("H:i:s", gg('ow_fact.sunset'));
         $out["FACT"]["day_length"]    = gmdate("H:i", gg('ow_fact.day_length'));
      }

      $forecast = $forecast-1;
      
      if ($forecast > 0)
      {
         $forecastOnLabel = constant('LANG_OW_FORECAST_ON');
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
            
            $dayTemp = gg('ow_day'.$i.'.temp_day');
            $eveTemp = gg('ow_day'.$i.'.temp_eve');

            $out["FORECAST"][$i]["temperature"] = $temp;
            $out["FORECAST"][$i]["temp_morn"]   = gg('ow_day'.$i.'.temp_morn');
            $out["FORECAST"][$i]["temp_day"]    = $dayTemp;
            $out["FORECAST"][$i]["temp_eve"]    = $eveTemp;
            $out["FORECAST"][$i]["temp_night"]  = gg('ow_day'.$i.'.temp_night');
            $out["FORECAST"][$i]["temp_min"]    = gg('ow_day'.$i.'.temp_min');
            $out["FORECAST"][$i]["temp_max"]    = gg('ow_day'.$i.'.temp_max');
            
            $out["FORECAST"][$i]["weatherIcon"]   = app_openweather::getWeatherIcon(gg('ow_day' . $i . '.image'));
            $windDirection                        = gg('ow_day' . $i . '.wind_direction');
            $out["FORECAST"][$i]["windDirection"] = app_openweather::getWindDirection($windDirection) . "(" . $windDirection . "&deg;)";
            $out["FORECAST"][$i]["windSpeed"]     = gg('ow_day'.$i.'.wind_speed');
            $out["FORECAST"][$i]["humidity"]      = gg('ow_day'.$i.'.humidity');
            $out["FORECAST"][$i]["weatherType"]   = gg('ow_day'.$i.'.weather_type');
            $out["FORECAST"][$i]["pressure"]      = gg('ow_day'.$i.'.pressure');
            $out["FORECAST"][$i]["pressure_mmhg"] = gg('ow_day'.$i.'.pressure_mmhg');
            $out["FORECAST"][$i]["clouds"]        = gg('ow_day'.$i.'.clouds');
            $out["FORECAST"][$i]["rain"]          = gg('ow_day'.$i.'.rain');
            $out["FORECAST"][$i]["snow"]          = gg('ow_day'.$i.'.snow');
            $out["FORECAST"][$i]["freeze"]        = self::GetFreezePossibility($dayTemp, $eveTemp);
            
            
            $out["FORECAST"][$i]["sunrise"]    = date("H:i:s", gg('ow_day'.$i.'.sunrise'));
            $out["FORECAST"][$i]["sunset"]     = date("H:i:s", gg('ow_day'.$i.'.sunset'));
            $out["FORECAST"][$i]["day_length"] = gmdate("H:i", gg('ow_day'.$i.'.day_length'));
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
      $weather    = app_openweather::GetJsonWeatherDataByCityID($cityID);
      $curWeather = self::GetCurrentWeatherDataByCityID($cityID);

      if ($weather->cod == "404" || $curWeather->cod == "404")
      {
         DebMes($weather->message);
         return;
      }
      
      $fact = $curWeather->main;
      
      $date = date("d.m.Y G:i:s T Y", $curWeather->dt);
     
      sg('ow_fact.temperature', $fact->temp);
      sg('ow_fact.weather_type', $curWeather->weather[0]->description);
      sg('ow_fact.wind_direction', $curWeather->wind->deg);
      sg('ow_fact.wind_speed',$curWeather->wind->speed);
      sg('ow_fact.humidity', $fact->humidity);
      sg('ow_fact.pressure', $fact->pressure);
      sg('ow_fact.pressure_mmhg', app_openweather::ConvertPressure($fact->pressure, "hpa", "mmhg", 2));
      sg('ow_fact.image', $curWeather->weather[0]->icon);
      sg('ow_fact.clouds', $curWeather->clouds->all);
      sg('ow_fact.rain', isset($fact->rain) ? $fact->rain : '');
      sg('ow_city.data_update', $date);
      
      $sunInfo = $this->GetSunInfoByCityID($cityID);
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
      
      $i = 0;
      foreach($weather->list as $day)
      {
         $date = date("d.m.Y", $day->dt);
         sg('ow_day'.$i.'.date', $date);
         
         sg('ow_day'.$i.'.temperature', app_openweather::GetCurrTemp($day->temp));
         sg('ow_day'.$i.'.temp_morn', $day->temp->morn);
         sg('ow_day'.$i.'.temp_day', $day->temp->day);
         sg('ow_day'.$i.'.eve', $day->temp->eve);
         sg('ow_day'.$i.'.temp_night', $day->temp->night);
         sg('ow_day'.$i.'.temp_min', $day->temp->min);
         sg('ow_day'.$i.'.temp_max', $day->temp->max);
         
         sg('ow_day'.$i.'.weather_type', $day->weather[0]->description);
         sg('ow_day'.$i.'.wind_direction', $day->deg);
         sg('ow_day'.$i.'.wind_speed', $day->speed);
         sg('ow_day'.$i.'.humidity', $day->humidity);
         sg('ow_day'.$i.'.pressure', $day->pressure);
         sg('ow_day'.$i.'.pressure_mmhg', app_openweather::ConvertPressure($day->pressure, "hpa", "mmhg", 2));
         sg('ow_day'.$i.'.image', $day->weather[0]->icon);
         sg('ow_day'.$i.'.clouds', $day->clouds);
         sg('ow_day'.$i.'.rain', isset($day->rain) ? $day->rain : 0);
         sg('ow_day'.$i.'.snow', isset($day->snow) ? $day->snow : 0);
         
         $curTimeStamp = strtotime('+' . $i . ' day', time());
         $sunInfo = $this->GetSunInfoByCityID($cityID, $curTimeStamp);
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
         $filePath = ROOT.'cached' . DIRECTORY_SEPARATOR . 'openweather' . DIRECTORY_SEPARATOR . 'image';
         
         if (!is_dir($filePath))
         {
            @mkdir(ROOT . 'cached', 0777);
            @mkdir(ROOT . 'cached' . DIRECTORY_SEPARATOR . 'openweather', 0777);
            @mkdir($filePath, 0777);
         }
         
         if (!file_exists($filePath . DIRECTORY_SEPARATOR . $fileName))
         {
            $contents = file_get_contents($urlIcon);
            if ($contents)
            {
               SaveFile($filePath . DIRECTORY_SEPARATOR . $fileName, $contents);
            }
         }
         
         $urlIcon = ROOTHTML . "cached/openweather/image/" . $fileName;
      }
      return $urlIcon;
   }
   
   /**
    * Get wind direction name by direction in degree 
    * @param mixed $degree Wind degree
    * @return string
    */
   private static function getWindDirection($degree)
   {
      $windDirection = ['<#LANG_N#>', '<#LANG_NNE#>', '<#LANG_NE#>', '<#LANG_ENE#>', '<#LANG_E#>', '<#LANG_ESE#>', '<#LANG_SE#>', '<#LANG_SSE#>', '<#LANG_S#>', '<#LANG_SSW#>', '<#LANG_SW#>', '<#LANG_WSW#>', '<#LANG_W#>', '<#LANG_WNW#>', '<#LANG_NW#>', '<#LANG_NNW#>', '<#LANG_N#>'];
      $direction = $windDirection[round($degree / 22.5)];
      
      return $direction;
   }

   public function save_setting()
   {
      global $ow_forecast_interval;
      global $ow_imagecache;
      global $ow_update_interval;
      global $ow_script;
      global $ow_api_key;
    
      if(!isset($ow_imagecache)) $ow_imagecache = 'off';
      if(isset($ow_script)) sg('ow_setting.updScript', $ow_script);
      if(isset($ow_api_key)) sg('ow_setting.api_key', $ow_api_key);

      sg('ow_setting.ow_imagecache', $ow_imagecache);
      sg('ow_setting.updatetime',$ow_update_interval);
      sg('ow_setting.forecast_interval', $ow_forecast_interval);
      sg('ow_setting.countTime', 1);
      
      $class = SQLSelectOne("SELECT ID FROM classes WHERE TITLE = 'openweather'");
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
            $obj_rec['DESCRIPTION'] = "Forecast on " . $i+1 . " day(s)";
            $obj_rec['ID'] = SQLInsert('objects', $obj_rec);
         }
      }
   }

   public function get_setting(&$out)
   {
      $out["ow_city"] = gg('ow_city.name');
      $out["ow_imagecache"] = gg('ow_setting.ow_imagecache');
      $out["updatetime"] = gg('ow_setting.updatetime');
      $out["script"] = gg('ow_setting.countTime');
      $out["forecast_interval"] = gg('ow_setting.forecast_interval');
      $out["ow_api_key"] = gg('ow_setting.api_key');
   }

   public function save_cityId()
   {
      global $ow_city_id;
      global $ow_city_name;
     
      if((isset($ow_city_id) && $ow_city_id != 0) && isset($ow_city_name))
      {
         sg('ow_city.id', $ow_city_id);
         sg('ow_city.name', $ow_city_name);
      }
   }

   public function get_cityId(&$out)
   {
      global $country;
      if (!isset($country)) $country = '';
      $data = SQLSelect("select c.COUNTRY_CODE, c.COUNTRY_NAME, cc.CITY_ID, cc.CITY_NAME from OPENWEATHER_CITY cc, COUNTRY c where cc.COUNTRY_ID = c.COUNTRY_ID ORDER BY c.COUNTRY_NAME, cc.CITY_NAME");
      
      $out["country"] = '<option value="0">--' . constant('LANG_OW_CHOOSE_COUNTRY') . '--</option>';
      $out["ow_city"] = '<option value="0">--'. constant('LANG_OW_CHOOSE_CITY') . '--</option>';
      $cc = "";
      $cc1 = "";
      
      foreach ($data as $row)
      {
         if ($row["COUNTRY_CODE"] == $country)
         {
            if ($cc !== $row["COUNTRY_CODE"])
            {
               $out["country"] .= '<option selected value = "' . $row["COUNTRY_CODE"] . '">' . $row["COUNTRY_NAME"] . '</option>';
            }
            $cc = $row["COUNTRY_CODE"];
            
            $out["ow_city"] .= '<option value = "' . $row["CITY_ID"] . '">' . $row["CITY_NAME"] . '</option>';
         }
         else
         {
            if ($cc1 !== $row["COUNTRY_CODE"])
            {
               $out["country"] .= '<option value = "' . $row["COUNTRY_CODE"] . '">' . $row["COUNTRY_NAME"] . '</option>';
            }
            $cc1 = $row["COUNTRY_CODE"];
         }
      }
   }
   
   /**
    * Get Weather data from openweathermap.org by city id
    * @param mixed $cityID City ID
    * @param mixed $unit   Unit(metric/imperial)
    * @return mixed
    */
   protected static function GetJsonWeatherDataByCityID($cityID, $unit = "metric")
   {
      if (!isset($cityID)) return null;

      $apiKey = gg('ow_setting.api_key');
      
      $unit = app_openweather::GetUnits($unit);
      $query  = "http://api.openweathermap.org/data/2.5/forecast/daily?id=" . $cityID . "&mode=json&units=" . $unit . "&cnt=16&lang=ru" . "&appid=" . $apiKey;
      
      $data =  getURL($query);
      
      $data   = json_decode($data);
      return $data;
   }

   /**
    * Get current weather data
    * @param mixed $cityID City ID
    * @param mixed $unit   Weather Unit (metric/imperial)
    * @return mixed
    */
   private static function GetCurrentWeatherDataByCityID($cityID, $unit = "metric")
   {
      if (!isset($cityID))
         return null;
      
      $apiKey = gg('ow_setting.api_key');
      
      $unit = app_openweather::GetUnits($unit);
      $query  = "http://api.openweathermap.org/data/2.5/weather?id=" . $cityID . "&mode=json&units=" . $unit . "&lang=ru" . "&appid=" . $apiKey;
      
      $data =  getURL($query);
      
      $data   = json_decode($data);
      return $data;
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
   public static function ConvertPressure($pressure, $from, $to, $precision = 2)
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
    * Check units for weather. If unit unknown or incorrect then units = metric
    * @param $vUnits
    * @return
    */
   private static function GetUnits($unit)
   {
      $units = "metric";
      
      if (!isset($unit)) return $units;
      
      if ($unit === "imperial")
         return $unit;
      
      return $units;
   }
   
   public function LoadCity()
   {
      $data = getURL('http://openweathermap.org/help/city_list.txt');
      
      if (count($data) <= 0) return;
      
      $lmDate = new DateTime;
      $dataArray = explode("\n", $data);
      
      SQLExec("truncate table OPENWEATHER_CITY");
      
      foreach($dataArray as $row) 
      {
         $city = explode("\t", $row);
         
         if ($city[0] == "id" || ($city[0] == "")) continue;
         
         $country = SQLSelectOne("SELECT COUNTRY_ID FROM COUNTRY WHERE COUNTRY_CODE = '" . $city[4] . "'");
      
         if (!isset($country['COUNTRY_ID'])) continue;
      
         $arr["CITY_ID"] = $city[0];
         $arr["COUNTRY_ID"] = $country['COUNTRY_ID'];
         $arr["CITY_NAME"] = $city[1];
         $arr["CITY_LAT"] = $city[2];
         $arr["CITY_LNG"] = $city[3];
         $arr["LM_DATE"] = $lmDate;
   
         SQLInsert("OPENWEATHER_CITY", $arr); // adding new record
      }
   }
   
   protected function LoadCountry()
   {
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (1, '25459617-F4D3-EB0A-6777-2D7CB5F876B9', 'Andorra', NOW(), 'AD', '376', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (2, '24E45AEB-7FD7-783A-2008-F5A35CA064F7', 'United Arab Emirates', NOW(), 'AE', '971', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (3, '3F039DE6-2908-4731-96FE-041E04A7E962', 'Afghanistan', NOW(), 'AF', '93', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (4, 'F1FEB2CF-618A-59D0-0DDC-B4F8B4ED976E', 'Antigua and Barbuda', NOW(), 'AG', '1268', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (5, '909E8639-771A-D2CD-4405-7A8368A9D04A', 'Anguilla', NOW(), 'AI', '1264', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (6, '35173F09-0E98-0C3E-77A6-2AD8F8F5DE89', 'Albania', NOW(), 'AL', '355', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (7, '632C5757-664D-108D-802F-3F62E78F41B9', 'Armenia', NOW(), 'AM', '374', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (8, '33C045E6-E7F0-6886-FE2D-46C5EAA79042', 'Angola', NOW(), 'AO', '244', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (9, '8E7E291E-B21C-ABAC-7791-4733C412D3CA', 'Argentina', NOW(), 'AR', '54', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (10, 'F4EC01D6-D793-EE6C-58EA-D66DABE0E15F', 'American Samoa', NOW(), 'AS', '1684', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (11, 'DA24D62E-C926-A91D-E11A-96AAD3E5DDE1', 'Austria', NOW(), 'AT', '43', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (12, 'F193E97D-B13C-9506-D976-CCB52BC864BF', 'Australia', NOW(), 'AU', '61', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (13, '363B89ED-E346-3EEE-67E1-77A9ACA104BE', 'Aruba', NOW(), 'AW', '297', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (14, '0129859C-6BC4-C7C6-F0E7-212E8BFFE04F', 'Åland Islands', NOW(), 'AX', '358', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (15, '96C23A30-8A7D-D545-6E33-1EF6070EAA2A', 'Azerbaijan', NOW(), 'AZ', '994', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (16, '1C37B968-7D44-2AED-7171-116232E0376F', 'Bosnia and Herzegovina', NOW(), 'BA', '387', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (17, '784A4C7F-8B44-353A-8B27-996E1B38791C', 'Barbados', NOW(), 'BB', '1246', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (18, '735D20D8-45D8-B861-6B2B-9F1B22F86447', 'Bangladesh', NOW(), 'BD', '880', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (19, '3B71F5B5-2AC2-9154-404D-191ABFBC4729', 'Belgium', 'BE', NOW(), '32', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (20, 'B5CB258B-4F64-D109-E9D5-60A78ED0A637', 'Burkina Faso', NOW(), 'BF', '226', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (21, '1B6E6DAD-F17E-C6C0-61A4-41B20394BDBE', 'Bulgaria', NOW(), 'BG', '359', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (22, '6AB06D58-6177-06A7-0937-AA363E22FE30', 'Bahrain', NOW(), 'BH', '973', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (23, 'D1581E51-B2B3-8078-F5EC-8463B5484393', 'Burundi', NOW(), 'BI', '257', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (24, '28966864-22F6-35BD-5145-1B79E5FE95C0', 'Benin', NOW(), 'BJ', '229', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (25, 'A0910149-4E0D-D1EF-9280-90C7CA4F713A', 'Saint Barthelemy', NOW(), 'BL', '590', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (26, 'D10E4FAB-B35B-36E2-1B11-BD87D19D493E', 'Bermuda', NOW(), 'BM', '1441', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (27, '2EF66919-5A42-5E57-9540-254370BCFA7F', 'Brunei', NOW(), 'BN', '673', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (28, 'B17DC404-815D-16D1-3EE6-01CFF6108DA6', 'Bolivia', NOW(), 'BO', '597', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (29, '1677F424-62EB-CDCA-59AE-82E6BE4CD643', 'Bonaire, Sint Eustatius and Saba', NOW(), 'BQ', '599', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (30, '364A13CD-9DD4-EB69-B481-CAF5C67AD0F3', 'Brazil', NOW(), 'BR', '55', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (31, '3287034B-9EF0-21E6-290F-4C1435488B14', 'Bahamas', NOW(), 'BS', '1242', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (32, '3ECFA6CC-B473-444F-60B2-F2C325726A59', 'Bhutan', NOW(), 'BT', '975', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (33, '5CB7E864-AA89-D6A6-6483-5A0B4BF4ECE7', 'Botswana', NOW(), 'BW', '267', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (34, '549B645E-602C-E8DC-E661-0BC58B872507', 'Belarus', NOW(), 'BY', '375', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (35, '6EBC4137-4D87-7153-6380-1917E5AB5CC8', 'Belize', NOW(), 'BZ', '501', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (36, '62B9E5BF-CD36-7BF9-8C86-7145E0BB87C3', 'Canada', NOW(), 'CA', '1', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (37, 'CE623909-C6BB-FBDB-AAC4-E93B1905A157', 'Cocos (Keeling) Islands', NOW(), 'CC', '61', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (38, 'EE5B1165-2914-906F-FC6F-CAAE4D346925', 'Democratic Republic of the Congo', NOW(), 'CD', '243', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (39, '2DAEE215-8156-00B0-9BCF-2A11D900E923', 'Central African Republic', NOW(), 'CF', '236', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (40, 'EDADCB74-E7C3-1081-29BA-4F59B60FF6B2', 'Republic of the Congo', NOW(), 'CG', '242', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (41, '5024DE57-E5BB-5A9D-917D-26A1749B8D60', 'Switzerland', NOW(), 'CH', '41', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (42, 'ABA5802C-8FE2-211F-8002-BB96E7A364DB', 'Ivory Coast', NOW(), 'CI', '225', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (43, '00C8EB2B-8085-1D54-BE4D-35EF87A255E6', 'Cook Islands', NOW(), 'CK', '682', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (44, '834BD2B5-93D2-17A7-91BE-CE4DEEC43366', 'Chile', NOW(),  'CL', '56', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (45, '28A21890-3B5D-8B00-9EBE-DED06FA1ECFB', 'Cameroon', NOW(), 'CM', '237', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (46, 'CE0E5440-B3C0-12A5-1BA3-2387A270C634', 'China', NOW(),'CN', '86', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (47, '622727AA-9C09-1CBB-9C8F-5F23E4F6E7AF', 'Colombia', NOW(), 'CO', '57', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (48, 'D3860772-0731-CCE8-3F3C-8DFC229DA443', 'Costa Rica', NOW(), 'CR', '506', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (49, 'A394D714-E73A-8327-EB0C-C1A03AD5B75C', 'Cuba', NOW(), 'CU', '53', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (50, '38DE8E73-B254-F18B-098D-FA063DB064DE', 'Cape Verde', NOW(), 'CV', '238', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (51, 'E05C1047-00F5-9447-4AA4-F4D231A60CDF', 'Christmas Island', NOW(), 'CX', '61', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (52, 'E29DE5EF-A293-9921-DB69-1AE56C94789C', 'Curaçao', NOW(), 'CW', '', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (53, '91F38946-68B4-F218-A93C-87F8018AD49A', 'Cyprus', NOW(), 'CY', '357', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (54, '673F53C2-5C02-9B22-E956-29879756BF15', 'Czech Republic', NOW(), 'CZ', '420', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (55, '60C952C3-0504-DC75-9E1D-499EC374B0F0', 'Germany', NOW(), 'DE', '49', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (56, '1A3A54C9-F2E1-3E5F-079C-999D110E8EEC', 'Djibouti', NOW(), 'DJ', '253', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (57, '7547B15D-E21A-4A2E-5F9B-1DC727C53F4A', 'Denmark', NOW(), 'DK', '45', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (58, '7E2CF84D-1879-374A-6A41-F03318A3FB25', 'Dominica', NOW(), 'DM', '1767', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (59, 'A07E10D2-DB62-436D-E203-A884ED728CBC', 'Dominican Republic', NOW(), 'DO', '1809', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (60, '6CFEBD8D-4ADA-B4FF-9160-90D0C2B2118E', 'Algeria', NOW(), 'DZ', '213', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (61,'BC4A1696-47D1-2617-ECF6-824BADEF4C4E','Ecuador', NOW(),'EC','593', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (62,'AFF8DFA6-F5EE-6A43-DC59-8213D20E198A','Estonia', NOW(),'EE','372', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (63,'ED2AC84C-40C7-F14D-378E-16EB10094294','Egypt', NOW(),'EG','20', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (64,'25C7A12B-226A-8989-F33B-98FEAB37B18F','Western Sahara', NOW(),'EH','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (65,'0C0F61A7-CED3-882E-440F-0B8B6ED939DB','Eritrea', NOW(),'ER','291', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (66,'81387FC3-0AD1-42E7-241E-2AA939641B95','Spain', NOW(),'ES','34', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (67,'7D8BD697-9E59-4861-4BE9-61A8AD8B4936','Ethiopia', NOW(),'ET','251', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (68,'561A54FF-8F65-8B92-483E-2DBE528429A4','Finland', NOW(),'FI','358', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (69,'4D930C83-DEFD-ADEA-0D64-501B4E4911CF','Fiji', NOW(),'FJ','679', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (70,'3EF989F2-6700-B8DA-F2B5-B4A831B30BCD','Falkland Islands', NOW(),'FK','500', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (71,'6E21E535-BF55-E53C-3CAC-7C2B621EF05B','Micronesia', NOW(),'FM','691', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (72,'25F1AC03-B255-50A9-654C-E043D61E7DA1','Faroe Islands', NOW(),'FO','298', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (73,'06A3DB9C-B358-74A7-BBA2-99F760FDE24E','France', NOW(),'FR','33', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (74,'7D2D339B-5568-2B6C-28F6-1CAD95AE685E','Gabon', NOW(),'GA','241', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (75,'73D1D5BB-571F-08E7-2A4F-4AB63AA9793C','United Kingdom', NOW(),'GB','44', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (76,'52764C53-FF3F-BA7C-14BF-7E6262C26BB6','Grenada', NOW(),'GD','1473', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (77,'ABAFEB9B-9DB0-5261-4625-53C6A32A8392','Georgia', NOW(),'GE','995', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (78,'93A6ABF8-C217-B219-0AEF-F63F51330113','French Guiana', NOW(),'GF','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (79,'DD5B81B6-DF46-6B93-40DD-32B63B1C4482','Guernsey', NOW(),'GG','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (80,'AE89DC78-DD22-0165-1F4A-37757DC2C6A6','Ghana', NOW(),'GH','233', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (81,'691A673C-A72B-45C6-EBA5-AE4D48128B0C','Gibraltar', NOW(),'GI','350', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (82,'E1C9DDCE-DFC6-C80E-9E6D-2CFACD5A3EF3','Greenland', NOW(),'GL','299', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (83,'DC6434F3-07BD-D0AF-5EF1-021830DD761A','Gambia', NOW(),'GM','220', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (84,'0EB68AA9-0863-564B-E8B9-7BE51B19C627','Guinea', NOW(),'GN','224', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (85,'64BAD731-A2B6-D774-4382-6A3F69041EB2','Guadeloupe ', NOW(),'GP','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (86,'72FFFB07-88AA-6CA5-C9A1-664AAE69E39F','Equatorial Guinea', NOW(),'GQ','240', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (87,'EBC8E95D-EFA7-A62F-E9D0-1230876D82AD','Greece', NOW(),'GR','30', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (88,'589A3D09-2DAA-54DD-B119-8A8D0CD9E9B1','South Georgia and the South Sandwich Islands', NOW(),'GS','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (89,'6119B99F-E4B7-0B13-45F3-293FD0A84862','Guatemala', NOW(),'GT','502', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (90,'6BC5F673-1D11-85D4-FF9E-3C43604EC2D0','Guam', NOW(),'GU','1671', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (91,'D94C617D-E792-CAAF-2E00-5A8EEA208C8C','Guinea-Bissau', NOW(),'GW','245', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (92,'9313337B-D700-D83C-CA29-5F48D8726308','Guyana', NOW(),'GY','592', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (93,'CEFBA783-88BA-FFDE-77F3-43B3FF801484','Hong Kong', NOW(),'HK','852', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (94,'CBA2E631-D4D1-B735-81D3-5DAC86AE8C76','Honduras', NOW(),'HN','504', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (95,'FEA02A43-5185-C6A7-A26C-935578D43E6D','Croatia', NOW(),'HR','385', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (96,'02927AD4-8894-FE99-C9DD-E0E7F4DC63B1','Haiti', NOW(),'HT','509', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (97,'42405C6A-097F-BAF2-93A6-86513986110A','Hungary', NOW(),'HU','36', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (98,'79798E7A-4327-469A-5EC1-81319DDC1E90','Indonesia', NOW(),'ID','62', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (99,'2A8985F3-8891-5685-A0FF-972A5DF8620B','Ireland', NOW(),'IE','353', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (100,'EEBDEB95-6B76-B2B0-F2C9-2BFB1499E9FD','Israel', NOW(),'IL','972', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (101,'71739793-0365-6479-F9A2-9B6F57239816','Isle of Man', NOW(),'IM','44', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (102,'18AC27F9-4025-C8CF-DA36-36004B0C5CF3','India', NOW(),'IN','91', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (103,'01BB783C-2123-8D92-36E3-397F1E9D5F7D','Iraq', NOW(),'IQ','964', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (104,'422B82CA-FC9F-6FFF-151E-E342CBD26A32','Iran', NOW(),'IR','98', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (105,'CD0DAF28-6A3C-0CE3-1A06-218A7AA11408','Iceland', NOW(),'IS','354', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (106,'8BA4B4DC-9E82-331B-7EE2-6577413FCFDE','Italy', NOW(),'IT','39', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (107,'D8777867-1C0B-ACF1-337A-6DD4B68B902B','Jersey', NOW(),'JE','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (108,'0DEFE08A-DFF2-7222-F6BC-C10BB2C2B3E3','Jamaica', NOW(),'JM','1876', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (109,'8864615C-5A8F-56C1-262C-97CB8739E154','Jordan', NOW(),'JO','962', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (110,'A8DFE7EB-BA50-DF73-C88A-EB7A83636C98','Japan', NOW(),'JP','81', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (111,'D6F5CF13-36E4-E7D7-A6AE-32D622E2E905','Kenya', NOW(),'KE','254', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (112,'6C1BE74C-56D3-ADFE-B75E-4DD174378C3B','Kyrgyzstan', NOW(),'KG','996', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (113,'88F59F28-C9DC-F8B5-0AA3-6EE2F5BA2680','Cambodia', NOW(),'KH','855', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (114,'221ACA06-D996-AA61-8EF8-A883B5EF088D','Kiribati', NOW(),'KI','686', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (115,'50711EC0-9C17-A53D-C8D5-7BFCB17B9F74','Comoros', NOW(),'KM','269', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (116,'5A63C8A4-AED9-E465-C42D-6B35D815788E','Saint Kitts and Nevis', NOW(),'KN','1869', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (117,'E2231181-F5A7-9C0D-A07D-DF498953BDB7','North Korea', NOW(),'KP','850', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (118,'51E443A4-BBB7-45B3-BCCB-5D900AAC9A10','South Korea', NOW(),'KR','82', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (119,'163246AC-AF1E-7EF1-ABD7-CA7C76048C0D','Kuwait', NOW(),'KW','965', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (120,'627EF7AE-33FA-CBB0-5165-B69DE5D88639','Cayman Islands', NOW(),'KY','1345', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (121,'4AF2B679-80E6-AE7A-6F8C-E30BB82170CB','Kazakhstan', NOW(),'KZ','7', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (122,'5B49382E-7792-4FB2-5841-BDF46D215FB1','Laos', NOW(),'LA','856', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (123,'4F9A2D48-4692-A93C-6A6C-A450F6B15B52','Lebanon', NOW(),'LB','961', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (124,'A969C15C-FDB8-B9BF-7A70-CA0B20F6E746','Saint Lucia', NOW(),'LC','1758', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (125,'D3A607AA-9E18-3D77-0504-8FB3762B08E9','Liechtenstein', NOW(),'LI','423', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (126,'08186EF5-A816-5F3F-6334-2ACAFFC1036F','Sri Lanka', NOW(),'LK','94', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (127,'FEB0132D-0FA3-0A05-3088-59E88DFB6FCC','Liberia', NOW(),'LR','231', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (128,'D82F3778-BA0B-F2D7-87F1-F455816C0B10','Lesotho', NOW(),'LS','266', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (129,'ACC7107B-FEE4-C49E-AB16-5BBADE9707BB','Lithuania', NOW(),'LT','370', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (130,'D46FBEE9-CA64-2513-460C-6DE7B82E70AD','Luxembourg', NOW(),'LU','352', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (131,'EF9EF912-DBDC-2A60-3ABA-8F6413AB376C','Latvia', NOW(),'LV','371', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (132,'331657A7-D80A-E3D9-306B-7B04580BE97A','Libya', NOW(),'LY','218', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (133,'6F6551A5-A0D2-9614-2570-DB098D41795B','Morocco', NOW(),'MA','212', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (134,'D2C3C719-CFEF-92F9-1C75-4674404E2129','Monaco', NOW(),'MC','377', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (135,'AF24CA6E-08CF-9103-A408-198419511606','Moldova', NOW(),'MD','373', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (136,'0B9CDE3D-E309-F36C-6C92-7A5A3CDB5C78','Montenegro', NOW(),'ME','382', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (137,'7E7C60D7-8187-45FF-5856-FD4FE0A9F7F2','Saint Martin', NOW(),'MF','1599', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (138,'D0D90087-2CB8-BBF9-A0A7-7B9F83F0842A','Madagascar', NOW(),'MG','261', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (139,'2148A143-2703-2688-50AC-4D449CC478C6','Marshall Islands', NOW(),'MH','692', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (140,'4EDCA486-8363-D905-2223-0DD536C64FB1','Macedonia', NOW(),'MK','389', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (141,'D10BC835-779F-63B5-CD7F-77715C93EF5B','Mali', NOW(),'ML','223', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (142,'C6DE57ED-3F9E-5ECE-BED8-750D635B32D6','Burma (Myanmar)', NOW(),'MM','95', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (143,'79B936B1-2C70-1B2B-CF22-5DF0EDA7BFF6','Mongolia', NOW(),'MN','976', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (144,'340E0027-3A88-B6EF-1389-9830FF0111A1','Macau', NOW(),'MO','853', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (145,'79F9CD19-DD9D-215E-C834-6896D595D1DB','Northern Mariana Islands', NOW(),'MP','1670', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (146,'82B9D34C-D138-D44F-C78F-66E27B0B9DB5','Martinique', NOW(),'MQ','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (147,'DE5DF1CD-0634-464F-0417-6E78249A9EFF','Mauritania', NOW(),'MR','222', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (148,'6185D85F-EFD1-F321-5C88-FD92070CC6F9','Montserrat', NOW(),'MS','1664', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (149,'E06F0201-CA6B-EF55-EE95-7422AE9455EA','Malta', NOW(),'MT','356', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (150,'FB9AE8C0-1A45-2E86-6787-73467681C533','Mauritius', NOW(),'MU','230', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (151,'CCCFD4DB-F847-EFB5-A954-95C3E84C8810','Maldives', NOW(),'MV','960', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (152,'BAD2B0BE-60A9-CBF1-8650-F709F3F71937','Malawi', NOW(),'MW','265', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (153,'8FB4F911-221D-C172-CD4A-2E3DDFC91E31','Mexico', NOW(),'MX','52', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (154,'21DE1250-CA05-4ABE-5402-5AF7CEDEF190','Malaysia', NOW(),'MY','60', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (155,'05CBFC51-7A5E-C473-BD2D-64E99EEF31AE','Mozambique', NOW(),'MZ','258', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (156,'0CAA4FA5-DC6D-601A-001C-6D44E0DC5317','Namibia', NOW(),'NA','264', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (157,'66330F5B-F3BC-C59D-E138-81AD487DCF5E','New Caledonia', NOW(),'NC','687', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (158,'D691B3AE-3239-514F-3A7C-82167837EA79','Niger', NOW(),'NE','227', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (159,'0F9225AD-F626-4D0B-6201-E299B8BD215A','Norfolk Island', NOW(),'NF','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (160,'146B312A-59D0-71F1-FE10-6DF2E9FF3B13','Nigeria', NOW(),'NG','234', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (161,'D1B612B1-9C6B-C0FA-D185-9EF37FDD5B6B','Nicaragua', NOW(),'NI','505', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (162,'DBAE55F1-92C1-618E-32DD-FCFBCA5287F2','Netherlands', NOW(),'NL','31', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (163,'AA1566AC-5FBB-4165-5297-E3D1F56D23A2','Norway', NOW(),'NO','47', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (164,'4BF12324-809E-BB44-C6B9-B7FCA9DC0160','Nepal', NOW(),'NP','977', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (165,'BE8F1645-08F9-9FBD-5885-1C0C6BC93848','Niue', NOW(),'NU','683', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (166,'8761839C-0257-21D2-C497-382E5888F299','New Zealand', NOW(),'NZ','64', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (167,'8AAA2CE8-5DDF-EE89-F73E-4757ED7AC246','Oman', NOW(),'OM','968', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (168,'CFFD5046-148C-6ED1-B68E-7A0FD7C591B7','Panama', NOW(),'PA','507', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (169,'CBF62F2B-70CD-EA41-2256-E45675C78C03','Peru', NOW(),'PE','51', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (170,'6503A050-3EA4-38FA-FCE8-992DCE1B590B','French Polynesia', NOW(),'PF','689', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (171,'BA0C9E60-2EFE-AD8E-C0E7-29FCA7267DF5','Papua New Guinea', NOW(),'PG','675', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (172,'88E336DC-5D75-752D-C77F-93A42D5B7C03','Philippines', NOW(),'PH','63', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (173,'55FE4DF8-9BB2-126B-1F00-4334F8F7C640','Pakistan', NOW(),'PK','92', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (174,'91DD425D-10F6-0FAC-73D3-5E3F5EFC86A2','Poland', NOW(),'PL','48', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (175,'24354927-BE69-9B63-7324-7AE896674FF6','Saint Pierre and Miquelon', NOW(),'PM','508', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (176,'A21AA655-6CF5-A7A2-0CF3-374E511B7516','Pitcairn Islands', NOW(),'PN','870', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (177,'7A0DF8B2-C5EC-FC58-9FC4-63FEC95F2DB9','Puerto Rico', NOW(),'PR','1', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (178,'2BEE3A29-B3C8-7FE8-95F5-5DA81ECE0E75','State of Palestine', NOW(),'PS','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (179,'3BB8EFB5-BB65-F239-28B9-9B38E8FE83AE','Portugal', NOW(),'PT','351', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (180,'7DFEFB58-159B-34CE-7B6E-5F5F74C0ED0B','Palau', NOW(),'PW','680', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (181,'4E63C338-52F7-D693-39C8-6D9B09ECD5A8','Paraguay', NOW(),'PY','595', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (182,'20319E02-E2D1-2FB9-B2B9-7CF8B7506DDF','Qatar', NOW(),'QA','974', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (183,'0DF59336-53F1-F7C7-AB18-8DBF084DA125','Réunion', NOW(),'RE','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (184,'E40912F1-CC15-4F2A-D8D1-FDC335CBC8CA','Romania', NOW(),'RO','40', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (185,'38AC3FA4-283A-B36C-DC3D-1B59E7052A25','Serbia', NOW(),'RS','381', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (186,'18A6D74E-6BE8-4CC8-8473-28EF91B436B2','Russia', NOW(),'RU','7', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (187,'5E1EAB16-2E2F-39D3-0B0E-BF709AC39056','Rwanda', NOW(),'RW','250', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (188,'FDAB4E51-45A0-E5DF-1152-5DEAEC73C3AB','Saudi Arabia', NOW(),'SA','966', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (189,'CF21FB5C-9723-94F7-9F2B-F45CCAFC260D','Solomon Islands', NOW(),'SB','677', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (190,'93C95D15-F10E-B87A-81DE-E9AAD84FC53A','Seychelles', NOW(),'SC','248', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (191,'16302452-E1A6-D22C-5C99-133C48B5C980','Sudan', NOW(),'SD','249', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (192,'472AA972-37BF-1FCF-5B13-03EAE9434088','Sweden', NOW(),'SE','46', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (193,'B2251D82-D868-DEAE-C811-4064951956F7','Singapore', NOW(),'SG','65', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (194,'F453E634-1155-9BCD-D903-08BEEC64C0A8','Saint Helena', NOW(),'SH','290', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (195,'B3D4C1FF-8E1D-DEEE-9A1A-39C83008F889','Slovenia', NOW(),'SI','386', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (196,'8BF3EE1A-9967-AEDD-BA78-17C4087B28D9','Svalbard', NOW(),'SJ','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (197,'6FFA1E78-2CB2-B1CF-9D58-E0C9DEDA18AC','Slovakia', NOW(),'SK','421', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (198,'D6A4C632-C93A-839F-B09E-5480C4FAF1AD','Sierra Leone', NOW(),'SL','232', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (199,'3DF96067-1DB6-B7CC-84B9-BAB7101F7131','San Marino', NOW(),'SM','378', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (200,'2F0BC615-1C65-C7AA-47E8-9B5CA054D867','Senegal', NOW(),'SN','221', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (201,'C561F1FE-B072-5973-1DEC-F67D965DE27E','Somalia', NOW(),'SO','252', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (202,'0775258C-EDC1-135D-E35C-01BD85F45F29','Suriname', NOW(),'SR','597', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (203,'AD0BAC3A-1DA4-23DA-0010-431A279304BE','Sao Tome and Principe', NOW(),'SS','239', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (204,'A4BDF3BD-B7F1-1B9B-CD98-A0C78ED53511','El Salvador', NOW(),'ST','503', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (205,'F4CEADD9-25F5-45F2-2E19-5B1795CE9D69','El Salvador', NOW(),'SV','503', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (206,'46AD5507-C8B7-9F6D-571A-9E6E1C044CD6','Sint Maarten', NOW(),'SX','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (207,'3B28BF51-D190-F7CE-1BBA-49F72481AE3B','Syria', NOW(),'SY','963', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (208,'1C48EC65-1073-25CD-5F53-7E4D22282B1B','Swaziland', NOW(),'SZ','268', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (209,'D3C33017-0BAE-B451-1190-B5A9A6D81265','Turks and Caicos Islands', NOW(),'TC','1649', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (210,'436517D3-FF50-05BE-04A4-6314683C29BF','Chad', NOW(),'TD','235', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (211,'373E212C-A17A-3D03-ACB3-588E2ECE1120','French Southern Territories', NOW(),'TF','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (212,'1DD74013-D596-D740-60F5-E007CFE9EE59','Togo', NOW(),'TG','228', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (213,'BF1DDCD3-0F3A-A81E-0508-910B20ED3822','Thailand', NOW(),'TH','66', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (214,'FB3A6E3C-53A9-CD6C-0F7C-302AB483B4BD','Tajikistan', NOW(),'TJ','992', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (215,'C343A6AB-0FAD-E093-7AF3-9589F5E58F64','Timor-Leste', NOW(),'TL','670', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (216,'DD3F11FD-CE04-AE45-D4DF-CB1324DDE122','Turkmenistan', NOW(),'TM','993', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (217,'0AEF23A3-F684-B964-4D93-B507C40B66B4','Tunisia', NOW(),'TN','216', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (218,'A0662AD7-4934-E321-563F-6476311DD70C','Tonga', NOW(),'TO','676', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (219,'5D0ECEE1-03F5-6D19-F5F3-F07F186DDF04','Turkey', NOW(),'TR','90', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (220,'F468D4C0-D571-5462-D0F1-6F0E4064AF46','Trinidad and Tobago', NOW(),'TT','1868', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (221,'BD92ECEC-C1D0-2516-23BF-75D5C2B05988','Tuvalu', NOW(),'TV','688', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (222,'C378CD23-31C4-6D56-EBA9-C38D38F74068','Taiwan', NOW(),'TW','886', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (223,'11BAD1B7-D5B5-6F57-AE04-F483B2DEDAAB','Tanzania', NOW(),'TZ','255', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (224,'4E7C6962-8A60-18F5-6380-6178B1BD4D1A','Ukraine', NOW(),'UA','380', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (225,'2795DDE4-B42F-E010-4444-6F9AD1F42E87','Uganda', NOW(),'UG','256', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (226,'EF77733B-A972-EC78-8799-0BACF0ABF529','United States', NOW(),'US','1', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (227,'B1395F88-00C0-939B-DDFA-9DCBFB467EA4','Uruguay', NOW(),'UY','598', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (228,'81E0BAA8-C899-C67F-9A1C-7F93971F181D','Uzbekistan', NOW(),'UZ','998', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (229,'2ED293A8-00CF-E7F0-CA85-771A46E9F0B1','Holy See (Vatican City)', NOW(),'VA','39', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (230,'B34513B1-18A0-6F0F-9F93-5EBF96B73ADB','Saint Vincent and the Grenadines', NOW(),'VC','1784', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (231,'B4F3DA05-56D3-25AE-F8A9-8506B67C9004','Venezuela', NOW(),'VE','58', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (232,'301B703B-B60C-4652-FB06-19CD778F0C74','British Virgin Islands', NOW(),'VG','1284', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (233,'C4ECE65F-CDFC-F50E-DCD9-3DB7CAB7C95E','US Virgin Islands', NOW(),'VI','1340', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (234,'C7245281-9AEA-4F60-73E4-0582061C77E9','Vietnam', NOW(),'VN','84', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (235,'BD92A44B-875F-79D2-3E12-D5175612D928','Vanuatu', NOW(),'VU','678', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (236,'E325ABAA-4150-836C-061D-6679EFEF3595','Wallis and Futuna', NOW(),'WF','681', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (237,'D8432DE9-535D-E306-0741-835AC0420A5B','Samoa', NOW(),'WS','685', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (238,'B133FA3B-7130-B4AF-373E-DEB720102D9B','Kosovo', NOW(),'XK','', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (239,'3ABF1436-FAE0-3FD9-8948-501028332B17','Yemen', NOW(),'YE','967', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (240,'7FA28C16-AE62-FBC2-7CEF-A5E4B71B826A','Mayotte', NOW(),'YT','262', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (241,'38690943-EB49-39BB-09C7-1F987A3DDB85','South Africa', NOW(),'ZA','27', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (242,'99795989-6422-28FE-D1A6-054605E86075','Zambia', NOW(),'ZM','260', NULL, NULL);");
      SQLExec("insert into COUNTRY(COUNTRY_ID, COUNTRY_GUID, COUNTRY_NAME, LM_DATE, COUNTRY_CODE, COUNTRY_PHONE_CODE, LATITUDE, LONGITUDE) values (243,'A6852D18-8C62-9B94-7F4B-2E65FE64EE88','Zimbabwe', NOW(),'ZW','263', NULL, NULL);");
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
   
   /**
    * Get sun info by coords and timestamp
    * 
    * sunrise                     - Время восхода солнца
    * sunset                      - Время заката
    * transit                     - Время прохождения планеты через меридиан
    * civil_twilight_begin        - Время начала гражданских сумерек
    * civil_twilight_end          - Время конца гражданских сумерек
    * nautical_twilight_begin     - Время начала навигационных сумерек
    * nautical_twilight_end       - Время конца навигационных сумерек
    * astronomical_twilight_begin - Время начала астрономических сумерек
    * astronomical_twilight_end   - Время конца астрономических сумерек
    * 
    * @param mixed $cityLat GeoCoord Latitude
    * @param mixed $cityLong GeoCoord Longitude
    * @param mixed $timeStamp TimeStamp. 
    * @return array|bool
    */
   private function GetSunInfoByGeoCoord($cityLat, $cityLong, $timeStamp = -1)
   {
      if($timeStamp == '' or $timeStamp == -1)
         $timeStamp = time(); 
      
      if(empty($cityLat) || empty($cityLong))
      {
         DebMes("CityCoords not found");
         return FALSE;
      }
      
      $info = date_sun_info($timeStamp, $cityLat, $cityLong);
      
      return $info;
   }
   
   /**
    * Get sun info by cityID on date
    * @param mixed $cityID    CityID
    * @param mixed $timeStamp TimeStamp
    * @return array|bool
    */
   public function GetSunInfoByCityID($cityID, $timeStamp = -1)
   {
      if($timeStamp == '' or $timeStamp == -1)
         $timeStamp = time(); 
      
      $id = intval($cityID);
      $rec = SQLSelectOne("select CITY_NAME, CITY_LAT, CITY_LNG from OPENWEATHER_CITY where CITY_ID = " . $id); 
      
      if (!isset($rec["CITY_LAT"]) || !isset($rec["CITY_LNG"]))
         return FALSE;
      
      $info = $this->GetSunInfoByGeoCoord($rec["CITY_LAT"], $rec["CITY_LNG"], $timeStamp);
      
      return $info;
   }

   /**
    * Get possibility freeze by evening and day temperature
    * @param mixed $tempDay      Temperature at 13:00
    * @param mixed $tempEvening  Termerature at 21:00
    * @return double|int         Freeze possibility %
    */
   public function GetFreezePossibility($tempDay, $tempEvening)
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
   
   /**
    * Install
    * Module installation routine
    * @access private
    */
   public function install($parent_name = '')
   {
	subscribeToEvent($this->name, 'HOURLY');
      $val = SQLSelectOne("select count(*)+2 CNT from information_schema.tables where table_schema = '" . DB_NAME . "' and table_name = 'COUNTRY'");
      $val = $val["CNT"] == 2 ? FALSE : TRUE;
      
      if (!$val)
      {
         $query = "create table COUNTRY";
         $query .= "(";
         $query .= " COUNTRY_ID           INT(10) not null,";
         $query .= " COUNTRY_GUID         VARCHAR(48) not null,";
         $query .= " COUNTRY_NAME         VARCHAR(64) not null,";
         $query .= " LM_DATE              DATETIME not null,";
         $query .= " COUNTRY_CODE         VARCHAR(8),";
         $query .= " COUNTRY_PHONE_CODE   VARCHAR(8),";
         $query .= " LATITUDE             FLOAT(18,5),";
         $query .= " LONGITUDE            FLOAT(18,5),";
         $query .= " primary key (COUNTRY_ID),";
         $query .= " key AK_COUNTRY__GUID (COUNTRY_GUID)";
         $query .= " ) ENGINE=InnoDB CHARACTER SET=utf8;";
         SQLExec($query);
         $this->LoadCountry();
      }
      
      $val = SQLSelectOne("select count(*)+2 CNT from information_schema.tables where table_schema = '" . DB_NAME . "' and table_name = 'OPENWEATHER_CITY'");
      $val = $val["CNT"] == 2 ? FALSE : TRUE;
      
      if (!$val)
      {
         SQLExec("drop table if exists OPENWEATHER_CITY");
         
         $query = "create table OPENWEATHER_CITY";
         $query .= "(";
         $query .= " CITY_ID              INT(10) not null,";
         $query .= " COUNTRY_ID           INT(10) not null,";
         $query .= " CITY_NAME            VARCHAR(255) not null,";
         $query .= " CITY_LAT             FLOAT(10,6) not null,";
         $query .= " CITY_LNG             FLOAT(10,6) not null,";
         $query .= " LM_DATE              DATETIME not null,";
         $query .= " primary key (CITY_ID)";
         $query .= " ) ENGINE=InnoDB CHARACTER SET=utf8;";
         SQLExec($query);
         
         SQLExec("alter table OPENWEATHER_CITY add constraint FK_OW_CITY__COUNTRY_ID foreign key (COUNTRY_ID)
                     references COUNTRY (COUNTRY_ID) on delete restrict on update restrict;");
         
         $this->LoadCity();
      }
      
      $className = 'openweather';
      $objectName = array('ow_city', 'ow_setting', 'ow_fact', 'ow_day0', 'ow_day1', 'ow_day2');
      $objDescription = array('Местоположение', 'Настройки', 'Текущая температура', 'Прогноз погоды на день', 'Прогноз погоды на завтра', 'Прогноз погоды на послезавтра');

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
      // --------------------------------------------------------------------
   }
   
   public function uninstall()
   {
      SQLExec("delete from pvalues where property_id in (select id FROM properties where object_id in (select id from objects where class_id = (select id from classes where title = 'openweather')))");
      SQLExec("delete from properties where object_id in (select id from objects where class_id = (select id from classes where title = 'openweather'))");
      SQLExec("delete from objects where class_id = (select id from classes where title = 'openweather')");
      SQLExec("delete from classes where title = 'openweather'");
      SQLExec('drop table if exists OPENWEATHER_CITY');
      SQLExec('drop table if exists COUNTRY');
      
      parent::uninstall();
   }
}
?>
