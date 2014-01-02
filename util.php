<?
set_time_limit(60*60*60*60);
error_reporting(0);
include $path."connect.php";
include $path."adm_general_functions.php";
include $path."dual_functions.php";

function getTransDate($edate)
{
global $smartMonthArray;
$arr_date=explode(" ",$edate);
$edate=$arr_date[0];
$arr_date=explode("-",$edate);
$mm=$arr_date[1];
$mm=$mm-1;
$edate=$arr_date[2]." ".$smartMonthArray[$mm].", ".$arr_date[0];

return $edate;
}
/***************************************************************************************************************/
function validate($tbl,$strLogin){ //,$strPassword){ // checking if this login name already exists
$query="select * from $tbl where login= '$strLogin'";
$result=SQLQuery($query);
 $count=getCount($tbl,"user_name= '$strLogin'");
 if ($count >= 1)
	{
     return 1; // 1 = that this login already exists
    }
} // end of function
 
function getCount($tbl,$criteria){
global $con_id;

 if(!empty($criteria)){
  $where="where $criteria";
 }
$query="select count(*) as cnt from $tbl $where";
 $result=mysql_query($query);
 
 return mysql_result($result,0,"cnt");

}//endof funiton

function if_exist($tbl,$criteria,$field){
global $con_id;
 if(!empty($criteria)){
  $where="where $criteria";
 }
$query="select $field from $tbl $where";
$rs=SQLQuery($query);
$rc= mysql_num_rows($rs);

 return $rc;

}//endof funiton


function getSum($tbl,$criteria,$sum){
global $con_id;
 
 if(!empty($criteria)){
  $where="where $criteria";
 }
$query="select sum($sum) as cnt from $tbl $where";
 $result=mysql_query($query);
 
 return mysql_result($result,0,"cnt");

}//endof funiton

function getFN($tbl,$condition,$field)
{
	global $con_id;
	$where="";

	if(!empty($condition))
		$where .= " where " . $condition;

	$query="select $field from $tbl $where ";
	//$result=SQLQuery($query);
	$result=mysql_query($query);
  
	if ($nRow= mysql_fetch_array($result))
		return $nRow[$field];
	else
		return "";

//	return $name=mysql_result($result,0,"$field");

}//endof function

function getRow($tbl,$condition,$field)
{
	global $con_id;
	$where="";

	if(!empty($condition))
		$where .= " where " . $condition;

	$query="select $field from $tbl $where ";
	//$result=SQLQuery($query);
	$result=mysql_query($query);
  
	if ($nRow= mysql_fetch_array($result))
		return $nRow;
	else
		return "";

//	return $name=mysql_result($result,0,"$field");

}//endof function


/***************************************************************************************************************/
Function SQLQuery($query,$flag=0){

		global $con_id;
		$success=mysql_query($query);
                $total=mysql_num_rows($success);
		
		if($total == 0 && $flag == 1){
		 heading("No Record Found ...");
		}else
		if(!$success)
		{	
			echo "<br>Error while executing the query<br>";
			echo "<hr>";
			echo $query;
			echo "<hr>";
		}

		return $success;

}//endof function
        
		/************************************************************/

    /*	the function remove single quote from the string
		and replace it with two single quotes

		strString:		string to be fixed
		returns:		fixed string
	*/
function FixString($strString){
        
		//$strString = trim($strString);
		$strString = trim($strString);
		$strString = str_replace("'", "''", $strString);
		$strString = str_replace("\'", "'", $strString);
		$strString = str_replace("", ",", $strString);
		$strString = str_replace("\\", "", $strString);
		$strString = str_replace("\"", "&#34;", $strString);
		$strString = str_replace('\"', '"', $strString);
		return $strString;

}//endof function
  

  function FixString2($strString){
        
		//$strString = trim($strString);

		$strString = str_replace("'", "''", $strString);
		$strString = str_replace("\'", "'", $strString);
		$strString = str_replace("", ",", $strString);
		$strString = str_replace("\\", "", $strString);
		$strString = str_replace("\"", "&#34;", $strString);
		$strString = str_replace('\"', '"', $strString);
		return $strString;

}//endof function

      /************************************************************/

function getMaxId($tblName){
global $con_id;

$query="select max(id) as mx from $tblName";
$rs=mysql_query($query);

return $id=mysql_result($rs,0,"mx");

}//endof function

     /************************************************************/
	  function getMaxCustomId($tblName,$field){
global $con_id;

$query="select max($field) as mx from $tblName";
$rs=mysql_query($query);

return $id=mysql_result($rs,0,"mx");

}//endof function

