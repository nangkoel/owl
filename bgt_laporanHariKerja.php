<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
echo"<fieldset><legend>".$_SESSION['lang']['list']." HKE</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
	   <td>Tahun Budget</td>
	   <td>Jlh.hari.Setahun</td>
	   <td>Jlh.hari.Minggu</td>
	   <td>Jlh.Hari.Libur</td>
	   <td>Jlh.HaliLiburMinggu</td>
	   <td>HK.Effektif</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
	 echo"<script>loadData()</script>";


		$str="select * from ".$dbname.".bgt_hk  order by tahunbudget desc";
		$res=mysql_query($str) or die(mysql_error($conn));
		while($bar=mysql_fetch_assoc($res))
		{
		$a[$bar['tahunbudget']]=intval($bar['harisetahun']);
                $b[$bar['tahunbudget']]=intval($bar['hrminggu']);
                $c[$bar['tahunbudget']]=intval($bar['hrlibur']);
                $d[$bar['tahunbudget']]=intval($bar['hrliburminggu']);
                $hasil[$bar['tahunbudget']]=$a[$bar['tahunbudget']]-(($b[$bar['tahunbudget']]+$c[$bar['tahunbudget']])-$d[$bar['tahunbudget']]);
		$no+=1;	
		echo"<tr class=rowcontent>
		<td>".$no."</td>
		<td align=right>".$bar['tahunbudget']."</td>
		<td align=right>".$bar['harisetahun']."</td>
		<td align=right>".$bar['hrminggu']."</td>
		<td align=right>".$bar['hrlibur']."</td>
		<td align=right>".$bar['hrliburminggu']."</td>
		<td align=right>".$hasil[$bar['tahunbudget']]."</td>
		</tr>";	
		} 
echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";   
CLOSE_BOX();
echo close_body();
?>