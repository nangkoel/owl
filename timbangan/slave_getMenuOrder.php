<?php
require_once('master_validation.php');
require_once('config/connection.php');

$parent=$_POST['parent'];
$sub=$_POST['sub'];

if($sub=='true')
{
	$str="select * from ".$dbname.".menu 
	      where parent=".$parent." order by urut";
}
else
{
	$str="select * from ".$dbname.".menu 
	      where type='master' order by urut";	
}
	$res=mysql_query($str);
	if(mysql_num_rows($res)<1)
	{
		
		echo " Gagal, Menu ini tidak memiliki submenu";
	}
	else
	{
		echo"<br> 
		     Use Up/Down Arrow to move menu item
		     <table width=100% cellspacing=1 border=0 class=data>
             <thead>
		     <tr>
			 <td>Menu.Id</td>
			 <td>Type</td>
			 <td>Caption</td>
			 <td>Action</td>
			 <td>Order</td>
			 <td>Move</td>
			 </tr>
			 </thead><tbody>";
		$max=mysql_num_rows($res);
		$no=0;
		while($bar=mysql_fetch_object($res))
		{
		   $no+=1;
		   if($bar->class=='devider')
		      $bar->caption='----------';
		   echo"<tr class=rowcontent>
		        <td class=firsttd id=orderid".$no.">".$bar->id."</td>
				<td id=ordertype".$no.">".$bar->class."</td>
		        <td id=ordercaption".$no.">".$bar->caption."</td>
				<td id=orderaction".$no.">".$bar->action."</td>
				<td id=orderurut".$no.">".$bar->urut."</td>
				<td>";
		     if($max>1)
		     {		
			  if($no!=$max)
			    echo"<img class=dellicon src=images/menu/arrow_57.gif title='Move down' onclick=change('down','".$no."','".$max."')>&nbsp &nbsp";
			  if($no>1)
			    echo"<img class=dellicon src=images/menu/arrow_58.gif title='Move up' onclick=change('up','".$no."','".$max."')>";
			 }
		  echo"</td></tr>";	 
		}
       echo"</tbody></table>
	        <br>";
	   if($max>1)
	       echo"<input type=button class=mybutton value=Done onclick=closeOrderEditor()> ";
	   echo" <input type=button class=mybutton value=Close onclick=closeOrderEditor()>";
	}
?>
