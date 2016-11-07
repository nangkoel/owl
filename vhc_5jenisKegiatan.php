<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript1.2 src='js/vhc_5jenisKegiatan.js'></script>
<?php
	$optkegiatan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	if($_SESSION['language']=='ID'){
            $fild="namaakun";
        }else{
            $fild="namaakun1";
        }
                
		$i="select noakun,$fild from ".$dbname.".keu_5akun where length(noakun)='7'";
		$n=mysql_query($i) or die (mysql_error($conn));/* distinct a.noakun,".$fild." from ".$dbname.".setup_kegiatan a left join 
                    ".$dbname.".keu_5akun b on a.noakun=b.noakun 
                    where kelompok in (select distinct kodeklp from ".$dbname.".setup_klpkegiatan order by kodeklp) order by noakun asc";*/
		while($d=mysql_fetch_assoc($n))
		{
			$optkegiatan.="<option value='".$d['noakun']."'>".$d['noakun']." - ".$d[$fild]."</option>";
		}
		
		
        $optnil.="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	for($nil=0;$nil<9;$nil++){
            $optnil.="<option value='".$nil."'>".$nil."</option>";
        }
?>


<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
		echo"<legend>".$_SESSION['lang']['vhc_kegiatan']."</legend>";
                echo"<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['regional']."</td>
					<td>:</td>
					<td><input type=text maxlength=4 id=regional onkeypress=\"return_tanpa_kutip(event);\" disabled value='".$_SESSION['empl']['regional']."' class=myinputtext style=\"width:150px;\"></td>
				</tr>
                                <tr>
					<td>".$_SESSION['lang']['kodekegiatan']."</td> 
					<td>:</td>
					<td><input type=text maxlength=7 id=kdKegiatan onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>
				</tr>
                                <tr>
					<td>".$_SESSION['lang']['namakegiatan']."</td> 
					<td>:</td>
					<td><input type=text maxlength=45 id=nmKegiatan onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>
				</tr>
                                <tr>
					<td>".$_SESSION['lang']['satuan']."</td> 
					<td>:</td>
					<td><input type=text maxlength=10 id=satuan onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['noakun']."</td> 
					<td>:</td>
					<td><select id=noakun style=\"width:150px;\">".$optkegiatan."</select></td>
				</tr>

				<tr>
					<td>".$_SESSION['lang']['basis']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=basis onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['hargasatuan']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=hrgSatuan onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
				</tr>
                                <tr>
					<td>".$_SESSION['lang']['hargalbhbasis']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=hrgLbhBasis onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
				</tr>   
                                <tr>
					<td>".$_SESSION['lang']['hargaHariMinggu']."</td> 
					<td>:</td>
					<td><input type=text maxlength=8 id=hrgHrMngg onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:150px;\"></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['isiauto']."</td>
					<td>:</td>
					<td><select id=auto style=width:150px>".$optnil."</select></td>
				</tr>
				<tr><td colspan=2></td>
					<td colspan=3>
						<button class=mybutton onclick=simpan()>Simpan</button>
						<button class=mybutton onclick=cancel()>Hapus</button>
					</td>
				</tr>
			
			</table></fieldset>
					<input type=hidden id=method value='insert'>";
echo"<fieldset style='text-align:left;width:350px;float:left;'>
<legend><b>[Info]</b></legend>
<p align=justify>Perubahan premi  hanya boleh dilaukan di awal bulan setelah tutup buku bulan sebelumnya dan input data bulan berjalan untuk BKM belum dilakukan. 
</p>";
echo"<fieldset><legend>".$_SESSION['lang']['form']."</legend><table>";
echo"<tr>
     <td rowspan=5 valign=top>Kenaikan</td>
     <td>".$_SESSION['lang']['basis']."</td>
     <td><input type=text maxlength=8 id=bsisPrsn onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\" />%</td></tr>";
echo"<tr><td>".$_SESSION['lang']['hargasatuan']."</td>";
echo"<td><input type=text maxlength=8 id=hrgStnPrsn onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\" />%</td></tr>";
echo"<tr><td>".$_SESSION['lang']['hargalbhbasis']."</td>";
echo"<td><input type=text maxlength=8 id=hrgLbhBsisPrsn onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\" />%</td></tr>";
echo"<tr><td>".$_SESSION['lang']['hargaHariMinggu']."</td>";
echo"<td><input type=text maxlength=8 id=hrgMnggPrsn onkeypress=\"return angka_doang(event);\"  class=myinputtextnumber style=\"width:100px;\" />%</td></tr>";
echo"<tr><td><button class=mybutton onclick=upGrade()>".$_SESSION['lang']['save']."</button></td></tr>";
echo"</table></fieldset></fieldset>";
echo"<fieldset style=float:left;width:470px;><legend><b>[Info]</b></legend>";
echo"<ol type=a>
    <li>0=((prestasi-basis)*lebihbasis)+(basis*hargasatuan) || prestasi*harga satuan,minggu=prestasi*hargaminggu</li>
    <li>1=prestasi*hargasatuan,minggu=prestasi*hargaminggu</li>
    <li>2= jika MO01 maka UMP+hargalebihbasis per hari</li>
    <li>3=(sum(hargasatuan)- (basis))*2+sum(hargasatuan) || sum(hargasatuan),minggu=prestasi*hargaminggu</li>
    <li>4=tidak mengenal hari sum(hargasatuan)</li>
    <li>6=(prestasi*hargasatuan)+(lebihbasis*hargalebihbasis) tanpa hari minggu</li>
    <li>7=(hargasatuan)+sum(hargaminggu)/jlhbaris</li>
    <li>8=hargasatuan+((prestasi-basis)*hargalebihbasis)+hargaminggu</li></ol>";
echo"</fieldset>";

CLOSE_BOX();
?>



<?php
OPEN_BOX();
//$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
//ISI UNTUK DAFTAR 
echo"<fieldset  style=float:left;clear:both;><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['data']."</legend>";
echo"<table>";
echo"<tr><td>".$_SESSION['lang']['namakegiatan']."</td>
     <td><input type=text maxlength=45 id=nmKegiatanCr onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>";
echo"<td>".$_SESSION['lang']['noakun']."</td>
     <td><select style=\"width:150px;\" onchange=loadData(0) id=noakunCr>".$optkegiatan."</select></td>
     <td>".$_SESSION['lang']['isiauto']."</td>
     <td><select style=\"width:150px;\" onchange=loadData(0) id=autoCr>".$optnil."</select></td>
	 <td>".$_SESSION['lang']['satuan']."</td>
     <td><input type=text maxlength=45 id=satuanCr onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>
    </tr>";
echo"<tr><td colspan=4><button class=mybutton onclick=loadData(0)>".$_SESSION['lang']['find']."</button></td></tr>";
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