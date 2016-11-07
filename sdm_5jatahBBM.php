<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>

<script language=javascript1.2 src=js/sdm_jatahBBM.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['jatahbbm']);
$str="select a.karyawanid, a.namakaryawan,a.kodegolongan,b.jatah from ".$dbname.".datakaryawan a
      left join ".$dbname.".sdm_5jatahbbm b
	  on a.karyawanid=b.karyawanid
	  where a.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
  
$res=mysql_query($str);
echo mysql_error($conn);
echo"<table class=sortable cellspacing=1 border=0>
     <thead>
	   <tr class=rowheader>
	   <td>No.</td>
	   <td>nama</td>
	   <td>Golongan</td>
	   <td>jatah</td>
	   <td></td>
	   </tr>
	 </thead>	  
	 <tbody>";
	 $no=0;
while($bar=mysql_fetch_object($res))
{
	$no+=1;
	echo"<tr class=rowcontent>
	     <td>".$no."</td>
		 <td>".$bar->namakaryawan."</td>
		 <td>".$bar->kodegolongan."</td>
		 <td><input type=text class=myinputtextnumber id='".$bar->karyawanid."' value='".$bar->jatah."' maxlength=5 onkeypress=\"return angka_doang(event);\" size=8>Ltr.</td>
		 <td><img src=images/save.png class=resicon title='save' onclick=saveJatah('".$bar->karyawanid."')></td>
		 </tr>";
}
		 
echo"</tbody>
      <tfoot>
	  </tfoot>
	  </table>";	 

CLOSE_BOX();
echo close_body();
?>