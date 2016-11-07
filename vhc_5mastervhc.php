<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');

echo open_body();
?>

<script language=javascript1.2 src='js/vhc.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['datamesinkendaraan']."</b>");

//get enum untuk kelompok vhc;
	$optklvhc="<option value=''></option>";
	$arrklvhc=getEnum($dbname,'vhc_5master','kelompokvhc');
	foreach($arrklvhc as $kei=>$fal)
	{
		switch($kei)
		{
                                                case 'AB':
                                                     $_SESSION['language']!='EN'?$fal='Alat Berat':$fal='Heavy Equipment';
                                                break;
                                                case 'KD':                            
                                                    $_SESSION['language']!='EN'?$fal='Kendaraan':$fal='Vehicle';
                                                break;
                                                case 'MS':
                                                    $_SESSION['language']!='EN'? $fal='Mesin':$fal='Machinery';
                                                break;		
		}
		$optklvhc.="<option value='".$kei."'>".$fal."</option>";
	} 
//ambil jenis mesin/kendaraan
  $str="select * from ".$dbname.".vhc_5jenisvhc order  by namajenisvhc";
  $res=mysql_query($str);
  $optjnsvhc="<option value=''></option>";;
  while($bar=mysql_fetch_object($res))
  {
  	$optjnsvhc.="<option value='".$bar->jenisvhc."'>".$bar->namajenisvhc."</option>";
  }	 
//=================ambil master barang untuk aset kendaraan (905)

  $str="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kelompokbarang='905' or kelompokbarang='904' order by namabarang";
  $res=mysql_query($str);
  $optbarang='';
  while($bar=mysql_fetch_object($res))
  {
    $optbarang.="<option value='".$bar->kodebarang."'>".$bar->namabarang."</option>";	
  }
#ambil traksi
  $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='TRAKSI' and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi";
  $res=mysql_query($str);
  $opttraksi='';
  while($bar=mysql_fetch_object($res))
  {
    $opttraksi.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
  }  
  
//ambil kode organisasi selain blok dan afdeling
  $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe  in('KANWIL','HOLDING','KEBUN','PABRIK','TRAKSI') 
        and length(kodeorganisasi)=4 order  by namaorganisasi";
  $res=mysql_query($str);
  $optorg="<option value=''></option>";
  while($bar=mysql_fetch_object($res))
  {
  	$optorg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
  }	 
    
$optkepemilikan=" <option value=1>".$_SESSION['lang']['miliksendiri']."</option>
                  <option value=0>".$_SESSION['lang']['sewa']."</option>";
 
echo"<fieldset><table>
    <tr><td>".$_SESSION['lang']['kodekelompok']."</td><td><select id=kelompokvhc onchange=loadJenis(this.options[this.selectedIndex].value)>".$optklvhc."</select></td>
        <td>".$_SESSION['lang']['jenkendabmes']."</td><td><select id=jenisvhc onchange=getList()>".$optjnsvhc."</select></td>
        <td>".$_SESSION['lang']['tglakhirstnk']."</td><td><input type=text class=myinputtext id=tglakhirstnk name=tglakhirstnk onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td></tr>
    <tr><td>".$_SESSION['lang']['kodeorganisasi']."(Owner)</td><td><select id=kodeorg onchange=getList()>".$optorg."</select></td>
        <td>".$_SESSION['lang']['kodenopol']."</td><td><input type=text id=kodevhc size=12 onkeypress=\"return tanpa_kutip_dan_sepasi(event);\" class=myinputtext maxlength=20></td>
        <td>".$_SESSION['lang']['tglakhirkir']."</td><td><input type=text class=myinputtext id=tglakhirkir name=tglakhirkir onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td></tr>
    <tr><td>".$_SESSION['lang']['namabarang']."</td><td><select id=kodebarang onchange style='width:200px'>".$optbarang."</select></td>
        <td>".$_SESSION['lang']['tahunperolehan']."</td><td><input type=text id=tahunperolehan size=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=4></td>
        <td>".$_SESSION['lang']['tglakhirijinbongkar']."</td><td><input type=text class=myinputtext id=tglakhirijinbm name=tglakhirijinbm onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td></tr>
    <tr><td></td><td></td>
        <td>".$_SESSION['lang']['beratkosong']."</td><td><input type=text id=beratkosong size=5 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=5>Kg.</td>
        <td>".$_SESSION['lang']['tglakhirijinangkut']."</td><td><input type=text class=myinputtext id=tglakhirijinang name=tglakhirijinang onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td>
        
</tr>
    <tr><td>".$_SESSION['lang']['nomorrangka']."</td><td><input type=text id=nomorrangka size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtextnumber maxlength=45></td>
        <td>".$_SESSION['lang']['nomormesin']."</td><td><input type=text id=nomormesin size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtextnumber maxlength=45></td>
        <td>".$_SESSION['lang']['kodeorganisasi']."(lokasi)</td><td><select id=kodelokasi>".$optorg."</select></td>            

