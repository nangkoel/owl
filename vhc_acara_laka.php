<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>
<link rel=stylesheet type=text/css href=style/zTable.css>
<script language=javascript1.2 src='js/vhc_acara_laka.js'></script>
<script language=javascript1.2 src='js/zMaster.js'></script>
<?php

    #Karyawan Security
      $str="select a.nik, a.karyawanid , a.namakaryawan  from ".$dbname.".datakaryawan a left join ".$dbname.".sdm_5jabatan b ".
		"on a.kodejabatan=b.kodejabatan left join ".$dbname.".organisasi c on a.lokasitugas=c.kodeorganisasi ".
		"left join ".$dbname.".organisasi d on a.subbagian=d.kodeorganisasi ".
		"left join ".$dbname.".bgt_regional_assignment e on a.lokasitugas=e.kodeunit ".
		" where a.lokasitugas='".$_SESSION['empl']['lokasitugas']."' or ".
		"(e.regional='".$_SESSION['empl']['regional']."' and (c.tipe='TRAKSI' or d.tipe='TRAKSI')) order by a.nik";
      $res=mysql_query($str);
      $optkarsecurity='';
      while($bar=mysql_fetch_object($res))
      {
        $optkarsecurity.="<option value='".$bar->karyawanid."'>".$bar->nik."-".$bar->namakaryawan."</option>";	
      }  

#Karyawan Manager
      // $str="select nik, karyawanid , namakaryawan  from ".$dbname.".datakaryawan where lokasitugas like '%E' order by nik";
      // $res=mysql_query($str);
      $optkarmanager=$optkarsecurity;
      // while($bar=mysql_fetch_object($res))
      // {
        // $optkarmanager.="<option value='".$bar->karyawanid."'>".$bar->nik."-".$bar->namakaryawan."</option>";	
      // }  

    #Karyawan Mekanik
      $str="select nik, karyawanid , namakaryawan  from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by nik";
      $res=mysql_query($str);
      $optkarmekanik='';
      while($bar=mysql_fetch_object($res))
      {
        $optkarmekanik.="<option value='".$bar->karyawanid."'>".$bar->nik."-".$bar->namakaryawan."</option>";	
      }  

    #Karyawan Mekanik
      $str="select nik, karyawanid , namakaryawan  from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by nik";
      $res=mysql_query($str);
      $optkarworkshop='';
      while($bar=mysql_fetch_object($res))
      {
        $optkarworkshop.="<option value='".$bar->karyawanid."'>".$bar->nik."-".$bar->namakaryawan."</option>";	
      }  
      
    #ambil traksi
      $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='TRAKSI' order by namaorganisasi";
      $res=mysql_query($str);
      $opttraksi='';
      while($bar=mysql_fetch_object($res))
      {
        $opttraksi.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
      }  
      
	$optkegiatan="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	if($_SESSION['language']=='ID'){
            $fild="namaakun";
        }else{
            $fild="namaakun1";
        }
                
		$i="select distinct a.noakun,".$fild." from ".$dbname.".setup_kegiatan a left join 
                    ".$dbname.".keu_5akun b on a.noakun=b.noakun 
                    where kelompok in (select distinct kodeklp from ".$dbname.".setup_klpkegiatan order by kodeklp) order by noakun asc";
		$n=mysql_query($i) or die (mysql_error($conn));
		while($d=mysql_fetch_assoc($n))
		{
			$optkegiatan.="<option value='".$d['noakun']."'>".$d['noakun']." - ".$d[$fild]."</option>";
		}
	
?>