///////////////////////////////////////////////////////////////////////////////////////////




 /************************************************************/

function arrCombo($arrCombo,$strVal)
{
while(list($strKey,$strValue)=each ($arrCombo)){
	   if($strVal ==$strKey)
        echo " <option value=\"$strKey\" selected>$strValue</option>";
	   else
        echo " <option value=\"$strKey\">$strValue</option>";
	}//endof while


}

		
				/************************************************************/

				
				/************************************************************/
function InsertRec($strTable, $arrValue){

		$strQuery = "	insert into $strTable (";

		reset($arrValue);
		while(list ($strKey, $strVal) = each($arrValue))
		{
			$strQuery .= $strKey . ",";
		}

		// remove last comma
		$strQuery = substr($strQuery, 0, strlen($strQuery) - 1);

		$strQuery .= ") values (";

		reset($arrValue);
		while(list ($strKey, $strVal) = each($arrValue))
		{
			$strQuery .= "'" . FixString($strVal) . "',";
		}

		// remove last comma
		$strQuery = substr($strQuery, 0, strlen($strQuery) - 1);
		$strQuery .= ");";

		// execute query
		SQLQuery($strQuery);
//		echo "<br>$strQuery<br>";
		
		// return id of last insert record
 $id=mysql_insert_id($strTable);
//$id= getMaxId($strTable);
		return $id;

}//endof function				
				
				/************************************************************/
function checkLogin($tblName,$fieldName,$strLogin){

$query="select count(*)as cnt from $tblName where $fieldName='$strLogin'";
$result=SQLQuery($query);
 return mysql_result($result,0,"cnt");

}//endof function				
				
				/************************************************************/


			
function DeleteRec($strTable, $strCriteria){

if(!empty($strCriteria))
	{
	$condition="where $strCriteria";
	}
 $strQuery = "delete from $strTable $condition";
 SQLQuery($strQuery);

}//endof function

        /************************************************************/
function unlinkFile($fileName,$path){
 
 $filePath=$path.$fileName;
 unlink("$filePath");

}//endof function

	/************************************************************/

/* the function updates the given table.
	
		strTable:		table name to be updates.
		strWhere:		where clause for record selection.
		arrValue:		an associated array with key-value of fields
						to be updated.
	*/
function UpdateRec($strTable, $strWhere, $arrValue){

		$strQuery = "	update $strTable set ";

		reset($arrValue);

		while (list ($strKey, $strVal) = each ($arrValue))
		{
			$strQuery .= $strKey . "='" . FixString2($strVal) . "',";
		}

		// remove last comma
		$strQuery = substr($strQuery, 0, strlen($strQuery) - 1);

		$strQuery .= " where $strWhere;";

		// execute query
		SQLQuery($strQuery);
	}



function getRandomString(){

$strAlphasNum="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
$code=simpleRandString(9,$strAlphasNum);
$first = substr($code, 0, 3);
$sec = substr($code, 3, 3); 
$thrd = substr($code, 6, 3);
return $str = $first ."-". $sec ."-". $thrd;

}//endof function
/************************************************************************************************/
function simpleRandString($length,$list){

mt_srand((double)microtime()*1000000);
$newstring="";
	if($length>0){
		while(strlen($newstring)<$length){
		$newstring.=$list[mt_rand(0, strlen($list)-1)];
		}
	}
return $newstring;

}
/************************************************************************************************/

function drawDBCombo($tablename,$display_field,$value_field,$order_by,$cond,$sel)
{
$query="select * from ".$tablename;
if(!empty($cond))
	{
	
	$query.=" where ".$cond;
	}

	if(!empty($order_by))
	{
	
	$query.=" order by ".$order_by;
	}
//echo $query;
$rs=SQLQuery($query);
while($row=mysql_fetch_array($rs))
	{
	$value=$row[$value_field];
	$lbl=$row[$display_field];
	if($value==$sel)
		{
			echo "<option value=\"$value\" selected>$lbl</option>";
		}
		else
		{
			echo "<option value=\"$value\" >$lbl</option>";
		}
	

	}
}
///////////////////////

