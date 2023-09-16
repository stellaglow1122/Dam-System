<?php

class Project{

	var $db;

	//建構函式
	public function Project(){
		$this->db = new WADB(SYSTEM_DBHOST, SYSTEM_DBNAME, SYSTEM_DBUSER, SYSTEM_DBPWD);
		return true;
	}
	
	//建立公司帳號
	public function add_company_account($data){
		$return = array();
		$project_name		= mysql_real_escape_string($data['project_name']);
		$manager		    = mysql_real_escape_string($data['manager']);
		$manager_acount		= mysql_real_escape_string($data['manager_acount']);
		$manager_passwd		= mysql_real_escape_string($data['manager_passwd']);
		$creater		    = mysql_real_escape_string($data['creater']);
		$project_code = $this->rand_company_code();
			if($this->isValidEmail($manager_acount)){
				if($this->check_company_name($project_name) == '0'){
						 $sql = "insert into ".PROJECT_LIST."
								  (
								  	  project_code,
									  project_name,
								  	  manager,
									  manager_acount,
									  manager_passwd,
									  creater,
									  time
								  )
								 value
								  (
								  	  '".$project_code."',
									  '".$project_name."',
									  '".$manager."',
									  '".$manager_acount."',
									  '".$manager_passwd."',
									  '".$creater."',
									  '".time()."'
								  )";
						$this->db->insertRecords($sql);
						
						$create_table = "CREATE TABLE ".$project_code." 
										(
											PID INT NOT NULL AUTO_INCREMENT, 
											PRIMARY KEY(PID),
											device_code varchar(5),
											name varchar(20),
											voltage varchar(20),
											temperature varchar(20),
											mathself varchar(20),
											formula_val varchar(50),
											default_val varchar(50),
											top_alert varchar(8),
											top_active varchar(8),
											down_alert varchar(8),
											down_active varchar(8),
											upper_limit varchar(8),
											lower_limit varchar(8),
											datetime varchar(30),
											file_name varchar(100),
											date varchar(30),
											load_value varchar(20)
										)";
						$this->db->creatTable($create_table);
						
						$src = '../../project_file/example/';
						$dst = '../../project_file/'.$project_code.'/';
						$this->Copy_File($src,$dst);
						$return['success'] 	= true;
						$return['project_code'] = $project_code ;
				}else{
					$return['error'] = '公司名稱重複';
				}
			}else{
				$return['error'] = 'Email格式錯誤';
			}
		 return $return;
	}
	
	//產生公司編號
	private function rand_company_code(){
		$rand =0;
		while (true){
			$rand = "C".rand(10000,99999);
			$count = $this->check_company_code($rand);
			if ($count==0){
				break;
			}
		}
		return $rand;
	}
	
	//判斷公司編號是否重複
	public function check_company_code($rand){
		$sql = "select
			 count(project_code) as count
			from
			 ".PROJECT_LIST."
			where
			 project_code='".$rand."'";
		$retStr = $this->db->selectRecords($sql);
		return $retStr['0']['count'];
	}
	
	//判斷公司名稱是否重複
	public function check_company_name($project_name){
		$project_name	= mysql_real_escape_string($project_name);
		$sql = "select
			 count(project_name) as count
			from
			 ".PROJECT_LIST."
			where
			 project_name='".$project_name."'";
		$retStr = $this->db->selectRecords($sql);
		return $retStr['0']['count'];
	}
	
