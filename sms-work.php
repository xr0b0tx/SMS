<?php 
set_time_limit(0);
error_reporting(15);
//include_once('util.php'); <-- if you want connect on table client
$nFreeMsg=10;
$nFreeMsg_On_Number=10;
//this is for sms account
$userName=" ";
$userPass=" ";
$adminSenderName="Email to SMS";
$adminSenderEmail="admin@mail.com";
$market_message="-Free SMS by YourCompany";
$nBody_Message_Limit=130;
//$adminSenderName="";
$server = '{imap.gmail.com:993/ssl/novalidate-cert}INBOX';
$login = 'your@gmail.com';
$password = 'yourpass';
$server1 = '{imap.gmail.com:993/ssl/novalidate-cert}[Gmail]/Spam';
$connection1 = imap_open($server1, $login, $password)or die("Connection to server failed");
/*$boxes = imap_listmailbox($connection1, "{localhost}", "*");
for ($i=0; $i<count($boxes); $i++) {
  echo "Mailbox: $boxes[$i]<BR>\n";
}*/
$mc = imap_check($connection1);
//echo $mc->Nmsgs;
$result = imap_fetch_overview($connection1,"1:{$mc->Nmsgs}",0);
 foreach ($result as $overview) {
 //echo '<pre>'; print_r($overview);echo '</pre>'; 
 $seen_msg = $overview->seen;
 if (!$seen_msg)
 {
 	imap_mail_move($connection1, "$overview->msgno:$overview->msgno", 'INBOX');
	imap_expunge($connection1);
 }
 }
imap_close($connection1);
$msgDetails = array();
$toAddress = array();
$fromAddress = array();
$subjectArray = array();
$smsText = array();
$seenCheck = array();