<?php
OPEN_BOX();
//print_r($_SESSION['empl']['regional']);
echo"<fieldset style='float:left;'>";
		echo"<legend>".$_SESSION['lang']['vhc_kegiatan']."</legend>";
                echo"<table border=0 cellpadding=1 cellspacing=1>
				<tr>
					<td>".$_SESSION['lang']['notransaksi']."</td>
					<td>:</td>
                                        <td><input type=text class=myinputtext id=notransaksi disabled style=width:150px; /></td></tr>
					
				</tr>
                    
				<tr>
					<td>".$_SESSION['lang']['tanggal']."</td>
					<td>:</td>
                                        <td><input type=text class=myinputtext id=tanggal disabled name=tanggal onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td></tr>
					
				</tr>
                                <tr>
					<td>".$_SESSION['lang']['kodetraksi']."</td> 
					<td>:</td>
                                        <td><select id=kodetraksi disabled name=kodetraksi style=width:150px; onchange=\"get_kd('','')\"><option value=>".$_SESSION['lang']['all']."</option>".$opttraksi."</select></td>
                                            
				</tr>
                                <tr>
					<td>".$_SESSION['lang']['kendaraan']."</td> 
					<td>:</td>
                                        <!--<td><select id=kde_vhc name=kde_vhc style=width:150px;><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td>-->
                                        <td><select id=kde_vhc disabled name=kde_vhc style=width:150px; onchange=\"get_kendaraan('','','','','','')\"><option value=>".$_SESSION['lang']['all']."</option>".$_SESSION['lang']['pilihdata']."</select></td>
				</tr>
                                <tr>
					<td>".$_SESSION['lang']['operator']."</td> 
					<td>:</td>
                                        <td><select id=operator disabled name=operator style=width:150px;><option value=>".$_SESSION['lang']['all']."</option>".$_SESSION['lang']['pilihdata']."</select></td>
                                </tr>
				<tr>
					<td>".$_SESSION['lang']['security']."</td> 
					<td>:</td>
                                        <!--//<td><input type=text maxlength=45 id=security disabled onkeypress=\"return tanpa_kutip(event);\"  class=myinputtext style=\"width:150px;\"></td>-->
                                        <td><select id=security disabled style=\"width:150px;\">".$optkarsecurity."</select></td>
                                        
                                </tr>

				<tr>
					<td>".$_SESSION['lang']['mekanik']."</td> 
					<td>:</td>
                                        <td><select id=karymekanik disabled style=\"width:150px;\">".$optkarmekanik."</select></td>
				</tr>
				
				<tr>
					<td>".$_SESSION['lang']['managerunit']."</td> 
					<td>:</td>
                                        <td><select id=managerunit disabled style=\"width:150px;\">".$optkarmanager."</select></td>
                                </tr>
                                <tr>
					<td>".$_SESSION['lang']['kaworkshop']."</td> 
					<td>:</td>
                                        <td><select id=karyworkshop disabled style=\"width:150px;\">".$optkarworkshop."</select></td>
                                    </tr>   
                                <tr>
					<td>".$_SESSION['lang']['kronologiskejadian']."</td> 
					<td>:</td>
					<td><textarea id=kronologiskejadian disabled onkeypress=\"return tanpa_kutip(event);\"></textarea></td>
				</tr>
				<tr>
					<td>".$_SESSION['lang']['akibatkejadian']."</td>
					<td>:</td>
                                        <td><textarea id=akibatkejadian disabled onkeypress=\"return tanpa_kutip(event);\"></textarea></td>
                                </tr>
				<tr><td colspan=2></td>
					<td colspan=3>
						<button class=mybutton onclick=simpan()>Simpan</button>
                                                <button class=mybutton onclick=new_acara_laka()>New</button>
					</td>
				</tr>
			
			</table></fieldset>
					<input type=hidden id=method value='insert'>
                                        <input type=hidden id=notransaksi value='insert'>";

CLOSE_BOX();
?>



<?php
OPEN_BOX();
//$optTahunBudgetHeader="<option value=''>".$_SESSION['lang']['all']."</option>";
//ISI UNTUK DAFTAR 

echo"<fieldset  style=float:left;clear:both;><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['data']."</legend>";
echo"<table>";
echo"<tr><td>".$_SESSION['lang']['notransaksi']."</td><td><input type=text maxlength=45 id=noTransCr onkeypress=\"return tanpa_kutip(event);\" onkeyup='var key=getKey(event);if(key==13){loadData(0);}' class=myinputtext style=\"width:150px;\"></td>";
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