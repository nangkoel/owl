<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/pabrik_produksi.js></script>
<?php
include('master_mainMenu.php');
$str="select kodeorganisasi from ".$dbname.".organisasi where tipe='PABRIK'
      order by kodeorganisasi";
$res=mysql_query($str);
$optpabrik="<option value=*></option>";
while($bar=mysql_fetch_object($res))
{
	$optpabrik="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi."</option>";
}	  
$arr[0]=date('Y');
$arr[1]=date('Y')-1;
$arr[2]=date('Y')-2;
$optper='';
for($x=0;$x<count($arr);$x++)
{
	$optper.="<option value='".$arr[$x]."'>".$arr[$x]."</option>";
	for($y=12;$y>=1;$y--)
	{
		$optper.="<option value='".$arr[$x]."-".STR_PAD($y,2,0,'STR_PAD_LEFT')."'>".STR_PAD($y,2,0,'STR_PAD_LEFT')."-".$arr[$x]."</option>";
	}
}

OPEN_BOX('',"<b>".$_SESSION['lang']['rprodksiPabrik'].":</b>");
echo "<fieldset style='width:500px'>
      ".$_SESSION['lang']['kodeorganisasi'].":<select id=pabrik>".$optpabrik."</select>
      ".$_SESSION['lang']['periode']."<select id=periode>".$optper."</select>
	  <button class=mybutton onclick=getLaporanPrdPabrik()>".$_SESSION['lang']['ok']."</button>
	 ";

CLOSE_BOX();
OPEN_BOX('','');
echo"<div id=container style='width:100%;height:500px overflow:scroll'>

     </div>"; 
CLOSE_BOX();
close_body();
?>