$connection = imap_open($server, $login, $password)or die("Connection to server failed");
//$mailboxes = imap_list($connection, $server, '*');
//echo '<pre>'; print_r($mailboxes);	echo '</pre>';
$headers = @imap_headers($connection)or die("Couldn't get emails");
$numEmails = sizeof($headers);
for($i = 1; $i < $numEmails+1; $i++)
{
	$mailHeader = @imap_headerinfo($connection, $i);
	if($mailHeader->Unseen =='U')
	{	
//		//echo '<pre>'; print_r($mailHeader);echo '</pre>';		
		$body = imap_fetchbody($connection, $i, "1.1");
		if ($body == "") {
		   $body = imap_fetchbody($connection, $i, "1");
		}
//		$body = trim(substr(quoted_printable_decode($body), 0, 140));
		$body = trim(substr(quoted_printable_decode($body), 0, $nBody_Message_Limit));
		$msgDetails['subject'][$i]   = strip_tags($mailHeader->subject);
		$msgDetails['fromName'][$i]  = $mailHeader->from[0]->personal;
		$msgDetails['fromEmail'][$i] = $mailHeader->from[0]->mailbox.'@'.$mailHeader->from[0]->host;
		foreach($mailHeader->to as $j => $v)
		{
			$msgDetails['Recipient'][$i][$j]   = $v->mailbox;
		}	
		$msgDetails['msgSMS'][$i]   = addslashes($body);
	}	
	$msgNo = trim($mailHeader->Msgno);
	imap_mail_move($connection, "$msgNo:$msgNo", '[Gmail]/Trash');
	imap_delete($connection, $mailHeader->Msgno);
	imap_expunge($connection); 
}
imap_close($connection);
//echo '<pre>'; print_r($msgDetails);echo '</pre>';
////fzee coded
if(count($msgDetails['fromEmail']) > 0)
{
	echo "Reading Msg<br>";
	foreach($msgDetails['fromEmail'] as $i => $v)
	{
		$userName22 = $msgDetails['subject'][$i];
		$senderEmail = $v;
		$senderName = $msgDetails['fromName'][$i];
		//email adddress for fire
		  $to=$senderEmail;
		///fzee code started
		$is_reg=getCount("tblclients","email='$senderEmail'");
		if($is_reg==0)
		{
			//echo "Not Registered";
		$is_free=getCount("tblf_credits_used","email='$senderEmail' and type=0");
		if($is_free<$nFreeMsg)
			{
			////send message code here
			foreach($msgDetails['Recipient'][$i] as $j=>$v1)
			{		
				$uniqueID  = mktime();				
				$dateAdded = date("Y-m-d H:i:s");
				 $recipientNo = $v1;
				//echo "<br>";
				echo  $recipientNo=parsePhone($recipientNo);
				if($recipientNo==-1)
				{
				// msg #1
				 regectionMsg($to,1);
				 continue;
				 }
				 $msgBody = $msgDetails['msgSMS'][$i];	
 				echo $ret=checkBadWords($msgBody,$to);
				 if($ret==-1)
				{
	 				 regectionMsg($to,2);
				 continue;
				 }
				$this_number_count=getCount("tblf_credits_used","cell=$recipientNo and type=0");
				if($this_number_count==$nFreeMsg_On_Number)
				{
				regectionMsg($to,1);
				 continue;
				}
				////////////////////////////////////
				//send free message
				$resultsms     = sendSMS($userName , $userPass, $recipientNo, $msgBody, $uniqueID);					
				$deliveryReport = sendDelivery($userName , $userPass, $uniqueID);		
				if($deliveryReport == 1)
				{
					$title="Message Sent To - $recipientNo";
					InsertRec("tblf_credits_used", array(
						"title"=>$title,
						"credits"=>0,
						"msg"=>$msgBody,
						"msg_date"=>$dateAdded,
						"email"=>$to,
						"cell"=>$recipientNo,
						"type"=>0
					));
				/////////////////////
				/// send message email
				// msg #2
				$subject=$_STR_SEND_MSG_SUB;
				$message=$_STR_SEND_MSG_TXT;
				 mailFunction($adminSenderEmail, $adminSenderName ,$to,$subject,$message);

				}
			}
			//last free message
				if(($is_free+1)==$nFreeMsg)
				{
				// msg #3
				$subject=$_STR_SEND_LAST_FREE_MSG_SUB;
				$message=$_STR_SEND_LAST_FREE_MSG_TXT;
				 mailFunction($adminSenderEmail, $adminSenderName ,$to,$subject,$message);
				}
			}
			else
			{
				// msg #4
				// No free messsage available
				$subject=$_STR_SEND_FREE_MSG_LIMIT_SUB;
				$message=$_STR_SEND_FREE_MSG_LIMIT_TXT;
				 mailFunction($adminSenderEmail, $adminSenderName ,$to,$subject,$message);
			}
		}
		else
		{
			//echo "Registered $to<br>";
			////send message code here
			foreach($msgDetails['Recipient'][$i] as $j=>$v1)
			{		
				$uniqueID  = mktime();				
				$dateAdded = date("Y-m-d H:i:s");
				//echo "<br> fzee ";					
			 $recipientNo = $v1;
				//echo "<br> fzee ";
				 echo $recipientNo=parsePhone($recipientNo);
				 $nCreditsBalance=getCreditsBalance($to);
 				 if($recipientNo==-1)
				{

				 				 regectionMsg($to,1);
				 				 continue;
				 }
				$msgBody = $msgDetails['msgSMS'][$i];	

				 $ret=checkBadWords($msgBody,$to);

				 if($ret==-1)
				{
 				 regectionMsg($to,2);
				 continue;
				 }
				//send sms message
			if($nCreditsBalance>0)
			{
				$resultsms     = sendSMS($userName , $userPass, $recipientNo, $msgBody, $uniqueID);					
				$deliveryReport = sendDelivery($userName , $userPass, $uniqueID);		
				if($deliveryReport == 1)
				{
					$title="Message Sent To - $recipientNo";
					InsertRec("tblf_credits_used", array(
						"title"=>$title,
						"credits"=>1,
						"msg"=>$msgBody,
						"msg_date"=>$dateAdded,
						"email"=>$to,
						"cell"=>$recipientNo,
						"type"=>1
					));
					$nbal=$nCreditsBalance-1;
				UpdateRec("tblclients","email='$to'", array("credit"=>$nbal));
				if($nbal==1)
					{
					$subject=$_STR_SEND_LAST_FREE_MSG_SUB;
					$message=$_STR_SEND_LAST_FREE_MSG_TXT;
					 mailFunction($adminSenderEmail, $adminSenderName ,$to,$subject,$message);
					}
				$subject=$_STR_SEND_MSG_SUB;
				$message=$_STR_SEND_MSG_TXT;
				 mailFunction($adminSenderEmail, $adminSenderName ,$to,$subject,$message);
				}
				}
			else
			{
			echo "Insufficent credits";
				$subject=$_STR_SEND_MSG_LIMIT_SUB;
				$message=$_STR_SEND_MSG_LIMIT_TXT;
				 mailFunction($adminSenderEmail, $adminSenderName ,$to,$subject,$message);
			}
			}
		}
	}
}
else{
	echo 'No unread Message';
}
/*************SMS SCRIPT**************/
 function sendSMS($userName , $userPass, $recipientNo, $smsText, $uniqueID)
 { 
 global $market_message;
 $smsText.=$market_message;
  $textMsg =   urlencode(trim($smsText));  
   $url1="http://www.abtxt.com/websms/sendsms.aspx?user=$userName&passwd=$userPass&mobilenumber=$recipientNo&message=$textMsg&senderid=ITCompany";
   $url1=str_replace(' ', '%20',$url1);
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($c, CURLOPT_URL, $url1);
	$contents = curl_exec($c);
	curl_close($c);
	if ($contents) 	
		return true;		
	else 
		return false;
 }
 function sendDelivery($userName , $userPass, $uniqueID)
 { 
		$url1 = "http://www.abtxt.com/websms/credits.aspx?user=$userName&passwd=$userPass&SMS_Job_NO=$uniqueID"; 
		$url1=str_replace(' ', '%20',$url1);
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($c, CURLOPT_URL, $url1);
		$contents = curl_exec($c);
		curl_close($c);
		if ($contents) 	
			return true;
		else 
			return false;
 }
