<?php
/*
 * 學校：台北科技大學 低溫能源
 * 製作者:莊智凱
 * 製作日期：20150520
 * 版次：0001
 * 修改日期：20150520
 */
include "DataMining_MySql.php";
//$_POST['FunctionCode']=9;
//$_POST['Pro_Name']='Test';
//$_POST['ColumnName']='空調設備耗電量$CHP_3_冰水泵耗電量(kW)';
//$_POST['Type']='Col_CutNum';
//$_POST['Value']='1';
//$_POST['Account']='Root';
//$_POST['Password']='51235200';
$MauMySqlSaveClass = new DataMining_MySql;
//檢查會員資料
if($_POST['FunctionCode']==1) echo $MauMySqlSaveClass ->CheckMemberData($_POST['Account'],$_POST['Password']);
//取得會員資料
if($_POST['FunctionCode']==2) echo $MauMySqlSaveClass ->GetMenberData();
//刪除會員資料
if($_POST['FunctionCode']==3) echo $MauMySqlSaveClass ->DeleteMenberData();
//取得專案名稱
if($_POST['FunctionCode']==4) echo $MauMySqlSaveClass ->GetProject();
//取得資料庫名稱
if($_POST['FunctionCode']==5) echo $MauMySqlSaveClass ->GetSouceProjectName();
//取得資料庫日期區間
if($_POST['FunctionCode']==6) echo $MauMySqlSaveClass ->GetSouceProjectTimeRange($_POST['SouceName']);
//取得專案資料做設定
if($_POST['FunctionCode']==7) echo $MauMySqlSaveClass ->GetProjectData($_POST['Pro_Name']);
//下載Histogram圖
if($_POST['FunctionCode']==8) echo json_encode($MauMySqlSaveClass ->DrawHistogram($_POST['Pro_Name'],$_POST['ColumnName']));
//更新專案設定欄位
if($_POST['FunctionCode']==9) $MauMySqlSaveClass ->UpdateProjectColumnsSetingValue($_POST['Pro_Name'],$_POST['ColumnName'],$_POST['Type'],$_POST['Value']);
//取得欄位設定的結果
if($_POST['FunctionCode']==10) echo $MauMySqlSaveClass ->GetColumnsResult($_POST['Pro_Name']);
//月份分群DunnIndex結果
if($_POST['FunctionCode']==11) echo $MauMySqlSaveClass ->GetMonthDunnIndex($_POST['Pro_Name']);

?>