	//判斷 Email格式 是否正確
	private function isValidEmail($email){
        	return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
	}
	
	
	//建立專案資料夾
	public function Copy_File($src,$dst){
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->Copy_File($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
	
	//抓出所有沒有停權的公司
	public function get_not_stop_use_company(){
		$sql = "select
			 ".USER_COMPANY.".company_code,
			 ".USER_COMPANY.".company_name
			from
			 ".USER_COMPANY."
			where
			 stop_use='1'";		
		$retStr = $this->db->selectRecords($sql);
		return $retStr;
	}
	
	//回傳所有公司管理員帳號
	public function get_all_company_account(){
		$sql = "select
			 *
			from
			 ".PROJECT_LIST;
		$retStr = $this->db->selectRecords($sql);
		return $retStr;
	}
	
	
	
	//回傳所有關於三聯資料
	public function get_all_about_CompanyName(){
		$sql = "select
			 *
			from
			 project_list";
		$retStr = $this->db->selectRecords($sql);
		return $retStr[0];
	}
	
	//回傳所有公司管理員帳號
	public function get_project_account_status(){
		$sql = "select
			 *
			from
			 ".PROJECT_LIST;
		$retStr = $this->db->selectRecords($sql);
		return $retStr;
	}
	
	//修改公司授權
	public function edit_project_stop_use($project_code,$stop_use){
		$sql = "update
			 ".PROJECT_LIST."
			set
			 status='".$stop_use."'
			where
			 project_code='".$project_code."'";
		$this->db->updateRecords($sql);
	}
	
	//修改人員授權
	public function edit_project_stop_user($project_code,$stop_use){
		$sql = "update
			 user_list
			set
			 status='".$stop_use."'
			where
			 user_code ='".$project_code."'";
		$this->db->updateRecords($sql);
	}
	
	//系統管理員發布告
	public function PostAnnouncement($user_name,
					 $announcement_title,
					 $announcement_content,
					 $announcement_attachment){
		$user_name		= mysql_real_escape_string($user_name);
		$announcement_title	= mysql_real_escape_string($announcement_title);
		$announcement_content	= mysql_real_escape_string($announcement_content);
		$announcement_attachment= mysql_real_escape_string($announcement_attachment);
		$sql = "insert into ".SYSTEM_ANNOUNCEMENT."
			 (
			 	user_name,
			 	announcement_title,
			 	announcement_content,
			 	announcement_attachment,
			 	announcement_time
			 )
			 values
			 (
			 	'".$user_name."',
			 	'".$announcement_title."',
			 	'".$announcement_content."',
			 	'".$announcement_attachment."',
			 	'".time()."'
			 )";
		$this->db->insertRecords($sql);
	}
	
	//判斷系統公告是否有此附件
	public function CheckAttachment($filename){
		$sql = "select
			 if(count(id)>0,'1','0') as count
			from
			 ".SYSTEM_ANNOUNCEMENT."
			where
			 announcement_attachment REGEXP '".$filename."'";
		$retStr = $this->db->selectRecords($sql);
		return $retStr['0']['count'];
	}
	
	//修改系統公告
	public function UpdateAnnouncement($user_name,
					   $announcement_title,
					   $announcement_content,
					   $announcement_attachment,
					   $announcement_time){					   
		$sql = "update
			 ".SYSTEM_ANNOUNCEMENT."
			set
			 announcement_title='".$announcement_title."',
			 announcement_content='".$announcement_content."',
			 announcement_attachment=if(announcement_attachment='','".$announcement_attachment."',concat(announcement_attachment,',".$announcement_attachment."'))
			where
			 user_name='".$user_name."'
			 &&
			 announcement_time='".$announcement_time."' ";
		$this->db->updateRecords($sql);
	}
	
	//列出該系統所有公告
	public function ShowAnnouncement(){
		$sql = "select
					*
		       from
		        ".SYSTEM_ANNOUNCEMENT."
		       order by
		        announcement_time desc";
		$retStr = $this->db->selectRecords($sql);
		return $retStr;	
	}
	
	//列出該筆公告
	public function GetAnnouncement($id,$code){
		$sql = "select
					*
		       from
		        ".SYSTEM_ANNOUNCEMENT."
		       where
		        id='".$id."'
				&&
				announcement_time='".$code."' ";
		$retStr = $this->db->selectRecords($sql);
		return $retStr['0'];
	}
	
	//判斷所有的管理員帳號負責的專案
	public function get_all_project_name($manager_acount,$manager_passwd){
		$manager_acount		= mysql_real_escape_string($manager_acount);
		$manager_passwd		= mysql_real_escape_string($manager_passwd);
		$sql = "select
			 		*
				from
			 		".PROJECT_LIST." 
				where
					manager_acount = '".$manager_acount."'
					&&
					manager_passwd = '".$manager_passwd."'	";
		$retStr = $this->db->selectRecords($sql);
		return $retStr;
	}
	
	//取得所有專案管理人
	public function get_all_project_manager(){
		$sql = "select
			 		manager_acount,
					manager_passwd,
					manager
				from
			 		".PROJECT_LIST." 
				GROUP BY manager_acount";
		$retStr = $this->db->selectRecords($sql);
		return $retStr;
	}
	
	//取得單一專案管理人
	public function get_project_data($project_code){
		$sql = "select
					*
				from
			 		".PROJECT_LIST." 
				where 
					project_code = '".$project_code."' ";
		$retStr = $this->db->selectRecords($sql);
		return $retStr;
	}
	
	//判斷系統內是否有這個專案管理人
	public function check_project_manager(){
		$sql = "select
			 		manager_acount,
					manager_passwd,
					manager
				from
			 		".PROJECT_LIST." 
				GROUP BY manager_acount";
		$retStr = $this->db->selectRecords($sql);
		return $retStr;
	}
	
	//取得專案內的所有資料
	public function get_project_info($project_code){
		$sql = "select
			 		*
				from
			 		".PROJECT_LIST." 
				where
					project_code = '".$project_code."' ";
		$retStr = $this->db->selectRecords($sql);
		return $retStr['0'];
	}
	
	//新增系統管理員修改紀錄
	public function Insertsystem_record($project_code,$system_user,$action){
		$project_code	= mysql_real_escape_string($project_code);
		$system_user	= mysql_real_escape_string($system_user);
		$action			= mysql_real_escape_string($action);
		$sql = "insert into ".SYSTEM_RECORD."
			 (
			 	project_code,
			 	system_user,
			 	action,
				time
			 )
			 values
			 (
			 	'".$project_code."',
			 	'".$system_user."',
			 	'".$action."',
			 	'".time()."'
			 )";
		$this->db->insertRecords($sql);
	}
	
	//修改專案
	public function Updateproject($data){
		foreach($data as $key => $value){
			$$key = mysql_real_escape_string($value);
		}
		if($manager != ''){
			$sql = "update ".PROJECT_LIST." set
						project_name  = '".$project_name."',
						manager		  = '".$manager."',
						manager_acount= '".$manager_acount."',
						manager_passwd= '".$manager_passwd."'
					where 
						project_code = '".$project_code."'	";
		}else{
			$sql = "update ".PROJECT_LIST." set
						project_name  = '".$project_name."'
					where 
						project_code = '".$project_code."'	";
		}
		$this->db->updateRecords($sql);
	}
	//產生公司編號
	private function rand_interface_code(){
		$rand =0;
		while (true){
			$rand = rand(10000,99999);
			$count = $this->check_interface_code($rand);
			if ($count==0){
				break;
			}
		}
		return $rand;
	}
	
	//判斷公司編號是否重複
	public function check_interface_code($rand){
		$sql = "select
			 count(interface_code) as count
			from
			 ".USER_INTERFACE."
			where
			 interface_code='".$rand."'";
		$retStr = $this->db->selectRecords($sql);
		return $retStr['0']['count'];
	}
	
	
	//新增系統管理員修改紀錄
	public function Insert_user_interface($project_code,$interface_name,$interface_text,$AttachmentName){
		$project_code	= mysql_real_escape_string($project_code);
		$interface_name	= mysql_real_escape_string($interface_name);
		$interface_text = mysql_real_escape_string($interface_text);
		$AttachmentName = mysql_real_escape_string($AttachmentName);
		$interface_code = $this->rand_interface_code();
		$sql = "insert into ".USER_INTERFACE."
			 (
			 	project_code,
				interface_code,
			 	interface_name,
			 	interface_text,
				AttachmentName,
				date
			 )
			 values
			 (
			 	'".$project_code."',
				'".$interface_code."',
			 	'".$interface_name."',
			 	'".$interface_text."',
				'".$AttachmentName."',
			 	'".time()."'
			 )";
		$this->db->insertRecords($sql);
		return  $interface_code;
	}
	
	//取得專案內的所有資料
	public function get_interface_info($project_code,$interface_code){
		$sql = "select
			 		*
				from
			 		".USER_INTERFACE." 
				where
					project_code = '".$project_code."' 
					&&
					interface_code = '".$interface_code."' ";
		$retStr = $this->db->selectRecords($sql);
		return $retStr['0'];
	}
	
	//取得專案內的所有資料
	public function get_interface_data($project_code){
		$sql = "select
			 		*
				from
			 		".USER_INTERFACE." 
				where
					project_code = '".$project_code."' ";
		$retStr = $this->db->selectRecords($sql);
		return $retStr;
	}

	//修改更新時間
	public function Updateprojectrefresh($data){
		foreach($data as $key => $value){
			$$key = mysql_real_escape_string($value);
		}
		if($type !="introduction"){
			$refresh = $refresh * 60 *1000;
			$sql = "update ".PROJECT_LIST." set
						refresh  = '".$refresh."'
					where 
						project_code = '".$project_code."'	";
		}else{
			$sql_pic = ($pic != '') ? "pic  = '".$pic."',":"";
			$sql = "update ".PROJECT_LIST." set
						".$sql_pic."
						content  = '".$content."'
					where 
						project_code = '".$project_code."'	";			
		}
		$this->db->updateRecords($sql);
	}
	
	public function custosql($sql){
		$retStr = $this->db->selectRecords($sql);
		return $retStr;
		
	}
	public function custoupdate($sql){
		$retStr = $this->db->updateRecords($sql);
	}
	public function custodelete($sql){
		$retStr = $this->db->deleteRecords($sql);
	}
	public function custoinsert($sql){
		print_r($sql);
		$retStr = $this->db->insertRecords($sql);
		print_r($retStr);
	}
}
?>