function getCreditsBalance($email)
{
//	echo $email;
$sms_count=0;
$sms_left=$sms_count=getFN("tblclients","email='$email'","credits");
return $sms_left;
}
function parsePhone($phone)
{
$first_number=substr($phone, 0, 1);
//echo "$first_number fzee";
if($first_number=="+")
	{
	$phone=substr($phone, 1);
	}
$first_number=substr($phone, 0, 1);
$first_two_number=substr($phone, 0, 2);
	if($first_number=="0")
	{
		if($first_two_number!="00")
		{
		$phone=substr($phone, 1);
		$phone="62".$phone;
		}
		else
		{
		$phone=substr($phone, 2);
		$first_two_number=substr($phone, 0, 2);
		if($first_two_number!=62)
			{
			$phone="62".$phone;
			}
		}
	}
$first_two_number=substr($phone, 0, 2);
if($first_two_number!=62)
	{
	$phone="62".$phone;
	}
	$phone_len=strlen($phone);
	if($phone_len!=11)
	{
	$phone=-1;
	}
	if(!is_numeric($phone))
	{
	$phone=-1;
	}
	return $phone;
}
function regectionMsg($to,$flag=1)
{
global $_STR_SEND_FREE_MSG_NO_LIMIT_SUB ,$_STR_SEND_FREE_MSG_NO_LIMIT_TXT , $adminSenderEmail , $adminSenderName,$_STR_SEND_BADWORD_SUB,$_STR_SEND_BADWORD_TXT;
$subject=$_STR_SEND_FREE_MSG_NO_LIMIT_SUB;
$message=$_STR_SEND_FREE_MSG_NO_LIMIT_TXT;
$today=date("Y-m-d");
switch($flag)
{
	case 1:
		$subject=$_STR_SEND_FREE_MSG_NO_LIMIT_SUB;
		$message=$_STR_SEND_FREE_MSG_NO_LIMIT_TXT;
						$rt_msg_flag=getCount("tblf_sms_can","email='$to'");
				if($rt_msg_flag==0)
				{
				 mailFunction($adminSenderEmail, $adminSenderName ,$to,$subject,$message);
				 InsertRec("tblf_sms_can", array(
						"email"=>$to,
						"sdate"=>$today
					));

			}
	break;
	case 2:
		$subject=$_STR_SEND_BADWORD_SUB;
		$message=$_STR_SEND_BADWORD_TXT;
						$rt_msg_flag=getCount("tblf_sms_can","badwords='$to'");
				if($rt_msg_flag==0)
				{
				 mailFunction($adminSenderEmail, $adminSenderName ,$to,$subject,$message);
				 InsertRec("tblf_sms_can", array(
						"badwords"=>$to,
						"sdate"=>$today
					));
				}

	break;
}

}
function checkBadWords($msg,$to)
{
//echo $msg;
$rflag=1;
$arr=explode(" ",$msg);
for($i=0;$i<count($arr);$i++)
	{
	$tok=trim($arr[$i]);
	if(!empty($tok))
		{
		$nCnt=getCount("bad_words","word='$tok'");
		if($nCnt>0)
			{
			echo "<br>bad words --- $tok <br>";
			$rflag=-1;
			}
		}
	}
	return $rflag;
}
?>
