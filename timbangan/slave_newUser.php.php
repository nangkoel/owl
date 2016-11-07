<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$uname=$_POST['uname'];
$sendmail=$_POST['sendmail'];
$pw=$_POST['pw1'];
$userid=$_POST['userid'];
$active=$_POST['active'];

//Has the password

$hpw=MD5($pw);

if($sendmail==1)
	$email=getUserEmail($uname,$userid,$conn);
else
    $email='';
if($active==1)
   $ac_comment='Active';
else
   $ac_comment='Inactive';   	

	$str="insert into ".$dbname.".user (uname,password,userid,lastuser,status)
	      values('".$uname."','".$hpw."',".$userid.",'".$_SESSION['username']."',".$active.")";
	if(mysql_query($str))
	{
		$str1='update '.$dbname.'.user_empl set account=1 where userid='.$userid;
		mysql_query($str1);
		echo "*Account ".$uname." has been created.<br>";
		//if email is available then send an email to user
		if($email!='')
		{
			$subject='Your User Account has been created';
			$content="Dear ".$uname.",<br><br>
			          <dd>Your Account has been created as follow:
					  <table>
					  <tr><td><i>UserName</i></td><td>:".$uname."</td></tr>
					  <tr><td><i>Password</i></td><td>:".$pw."</td></tr>
					  <tr><td><i>UserId(Empl.ID)</i></td><td>:".$userid."</td></tr>
					  <tr><td><i>AccountStatus</i></td><td>:".$ac_comment."</td></tr>
					  </table><br>
					  Please maintain your password periodically.
					  <br>
					  Regards,
					  System, at ".date('d-m-YYY H:i:s');
			$from   ='system@'.$_SERVER['HOST'].'.local';
			$to     =$email;	  
			
			if(sendMail($subject,$content,$from,$to))
			{
				echo "<dd><font color=green>An announcement email has been sent to user</font><br>";
			}
			else
			{
				echo "<dd><font color=red>An announcement email was failed.</font><br>";
			}
		}
	}
	else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}
?>
