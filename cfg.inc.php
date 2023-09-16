<?php
//預設日期
define('DATE_START','2015-01-01');
date_default_timezone_set('Asia/Taipei');
/************* 資料庫定義  ****************/
define ('SYSTEM_DBHOST',''); 
define ('SYSTEM_DBNAME','dbname');
define ('SYSTEM_DBUSER','');
define ('SYSTEM_DBPWD','');


/*************系統變數定義************/
define('ADMIN_USER','admin_user');
define('COMPANY_USER','company_user');
define('PROJECT_USER','project_user');
define('USER','user');

/************* APP  ****************/
define ('APP_KEY','');
define ('APP_SECRET','');
define ('APP_ID','');


/************* 設定COOKIE 時間 ****************/
define('CREATE_COOKIE_TIME',time()+3600);
define('DELETE_COOKIE_TIME',time()-3600);


/*設定一天秒數*/
define('DAY_SECOND',86400);


/************* 簡訊傳送設定 *******************/
define('SERVER_IP',''); 
define('SERVER_PROT','');
define('TIMEOUT','');
define('USER_ACC','');
define('USER_PWD','');


/************* 定預DOMAIN NAME 與資料夾名稱 ****************/
define('DOMAIN','');
define('DOMAIN_PATH',"http://".$_SERVER['HTTP_HOST']);
//define path
define('PATH','/');
//首頁網址
define('INDEX','');

/************* 語言資料價與變數定義 ****************/
//LANGUAGE  
define('PACKAGE', 'message');
//$your_path/locale, ex: /var/www/test/locale
define('LOCALPATH','locale');


/************* 定義最大顯示筆數 ****************/
define('MAXRECODR',10);

/************* database tables ****************/
//define Ip data-table
define('IP2NATION','ip2nation');
define('IP2NATIONCOUNTRIES','ip2nationCountries');


//define user_admin	  系統管理員資料表
define('SYSTEM_MANAGER','system_manager');

//define project_list	  專案管理員資料表
define('PROJECT_LIST','project_list');

//define system_announcement  系統公告資料表
define('SYSTEM_ANNOUNCEMENT','system_announcement');

//define system_record  系統管理員修改紀錄資料表
define('SYSTEM_RECORD','system_record');

//define user_list  使用者資料表
define('USER_LIST','user_list');

//define user_list  專案管理員修改紀錄資料表
define('USER_RECORD','user_record');

//define user_device  新增專案中的儀器
define('USER_DEVICE','user_device');

//define user_device  新增專案中的儀器種類的感測器
define('USER_DEVICE_SENSOR','user_device_sensor');

//define user_device  新增專案中的Load檔
define('USER_DEVICE_SENSOR_LOAD','user_device_sensor_load');

//define user_device  新增專案中的介面圖
define('USER_INTERFACE','user_interface');

//define user_contact  新增聯絡人設定
define('USER_CONTACT','user_contact');

//define user_query_chart  新增統計圖查詢設定
define('USER_QUERY_CHART','user_query_chart');

?>
