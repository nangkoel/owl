<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$uname=trim($_POST['uname']);
$password=$_POST['password'];
$sendmail=$_POST['sendmail'];
$userid=$_POST['userid'];

if($sendmail==1)
        $email=getUserEmail($userid,$conn);
else
    $email='';


        $str="update ".$dbname.".user
              set password=MD5('".$password."'),
                  lastuser='".$_SESSION['standard']['username']."' 
                  where namauser='".$uname."'"; 
   if(mysql_query($str))
   {
                if($email!='')
                {
                        $subject='Your Password Has been reset by Administrator';
                        $body="<html><head></head><body>
                            Dear ".$uname.",<br><br>
                                  Here is your new password:
                                          <table>
                                          <tr><td>UserName</td><td>:".$uname."</td></tr>
                                          <tr><td>NewPassword</td><td>:<b>".$password."</b></td></tr>
                                          </table><br>
                                          Please maintain your password periodically.
                                          <br>
                                          Regards,
                                          System, at ".date('d-m-YYY H:i:s')."
                               </body></html>"; 
                            
                                $to     =$email;	  
                               if($to!=''){ 
                                $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;     
                               }
                        if($kirim==1)
                        {
                                echo "\nAn announcement email has been sent to user.";
                        }
                        else
                        {
                                echo "\nBut, An announcement email was failed to send:".$kirim;
                        }
                }

   }
        else
        {
                echo " Gagal,".addslashes(mysql_error($conn));
        }
?>
