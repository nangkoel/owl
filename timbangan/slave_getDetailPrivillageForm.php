<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');

//==================================================================================================================================================================
 echo"<div>
     <fieldset style='width:300px;color:#333399;'>
	 <legend>[Info]Pengaturan Detail Hak Akses Pemakai :</legend>
	 Menggunakan Detail Hak Akses u/ setiap Pemakai dan Menu.
     Setiap Pemakai hanya dapat mengakses Menu yang sudah ditentukan.
	 </fieldset>
	 <input type=button value=Apply class=mybutton onclick=window.location.reload()>
     <input type=button value=Close class=mybutton onclick=\"hideDetailForm('ctr','ctrmenu');hideThis('lab3');\">
	 <hr>
	 	 <font color=#F8800A>Klik Kolom Username u/ memberikan Hak Akses..!</font>
	 ";


$opt='<option>0</option>';
for($d=1;$d<25;$d++)
{
	$opt.="<option>".$d."</option>";
}

$str="select * from ".$dbname.".user order by uname";
$res=mysql_query($str);

echo "<table width=100% cellspacing=1 border=0 class=data>
      <thead>
	  <tr class=rowheader>
	  <td>No.</td>
	  <td>UserName</td>
	      <td>UID</td>
	      <td>Status</td>
	  </tr>
	  </thead>
	  <tbody>
	  ";
	$n=0;
	while($bar=mysql_fetch_object($res))
	{
	  $no+=1;
	  echo"<tr bgcolor=#DEDEDE class=standardrow onclick=\"setMapUserMenu(event,this,'".$bar->uname."')\" title='Click to Append menu to user ".$bar->uname."'>
	         <td align=right class=firsttd>".$no."</td>
			 <td>".$bar->uname."</td>
			 <td>".$bar->userid."</td>";
	   if($bar->status==1)
	     echo"<td><font color=#00AA00><b>Active</b></td>";
	   else
	   	 echo"<td>Inactive</td>";
	 echo "</tr>";
	}
echo"</tbody></table><br>";
echo "
<input type=button value=Apply class=mybutton onclick=window.location.reload()>
<input type=button value=Close class=mybutton onclick=\"hideDetailForm('ctr','ctrmenu');hideThis('lab3');\">
<br><br>";
?>
