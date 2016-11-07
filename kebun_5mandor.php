<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript1.2 src='js/kebun_5mandor.js'></script>
<?php

include('master_mainMenu.php');
$optmandor='<option value=\'\'>'.$_SESSION['lang']['pilihdata'].'</option>';
$str="select karyawanid, namakaryawan from ".$dbname.".datakaryawan
    where lokasitugas like '".$_SESSION['empl']['lokasitugas']."%' and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].") and alokasi = 0
    order by namakaryawan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $optmandor.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan." [".$bar->karyawanid."]</option>";
}

$optkaryawan='<option value=\'\'></option>';
 
OPEN_BOX();
echo"<fieldset>
     <legend>".$_SESSION['lang']['mandor']."</legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['mandor']."</td>
	   <td>: <select onchange=\"pilihmandor();\" id=mandor style='width:200px'>".$optmandor."</select></td>
	 </tr>
	 <tr>
	   <td>".$_SESSION['lang']['karyawan']."</td>
	   <td>
                : <select id=karyawan style='width:200px'>".$optkaryawan."</select>
                ".$_SESSION['lang']['nourut']." 
                <input type=text class=myinputtext onkeypress=\"return angka_doang(event);\" id=urut size=3 maxlength=3 class=myinputtextnumber>
                <button class=mybutton onclick=tambahkaryawan()>".$_SESSION['lang']['save']."</button>
           </td>
	 </tr>
	 <tr>
	   <td></td>
	   <td><div id=anggota></td>
	 </tr>
	 </table>
     </fieldset>";
CLOSE_BOX();

OPEN_BOX();
//$str="select * from ".$dbname.".kebun_5mandor";
//$res=mysql_query($str);
echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>".$_SESSION['lang']['mandor']."</td>
	   <td>".$_SESSION['lang']['action']."</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
echo"<script>tampilmandor()</script>";
echo"</tbody>
     <tfoot>
     </tfoot>
     </table></fieldset>";
CLOSE_BOX();
echo close_body();
?>