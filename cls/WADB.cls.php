<?php

	class WADB
	{
		/* Database Host */
		var $sDbHost;           
		var $sDbName;           // Database Name
		var $sDbUser;           // Database User
		var $sDbPwd;            // Database Password
		var $sDbDetail;         // Database Details
		var $iNoOfRecords;      // Total No of Records
		var $oQueryResult;      // Results of sql query
		var $aSelectRecords;    // Array
		var $aArrRec;           // Array
		var $bInsertRecords;    // Boolean
		var $iInsertRecId;      // Integer - the primary key for inserted record
		var $bUpdateRecords;    // Boolean
		
		/* Constructor */
		function WADB ($sDbHost, $sDbName, $sDbUser, $sDbPwd)
		{
			$oDbLink = mysql_connect ($sDbHost, $sDbUser, $sDbPwd) or die ("MySQL DB could not be connected");
			@mysql_select_db ($sDbName, $oDbLink)or die ("MySQL DB could not be selected");
			@mysql_query("set names 'utf8'");
		}
		
		/* seelct Record Object */
		function selectRecordsObject($sSqlQuery){
			unset($this->aSelectRecords);
			$this->oQueryResult = mysql_query($sSqlQuery) or die(mysql_error());
			$this->iNoOfRecords = mysql_num_rows($this->oQueryResult);
			if ($this->iNoOfRecords > 0) {
				while($obj = mysql_fetch_object($this->oQueryResult)) {
					$this->aSelectRecords[] = $obj;
				}	
				mysql_free_result($this->oQueryResult);				
			}						
			$this->aArrRec = $this->aSelectRecords;
			return $this->aArrRec;	
		}

		
	    /* Select Records */
		function selectRecords ($sSqlQuery)
		{
			unset($this->aSelectRecords);
			$this->oQueryResult = mysql_query($sSqlQuery) or die(mysql_error());
			$this->iNoOfRecords = mysql_num_rows($this->oQueryResult);
			if ($this->iNoOfRecords > 0) {
				while ($oRow = mysql_fetch_array($this->oQueryResult,MYSQL_ASSOC)) {
					$this->aSelectRecords[] = $oRow;
				}
				mysql_free_result($this->oQueryResult);
			}
			$this->aArrRec = $this->aSelectRecords;
			return $this->aArrRec;
		}
	
		/*Get Number of Records */
		function getNumberOfRecords () {
			return $this->iNoOfRecords;
		}
	
		/* Get selected data */
		function getSelectedData (){
			return $this->aSelectRecords;
		}
	
		/* Insert Records */
		function insertRecords($sSqlQuery)
		{
			$this->bInsertRecords = mysql_query ($sSqlQuery) or die (mysql_error());
			$this->iInsertRecId = mysql_insert_id();
			return $this->iInsertRecId;
		}
	
		/* Find Inserted Id */
		function getIdForInsertedRecord()
		{
			return $this->iInsertRecId;
		}
	
		/* Update Records */
		function updateRecords($sSqlQuery)
		{
			return mysql_query($sSqlQuery) or die(mysql_error());
		}
		function deleteRecords($sSqlQuery)
		{
			return mysql_query($sSqlQuery) or die(mysql_error());
		}
		/* 測試新增用 */
		function insertUser($sSqlQuery)
		{
			return mysql_query($sSqlQuery) or die(mysql_error());
		}
		
		/* 建立資料表 */
		function creatTable($sSqlQuery)
		{
			return mysql_query($sSqlQuery) or die(mysql_error());
		}
		
		
	}
?>