<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src='js/sdm_5cuti.js'></script>
<?php
OPEN_BOX('',$_SESSION['lang']['cuti']);


//print_r($_SESSION['empl']);

if(trim($_SESSION['empl']['tipelokasitugas'])=='HOLDING')//user holding dapat menempatkan dimana saja
{
    $optlokasitugas.="<option value=''>".$_SESSION['lang']['all']."</option>";	
    $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe not in('BLOK','PT','STENGINE','STATION') 
	      and length(kodeorganisasi)=4 order by namaorganisasi";
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
			$optlokasitugas.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
	}
}
if(trim($_SESSION['empl']['tipelokasitugas'])=='KANWIL')
{
    $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi in (select kodeunit from ".$dbname.".bgt_regional_assignment "
            . " where regional='".$_SESSION['empl']['regional']."') and length(kodeorganisasi)=4 order by namaorganisasi";
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
			$optlokasitugas.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
	}
}


else //user unit hanya dapat menempatkan pada unitnya dan anak unitnya
{
     $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe not in('BLOK','PT','STENGINE','STATION') 
	      and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi";
	$res=mysql_query($str);
	#echo mysql_error($conn);
	while($bar=mysql_fetch_object($res))
	{
			$optlokasitugas.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
	}
}

//echo $str;


$optperiode='';
for($x=-1;$x<3;$x++)
{
	$dt=date('Y')-$x;
	$optperiode.="<option value='".$dt."'>".$dt."</option>";
}


echo"
     <fieldset><legend>".$_SESSION['lang']['navigasi']."</legend>
	   <table>
	      <tr>
		      <td>".$_SESSION['lang']['lokasitugas']."</td><td><select id=lokasitugas>".$optlokasitugas."</select></td>
		      <td>".$_SESSION['lang']['periode']."</td><td><select id=periode>".$optperiode."</select></td>
		      <td><button class=mybutton onclick=\"loadList(document.getElementById('lokasitugas').options[document.getElementById('lokasitugas').selectedIndex].value,document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value)\">".$_SESSION['lang']['lihat']."</button></td>
			  <td><button class=mybutton onclick=prosesAwal()>".$_SESSION['lang']['proses']."</button></td>
		  </tr>	  
	   </table>
	 </fieldset>  
    ";


CLOSE_BOX();
OPEN_BOX('','');
$arr[0]="<div id=containerlist1 style='width:970px;height:350px;overflow:scroll'>
      </div>";
$arr[1]="<div id=containerlist2 style='width:970px;height:350px;overflow:scroll'>
      </div>";	  
$hfrm[0]=$_SESSION['lang']['header'];
$hfrm[1]=$_SESSION['lang']['detail'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$arr,100,970);	  
CLOSE_BOX();
echo close_body();
?>