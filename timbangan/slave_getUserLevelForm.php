<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');

//==================================================================================================================================================================
 echo"<div>
     <fieldset style='width:200px;color:#333399;'>
	 <legend>[Info] Pengaturan Level Pemakai:</legend>
	 Menggunakan Level Akses u/ dapat mengakses menu.
	 Setiap Pemakai dapat mengakses Menu berdasarkan Level Akses yang diberikan.
	 <br>
	 <b>0</b> = <b>anonymous</b>(Pemakai Standard).
	 </fieldset>
	 <input type=button value=Apply class=mybutton onclick=window.location.reload()>
     <input type=button value=Close class=mybutton onclick=\"hideDetailForm('ctr','ctrmenu');hideThis('lab2');\">
	 <hr>";


$opt='<option>0</option>';
for($d=1;$d<25;$d++)
{
	$opt.="<option>".$d."</option>";
}

$str="select * from ".$dbname.".user order by uname";
$res=mysql_query($str);

echo "<table width=100% cellspacing=1 border=0 class=data>
      <thead>
	  <tr><td>Username</td>
	      <td>UID</td>
		  <td>UserStatus</td>
		  <td>Access Level</td>
	  </tr>
	  </thead>
	  <tbody>
	  ";
	while($bar=mysql_fetch_object($res))
	{
	  echo"<tr class=rowcontent>
	         <td class=firsttd>".$bar->uname."</td>
			 <td>".$bar->userid."</td>";
	   if($bar->status==1)
	     echo"<td><font color=#00AA00><b>Active</b></td>";
	   else
	   	 echo"<td>Inactive</td>";

	 echo"	 <td align=right>
			   <select id=\"select".$bar->uname."\" onchange=\"setAccessLevel(this,'".$bar->uname."',this.options[this.selectedIndex].text)\">
			     <option>".$bar->access_level."</option"
				 .$opt."
			   </select>
             </td>
		 </tr>";
	}
echo"</tbody></table><br>";
echo "
<input type=button value=Apply class=mybutton onclick=window.location.reload()>
<input type=button value=Close class=mybutton onclick=\"hideDetailForm('ctr','ctrmenu');hideThis('lab2');\">
<br><br>";
?>
