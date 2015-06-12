<?php
/*
 * 學校：台北科技大學 低溫能源
 * 製作者:莊智凱
 * 製作日期：20150528
 * 版次：0001
 * 修改日期：20150528
 */
 
class DataMining_MySql {
	 
	function CheckMemberData($Name,$Password)
	{
		//資料庫帳號密碼設定
		require("DB_DataMinig_config.php");
		//MySql操作的程式
		require("DB_class.php");

		//上傳至MAUBasic
		$db = new DB();
		$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
		$Sql = "select * FROM `Member` where `Mem_Account` = '".$Name."' and `Mem_Password` = '".$Password."'";
		$db->query($Sql);
		$ReturnValue = array();
		//假設沒有資料，回傳沒有
		if($db->get_num_rows()==0) {
			$Value['CheckValue']='No';
			$Value['WoringMessage']=
			<<<'EOT'
				<div class="alert alert-warning alert-dismissible" role="alert" id="IndexWoringMessage"  >
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				Woring PassWord
				</div>
EOT;
			array_push($ReturnValue, $Value);
			return  json_encode($ReturnValue);
		}
		//啟動Session
		session_start();
		while($result = $db->fetch_array())
		{
			//紀錄會員資料與權限
			$_SESSION['Account']=$result["Mem_Account"];
			$_SESSION['Password']=$result["Mem_Password"];
			$_SESSION['Limite']=$result["Mem_Limite"];
			$_SESSION['Note']=$result["Mem_Note"];
		}
		$db->closeConnect();
		$Value['CheckValue']='Yes';
		array_push($ReturnValue, $Value);
		return json_encode($ReturnValue);
	} 
	
	function GetMenberData(){
		$ReturnValue = array();
		//啟動Session
		session_start();
		//假設沒有資料紀錄
		if(!isset($_SESSION['Account'])) {
			$Value['CheckValue']='No';
			array_push($ReturnValue, $Value);
			return json_encode($ReturnValue);
		}
		$Value['CheckValue']='Yes';
		array_push($ReturnValue, $Value);
		array_push($ReturnValue, $_SESSION);
		return json_encode($ReturnValue);
	}
	
	function DeleteMenberData(){
		$ReturnValue = array();
		//啟動Session
		session_start();
		// remove all session variables
		session_unset(); 
		// destroy the session 
		session_destroy(); 
		$Value['CheckValue']='Delete';
		array_push($ReturnValue, $Value);
		return json_encode($ReturnValue);
	}
	
	function GetProject()
	{
		//資料庫帳號密碼設定
		require("DB_DataMinig_config.php");
		//MySql操作的程式
		require("DB_class.php");

		//上傳至MAUBasic
		$db = new DB();
		$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
		$Sql = "select `Pro_Name` FROM `Project`";
		$db->query($Sql);
		$ReturnValue = array();
		while($result = $db->fetch_array())
		{
			array_push($ReturnValue, $result["Pro_Name"]);
		}
		$db->closeConnect();
		return json_encode($ReturnValue);
	} 
	
