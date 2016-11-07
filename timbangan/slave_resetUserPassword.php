<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$uname=trim($_POST['uname']);
$password=$_POST['password'];
$sendmail=$_POST['sendmail'];
$userid=$_POST['userid'];

if($sendmail==1)
	$email=getUserEmail($uname,$userid,$conn);
else
    $email='';
	
	
	$str="update ".$dbname.".user
	      set password=MD5('".$password."'),
		  lastuser='".$_SESSION['standard']['username']."' 
		  where uname='".$uname."'";

   if(mysql_query($str))
   {
		if($email!='')
		{
			$subject='Your Password Has been reset by Administrator';
			$content="Dear ".$uname.",<br><br>
			          <dd>Here is your new password:
					  <table>
					  <tr><td><i>UserName</i></td><td>:".$uname."</td></tr>
					  <tr><td><i>NewPassword</i></td><td>:<b>".$password."</b></td></tr>
					  </table><br>
					  Please maintain your password periodically.
					  <br>
					  Regards,
					  System, at ".date('d-m-YYY H:i:s');
			$from   ='system@'.$_SERVER['HOST'].'.local';
			$to     =$email;	  
			
			if(sendMail($subject,$content,$from,$to))
			{
				echo "\nAn announcement email has been sent to user.";
			}
			else
			{
				echo "\nBut, An announcement email was failed to send.";
			}
		}
	  
   }
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>
