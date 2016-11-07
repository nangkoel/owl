<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/stockOpneme.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();

if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe like 'GUDANG%' and 
          kodeorganisasi like '".substr($_SESSION['empl']['lokasitugas'],0,1)."%' order by namaorganisasi";
} else {
    $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe like 'GUDANG%' and 
          kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'";
}
$res=mysql_query($str);
//echo $str;
while($bar=mysql_fetch_object($res)){
    $optGudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}
echo"<fieldset>
     <legend><b>Stock Opname Adjustment</b></legend>
	 <table>
                    <tr><td>".$_SESSION['lang']['kodeorg']."</td><td>
                    <select id=kodegudang>
                    ".$optGudang."
                    </select></td></tr> 
	 <tr>
	    <td>".$_SESSION['lang']['materialname']."</td><td><span id=kodebarang></span><input type=text id=namadisabled size=50 class=myinputtext disabled>
		<img src=images/search.png class=dellicon title='".$_SESSION['lang']['find']."' onclick=\"searchBarang('".$_SESSION['lang']['findmaterial']."','<fieldset><legend>".$_SESSION['lang']['findmaterial']."</legend>Find<input type=text class=myinputtext id=namabrg><button class=mybutton onclick=findBarang()>Find</button></fieldset><div id=container></div>',event);\">
		</td>
	 </tr> 
                    <tr><td>".$_SESSION['lang']['jumlah']."</td><td><input type=text id=jumlah value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\" size=5><span id=sat></span></td></tr>
                   <tr><td>".$_SESSION['lang']['hargasatuan']."(Rp)</td><td><input type=text id=harga class=myinputtextnumber value=0 onkeypress=\"return angka_doang(event);\" size=12></td></tr>
                    </tr>
	</table>
                   <button class=mybutton onclick=saveAdjustment()>".$_SESSION['lang']['save']."</button>
     </fieldset>";
CLOSE_BOX();
echo close_body();
?>