	function GetSouceProjectName()
	{
		//資料庫帳號密碼設定
		require("DB_config.php");
		//MySql操作的程式
		require("DB_class.php");

		//上傳至MAUBasic
		$db = new DB();
		$db->connect_db($_DB['host'], $_DB['username'], $_DB['password'], $_DB['dbname']);
		$Sql = "select `Pro_Name` FROM `Project`";
		$db->query($Sql);
		$ReturnValue = array();
		while($result = $db->fetch_array())
		{
			array_push($ReturnValue, $result["Pro_Name"]);
		}
		$db->closeConnect();
		return json_encode($ReturnValue);
	} 
	//取得資料庫日期區間
	function GetSouceProjectTimeRange($DataSouceName)
	{
		//資料庫帳號密碼設定
		require("DB_config.php");
		//資料庫帳號密碼設定
		require("DB_DataMinig_config.php");
		//MySql操作的程式
		require("DB_class.php");
	
		//冠呈資料庫
		$db = new DB();
		$db->connect_db($_DB['host'], $_DB['username'], $_DB['password'], $_DB['dbname']);
		$Sql = "select DATE_FORMAT(Min(`DataTime`),'%Y/%m/%d') as 'StartTime' ,  DATE_FORMAT(max(`DataTime`),'%Y/%m/%d') as 'EndTime' FROM `Data_".$DataSouceName."`";
		$db->query($Sql);
		$ReturnValue = array();
		$result = $db->fetch_array();
		$ReturnValue["SelectRangeStartTime"]=$result["StartTime"];
		$ReturnValue["SelectRangeEndTime"]=$result["EndTime"];
		
		return json_encode($ReturnValue);
	} 
	//取得專案資料做設定
	function GetProjectData($Pro_Name)
	{
		//資料庫帳號密碼設定
		require("DB_config.php");
		//資料庫帳號密碼設定
		require("DB_DataMinig_config.php");
		//MySql操作的程式
		require("DB_class.php");
	
		//DataMining資料庫
		$db = new DB();
		$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
		$Sql = "select * FROM `Columns` where `Pro_Name` = '".$Pro_Name."'";
		$db->query($Sql);
		$ReturnValue = array();
		while($result = $db->fetch_array())
		{
			$DataSet = array();
			$DataSet['Col_Columns']=$result["Col_Columns"];
			$DataSet['Col_MaxValue']=$result["Col_MaxValue"];
			$DataSet['Col_MinValue']=$result["Col_MinValue"];
			$DataSet['Col_Selector']=$result["Col_Selector"];
			$DataSet['Col_CutNum']=$result["Col_CutNum"];
			$DataSet['Col_Note']=$result["Col_Note"];
			array_push($ReturnValue, $DataSet);
		}
		return json_encode($ReturnValue);
	} 	
	
