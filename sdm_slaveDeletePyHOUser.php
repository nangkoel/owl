<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
  $uname=$_POST['uname'];

   $stra="delete from ".$dbname.".sdm_ho_payroll_user where uname='".$uname."'";
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
