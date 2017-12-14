<?php

   /**
    * Get wind direction name by direction in degree 
    * @param mixed $degree Wind degree
    * @return string
    */
function getWindDirection($degree)
   {
	$windDirection = array('<#LANG_N#>', '<#LANG_NNE#>', '<#LANG_NE#>', '<#LANG_ENE#>', '<#LANG_E#>', '<#LANG_ESE#>', '<#LANG_SE#>', '<#LANG_SSE#>', '<#LANG_S#>', '<#LANG_SSW#>', '<#LANG_SW#>', '<#LANG_WSW#>', '<#LANG_W#>', '<#LANG_WNW#>', '<#LANG_NW#>', '<#LANG_NNW#>', '<#LANG_N#>');
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
?>
