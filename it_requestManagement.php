<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/it_requestManagement.js'></script>
<script>
    tolak="<?php echo $_SESSION['lang']['ditolak'];?>";
    </script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper("Request management:").'</b>');
$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan where bagian='IT' and alokasi=1 order by namakaryawan asc";
$qKary=mysql_query($sKary) or die(mysql_error($sKary));
while($rKary=mysql_fetch_assoc($qKary))
{
    $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."</option>";
}




echo"".
    $_SESSION['lang']['namakaryawan'].": <select id=karyidCari style=width:150px onchange=loadData()>".$optKary."</select>&nbsp;
    <button class=mybutton onclick=dtReset()>".$_SESSION['lang']['cancel']."</button><div style='width:1180px;display:fixed;'>
       <table class=sortable cellspacing=1 border=0 width=1160px>
	     <thead>
		    <tr>
			  <td align=center style='width:40px;'>".$_SESSION['lang']['nomor']."</td>
			  <td align=center style='width:80px;'>".$_SESSION['lang']['tanggal']."</td>
			  <td align=center style='width:180px;'>".$_SESSION['lang']['namakegiatan']."</td>
			  <td align=center style='width:125px;'>".$_SESSION['lang']['namakaryawan']."</td>
			  <td align=center style='width:125px;'>".$_SESSION['lang']['atasan']."</td>
			  <td align=center style='width:100px;'>".$_SESSION['lang']['status']." ".$_SESSION['lang']['atasan']."</td>
			  <td align=center style='width:80px;'>".$_SESSION['lang']['tanggal']." ".$_SESSION['lang']['atasan']."</td>
			  <td align=center style='width:200px;'>".$_SESSION['lang']['pelaksana']."</td>
			  <td align=center style='width:150px;'>".$_SESSION['lang']['standard']." ".$_SESSION['lang']['jam']."</td>
			  <td align=center style='width:40px;'>".$_SESSION['lang']['view']."</td>
			</tr>  
		 </thead>
		 <tbody>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div><div style='width:1180px;height:420px;overflow:scroll;'>
           <table class=sortable cellspacing=1 border=0 width=1160px>
                 <thead>
                      <tr>
                     </tr>  
                     </thead>
                     <tbody id=container>
                     <script>loadData()</script>
                     </tbody>
                     	 
               </table>
         </div>";
      

CLOSE_BOX();
close_body();
?>