<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src='js/menusetting.js'></script>
<?php
include('master_mainMenu.php');
/*ambil pengguna */
$str="select a.namauser,b.namakaryawan,b.lokasitugas,c.namajabatan,d.nama from ".$dbname.".user a
      left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid
      left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan
      left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode";
$res=mysql_query($str);
$optPengguna="";
while($bar=mysql_fetch_object($res))
{
    $optPengguna.="<option value='".$bar->namauser."'>".$bar->namauser."-".$bar->lokasitugas."</option>";
}
OPEN_BOX();
echo OPEN_THEME($_SESSION['lang']['privconf'].':');
echo"<fieldset>
     <legend><img src='images/vista_icons_03.png' height=60px style='vertical-align:middle;'>".$_SESSION['lang']['newuser']."</legend> 
	 <table>
	 <tr>
	 <td>".$_SESSION['lang']['newuser']."</td><td>:<select id=pengguna>".$optPengguna."</select></td></tr>
	 <tr><td>".$_SESSION['lang']['copyfrom']."</td><td>:<select id=dari>".$optPengguna."</select></td></tr>
	 <tr><td colspan=2 align=right><button class=mybutton onclick=copyPrivileges()>".$_SESSION['lang']['proses']."</button></td></tr>
	 </tr>
	 </table>
	 </fieldset>
	 ";  
echo CLOSE_THEME();
echo"<div id=container></div>";
CLOSE_BOX();
echo close_body();
?>