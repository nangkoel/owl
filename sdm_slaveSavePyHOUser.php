<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
  $user=$_POST['user'];
  $type  =$_POST['type'];

   $stra="insert into ".$dbname.".sdm_ho_payroll_user
			(uname,type) values('".$user."','".$type."')";
		if(mysql_query($stra))
		{
			$str="select * from ".$dbname.".sdm_ho_payroll_user order by uname";
			$res=mysql_query($str,$conn);
			$no=0;
			while($bar=mysql_fetch_object($res))
			{
				$no+=1;
				echo "<tr class=rowcontent><td class=fisttd>".$no."</td>
				      <td id='uname".$no."'>".$bar->uname."</td>
				      <td>".$bar->type."</td>	
					  <td align=center><img src=images/close.png  height=11px class=dellicon title=Delete  onclick=\"delPyUser('".$bar->uname."')\"></td>  
					  </tr>";
			}			
		}
		else
		{
			echo " Error: ".addslashes(mysql_error($conn));
		} 	
?>
