<?php
require_once 'Phpmodbus/ModbusMaster.php'; 
include("cls/WADB.cls.php");
include("cls/Project.cls.php");
include("cfg/cfg.inc.php");

 function myutf8_unicode($name){
     $name = iconv('UTF-8', 'UCS-2BE', $name);
     $len  = strlen($name);
     $str  = '';
     for ($i = 0; $i < $len - 1; $i = $i + 2){
         $c  = $name[$i];
         $c2 = $name[$i + 1];
         if (ord($c) > 0){
             $str .= base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
         } else {
             $str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
         }
     }
     return $str;
 }
$modbusCount = 1; //modbus程式只要執行一次

$project = new Project();
$sql = mb_convert_encoding("SELECT TOP 1 location_CHT, degree, resultDT, iniValue, result, height FROM DabaBase.dbo.EQ_Detail WHERE location_CHT = 'Shui地點一' OR location_CHT = '地點N' order by resultDT desc", 'big5', 'utf-8');
$data = $project->custosql($sql);
if ($data)
{
	foreach ($data as $index => $earthquakeData) {
	if (strtotime($earthquakeData['resultDT']) >= strtotime( '- 2 minutes' ))
{

	$sql2 = "SELECT * FROM DabaBase.dbo.contact";
	$data2 = $project->custosql($sql2);
	$sql3 = "SELECT content FROM DabaBase.dbo.messageContent WHERE degree = ".$earthquakeData['degree']."";
	$data3 = $project->custosql($sql3);

	$earthquakePeople = array();
foreach ($data2 as $key => $value) {
		$rankStr = sprintf( "%05d", decbin( $value['contactRank'] ));
		if (mb_convert_encoding($earthquakeData['location_CHT'], 'utf-8', 'big5') == '地點N')
		{
			if ($rankStr[0] == '1')
			{
				if ($value['degree'] <= $earthquakeData['degree'])
				{
					$earthquakePeople[] = array($value['name'], $value['phoneNumber']);
				}
			}
		}
		else
		{
			if ($rankStr[1] == '1')
			{
				if ($value['degree'] <= $earthquakeData['degree'])
				{
					$earthquakePeople[] = array($value['name'], $value['phoneNumber']);
				}
			}
		}
		
}
	$timeNow = date("Y/m/d H:i:s");
	if (mb_convert_encoding($earthquakeData['location_CHT'], 'utf-8', 'big5') == '地點N')
	{
		$message = "地震通報：".$earthquakeData['resultDT']." 地點N".mb_convert_encoding($data3[0]['content'], 'utf-8', 'big5')."X最大值為".$earthquakeData['iniValue']."，Ｙ最大值為".$earthquakeData['result']."，Ｚ最大值為".$earthquakeData['height']."";
	}
	else
	{
		$message = "地震通報：".$earthquakeData['resultDT']." 地點M".mb_convert_encoding($data3[0]['content'], 'utf-8', 'big5')."X最大值為".$earthquakeData['iniValue']."，Ｙ最大值為".$earthquakeData['result']."，Ｚ最大值為".$earthquakeData['height']."";
	}
	
	//$insertLog = "INSERT INTO DabaBase.dbo.messageLog (content, sendTime, degree) VALUES ('".$data3[0]['content']."', '".date("Y/m/d H:i:s")."', ".$earthquakeData['degree'].")";
	$insertLog = "INSERT INTO DabaBase.dbo.messageLog (messageType, content, sendTime, degree) VALUES (1, '".mb_convert_encoding($message, 'big5', 'utf-8')."', '".$timeNow."', ".$earthquakeData['degree'].")";
			$project->custoinsert($insertLog);
		$mlID = "SELECT TOP 1 logID FROM DabaBase.dbo.messageLog WHERE messageType = 1 order by logID desc";
		$mLogID = $project->custosql($mlID);
		for ($j=0; $j < sizeof($earthquakePeople); $j++) { 
		if ($data3[0]['content'])
			{
				if (mb_convert_encoding($earthquakeData['location_CHT'], 'utf-8', 'big5') == '地點N')
				{
					$utf8_str = "地震通報：".$earthquakeData['resultDT']." 地點N".mb_convert_encoding($data3[0]['content'], 'utf-8', 'big5')."X最大值為".$earthquakeData['iniValue']."，Ｙ最大值為".$earthquakeData['result']."，Ｚ最大值為".$earthquakeData['height']."";
				}
				else
				{
					$utf8_str = "地震通報：".$earthquakeData['resultDT']." 地點M".mb_convert_encoding($data3[0]['content'], 'utf-8', 'big5')."X最大值為".$earthquakeData['iniValue']."，Ｙ最大值為".$earthquakeData['result']."，Ｚ最大值為".$earthquakeData['height']."";
				}
				
			}
			else
			{
				$utf8_str = '沒有輸入簡訊內容';
			}
		$phoneNumber = $earthquakePeople[$j][1];

		$numberConverted = array();
		for ($i = 0; $i < 10; $i++) {
    		if ($i % 2 == 0)
			{
				$numberConverted[] = $phoneNumber[$i + 1];
			}
			else
			{
				$numberConverted[] = $phoneNumber[$i - 1];
			}
		}

		$input = strtoupper(myutf8_unicode($utf8_str));
		$enter = "node C:\Users\Administrator\Desktop\hey.js"." ".$input." ";
		foreach ($numberConverted as $key => $value) {
			$enter = $enter.$value;
		}
		exec($enter.">null");
		$receive = "INSERT INTO DabaBase.dbo.messageReceived (messageLogID, name, phoneNumber, sendTime) VALUES (".$mLogID[0]['logID'].", '".mb_convert_encoding($earthquakePeople[$j][0], 'big5', 'utf-8')."', '".$earthquakePeople[$j][1]."', '".date("Y/m/d H:i:s")."')";
		$project->custoinsert($receive);
		sleep(2);
		# code...
	}
	if ($earthquakeData['degree'] >= 4 && $modbusCount == 1)
	{
		++$modbusCount; 

		$modbus = new ModbusMaster('', "TCP"); 
	// Data to be writen 
	$data = array(1); 
	$dataTypes = array("INT"); 
	try { 
	    // FC6 
	    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
	} 
	catch (Exception $e) { 
	    // Print error information if any 
	    echo $modbus; 
	    echo $e; 
	    exit; 
	} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 

$modbus = new ModbusMaster('', "TCP"); 
// Data to be writen 
$data = array(1); 
$dataTypes = array("INT"); 
try { 
    // FC6 
    $modbus->writeSingleRegister(1,799, $data, $dataTypes); 
} 
catch (Exception $e) { 
    // Print error information if any 
    echo $modbus; 
    echo $e; 
    exit; 
} 
// Print status information 
echo $modbus; 
}
}
	# code...
}
}






		


?>