	//下載Histogram圖
	function DrawHistogram($ProjectName,$ColumnName)
	{
		//專案資料庫帳號密碼設定
		require("DB_DataMinig_config.php");
		//SouceData資料庫帳號密碼設定
		require("DB_config.php");
		//MySql操作的程式
		require("DB_class.php");
		
		//下載專案的資料
		$db = new DB();
		$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
		$Sql = "Select `Pro_DateStart` , `Pro_DateEnd` ,`Pro_DataSouce` from `Project` where `Pro_Name` = '".$ProjectName."'";
		$db->query($Sql);
		$result = $db->fetch_array();
		$ProjectTime["StartTime"]=$result["Pro_DateStart"];
		$ProjectTime["EndTime"]=$result["Pro_DateEnd"];
		$DataSouce=$result["Pro_DataSouce"];
		$db->closeConnect();
		//先取最大值，然後再計算圖表的間格
		$db->connect_db($_DB['host'], $_DB['username'], $_DB['password'], $_DB['dbname']);
		$Sql = "Select max(`".$ColumnName."`) as 'MaxValue' from `Data_".$DataSouce."` where `DataTime` > '".$ProjectTime["StartTime"]."' and `DataTime` < '".$ProjectTime["EndTime"]."'";
		$db->query($Sql);
		$result = $db->fetch_array();
		
		if($result["MaxValue"]<10) $Interval=1;
		else if ($result["MaxValue"] >= 10 && $result["MaxValue"]<20) $Interval=2;
		else if ($result["MaxValue"] >= 20 && $result["MaxValue"]<30) $Interval=3;
		else if ($result["MaxValue"] >= 30 && $result["MaxValue"]<40) $Interval=3;
		else if ($result["MaxValue"] >= 40 && $result["MaxValue"]<50) $Interval=4;
		else if ($result["MaxValue"] >= 50 && $result["MaxValue"]<60) $Interval=5;
		else if ($result["MaxValue"] >= 60 && $result["MaxValue"]<70) $Interval=6;
		else if ($result["MaxValue"] >= 70 && $result["MaxValue"]<80) $Interval=7;
		else if ($result["MaxValue"] >= 80 && $result["MaxValue"]<90) $Interval=8;
		else if ($result["MaxValue"] >= 90 && $result["MaxValue"]<100) $Interval=9;
		else if ($result["MaxValue"] >= 100 && $result["MaxValue"]<110) $Interval=10;
		else if ($result["MaxValue"] >= 110 && $result["MaxValue"]<120) $Interval=11;
		else if ($result["MaxValue"] >= 120 && $result["MaxValue"]<130) $Interval=12;
		else if ($result["MaxValue"] >= 130 && $result["MaxValue"]<140) $Interval=13;
		else if ($result["MaxValue"] >= 140 && $result["MaxValue"]<150) $Interval=14;
		else if ($result["MaxValue"] >= 150 && $result["MaxValue"]<160) $Interval=15;
		else if ($result["MaxValue"] >= 160 && $result["MaxValue"]<170) $Interval=16;
		else if ($result["MaxValue"] >= 170 && $result["MaxValue"]<180) $Interval=17;
		else if ($result["MaxValue"] >= 180 && $result["MaxValue"]<190) $Interval=18;
		else if ($result["MaxValue"] >= 190 && $result["MaxValue"]<200) $Interval=19;
		else if ($result["MaxValue"] >= 200 && $result["MaxValue"]<210) $Interval=20;
		else if ($result["MaxValue"] >= 210 && $result["MaxValue"]<220) $Interval=21;
		else if ($result["MaxValue"] >= 220 && $result["MaxValue"]<230) $Interval=22;
		else if ($result["MaxValue"] >= 230 && $result["MaxValue"]<240) $Interval=23;
		else if ($result["MaxValue"] >= 240 && $result["MaxValue"]<250) $Interval=24;
		else $Interval=24;
		
		
		$Sql = "Select (`".$ColumnName."`-mod(`".$this->SpecialLetter($ColumnName)."`,'".$Interval."')) AS 'bucket' , COUNT(*) AS 'COUNT' from `Data_".$DataSouce."` where `DataTime` > '".$ProjectTime["StartTime"]."' and `DataTime` < '".$ProjectTime["EndTime"]."' GROUP  BY `bucket`";
		$db->query($Sql);
		$ReturnValue = array();
		while($result = $db->fetch_array())
		{
			$DataSet = array();
			$DataSet['bucket']=$result["bucket"];
			$DataSet['COUNT']=$result["COUNT"];
			array_push($ReturnValue, $DataSet);
		}
		return $ReturnValue;
	}
	
