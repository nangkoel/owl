<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/it_requestResponse.js'></script>
<script>
    tolak="<?php echo $_SESSION['lang']['ditolak'];?>";
    </script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper("Permintaan Layanan IT:").'</b>');

$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optJenis=$optKary;
$sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan where bagian='IT' and alokasi=1 order by namakaryawan asc";
$qKary=mysql_query($sKary) or die(mysql_error($sKary));
while($rKary=mysql_fetch_assoc($qKary))
{
    $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."</option>";
}
		
echo"
     <!--<img onclick=detailExcel(event,'sdm_slave_laporan_ijin_meninggalkan_kantor.php') src=images/excel.jpg class=resicon title='MS.Excel'>-->
     &nbsp;".$_SESSION['lang']['namakaryawan'].": <select id=karyidCari style=width:150px onchange=loadData()>".$optKary."</select>&nbsp;
     
         <button class=mybutton onclick=dtReset()>".$_SESSION['lang']['cancel']."</button>
	 <div style='width:100%;height:600px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0>
	     <thead>
		    <tr>
			  <td align=center>No.</td>
			  <td align=center>".$_SESSION['lang']['tanggal']."</td>
			  <td align=center>".$_SESSION['lang']['namakegiatan']."</td>
			  <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
                          <td align=center>".$_SESSION['lang']['status']." ".$_SESSION['lang']['atasan']."</td>  
                          <td align=center>".$_SESSION['lang']['pelaksana']."</td>
                          <td align=center>".$_SESSION['lang']['saran']."</td>
                          <td align=center>".$_SESSION['lang']['selesai']."</td>
                          <td align=center>".$_SESSION['lang']['view']."</td>  
			</tr>  
		 </thead>
		 <tbody id=container><script>loadData()</script>
		 </tbody>
		 		 
	   </table>
     </div>";
CLOSE_BOX();
close_body();
?>