/************************************************************************************************/
function saveImg($file,$prefixName,$nId,$path,$tbl,$field,$id){

 $file_name=$_FILES[$file]['name'];
 $file_type=$_FILES[$file]['type'];
 $file_size=$_FILES[$file]['size'];
 $tempFile=$_FILES[$file]['tmp_name']; 

  // ra_totaling file and coping into correct path
if(!empty($file_size))
	{
		// mapping unique name of the file with each file instance
        
		$strFileName=$nId . "-" . $prefixName . $file_name;
		$strFile_Path= $path."/".$strFileName;
        
		copy($tempFile,$strFile_Path);
       
		$query="update $tbl set $field='$strFileName' where $id=$nId";
		mysql_query($query);
	} /// endof if

}

/************************************************************************************************/

function Today()
{
	return $postedDay=date("Y-m-d");
}

/********************************************************************/
function getDateAndTime(){
 return date("Y-m-d H:i:s");
}
/********************************************************************/
function Tomorrow($nDays)
{
return $expiryDay=date("Y-m-d" , mktime (0,date("m"), date("d")+$nDays ,date("Y")));
}
/********************************************************************/
function parseDbDate($date){
if(empty($date) || $date=='0000-00-00') return "";

$dt=explode("/",$date); 

$d0=$dt[0];  // month
$d1=$dt[1];  // day
$d2=$dt[2];  // year
return $strO=$d2."-".$d1."-".$d0;

}
/********************************************************************/
function parseDbDate2($date){
if(empty($date) || $date=='0000-00-00') return "";

$dt=explode("/",$date); 

$d0=$dt[0];  // month
$d1=$dt[1];  // day
$d2=$dt[2];  // year
return $strO=$d2."-".$d0."-".$d1;

}
////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
function revrseDate($date){

if(empty($date) || $date=='0000-00-00'){
 return "";
}
$dt=explode("-",$date); 

$d0=$dt[0];  // year
$d1=$dt[1];  // month
$d2=$dt[2];  // day
return $strO=$d2."/".$d1."/".$d0;

}
////////////////////////////////////////////////////////////////////////////////////
function revrseDate2($date){

if(empty($date) || $date=='0000-00-00'){
 return "";
}
$dt=explode("-",$date); 

$d0=$dt[0];  // year
$d1=$dt[1];  // month
$d2=$dt[2];  // day
return $strO=$d1."/".$d2."/".$d0;

}
////////////////////////////////////////////////////////////////////////////////////
function parseMsg($msg,$param)
{
$arrMsg=explode("~",$msg);
$arrParam=explode("|",$param);
$new_msg="";
for($i=0;$i<count($arrMsg);$i++)
	{
	$temp=$arrParam[$i];
	$temp_msg=$arrMsg[$i];
	$new_msg.=$temp_msg."".$temp;
	}
	return 	$new_msg;
}

///	CUSTOM APPLICATION FUNCTIONS
////////////////////////////////////////////////////////////////////////////////
function date_diff2($date1, $date2) {
	if(empty($date2)) return array("d"=>1); // considering only [d] so if date 2 is empty then return 1

 $s = strtotime($date2)-strtotime($date1);
 $d = intval($s/86400); 
 $s -= $d*86400;
 $h = intval($s/3600);
 $s -= $h*3600;
 $m = intval($s/60); 
 $s -= $m*60;
 return array("d"=>$d,"h"=>$h,"m"=>$m,"s"=>$s);
}
////////////////////////////////////////////////////////////////////////////////
function getNoOfDays(){
 return date("t");
}
////////////////////////////////////////////////////////////////////////////////
function getNextYear($switch){
 $no_of_days=getNoOfDays();

 if($switch == "/"){
  return date("m/d/Y" ,mktime (0,0,0,date("m"),  date($no_of_days),  date("Y")+1)); 
 }elseif($switch == "-"){
  return date("Y-m-d" ,mktime (0,0,0,date("m"),  date($no_of_days),  date("Y")+1)); 
 }

}
////////////////////////////////////////////////////////////////////////////////
function getTextualDate($date){
if(empty($date) || $date=='0000-00-00'){
 return "";
}
$dt=explode("-",$date); 

$d0=$dt[0];  // year
$d1=$dt[1];  // month
$d2=$dt[2];  // day
return date ("F j, Y", mktime (0,0,0,$d1,$d2,$d0)); 
}//endof function
////////////////////////////////////////////////////////////////////////////////
function getTextualDate2($date){
if(empty($date) || $date=='0000-00-00'){
 return "";
}
$dt=explode("/",$date); 

$d0=$dt[0];  // month
$d1=$dt[1];  // day
$d2=$dt[2];  // year
return date ("F j, Y", mktime (0,0,0,$d0,$d1,$d2)); 
}//endof function

