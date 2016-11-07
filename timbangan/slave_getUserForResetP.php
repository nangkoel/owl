<?php
require_once('master_validation.php');
require_once('config/connection.php');
$uname=$_POST['uname'];

	$str="select * from ".$dbname.".user where uname like '%".$uname."%'";
	$res=mysql_query($str);

	if(mysql_num_rows($res)>0)
	{
		echo"<b>Klik Pada Baris yang dipilih <br>untuk menampilkan \" form reset password \".</b><hr>
            <table class=sortable cellspacing=1 border=0 onmousedown=sorttable.makeSortable(this)>
		     <thead>
			   <tr>
			   <td>Uname</td>
			   <td>UserId</td>
			   <td>Status</td>
			   </tr>
			 </theader>
			 <tbody>";
		while($bar=mysql_fetch_object($res))
		 {
			$opt='';
			if($bar->status==0)
			{
				$opt.="<font color=#aa3333>Not Active</font>";
			}
			else
			{
				$opt.="<font color=#00ff00>Active</font>";
			}
			echo" <tr class=rowcontent id='row".$bar->uname."' title='Click to show dialog' style='cursor:pointer;' onclick=\"showDial('".$bar->uname."','".$bar->userid."',event,this);\">
			      <td class=firsttd>".$bar->uname."</td>
				  <td>".$bar->userid."</td>
				  <td align=center>".$opt."</td>
			 </tr>";
	      }
		echo"
			 </tbody>
		    </table>
			";
	}
	else
	{
		echo "No data found..";
	}
?>
