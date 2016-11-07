<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/kelompok_barang.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['daftargudang']."</b>");
echo"<table class=sortable cellspacing=1 border-0>
     <thead>
	   <tr class=rowheader>
	     <td>No.</td>
		 <td>".$_SESSION['lang']['orgcode']."</td>
		 <td>".$_SESSION['lang']['orgname']."</td>
		 <td>".$_SESSION['lang']['parent']."</td>
		 <td>".$_SESSION['lang']['alamat']."</td>
	   </tr>
	 </thead>
	 <tbody>";
$str="select * from ".$dbname.".organisasi where tipe='GUDANG'";
$res=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res))
{
  $no+=1;
  echo" <tr class=rowcontent>
	     <td>".$no."</td>
		 <td>".$bar->kodeorganisasi."</td>
		 <td>".$bar->kodeorganisasi."</td>
		 <td>".$bar->induk."</td>
		 <td>".$bar->alamat.", ".$bar->wilayahkota.", ".$bar->negara.", ".$bar->kodepos."</td>
	   </tr>";	
}	 
	 
echo"</tbody>
	 <tfoot>
	 </tfoot>
	 </table>
	 ";
CLOSE_BOX();
echo close_body();
?>