<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_printlembaran.js></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',$_SESSION['lang']['penghuni']);
$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
      where tipe not in('STENGINE','BLOK','PT','HOLDING','GUDANG','STATION')
	  order by kodeorganisasi";
$res=mysql_query($str);
$optorg.="";
while($bar=mysql_fetch_object($res))
{
	$optorg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}	
  
echo"<fieldset>";  
echo $_SESSION['lang']['kodeorganisasi']."<select id=kodeorg>".$optorg."</select>
    <button class=mybutton onclick=showPrabot()>".$_SESSION['lang']['tampilkan']."</button>";
echo"</fieldset>";
echo"<fieldset>
      <legend>".$_SESSION['lang']['list']."</legend>
	  <table class=sortable border=0 cellspacing=1>
	  		<thead>
	  		 <tr class=rowheader>
			 <td>No</td>
			 <td>".$_SESSION['lang']['kodeorg']."</td>
			 <td>".$_SESSION['lang']['komplek_rmh']."</td>
			 <td>".$_SESSION['lang']['blok']."</td>
			 <td>".$_SESSION['lang']['no_rmh']."</td>
			 <td>".$_SESSION['lang']['tipe']."</td>
			 <td>".$_SESSION['lang']['jumlahasset']."</td>
			 <td></td>
			 </tr>
			</thead>
			<tbody id=container>
			</tbody>
			<tfoot>
			</tfoot>
	  </table>
	  ";
echo"</fieldset>";
CLOSE_BOX();
echo close_body();
?>