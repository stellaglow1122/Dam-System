<?php

include('model.php');

$project = new Project();

	$dir = "../rec/";
$file_list = array();             //ファイル名を保存する配列
$time_list = array();           //ファイルの日付を保存する配列
// Open a directory, and read its contents
if (is_dir($dir)){
  if ($dh = opendir($dir)){
    while (($fileNameX = readdir($dh)) !== false){
        if($fileNameX[0] != ".")
        {
            $time_list[] = filemtime($dir.$fileNameX);    //ソート用にファイル時刻を取得
            $file_list[] = $fileNameX;   //ファイル名を取得
        }

    }
    closedir($dh);
    array_multisort($time_list,SORT_DESC,$file_list);         //時刻でソート
  }
  else
  {
    echo "not found";
  }
}

$n = 0;
foreach ($file_list as $key => $fileNameS) {

  

  if ($n % 3 == 2)
  {
      if (date('Y-m-d H:i', filemtime($dir.$fileNameS)) == date('Y-m-d H:i'))
      {
          for ($i = $n; $i >= $n - 2; $i--)
          {
              $fileName = $file_list[$i];
              copy("../rec/".$fileName, "../event/".$fileName);
              if (substr($fileName, 15, 4) == '3865')
              {
                  $FileName3865 = $fileName;

                  $file = fopen($dir.$fileName, "rt");

                  //read file 3865
                  $keyMeaning = array('a'=>1, 'b'=>2, 'c'=>3);

                  $rowNumber = 0;

                  while( $lineData=fgets($file) )
                  {
                      // echo $lineData.'<br>';
                      if ($rowNumber == 2)
                      {
                          $startTime3865 = substr($lineData, 11);
                      }
                      if ($rowNumber == 6)
                      {
                          $axisAValueS = explode('~', str_replace('#AmMAX. a: ', '', $lineData));
                      }
                      if ($rowNumber == 7)
                      {
                          $axisBValueS = explode('~', str_replace('#AmMAX. b: ', '', $lineData));
                      }
                      if ($rowNumber == 8)
                      {
                          $axisCValueS = explode('~', str_replace('#AmMAX. c: ', '', $lineData));
                      }
                      if ($rowNumber == 9)
                      {
                          if (abs($axisAValueS[0]) > abs($axisAValueS[1]))
                          {
                              $axisAValue3865 = $axisAValueS[0];
                          }
                          else
                          {
                              $axisAValue3865 = $axisAValueS[1];
                          }
                          if (abs($axisBValueS[0]) > abs($axisBValueS[1]))
                          {
                              $axisBValue3865 = $axisBValueS[0];
                          }
                          else
                          {
                              $axisBValue3865 = $axisBValueS[1];
                          }
                          if (abs($axisCValueS[0]) > abs($axisCValueS[1]))
                          {
                              $axisCValue3865 = $axisCValueS[0];
                          }
                          else
                          {
                              $axisCValue3865 = $axisCValueS[0];
                          }
                          
                          $maxValue[0] = 'a';
                          $maxValue[1] = $axisAValue3865;
                         
                          if (abs($axisBValue3865) > abs($maxValue[1]))
                          {
                              $maxValue[0] = 'b';
                              $maxValue[1] = $axisBValue3865;
                          }
                          
                          if (abs($axisCValue3865) > abs($maxValue[1]))
                          {
                              $maxValue[0] = 'c';
                              $maxValue[1] = $axisCValue3865;
                          }
                          $scale3865 = 0;
                          $scaleArray = array(0.8, 2.5, 8, 25, 80, 250, 400);
                          foreach ($scaleArray as $key => $value) {
                              if (abs($maxValue[1]) >= $value)
                              {
                                  $scale3865 = $key + 1;
                              }
                          }

                      }
                      if ($rowNumber >= 13)
                      {
                          $dataArray = array();
                          $dataArray = explode(',', $lineData);
                          if (doubleval($dataArray[$keyMeaning[$maxValue[0]]]) == $maxValue[1])
                          {
                              $maxTime3865 = $dataArray[0];
                              break;
                          }
                          

                      }

                      ++$rowNumber;
                  }
                  fclose($file);
              }
              else if (substr($fileName, 15, 4) == '3837')
              {
                  $FileName3837 = $fileName;


                  $file = fopen($dir.$fileName, "rt");

                  //read file 3837
                  $keyMeaning = array('a'=>1, 'b'=>2, 'c'=>3);

                  $rowNumber = 0;


                  while( $lineData=fgets($file) )
                  {
                      // echo $lineData.'<br>';
                      if ($rowNumber == 2)
                      {
                          $startTime3837 = substr($lineData, 11);
                      }
                      if ($rowNumber == 6)
                      {
                          $axisAValueS = explode('~', str_replace('#AmMAX. a: ', '', $lineData));
                      }
                      if ($rowNumber == 7)
                      {
                          $axisBValueS = explode('~', str_replace('#AmMAX. b: ', '', $lineData));
                      }
                      if ($rowNumber == 8)
                      {
                          $axisCValueS = explode('~', str_replace('#AmMAX. c: ', '', $lineData));
                      }
                      if ($rowNumber == 9)
                      {
                          if (abs($axisAValueS[0]) > abs($axisAValueS[1]))
                          {
                              $axisAValue3837 = $axisAValueS[0];
                          }
                          else
                          {
                              $axisAValue3837 = $axisAValueS[1];
                          }
                          if (abs($axisBValueS[0]) > abs($axisBValueS[1]))
                          {
                              $axisBValue3837 = $axisBValueS[0];
                          }
                          else
                          {
                              $axisBValue3837 = $axisBValueS[1];
                          }
                          if (abs($axisCValueS[0]) > abs($axisCValueS[1]))
                          {
                              $axisCValue3837 = $axisCValueS[0];
                          }
                          else
                          {
                              $axisCValue3837 = $axisCValueS[0];
                          }
                          
                          $maxValue[0] = 'a';
                          $maxValue[1] = $axisAValue3837;
                         
                          if (abs($axisBValue3837) > abs($maxValue[1]))
                          {
                              $maxValue[0] = 'b';
                              $maxValue[1] = $axisBValue3837;
                          }
                          
                          if (abs($axisCValue3837) > abs($maxValue[1]))
                          {
                              $maxValue[0] = 'c';
                              $maxValue[1] = $axisCValue3837;
                          }
                          $scale3837 = 0;
                          $scaleArray = array(0.8, 2.5, 8, 25, 80, 250, 400);
                          foreach ($scaleArray as $key => $value) {
                              if (abs($maxValue[1]) >= $value)
                              {
                                  $scale3837 = $key + 1;
                              }
                          }
                          

                      }
                      if ($rowNumber >= 13)
                      {
                          $dataArray = array();
                          $dataArray = explode(',', $lineData);
                          if (doubleval($dataArray[$keyMeaning[$maxValue[0]]]) == $maxValue[1])
                          {
                              $maxTime3837 = $dataArray[0];
                              break;
                          }
                          

                      }

                      ++$rowNumber;
                  }
                  fclose($file);
              }
              else if (strpos($fileName, 'windEvent') !== false)
              {
                  //read file wind

                  $windFileName = $fileName;
                  $file = fopen($dir.$fileName, "rt");
                  $rowNumber = 0;
                  while( $lineData=fgets($file) )
                  {

                      if ($rowNumber == 0)
                      {
                          if ($lineData[6] == 'W')
                          {
                              $trigger = 'Wind';
                          }
                          else
                          {
                              $trigger = 'Vibration';
                          }
                      }
                      if ($rowNumber == 2)
                      {
                          $dataArray = array();
                          $dataArray = explode(',', $lineData);
                          
                          $maxSpeed = $dataArray[1];
                          $maxDirection = $dataArray[2];
                      }
                      if ($rowNumber > 2)
                      {
                          $dataArray = array();
                          $dataArray = explode(',', $lineData);
                          
                          if(doubleval($dataArray[1]) > doubleval($maxSpeed))
                          {
                              $maxSpeed = $dataArray[1];
                              $maxDirection = $dataArray[2];
                          }
                      }
                      ++$rowNumber;
                  }
                  //read file wind end
                  fclose($file);
              }


      if (strtotime($startTime3865) > strtotime($startTime3837))
      {
          $startTime = $startTime3837;
      }
      else
      {
          $startTime = $startTime3865;
      }
      
          }
          // EventID, DataTime, EventType, 3865_Axis_a, 3865_Axis_b, 3865_Axis_c, 3865_MaxTime, 3837_Axis_a, 3837_Axis_b, 3837_Axis_c, 3837_MaxTime, WindSpeed, WindDirection, 3865, 3837, wind
      $insert = "INSERT INTO event(DataTime, EventType, 3865_Scale, 3865_Axis_a, 3865_Axis_b, 3865_Axis_c, 3865_MaxTime, 3837_Scale, 3837_Axis_a, 3837_Axis_b, 3837_Axis_c, 3837_MaxTime, WindSpeed, WindDirection, 3865FileName, 3837FileName, windFileName) VALUES('".$startTime."', '".$trigger."', ".$scale3865.", ".$axisAValue3865.", ".$axisBValue3865.", ".$axisCValue3865.", '".$maxTime3865."', ".$scale3837.", ".$axisAValue3837.", ".$axisBValue3837.", ".$axisCValue3837.", '".$maxTime3837."', ".$maxSpeed.", ".$maxDirection.", '".$FileName3865."', '".$FileName3837."', '".$windFileName."')";

      $project->custoinsert($insert);
      }
  }
  ++$n;
}


    





?>