</tr>
    <tr><td rowspan=2>".$_SESSION['lang']['tmbhDetail']."</td><td rowspan=2><textarea id=detailvhc cols=25 rows=2 onkeypress=\"return tanpa_kutip(event);\" maxlength=255></textarea></td>
        <td valign=top>".$_SESSION['lang']['kepemilikan']."</td><td valign=top><select id=kepemilikan>".$optkepemilikan."</select></td>
    </tr>
    <tr>
        <td>".$_SESSION['lang']['kodetraksi']."</td><td><select id=kodetraksi>".$opttraksi."</select></td>
    </tr>
    </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanMasterVhc()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelMasterVhc()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo "<fieldset><legend>".$_SESSION['lang']['list']."</legend>
    <img onclick=dataKeExcel(event,'vhc_slave_save_vhc_excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
    <div style='width:95%;height:225px;overflow:scroll;'>";
	$str1="select * from ".$dbname.".vhc_5master where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%' 
               order by status desc,kodeorg,kodevhc asc";
	//echo $str1;
        $res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
		  <td>No</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodeorganisasi'])."(owner)</td>		 
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodekelompok'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['jenkendabmes'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodenopol'])."</td>		
                   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['namabarang'])."</td>		
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['tahunperolehan'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['nomormesin'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['detail'])."</td>	   
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kepemilikan'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodetraksi'])."</td>
                   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodeorganisasi'])."(lokasi)</td>		 
                  <td>*</td></tr>
		 </thead>
		 <tbody id=container>";
//        <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['beratkosong'])."</td>
//        <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['nomorrangka'])."</td>
	$no=0;	 
	while($bar1=mysql_fetch_object($res1))
	{
		$no+=1;
		$str="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar1->kodebarang."'";
		$res=mysql_query($str);
		$namabarang='';
		while($bar=mysql_fetch_object($res))
		{
			$namabarang=$bar->namabarang;
		}
		if($bar1->kepemilikan==1)
		{
	      $dptk=$_SESSION['lang']['miliksendiri'];	
		}
		else
		{
			$dptk=$_SESSION['lang']['sewa'];
		}		
                    $sttd="";
                    $sttd="Deactivate";
                    $bgcrcolor="class='rowcontent'";
                if($bar1->status=='0'){
                    $bgcrcolor="bgcolor=orange";
                    $sttd="";
                    $sttd="Actived";
                }
                 $clidt=" style='cursor:pointer' title='".$sttd." ".$bar1->kodevhc."' onclick=deAktif('".$bar1->kodevhc."','".$bar1->status."')";
		echo"<tr ".$bgcrcolor.">
		     <td  ".$clidt."  >".$no."</td>
		     <td  ".$clidt."  >".$bar1->kodeorg."</td>
			 <td  ".$clidt."  >".$bar1->kelompokvhc."</td>				 
			 <td  ".$clidt."  >".$bar1->jenisvhc."</td>			 		
			 <td  ".$clidt."  >".$bar1->kodevhc."</td>
			 <td  ".$clidt."  >".$namabarang."</td>
			 <td  ".$clidt."  >".$bar1->tahunperolehan."</td>
			 <input type=hidden value=".$bar1->beratkosong.">		
			 <input type=hidden value=".$bar1->nomorrangka.">
			 <td  ".$clidt."  >".$bar1->nomormesin."</td> 
			 <td>".$bar1->detailvhc."</td> 	
			 <td  ".$clidt."  >".$dptk."</td>
                         <td  ".$clidt."  >".$bar1->kodetraksi."</td>
                         <td  ".$clidt."  >".$bar1->kodelokasi."</td>
			 <td>
			     <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillMasterField('".$bar1->kodeorg."','".$bar1->kelompokvhc."','".$bar1->jenisvhc."','".$bar1->kodevhc."','".$bar1->beratkosong."',
                                 '".$bar1->nomorrangka."','".$bar1->nomormesin."','".$bar1->tahunperolehan."','".$bar1->kodebarang."','".$bar1->kepemilikan."','".$bar1->kodetraksi."','".tanggalnormal($bar1->tglakhirstnk)."',
                                 '".tanggalnormal($bar1->tglakhirkir)."','".tanggalnormal($bar1->tglakhirijinbm)."','".tanggalnormal($bar1->tglakhirijinang)."','".$bar1->kodelokasi."');\">
			     <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"deleteMasterVhc('".$bar1->kodeorg."','".$bar1->kelompokvhc."','".$bar1->jenisvhc."','".$bar1->kodevhc."');\">
			</td></tr>";
	}	 
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div></fieldset>";

CLOSE_BOX();
echo close_body();
?>