<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/log_5stokminimum.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX();
//semua pt
$sPt="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi where tipe='PT'";
$qPt=mysql_query($sPt) or die(mysql_error($conn));
while($rPt=mysql_fetch_assoc($qPt))
{
    $optPt.="<option value='".$rPt['kodeorganisasi']."'>".$rPt['namaorganisasi']."</option>";
}
#buat kodegudang
$optGudang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$iGudang="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe in ('GUDANG','GUDANGTEMP') "
        . " order by namaorganisasi asc ";
$nGudang=mysql_query($iGudang) or die (mysql_error($conn));
while($dGudang=mysql_fetch_assoc($nGudang))
{
    $optGudang.="<option value='".$dGudang['kodeorganisasi']."'>".$dGudang['namaorganisasi']."</option>";
}

echo"<fieldset style='float:left;'>";
		if($_SESSION['language']=='ID')
		echo"<legend>Stok Maksimum & Minimum</legend>";
		else
		echo"<legend>Maximum & Minimum Stock</legend>";
		
			echo"<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['gudang']."</td>
					<td>:</td>
                                        <td><select id=kdOrg name=kdOrg style=\"width:200px\">".$optGudang."</select></td>
				</tr>
				<tr>
                                        <td>".$_SESSION['lang']['materialname']."</td>
                                        <td>:</td>
                                        <td><span id=kodebarang></span><input type=text id=namadisabled size=50 class=myinputtext disabled>
                                            <img src=images/search.png class=dellicon title='".$_SESSION['lang']['find']."' onclick=\"searchBarang('".$_SESSION['lang']['findmaterial']."','<fieldset><legend>"
                                                .$_SESSION['lang']['findmaterial']."</legend>Find<input type=text class=myinputtext id=namabrg><button class=mybutton onclick=findBarang()>Find</button></fieldset><div id=containerBarang></div>',event);\">
                                            </td>
				</tr>

				<tr>
					<td>".$_SESSION['lang']['minstok']."</td> 
					<td>:</td>
					<td><input type=text id=minstok onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:150px;\">&nbsp;<span id=sat></span></td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['maxstok']."</td> 
					<td>:</td>
					<td><input type=text id=maxstok onkeypress=\"return angka_doang(event);\" value='' class=myinputtextnumber style=\"width:150px;\">&nbsp;<span id=sat></span></td>
				</tr>
				
				<tr><td colspan=2></td>
					<td colspan=3>
						<button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>
                                                <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
					</td>
				</tr>
			
			</table></fieldset>
					<input type=hidden id=method value='insert'>";


CLOSE_BOX();
?>



<?php
OPEN_BOX();
//ISI UNTUK DAFTAR 
echo "<fieldset>
		<legend>".$_SESSION['lang']['list']."</legend>
                <fieldset style='width:800px;'>
                <legend><b>".$_SESSION['lang']['find']."</b></legend>
                Gudang <select id=optcari  onchange=loadData()>".$optGudang."</select>
                Nama Barang <input title='Enter untuk mencari' type=text id=txtcari class=myinputtext size=40 onkeypress=\"return carinmbarang(event);\" maxlength=30>
                </fieldset>
		<div id=container> 
			<script>loadData('','')</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();					
?>