/******************************************************************************************************/
function replaceString($str,$strToReplace,$strReplaceWith){
 return str_replace($strToReplace, $strReplaceWith, $str);
}//endof function
/******************************************************************************************************/
function getDateTime($date){

if(empty($date) || $date=='0000-00-00'){
 return "";
}

$strTemp = explode(" ",$date);
$d = explode("-",$strTemp[0]);

$d0=$d[0];  // year
$d1=$d[1];  // month
$d2=$d[2];  // day

return $strDate = $d1 . " / " . $d2 . " / " . $d0 ." " . $strTemp[1];

}//endof function


/***************************************************************************************************************/

function fetchData($data)
{
$data=$_REQUEST["$data"];

return $data;
}

/***********************************************************************************************************************/
function getNextDate($dfrom,$nDays)
{
$dat_arr=explode("-",$dfrom);
$dd=$dat_arr[1];
//echo "<br>";
 $mm=$dat_arr[0];
//echo "<br>";
$yy=$dat_arr[2];
$dd2=$dd+$nDays;
$nextday  = mktime (0,0,0,$mm  ,$dd2,$yy); 
$day_date=getdate($nextday);
$nx_day= $day_date['mday'];
$dy_month=$day_date['mon'];
$year=$day_date['year'];
		if($dy_month<10)
			{
				$dy_month="0".$dy_month;
			}
			if($nx_day<10)
			{
			$nx_day="0".$nx_day;
			}

		  //$that_day=$nx_month."/".$dy_day."/".$year;
//			$that_day=$year."/".$nx_month."/".$dy_day;
			$that_day=$year."-".$nx_month."-".$dy_day;

		  return $that_day;

}

function getNextDate4($dfrom,$nDays)
{
$dat_arr=explode("/",$dfrom);
$dd=$dat_arr[0];
//echo "<br>";
 $mm=$dat_arr[1];
//echo "<br>";
$yy=$dat_arr[2];
$dd2=$dd+$nDays;
$nextday  = mktime (0,0,0,$mm  ,$dd2,$yy); 
$day_date=getdate($nextday);
$nx_day= $day_date['mday'];
$nx_month=$day_date['mon'];
$year=$day_date['year'];
		if($dy_month<10)
			{
				$dy_month="0".$dy_month;
			}
			if($nx_day<10)
			{
			$nx_day="0".$nx_day;
			}

		  //$that_day=$nx_month."/".$dy_day."/".$year;
//			$that_day=$year."/".$nx_month."/".$dy_day;
			$that_day=$year."-".$nx_month."-".$nx_day;

		  return $that_day;

}

function getNextDate3($dfrom,$nDays)
{
$dat_arr=explode("/",$dfrom);
$dd=$dat_arr[0];
//echo "<br>";
 $mm=$dat_arr[1];
//echo "<br>";
$yy=$dat_arr[2];
$dd2=$dd+$nDays;
//echo "$dd2 - - $dd- - $nDays;";
$nextday  = mktime (0,0,0,$mm  ,$dd2,$yy); 
$day_date=getdate($nextday);
//print_r($day_date);
$nx_day= $day_date['mday'];
$nx_month=$day_date['mon'];
$year=$day_date['year'];
		if($dy_month<10)
			{
				$dy_month="0".$dy_month;
			}
			if($nx_day<10)
			{
			$nx_day="0".$nx_day;
			}

		 $that_day=$nx_day."/".$nx_month."/".$year;
//			$that_day=$year."/".$nx_month."/".$nx_day;
//			$that_day=$year."-".$nx_month."-".$nx_day;

		  return $that_day;

}

function getNextDate_2($dfrom,$nDays)
{
$dat_arr=explode("-",$dfrom);
$dd=$dat_arr[1];
//echo "<br>";
 $mm=$dat_arr[0];
//echo "<br>";
$yy=$dat_arr[2];
$dd2=$dd+$nDays;
$nextday  = mktime (0,0,0,$mm  ,$dd2,$yy); 
$day_date=getdate($nextday);
$nx_day= $day_date['mday'];
$dy_month=$day_date['mon'];
$year=$day_date['year'];
$w=$day_date['w'];
		if($dy_month<10)
			{
				$dy_month="0".$dy_month;
			}
			if($nx_day<10)
			{
			$nx_day="0".$nx_day;
			}

		  //$that_day=$nx_month."/".$dy_day."/".$year;
//			$that_day=$year."/".$nx_month."/".$dy_day;
			$that_day=$year."-".$nx_month."-".$dy_day.":".$w;

		  return $that_day;

}
?>