	//更新專案設定欄位
	function UpdateProjectColumnsSetingValue($ProjectName,$ColumnName,$Type,$Value)
	{
		//資料庫帳號密碼設定
		require("DB_DataMinig_config.php");
		//MySql操作的程式
		require("DB_class.php");
		//SouceData資料庫帳號密碼設定
		require("DB_config.php");
		
		//更新資料
		$db = new DB();
		$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
		$Sql = "update `Columns` set `".$Type."`='".$Value."' where `Pro_Name` = '".$ProjectName."' and `Col_Columns` = '".$this->SpecialLetter($ColumnName)."'";
		$db->query($Sql);
		$db->closeConnect();
		//計算分割
		if($Type=="Col_CutNum"){
			//下載專案設定的數值上下限
			$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
			$Sql = "select `Col_MaxValue` , `Col_MinValue`from `Columns` where `Pro_Name` = '".$ProjectName."' and `Col_Columns` = '".$this->SpecialLetter($ColumnName)."'";
			$db->query($Sql);
			$db->closeConnect();
			$result = $db->fetch_array();
			$MaxValue=$result['Col_MaxValue'];
			$MinValue=$result['Col_MinValue'];
			//如果沒有設定最小值則預設1
			if($MinValue==0){
				$MinValue=1;
			}
			//下載專案資料
			$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
			$Sql = "Select `Pro_DateStart` , `Pro_DateEnd` ,`Pro_DataSouce` from `Project` where `Pro_Name` = '".$ProjectName."'";
			$db->query($Sql);
			$result = $db->fetch_array();
			$db->closeConnect();
			$ProjectTime["StartTime"]=$result["Pro_DateStart"];
			$ProjectTime["EndTime"]=$result["Pro_DateEnd"];
			$DataSouce=$result["Pro_DataSouce"];
			//取最大值
			$db->connect_db($_DB['host'], $_DB['username'], $_DB['password'], $_DB['dbname']);
			$Sql = "Select max(`".$ColumnName."`) as 'MaxValue' from `Data_".$DataSouce."` where `DataTime` > '".$ProjectTime["StartTime"]."' and `DataTime` < '".$ProjectTime["EndTime"]."'";
			$db->query($Sql);
			$result = $db->fetch_array();
			//判斷最大值，如果資料內的最大值大於設定值則採用資料內的最大值
			if($MaxValue>$result['MaxValue'])$MaxValue = $result['MaxValue'];
			$db->closeConnect();
			
			//分割間距
			if($MaxValue!=$MinValue) $IntervalValue=($MaxValue-$MinValue)/$Value;
			else $IntervalValue=0;
			for( $i=0; $i<$Value;$i++){
				//Low
				if($i==0){
					$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
					$Sql = "update `Columns` set `Col_Low`='".($IntervalValue)."' where `Pro_Name` = '".$ProjectName."' and `Col_Columns` = '".$this->SpecialLetter($ColumnName)."'";
					$db->query($Sql);
					$db->closeConnect();
				}
				//Mid
				else if($i==1){
					$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
					$Sql = "update `Columns` set `Col_Mid`='".($IntervalValue*2)."' where `Pro_Name` = '".$ProjectName."' and `Col_Columns` = '".$this->SpecialLetter($ColumnName)."'";
					$db->query($Sql);
					$db->closeConnect();
				}
				//Hight
				else if($i==2){
					$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
					$Sql = "update `Columns` set `Col_Hight`='".($IntervalValue*3)."' where `Pro_Name` = '".$ProjectName."' and `Col_Columns` = '".$this->SpecialLetter($ColumnName)."'";
					$db->query($Sql);
					$db->closeConnect();
				}
				//SuperHight
				else if($i==3){
					$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
					$Sql = "update `Columns` set `Col_SuperHight`='".($IntervalValue*4)."' where `Pro_Name` = '".$ProjectName."' and `Col_Columns` = '".$this->SpecialLetter($ColumnName)."'";
					$db->query($Sql);
					$db->closeConnect();
				}
			}
		}
		
	}
	
	//取得欄位設定的結果
	function GetColumnsResult($Pro_Name)
	{
		//資料庫帳號密碼設定
		require("DB_config.php");
		//資料庫帳號密碼設定
		require("DB_DataMinig_config.php");
		//MySql操作的程式
		require("DB_class.php");
	
		//DataMining資料庫
		$db = new DB();
		$db->connect_db($_DB_DataMining['host'], $_DB_DataMining['username'], $_DB_DataMining['password'], $_DB_DataMining['dbname']);
		$Sql = "select * FROM `Columns` where `Pro_Name` = '".$Pro_Name."' and `Col_Selector` ='1'";
		$db->query($Sql);
		$ReturnValue = array();
		while($result = $db->fetch_array())
		{
			$DataSet = array();
			$DataSet['Col_Columns']=$result["Col_Columns"];
			$DataSet['Col_MaxValue']=$result["Col_MaxValue"];
			$DataSet['Col_MinValue']=$result["Col_MinValue"];
			$DataSet['Col_CutNum']=$result["Col_CutNum"];;
			$DataSet['Col_Low']=$result["Col_Low"];
			$DataSet['Col_Mid']=$result["Col_Mid"];
			$DataSet['Col_Hight']=$result["Col_Hight"];
			$DataSet['Col_SuperHight']=$result["Col_SuperHight"];
			array_push($ReturnValue, $DataSet);
		}
		return json_encode($ReturnValue);
	}
	//月份分群DunnIndex結果
	function GetMonthDunnIndex($Pro_Name){
	
	}
	
	//特殊字元轉換
	function SpecialLetter($Word){
		
		$Word = str_replace("\\", "\\\\",$Word);
		
		return $Word;
	}
	
}
?>