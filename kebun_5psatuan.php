<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript1.2 src='js/kebun_5psatuan.js'></script>
<?php
	$optkegiatan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	if($_SESSION['language']=='ID'){
                $fild="namaakun";
		$i="select * from ".$dbname.".setup_kegiatan where kelompok in ('BBT','TB','TBM','TM','PNN') order by namakegiatan asc";
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$optkegiatan.="<option value='".$d['kodekegiatan']."'>".$d['namakegiatan']." - ".$d['kodekegiatan']." - ".$d['kelompok']."</option>";
		}
	}
	else{
            $fild="namaakun1";
		$i="select * from ".$dbname.".setup_kegiatan where kelompok in ('BBT','TB','TBM','TM','PNN') order by namakegiatan1 asc";
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$optkegiatan.="<option value='".$d['kodekegiatan']."'>".$d['namakegiatan1']." - ".$d['kodekegiatan']." - ".$d['kelompok']."</option>";
		}
	}
        $optkegiatan2.="<option value=''>".$_SESSION['lang']['all']."</option>";
        $sNoakun="select distinct * from ".$dbname.".setup_kegiatan
                  where kelompok in ('BBT','TB','TBM','TM','PNN') order by noakun asc";
        $qNoakun=  mysql_query($sNoakun) or die(mysql_error($conn));
        while($rNoakun=  mysql_fetch_assoc($qNoakun)){
           $optkegiatan2.="<option value='".$rNoakun['kodekegiatan']."'>".$rNoakun['kodekegiatan']." - ".$rNoakun['namakegiatan1']." - ".$rNoakun['kelompok']."</option>";
        }
        
?>


<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
		if($_SESSION['language']=='ID')
		echo"<legend>Tarif Satuan Pekerjaan Non Panen</legend>";
		else
		echo"<legend>Premi non Harvesting</legend>";
		
			echo"<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['regional']."</td>
					<td>:</td>
					<td><input type=text maxlength=4 id=regional onkeypress=\"return_tanpa_kutip(event);\" disabled value='".$_SESSION['empl']['regional']."' class=myinputtext style=\"width:150px;\"></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['kodekegiatan']."</td> 
					<td>:</td>
					<td><select id=kdkegiatan style=\"width:150px;\">".$optkegiatan."</select></td>
				</tr>

				<tr>
					<td>".$_SESSION['lang']['biaya']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=rp onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['insentif']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=insen onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['konversi']."</td>
					<td>:</td>
					<td><input type=checkbox id=konversi value=0 /></td>
				</tr>
				<tr><td colspan=2></td>
					<td colspan=3>
						<button class=mybutton onclick=simpan()>Simpan</button>
						<button class=mybutton onclick=cancel()>Hapus</button>
					</td>
				</tr>
			
			</table></fieldset>
					<input type=hidden id=method value='insert'>";
echo"<fieldset style='text-align:left;width:650px;'>
<legend><b>[Info]</b></legend>
<p align=justify>Perubahan premi  hanya boleh dilaukan di awal bulan setelah tutup buku bulan sebelumnya dan input data bulan berjalan untuk BKM belum dilakukan. 
</p>";
echo"<fieldset><legend>".$_SESSION['lang']['form']."</legend><table>";
echo"<tr>
     <td>".$_SESSION['lang']['kodekegiatan']."</td>
     <td><select id=kdkegiatanCrPrsn style=\"width:150px;\">".$optkegiatan2."</select></td>
     <td>".$_SESSION['lang']['biaya']."</td>";
echo"<td><input type=text maxlength=8 id=prsnrpCr onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\" />%</td></tr><tr>";
echo"<td>".$_SESSION['lang']['insentif']."</td>";
echo"<td><input type=text maxlength=8 id=prsninsenCr onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\" />%</td>";
echo"<td><button class=mybutton onclick=upGrade()>".$_SESSION['lang']['save']."</button></td></tr>";
echo"</table></fieldset>";


CLOSE_BOX();
?>



<?php
OPEN_BOX();
//$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
//ISI UNTUK DAFTAR 
echo"<fieldset  style=float:left;clear:both;><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['data']."</legend>";
echo"<table>";
echo"<tr><td>".$_SESSION['lang']['kodekegiatan']."</td><td><input type=text id=kdkegiatanCr style=\"width:150px;\" onblur=loadData(0) /></td>";
echo"<td>".$_SESSION['lang']['biaya']."</td>
     <td><input type=text maxlength=8 id=rpCr onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\" onblur=loadData(0) /></td></tr>";
echo"<tr><td>".$_SESSION['lang']['insentif']."</td><td><input type=text maxlength=8 id=insenCr onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"  onblur=loadData(0)/ ></td>";
echo"<td>".$_SESSION['lang']['konversi']."</td>
     <td><input type=checkbox id=konversiCr value=0  onclick=loadData(0) /></td></tr>";
echo"<tr><td colspan=4><button class=mybutton onclick=loadData(0)>".$_SESSION['lang'] ['find']."</button></td></tr>";
echo"</table>";
echo"</fieldset>";
echo "<fieldset style=float:left;clear:both;>
		<legend>".$_SESSION['lang']['list']."</legend>
		<div id=container> 
			<script>loadData(0)</script>
		</div>
	</fieldset>";
CLOSE_BOX();
echo close_body();					
?>