<?php
function ws_reg(&$out) {
	global $external_id;
	global $name;
	global $latitude;
	global $longitude;
	global $altitude;
	$data['external_id']=$external_id;
	$data['name']=$name;
	$data['latitude']=$latitude;
	$data['longitude']=$longitude;
	$data['altitude']=$altitude;
	$json_data=json_encode($data, JSON_UNESCAPED_UNICODE);
	$ch = curl_init();
	$apiKey = gg('ow_setting.api_key');
	curl_setopt($ch, CURLOPT_URL, 'http://api.openweathermap.org/data/3.0/stations?appid='.$apiKey);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
	$response = curl_exec($ch);
	curl_close($ch);
	$data=json_decode($response, TRUE);
	sg('ow_ws.id', $data['ID']);
	sg('ow_ws.user_id', $data['user_id']);
	sg('ow_ws.name', $data['name']);
}
function ws_get_info(&$out) {
	$out["ow_ws_id"] = gg('ow_ws.id');
	$out["ow_ws_name"] = gg('ow_ws.name');
	If ($out["ow_ws_id"]!='') $out["NEED_REG"]==false; else $out["NEED_REG"]==true;
	$this->ws_send_data(&$out, false);
}
function ws_send_data(&$out, $send=false) {
	if (gg('ow_ws.id')!='') 				$data['station_id']=gg('ow_ws.id');
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
	foreach ($data as $k=>$v) {
		$out['data']['name'][$i]=$k;
		$out['data']['val'][$i]=$v;
	}
	if($send==true) {
		$apiKey = gg('ow_setting.api_key');
		$json_data=json_encode($data, JSON_UNESCAPED_UNICODE);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.openweathermap.org/data/3.0/stations?appid='.$apiKey.'&type=h&limit=100&station_id='.$data['station_id']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
		$response = curl_exec($ch);
		curl_close($ch);
	}
}
?>
