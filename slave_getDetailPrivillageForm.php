<?php
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('config/connection.php');

//==================================================================================================================================================================
 echo"<div>
     <fieldset style='width:300px;color:#333399;'>
	 <legend>[Info] ".$_SESSION['lang']['userdetailprivsetup'].":</legend>
	 ".$_SESSION['lang']['thisusesdetailpriv']." 
	 </fieldset>
	 <input type=button value='".$_SESSION['lang']['apply']."' class=mybutton onclick=window.location.reload()>
     <input type=button value='".$_SESSION['lang']['close']."' class=mybutton onclick=\"hideDetailForm('ctr','ctrmenu');hideThis('lab3');\">
	 <hr>
	 	 <font color=#F8800A>".$_SESSION['lang']['clickuser']."..!</font>
	 ";


$opt='<option>0</option>';
for($d=1;$d<25;$d++)
{
	$opt.="<option>".$d."</option>";
}

$str="select a.*,b.namakaryawan,b.lokasitugas,c.namajabatan from ".$dbname.".user a left join
         ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid left join 
         ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan order by a.namauser";
$res=mysql_query($str);

echo "<table width=100% cellspacing=1 border=0 class=data>
      <thead>
	  <tr class=rowheader>
	  <td>No.</td>
	  <td>".$_SESSION['lang']['username']."</td>
	      <td>UID</td>
	      <td>".$_SESSION['lang']['employeename']."</td>
                         <td>".$_SESSION['lang']['lokasitugas']."</td>
                         <td>".$_SESSION['lang']['jabatan']."</td>
                         <td>".$_SESSION['lang']['status']."</td>
	  </tr>	  
	  </thead>
	  <tbody>
	  ";
	$no=0;  
	while($bar=mysql_fetch_object($res))
	{
            $no+=1;
            echo"<tr bgcolor=#DEDEDE class=standardrow onclick=\"setMapUserMenu(event,this,'".$bar->namauser."')\" title='Click to Append menu to user ".$bar->uname."'>
	         <td align=right class=firsttd>".$no."</td>
                        <td>".$bar->namauser."</td>
                        <td>".$bar->karyawanid."</td>
                        <td>".$bar->namakaryawan."</td>
                        <td>".$bar->lokasitugas."</td>
                        <td>".$bar->namajabatan."</td>";
            if($bar->status==1){
                echo"<td><font color=#00AA00><b>".$_SESSION['lang']['active']."</b></td>"; 
            } else {
                echo"<td>".$_SESSION['lang']['inactive']."</td>";
            }
	 echo "</tr>";
	}
echo"</tbody></table><br>";	  		
echo "
<input type=button value='".$_SESSION['lang']['apply']."' class=mybutton onclick=window.location.reload()>
<input type=button value='".$_SESSION['lang']['close']."' class=mybutton onclick=\"hideDetailForm('ctr','ctrmenu');hideThis('lab3');\">
<br><br>";
?>
