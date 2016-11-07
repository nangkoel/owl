<?php
require_once('master_validation.php');
require_once('config/connection.php');
$uname=$_POST['uname'];

	$str="select * from ".$dbname.".user where uname like '%".$uname."%'";
	$res=mysql_query($str);
	
	if(mysql_num_rows($res)>0)
	{
		echo"<table class=sortable cellspacing=1 border=0 onmousedown=sorttable.makeSortable(this)>
		     <thead>
			   <tr>
			   <td>Uname</td>
			   <td>UserId</td>
			   <td>Status<br>Active/NotActive</td>
			   <td>Delete</td>
			   </tr>
			 </theader>
			 <tbody>";
		while($bar=mysql_fetch_object($res))
		 {
			$opt='';
			if($bar->status==0)
			{
				$opt.="<input type=checkbox id=".$bar->uname." title='Click to activate' onclick=\"setActivate('".$bar->uname."');\">";
			}
			else
			{
				$opt.="<input type=checkbox id=".$bar->uname." checked  title='Click to deActivate' onclick=\"setActivate('".$bar->uname."');\">";
			}
			echo" <tr class=rowcontent id='row".$bar->uname."'>
			      <td class=firsttd>".$bar->uname."</td>
				  <td>".$bar->userid."</td>
				  <td align=center>".$opt."</td>
				  <td align=center>
                  <img class=iconclick src=images/delete1.png  height=14px title='Delete' onclick=delUser('".$bar->uname."','".$bar->userid."')> &nbsp
				  </td>
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
