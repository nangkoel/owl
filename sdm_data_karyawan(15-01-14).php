<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
echo open_body();
?>

<script language=javascript1.2 src='js/datakaryawan.js'></script>
<?php
include('master_mainMenu.php');
OPEN_BOX('','');
 //ambil regional
$sReg="select distinct regional from ".$dbname.".bgt_regional_assignment where kodeunit='".$_SESSION['empl']['lokasitugas']."'";
$qReg=mysql_query($sReg) or die(mysql_error($conn));
$rReg=mysql_fetch_assoc($qReg);

//lokasi tugas
$optlokasitugas='';
//if(user is under holding)
$saveable='';
$str="select 1=1";
if(trim($_SESSION['empl']['tipelokasitugas'])=='HOLDING')//user holding dapat menempatkan dimana saja
{
    $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe not in('BLOK','PT','STENGINE','STATION') 
              and length(kodeorganisasi)=4 order by namaorganisasi";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $optlokasitugas.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
        }
            $optsubbagian="<option value='0'></option>";

            $stdy="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe not in('PT','BLOK','GUDANG','WORKSHOP','STENGINE')";
            $redy=mysql_query($stdy);
            while($bardy=mysql_fetch_object($redy))
            {
                    $optsubbagian.="<option value='".$bardy->kodeorganisasi."'>".$bardy->namaorganisasi."</option>";
            }	
}
else if(trim($_SESSION['empl']['tipelokasitugas'])=='KANWIL')
{

    $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe not in('BLOK','PT','STENGINE','STATION')
                   and length(kodeorganisasi)=4
          and (kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$rReg['regional']."')
          or induk in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$rReg['regional']."')) 
          order by kodeorganisasi asc";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $optlokasitugas.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
        }
            $optsubbagian="<option value='0'></option>";

            $stdy="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe not in('PT','BLOK','WORKSHOP','STENGINE')
                   and (kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$rReg['regional']."')
                   or induk in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$rReg['regional']."')) 
                   order by kodeorganisasi asc";
            $redy=mysql_query($stdy);
            while($bardy=mysql_fetch_object($redy))
            {
                    $optsubbagian.="<option value='".$bardy->kodeorganisasi."'>".$bardy->namaorganisasi."</option>";
            }	
}
else if(trim($_SESSION['org']['induk']!=''))//user unit hanya dapat menempatkan pada unitnya dan anak unitnya
{

    $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where LENGTH(kodeorganisasi)=4 
        and kodeorganisasi  like '".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi";

    $res=mysql_query($str);

    while($bar=mysql_fetch_object($res))
    {
        $optlokasitugas.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
    }
        $optsubbagian="<option value='0'></option>";
        $stdy="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe in('AFDELING','TRAKSI','GUDANG','WORKSHOP','BIBITAN','STATION','SIPIL') and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'";
        $redy=mysql_query($stdy);
        while($bardy=mysql_fetch_object($redy))
        {
                $optsubbagian.="<option value='".$bardy->kodeorganisasi."'>".$bardy->namaorganisasi."</option>";
        }	
}
else
{
  $saveable='disabled';
  echo"<script>
        alert('You are not authorized');
       </script>";
}

//opt catu

$str="select kode,keterangan from ".$dbname.".sdm_5catuporsi order by kode";
$res=  mysql_query($str);
$optCatu="<option value=0>Tidak dapat catu</option>";
while($bar=mysql_fetch_object($res))
{
    $optCatu.="<option value='".$bar->kode."'>".$bar->kode."-".$bar->keterangan."</option>";
}



//Tipe karyawan
$opttipekaryawan='';
if(trim($_SESSION['empl']['tipelokasitugas'])=='HOLDING')//jika user holding dapat memunculkan pilihan Permanen(staff))
  $str="select * from ".$dbname.".sdm_5tipekaryawan order by tipe";
else//pilihan staff dihilangkan, input data staff hanya dari pusat
  $str="select * from ".$dbname.".sdm_5tipekaryawan where id<>0 order by tipe";

$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
$opttipekaryawan.="<option value='".$bar->id."'>".$bar->tipe."</option>";	
}	
echo"<table>
     <tr valign=moiddle>
         <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>
           <img class=delliconBig src=images/user_add.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
         <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
           <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
         <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
                        echo $_SESSION['lang']['caripadanama'].":<input type=text id=txtsearch size=20 maxlength=30 class=myinputtext>";
                        echo $_SESSION['lang']['lokasitugas'].":<select id=schorg style='width:100px' onchange=changeCaption(this.options[this.selectedIndex].text);><option value=''>".$_SESSION['lang']['all']."</option>".$optsubbagian."</select>";
                        echo $_SESSION['lang']['tipekaryawan'].":<select id=schtipe  style='width:75px' onchange=changeCaption1(this.options[this.selectedIndex].text);><option value=''>".$_SESSION['lang']['all']."</option>".$opttipekaryawan."</select>";
                        echo $_SESSION['lang']['status'].":<select id=schstatus  style='width:75px' onchange=changeCaption1(this.options[this.selectedIndex].text);><option value=''>".$_SESSION['lang']['all']."</option><option value='0000-00-00'>".$_SESSION['lang']['aktif']."</option><option value='*'>".$_SESSION['lang']['tidakaktif']."</select>";
                        echo"<button class=mybutton onclick=cariKaryawan(1)>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
         </table> "; 
CLOSE_BOX();
OPEN_BOX('','');
echo"<div id='frminput'>
    <b>".$_SESSION['lang']['input']." ".$_SESSION['lang']['data']."</b>";
//=================input form
// get sistem penggajian enum
                $optagama='';
                $arragama=getEnum($dbname,'datakaryawan','agama');
                foreach($arragama as $kei=>$fal)
                {
                        $optagama.="<option value='".$kei."'>".$fal."</option>";
                }  
//departemen		  
                $optbagian='';
                $str="select * from ".$dbname.".sdm_5departemen order by kode";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                $optbagian.="<option value='".$bar->kode."'>".$bar->nama."</option>";	
                }	
//jabatan
$optjabatan='';
$str="select * from ".$dbname.".sdm_5jabatan where namajabatan not like '%available' order by namajabatan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
$optjabatan.="<option value='".$bar->kodejabatan."'>".$bar->namajabatan."</option>";	
}	
//golongan
                $optgolongan='';
                $str="select * from ".$dbname.".sdm_5golongan order by kodegolongan";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                $optgolongan.="<option value='".$bar->kodegolongan."'>".$bar->namagolongan."</option>";	
                }	



// Get country list
            $country   =readCountry("./config/country.lst");
                $optCountry='';
            for($x=0;$x<count($country);$x++)
             {
               $optCountry.="<option value='".$country[$x][2]."' >".$country[$x][0]."</option>";
             }
// Get provinsi list
$country   =readCountry("./config/provinsi.lst");
$optProvinsi='';
for($x=0;$x<count($country);$x++)
 {
   $optProvinsi.="<option value='".$country[$x][1]."' >".$country[$x][0]."</option>";
 }	  

// Get status pajak
                $optstatuspajak='';
            $str="select * from ".$dbname.".sdm_5statuspajak kode";  
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                   if($_SESSION['language']=='EN'){
                       switch($bar->kode){
                         case'K0':
                             $bar->nama='Married without children';
                             break;
                         case'K1':
                             $bar->nama='Married 1 children';
                             break;
                         case'K2':
                             $bar->nama='Married 2 children';
                             break;
                         case'K3':
                             $bar->nama='Married 3 children';
                             break;
                         default:
                             $bar->nama='Single';
                             break;                         
                    } 	
                    }
                 $optstatuspajak.="<option value='".$bar->kode."'>".$bar->nama."</option>";    
                }
//get Golongan darah from enum
$optGoldar='';
$arrenum=getEnum($dbname,'datakaryawan','golongandarah');
foreach($arrenum as $key=>$val)
{
        $optGoldar.="<option value='".$key."'>".$val."</option>";
} 	
//kode organisasi harus PT
                $optorganisasi='';
                //if(user is under holding)
                $str="select 1=1";
                if(trim($_SESSION['empl']['tipelokasitugas'])=='HOLDING')//user holding dapat memilih semua PT 
                {
                    $str="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi";
                        $res=mysql_query($str);
                        while($bar=mysql_fetch_object($res))
                        {
                                        $optorganisasi.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
                        }
                }
                else if(trim($_SESSION['empl']['tipelokasitugas'])=='KANWIL')
                {
                    $str="select distinct induk from ".$dbname.".organisasi where kodeorganisasi in (select distinct kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$rReg['regional']."')
                          order by namaorganisasi";
                    //exit("Error:".$str);
                    $res=mysql_query($str);
                    while($bar=mysql_fetch_object($res))
                    {
                        $sNama="select distinct namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$bar->induk."'";
                        $qNama=mysql_query($sNama) or die(mysql_error($conn));
                        $rNama=mysql_fetch_object($qNama);
                                    $optorganisasi.="<option value='".$bar->induk."'>".$rNama->namaorganisasi."</option>";	
                    }
                }
                else if(trim($_SESSION['org']['induk'])!='')//hanya kode PT dari user ybs
                {
                  $optorganisasi="<option value='".trim($_SESSION['org']['kodeorganisasi'])."'>".$_SESSION['org']['namaorganisasi']."</option>";
                }
                else
                {
                }

//===========get jeniskeamin enum
//get Golongan darah from enum
$optJK='';
$arrenum=getEnum($dbname,'datakaryawan','jeniskelamin');
foreach($arrenum as $key=>$val)
{
        $optJK.="<option value='".$key."'>".$val."</option>";
} 	

// get sistem penggajian enum
                $optsisgaji='';
                $arrsgaj=getEnum($dbname,'datakaryawan','sistemgaji');
                foreach($arrsgaj as $kei=>$fal)
                {
                     if($_SESSION['language']=='EN' && $fal=='Harian')
                         $fal='Daily';
                     if($_SESSION['language']=='EN' && $fal=='Bulanan')
                            $fal='Monthly';                      
                     $optsisgaji.="<option value='".$kei."'>".$fal."</option>";
                }  
//Get status perkawinan enum
$optstkawin='';
$arrsstk=getEnum($dbname,'datakaryawan','statusperkawinan');
foreach($arrsstk as $kei=>$fal)
{
        if($_SESSION['language']=='EN' && $fal=='Menikah')
            $fal='Married';
        if($_SESSION['language']=='EN' && $fal=='Janda')
               $fal='Widow';       
        if($_SESSION['language']=='EN' && $fal=='Duda')
               $fal='Widower';      
        if($_SESSION['language']=='EN' && $fal=='Lajang')
               $fal='Single';              
        $optstkawin.="<option value='".$kei."'>".$fal."</option>";
} 
//Get level pendidikan
                $optlvlpendidikan='';
                $str="select * from ".$dbname.".sdm_5pendidikan order by levelpendidikan";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                  $optlvlpendidikan.="<option value='".$bar->levelpendidikan."'>".$bar->kelompok."</option>";	
                }	

//first tab content=======================================================		
$frm[0]="<fieldset><legend>".$_SESSION['lang']['inputdatakaryawan']."</legend>
         <table border=0 cellspacing=1>
                 <tr>
                    <td align=right>".$_SESSION['lang']['nik']."</td><td><input type=text class=myinputtext id=nik size=26 maxlength=10 onkeypress=\"return tanpa_kutip(event);\"></td>
                    <td align=right>".$_SESSION['lang']['employeename']."</td><td><input type=text class=myinputtext id=namakaryawan size=26 maxlength=40 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>
                        <td align=right>".$_SESSION['lang']['tempatlahir']."</td><td><input type=text class=myinputtext id=tempatlahir size=26 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td> 
                 </tr>
                 <tr>
                    <td align=right>".$_SESSION['lang']['tanggallahir']."</td><td><input type=text class=myinputtext id=tanggallahir size=26 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\"><img src=images/obl.png title='Obligatory'></td>
                    <td align=right>".$_SESSION['lang']['jeniskelamin']."</td><td><select id=jeniskelamin  style='width:150px;'>".$optJK."</select></td>
                        <td align=right>".$_SESSION['lang']['agama']."</td><td><select id=agama style='width:150px;'>".$optagama."</select></td> 
                 </tr>
                 <tr>
                    <td align=right>".$_SESSION['lang']['bagian']."</td><td><select id=bagian style='width:150px;'>".$optbagian."</select></td>
                    <td align=right>".$_SESSION['lang']['kodejabatan']."</td><td><select id=kodejabatan style='width:150px;'>".$optjabatan."</select></td>
                        <td align=right>".$_SESSION['lang']['levelname']."</td><td><select id=kodegolongan style='width:150px;'>".$optgolongan."</select></td> 
                 </tr>	
                 <tr>
                    <td align=right>".$_SESSION['lang']['lokasitugas']."</td><td><select id=lokasitugas style='width:150px;'>".$optlokasitugas."</select></td>
                    <td align=right>".$_SESSION['lang']['pt']."</td><td><select id=kodeorganisasi style='width:150px;'>".$optorganisasi."</select></td>
                        <td align=right>".$_SESSION['lang']['tipekaryawan']."</td><td><select id=tipekaryawan style='width:150px;'>".$opttipekaryawan."</select></td> 
                 </tr>	
                 <tr>
                    <td align=right>".$_SESSION['lang']['noktp']."</td><td><input type=text class=myinputtext id=noktp size=26 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>
                    <td align=right>No.".$_SESSION['lang']['passport']."</td><td><input type=text class=myinputtext id=nopassport size=26 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"></td>
                        <td align=right>".$_SESSION['lang']['warganegara']."</td><td><select id=warganegara style='width:150px;'>".$optCountry."</select></td> 
                 </tr>		 	 	 
                 <tr>
                    <td align=right>".$_SESSION['lang']['lokasipenerimaan']."</td><td><select id=lokasipenerimaan style='width:150px;'>".$optProvinsi."</select></td>
                    <td align=right>".$_SESSION['lang']['statuspajak']."</td><td><select id=statuspajak style='width:150px;'>".$optstatuspajak."</select></td>
                        <td align=right>".$_SESSION['lang']['npwp']."</td><td><input type=text id=npwp size=26 maxlength=30 class=myinputtext onkeypress=\"return tanpa_kutip(event);\"></td> 
                 </tr>
                 <tr>
                    <td align=right rowspan=2>".$_SESSION['lang']['alamataktif']."</td><td rowspan=2><textarea id=alamataktif cols=16 rows=2></textarea><img src=images/obl.png title='Obligatory'></td>
                    <td align=right>".$_SESSION['lang']['kota']."</td><td><input type=text class=myinputtext id=kota size=26 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>
                        <td align=right>".$_SESSION['lang']['province']."</td><td><select id=provinsi style='width:150px;'>".$optProvinsi."</select></td> 
                 </tr>		 
                 <tr>
                    <td align=right>".$_SESSION['lang']['kodepos']."</td><td><input type=text class=myinputtext id=kodepos size=26 maxlength=5 onkeypress=\"return angka_doang(event);\"></td>
                        <td align=right>".$_SESSION['lang']['telp']."</td><td><input type=text class=myinputtext id=noteleponrumah size=26 maxlength=15 onkeypress=\"return tanpa_kutip(event);\"></td> 
                 </tr>
                 <tr>
                    <td align=right>".$_SESSION['lang']['nohp']."</td><td><input type=text class=myinputtext id=nohp size=26 maxlength=15 onkeypress=\"return tanpa_kutip(event);\"></td>
                    <td align=right>".$_SESSION['lang']['norekeningbank']."</td><td><input type=text class=myinputtext id=norekeningbank size=26 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"></td>
                        <td align=right>".$_SESSION['lang']['namabank']."</td><td><input type=text class=myinputtext id=namabank size=26 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td> 
                 </tr>
                 <tr>
                    <td align=right>".$_SESSION['lang']['sistemgaji']."</td><td><select id=sistemgaji style='width:150px;'>".$optsisgaji."</select></td>
                    <td align=right>".$_SESSION['lang']['golongandarah']."</td><td><select id=golongandarah style='width:150px;'>".$optGoldar."</select></td>
                        <td align=right>".$_SESSION['lang']['tanggalmasuk']."</td><td><input type=text class=myinputtext id=tanggalmasuk size=26 maxlength=10 onkeypress=\"return false;\" onmousemove=setCalendar(this)><img src=images/obl.png title='Obligatory'></td> 
                 </tr>		 		 
                 <tr>
                    <td align=right>".$_SESSION['lang']['tanggalkeluar']."</td><td><input type=text class=myinputtext id=tanggalkeluar size=26 maxlength=10 onkeypress=\"return false;\" onmousemove=setCalendar(this) onblur=cekKeluar()></td> 
                    <td align=right>".$_SESSION['lang']['statusperkawinan']."</td><td><select id=statusperkawinan style='width:150px;'>".$optstkawin."</select></td>
                    <td align=right>".$_SESSION['lang']['tanggalmenikah']."</td><td><input type=text class=myinputtext id=tanggalmenikah size=26 maxlength=10 onkeypress=\"return false;\" onmousemove=setCalendar(this)></td>
                 </tr>
                 <tr>
                        <td align=right>".$_SESSION['lang']['jumlahanak']."</td><td><input type=text class=myinputtext id=jumlahanak size=26 maxlength=2 onkeypress=\"return angka_doang(event);\"></td> 
                    <td align=right>".$_SESSION['lang']['jumlahtanggungan']."</td><td><input type=text class=myinputtext id=jumlahtanggungan size=26 maxlength=2  onkeypress=\"return angka_doang(event);\"></td>
                        <td align=right>".$_SESSION['lang']['alokasibiaya']."</td><td><select id=alokasi><option value=0>Unit</option><option value=1>".$_SESSION['lang']['ho']."</option></select></td>
                 </tr>
                 <tr>
                        <td align=right>".$_SESSION['lang']['levelpendidikan']."</td><td><select id=levelpendidikan style='width:150px;'>".$optlvlpendidikan."</select></td> 
                    <td align=right>".$_SESSION['lang']['notelepondarurat']."</td><td><input type=text class=myinputtext id=notelepondarurat size=26 maxlength=15  onkeypress=\"return tanpa_kutip(event);\"></td>
                    <td align=right>".$_SESSION['lang']['email']."</td><td><input type=text class=myinputtext id=email onblur=emailCheck(this.value) size=26 maxlength=45  onkeypress=\"return tanpa_kutip(event);\"></td>
                 </tr>
                 <tr>
                            <td align=right>".$_SESSION['lang']['subbagian']."</td><td><select id=subbagian style='width:150px;'>".$optsubbagian."</select></td> 
                    <td align=right>".$_SESSION['lang']['jms']."</td><td><input type=text class=myinputtext id=jms size=26 maxlength=30  onkeypress=\"return tanpa_kutip(event);\"></td>
                    <td align=right></td><td><input type=hidden id=catu style='width:150px;' /></td>
                 </tr>
                 <tr>
                    <td align=right></td><td><input type=hidden id=dptPremi /></td> 
                    <td align=right>&nbsp;</td><td>&nbsp;</td>
                    <td align=right>&nbsp;</td><td>&nbsp;</td>
                 </tr>
                 <input type=hidden id=karyawanid value=''>	
                 <input type=hidden id=method value='insert'>	 
                 </table>
                 </fieldset>
                 <button ".$saveable." class=mybutton onclick=simpanKaryawan()>".$_SESSION['lang']['save']."</button>
                 <button ".$saveable." class=mybutton onclick=cancelDataKaryawan()>".$_SESSION['lang']['new']."</button>
                ";
//Tab experiences=================================
$optbln="<option value=''>".$_SESSION['lang']['bulan']."</option>";
for($x=1;$x<13;$x++)
{
        if($x<10)
           $bln="0".$x;
        else
           $bln=$x;

        $optbln.="<option value='".$bln."'>".$bln."</option>";
}
$optthn="<option value=''>".$_SESSION['lang']['tahun']."</option>";
for($x=0;$x<60;$x++)
{
        $thn=date('Y')-$x;
        $optthn.="<option value='".$thn."'>".$thn."</option>";
}

$frm[1]="<fieldset><legend>".$_SESSION['lang']['pengalamankerja']."</legend>
         <table border=0 cellspacing=1>
                 <tr>
                    <td align=right>".$_SESSION['lang']['orgname']."</td><td><input type=text class=myinputtext id=namaperusahaan size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>
                    <td align=right>".$_SESSION['lang']['bidangusaha']."</td><td><input type=text class=myinputtext id=bidangusaha size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>
                 </tr>
                 <tr>
                    <td align=right>".$_SESSION['lang']['bulanmasuk']."</td><td><select id=blnmasuk style='width:85px;'>".$optbln."</select>-<select id=thnmasuk style='width:85px;'>".$optthn."</select></td>
                    <td align=right>".$_SESSION['lang']['bulankeluar']."</td><td><select id=blnkeluar style='width:85px;'>".$optbln."</select>-<select id=thnkeluar style='width:85px;'>".$optthn."</select></td>
                 </tr>
                 <tr>
                        <td align=right>".$_SESSION['lang']['jabatanterakhir']."</td><td><input type=text class=myinputtext id=pengalamanjabatan size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>
                        <td align=right>".$_SESSION['lang']['bagian']."</td><td><input type=text class=myinputtext id=pengalamanbagian size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>	 
                 </tr>
                 <tr>
                        <td align=right>".$_SESSION['lang']['alamat']."</td><td colspan=3><input type=text class=myinputtext id=pengalamanalamat size=89 maxlength=100 onkeypress=\"return tanpa_kutip(event);\"></td>	 
                 </tr>		 
                 </table>
                 </fieldset>
                 <button id=btncv disabled class=mybutton onclick=simpanPengalaman()>".$_SESSION['lang']['save']."</button>
                <br>
                <div style='width:100%;height:250px;overflow:scroll;'>
                <table class=sortable border=0 cellspacing=1 width=100%>
                        <thead>
                        <tr class=rowheader>
                          <td class=firsttd>No.</td>
                          <td>".$_SESSION['lang']['orgname']."</td>
                          <td>".$_SESSION['lang']['bidangusaha']."</td>
                          <td>".$_SESSION['lang']['bulanmasuk']."</td>
                          <td>".$_SESSION['lang']['bulankeluar']."</td>
                          <td>".$_SESSION['lang']['jabatanterakhir']."</td>
                          <td>".$_SESSION['lang']['bagian']."</td>
                          <td>".$_SESSION['lang']['masakerja']."</td>
                          <td>".$_SESSION['lang']['alamat']."</td>	
                          <td></td>
                        </tr>
                        </thead>
                        <tbody id=container>
                        </tbody>
                        <tfoot>
                        </tfoot>
                </table>
                </div>
                ";
//tab Education History=========================================
//get Pendidikan
$str="select kelompok,levelpendidikan from ".$dbname.".sdm_5pendidikan order by levelpendidikan";
$res=mysql_query($str);
$optpendidikan="";
while($bar=mysql_fetch_object($res))
{
        $optpendidikan.="<option value='".$bar->levelpendidikan."'>".$bar->kelompok."</option>";
} 
$frm[2]="<fieldset><legend>".$_SESSION['lang']['educationentry']."</legend>
         <table border=0 cellspacing=1>
                 <tr>
                    <td align=right>".$_SESSION['lang']['edulevel']."</td><td><select id=levelpendidikan2 style='width:170px;'>".$optpendidikan."</select></td>
                    <td align=right>".$_SESSION['lang']['jurusan']."</td><td><input type=text class=myinputtext id=spesialisasi size=30 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"></td>
                 </tr>
                 <tr>
                    <td align=right>".$_SESSION['lang']['gelar']."</td><td><input type=text class=myinputtext id=gelar size=30 maxlength=20 onkeypress=\"return tanpa_kutip(event);\"></td>
                    <td align=right>".$_SESSION['lang']['tahunlulus']."</td><td><select id=tahunlulus style='width:170px;'>".$optthn."</select></td>
                 </tr>
                 <tr>
                        <td align=right>".$_SESSION['lang']['namasekolah']."</td><td><input type=text class=myinputtext id=namasekolah size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>
                        <td align=right>".$_SESSION['lang']['nilai']."</td><td><input type=text class=myinputtextnumber id=nilai size=30 maxlength=4 onkeypress=\"return angka_doang(event);\"></td>	 
                 </tr>
                 <tr>
                        <td align=right>".$_SESSION['lang']['kota']."</td><td><input type=text class=myinputtext id=pendidikankota size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>
                        <td align=right>".$_SESSION['lang']['keterangan']."</td><td><input type=text class=myinputtextnumber id=pendidikanketerangan size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>	 
                 </tr>	 
                 </table>
                 </fieldset>
                 <button id=btnpendidikan disabled class=mybutton onclick=simpanPendidikan()>".$_SESSION['lang']['save']."</button>
                <br>
                <div style='width:100%;height:250px;overflow:scroll;'>
                <table class=sortable border=0 cellspacing=1 width=100%>
                        <thead>
                        <tr class=rowheader>
                          <td>No.</td>
                          <td>".$_SESSION['lang']['edulevel']."</td>			  
                          <td>".$_SESSION['lang']['namasekolah']."</td>
                          <td>".$_SESSION['lang']['kota']."</td>			  
                          <td>".$_SESSION['lang']['jurusan']."</td>			  
                          <td>".$_SESSION['lang']['tahunlulus']."</td>
                          <td>".$_SESSION['lang']['gelar']."</td>
                          <td>".$_SESSION['lang']['nilai']."</td>
                          <td>".$_SESSION['lang']['keterangan']."</td>	
                          <td></td>
                        </tr>
                        </thead>
                        <tbody id=containerpendidikan>
                        </tbody>
                        <tfoot>
                        </tfoot>
                </table>
                </div>
                ";
 //===Tab Courses & Training====================

$frm[3]="<fieldset><legend>".$_SESSION['lang']['kursus']."</legend>
         <table border=0 cellspacing=1>
                 <tr>
                    <td align=right>".$_SESSION['lang']['jeniskursus']."</td><td><input type=text class=myinputtext id=jenistraining size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>
                    <td align=right>".$_SESSION['lang']['legend']."</td><td><input type=text class=myinputtext id=judultraining size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>
                    <td align=right>".$_SESSION['lang']['biaya']."</td><td>Rp.<input type=text class=myinputtextnumber id=biaya value=0 size=12 maxlength=15 onkeypress=\"return angka_doang(event);\"></td>
                 </tr>
                 <tr>
                    <td align=right>".$_SESSION['lang']['bulanmasuk']."</td><td><select id=trainingblnmulai style='width:85px;'>".$optbln."</select>-<select id=trainingthnmulai style='width:85px;'>".$optthn."</select></td>
                    <td align=right>".$_SESSION['lang']['bulankeluar']."</td><td><select id=trainingblnselesai style='width:85px;'>".$optbln."</select>-<select id=trainingthnselesai style='width:85px;'>".$optthn."</select></td>
                    <td></td><td></td>
                 </tr>
                 <tr>
                        <td align=right>".$_SESSION['lang']['penyelenggara']."</td><td><input type=text class=myinputtext id=penyelenggara size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>
                        <td align=right>".$_SESSION['lang']['sertifikat']."</td><td><select id=sertifikat style='width:170px;'><option value=0>".$_SESSION['lang']['no']."</option><option value=1>".$_SESSION['lang']['yes']."</option></select></td>	 
                        <td></td><td></td>
                 </tr> 
                 </table>
                 </fieldset>
                 <button id=btntraining disabled class=mybutton onclick=simpanTraining()>".$_SESSION['lang']['save']."</button>
                <br>
                <br>
                <div style='width:100%;height:250px;overflow:scroll;'>
                <table class=sortable border=0 cellspacing=1 width=100%>
                        <thead>
                        <tr class=rowheader>
                          <td>No.</td>
                          <td>".$_SESSION['lang']['jeniskursus']."</td>			  
                          <td>".$_SESSION['lang']['legend']."</td>
                          <td>".$_SESSION['lang']['penyelenggara']."</td>			  
                          <td>".$_SESSION['lang']['bulanmasuk']."</td>			  
                          <td>".$_SESSION['lang']['bulankeluar']."</td>
                          <td>".$_SESSION['lang']['sertifikat']."</td>
                          <td>".$_SESSION['lang']['biaya']."</td>    
                          <td></td>
                        </tr>
                        </thead>
                        <tbody id=containertraining>
                        </tbody>
                        <tfoot>
                        </tfoot>
                </table>
                </div>		
                ";
//Tab Keluarga================================ 
//get enum untuk hub keluarga
$opthubk='';
$arrenum=getEnum($dbname,'sdm_karyawankeluarga','hubungankeluarga');
foreach($arrenum as $key=>$val)
{
                   if($_SESSION['language']=='EN'){
                       switch($key){
                         case'Pasangan':
                             $val='Couple';
                             break;
                         case'Anak':
                             $val='Child';
                             break;
                         case'Ibu':
                             $val='Mother';
                             break;
                         case'Bapak':
                             $val='Father';
                             break;
                         case'Adik':
                             $val='Younger brother/sister';
                             break;        
                         case'Kakak':
                             $val='Older brother/sister';
                             break;      
                         case'Ibu Mertua':
                             $val='Monther-in-law';
                             break;   
                         case'Bapak Mertua':
                             $val='Father-in-law';
                             break;   
                         case'Sepupu':
                             $val='Cousin';
                             break;  
                         case'Ponakan':
                             $val='Nephew';
                             break;                                
                         default:
                             $val='Foster child';
                             break;                         
                    } 	
                    }    
        $opthubk.="<option value='".$key."'>".$val."</option>";
} 	
//get enum untuk hub keluarga
                $optstk='';
                $arrenum=getEnum($dbname,'sdm_karyawankeluarga','status');
                foreach($arrenum as $key=>$val)
                {
                    if($_SESSION['language']=='EN' && $val=='Kawin')
                       $val='Married';
                   if($_SESSION['language']=='EN' && ($val=='Bujang' or $val=='Lajang'))
                          $val='Single';                          
                        $optstk.="<option value='".$key."'>".$val."</option>";
                } 
$frm[4]="<fieldset><legend>".$_SESSION['lang']['keluarga']."</legend>
         <table border=0 cellspacing=1>
                 <tr>
                    <td align=right>".$_SESSION['lang']['nama']."</td><td><input type=text class=myinputtext id=keluarganama size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"><img src=images/obl.png title='Obligatory'></td>
                    <td align=right>".$_SESSION['lang']['jeniskelamin']."</td><td><select id=keluargajk  style='width:170px;'>".$optJK."</select></td>
                 </tr>
                 <tr>
                    <td align=right>".$_SESSION['lang']['tempatlahir']."</td><td><input type=text class=myinputtext id=keluargatmplahir size=30 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"></td>
                    <td align=right>".$_SESSION['lang']['tanggallahir']."</td><td><input type=text class=myinputtext id=keluargatgllahir size=30 onmousemove=setCalendar(this.id) size=10 maxlength=10 onkeypress=\"return false;\"></td>
                 </tr>
                 <tr>
                        <td align=right>".$_SESSION['lang']['hubungan']."</td><td><select id=hubungankeluarga  style='width:170px;'>".$opthubk."</select></td>
                        <td align=right>".$_SESSION['lang']['statusperkawinan']."</td><td><select id=keluargastatus style='width:170px;'>".$optstk."</select></td>	 
                 </tr> 
                 <tr>
                        <td align=right>".$_SESSION['lang']['edulevel']."</td><td><select id=keluargapendidikan  style='width:170px;'>".$optpendidikan."</select></td>
                    <td align=right>".$_SESSION['lang']['pekerjaan']."</td><td><input type=text class=myinputtext id=keluargapekerjaan size=30 maxlength=30 onkeypress=\"return tanpa_kutip(event);\"></td>
                 </tr>	
                 <tr>
                    <td align=right>".$_SESSION['lang']['telp']."</td><td><input type=text class=myinputtext id=keluargatelp size=30 maxlength=15 onkeypress=\"return tanpa_kutip(event);\"></td>
                    <td align=right>".$_SESSION['lang']['email']."</td><td><input type=text class=myinputtext id=keluargaemail size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\" onblur=emailCheck(this.value)></td>
                 </tr>			 	 
                 <tr>
                        <td align=right>".$_SESSION['lang']['tanggungan']."</td><td colspan=3><select id=keluargatanggungan style='width:170px;'><option value=0>".$_SESSION['lang']['no']."</option><option value=1>".$_SESSION['lang']['yes']."</option></select></td>	 
                 </tr>
                 </table>
                 <input type=hidden value=insert id=keluargamethod>
                 <input type=hidden value='' id=keluarganomor>
                 </fieldset>
                 <button id=btnkeluarga disabled class=mybutton onclick=simpanKeluarga()>".$_SESSION['lang']['save']."</button>
                 <button  class=mybutton onclick=clearKeluarga()>".$_SESSION['lang']['new']."</button>
                <br>
                <br>
                <div style='width:100%;height:250px;overflow:scroll;'>
                <table class=sortable border=0 cellspacing=1 width=100%>
                        <thead>
                        <tr class=rowheader>
                          <td>No.</td>
                          <td>".$_SESSION['lang']['nama']."</td>			  
                          <td>".$_SESSION['lang']['jeniskelamin']."</td>
                          <td>".$_SESSION['lang']['hubungan']."</td>			  
                          <td>".$_SESSION['lang']['tanggallahir']."</td>			  
                          <td>".$_SESSION['lang']['statusperkawinan']."</td>
                                                  <td>".$_SESSION['lang']['umur']."</td> 
                          <td>".$_SESSION['lang']['edulevel']."</td>
                          <td>".$_SESSION['lang']['pekerjaan']."</td>
                          <td>".$_SESSION['lang']['telp']."</td>
                          <td>".$_SESSION['lang']['email']."</td>
                          <td>".$_SESSION['lang']['tanggungan']."</td>
                          <td></td>
                        </tr>
                        </thead>
                        <tbody id=containerkeluarga>
                        </tbody>
                        <tfoot>
                        </tfoot>
                </table>
                </div>		
                ";
//photo
$frm[5]="<fieldset style='width:155px;height:180px;'>
         <legend>Photo</legend>
         <img src='' id=displayphoto style='width:150;height:175px;'>
                 </fieldset>
                 <fieldset><legend>Upload.Photo (Max.50Kb)</legend>
                 <iframe frameborder=0 width=350px height=70px name=winForm id=winForm src=sdm_form_upload_photo.php>
                 </iframe>
                 </fieldset>
                 <iframe name=frame id=frame  frameborder=0 width=0px height=0px></iframe> 
                 <button id=btnphoto disabled class=mybutton onclick=simpanPhoto()>".$_SESSION['lang']['save']."</button>
                 <button  class=mybutton onclick=cancelPhoto()>".$_SESSION['lang']['cancel']."</button>
                ";

$frm[6]="<fieldset><legend>".$_SESSION['lang']['alamat']."</legend>
         <table border=0 cellspacing=1>
                 <tr>
                    <td align=right rowspan=2>".$_SESSION['lang']['alamat']."</td><td rowspan=2><textarea id=alamatalamat cols=19 rows=2 onkeypress=\"return tanpa_kutip(event);\"></textarea><img src=images/obl.png title='Obligatory'></td>
                    <td align=right>".$_SESSION['lang']['kota']."</td><td><input type=text class=myinputtext id=alamatkota size=30 maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>
                 </tr>
                 <tr>
                        <td align=right>".$_SESSION['lang']['province']."</td><td><select id=alamatprovinsi style='width:170px;'>".$optProvinsi."</select></td> 
                 </tr>
                 <tr>
                    <td align=right>".$_SESSION['lang']['kodepos']."</td><td><input type=text class=myinputtext id=alamatkodepos size=30  maxlength=5 onkeypress=\"return angka_doang(event);\"></td>
                        <td align=right>".$_SESSION['lang']['telp']."</td><td><input type=text class=myinputtext id=alamattelepon size=30  maxlength=15 onkeypress=\"return tanpa_kutip(event);\"></td>	 
                 </tr> 
                 <tr>
                    <td align=right>".$_SESSION['lang']['emplasmen']."</td><td><input type=text class=myinputtext id=alamatemplasement size=30  maxlength=45 onkeypress=\"return tanpa_kutip(event);\"></td>
                        <td align=right>".$_SESSION['lang']['alamataktif']."</td><td colspan=3><select id=alamatstatus  style='width:170px;'><option value='0'>".$_SESSION['lang']['no']."</option><option value='1'>".$_SESSION['lang']['yes']."</option></select></td>
                 </tr>				 	 
                 </table>
                 </fieldset>
                 <button id=btnalamat disabled class=mybutton onclick=simpanAlamat()>".$_SESSION['lang']['save']."</button>
                <br>
                <br>
                <div style='width:100%;height:250px;overflow:scroll;'>
                <table class=sortable border=0 cellspacing=1 width=100%>
                        <thead>
                        <tr class=rowheader>
                          <td>No.</td>
                          <td>".$_SESSION['lang']['alamat']."</td>			  
                          <td>".$_SESSION['lang']['kota']."</td>
                          <td>".$_SESSION['lang']['province']."</td>			  
                          <td>".$_SESSION['lang']['kodepos']."</td>			  
                          <td>".$_SESSION['lang']['emplasmen']."</td>
                          <td>".$_SESSION['lang']['status']."</td>
                          <td></td>
                        </tr>
                        </thead>
                        <tbody id=containeralamat>
                        </tbody>
                        <tfoot>
                        </tfoot>
                </table>
                </div>		
                ";

$hfrm[0]=$_SESSION['lang']['karyawanbaru'];
$hfrm[1]=$_SESSION['lang']['pengalamankerja'];
$hfrm[2]=$_SESSION['lang']['pendidikan'];
$hfrm[3]=$_SESSION['lang']['kursus'];
$hfrm[4]=$_SESSION['lang']['keluarga'];
$hfrm[5]=$_SESSION['lang']['photo'];
$hfrm[6]=$_SESSION['lang']['alamat'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//========================end input form
echo"</div>";
echo"<div id='searchplace' style='display:none;'>".$_SESSION['lang']['daftarkaryawan']." ".$_SESSION['empl']['lokasitugas'].":<span id=cap1></span>-<span id=cap2></span>
     <br>
         <button class=mybutton value=0 onclick=prefDatakaryawan(this,this.value) id=prefbtn>< ".$_SESSION['lang']['pref']." </button> 
         &nbsp 
         <button class=mybutton value=2 onclick=nextDatakaryawan(this,this.value) id=nextbtn> ".$_SESSION['lang']['lanjut']." ></button>
         <table class=sortable border=0 cellspacing=1>
         <thead>
           <tr class=rowheader>
             <td align=center>No.</td>
                 <td align=center>".$_SESSION['lang']['nik']."</td>
                 <td align=center>".$_SESSION['lang']['nama']."</td>
                 <td align=center>".$_SESSION['lang']['functionname']."</td>
                 <td align=center>".$_SESSION['lang']['kodegolongan']."</td>
                 <td align=center>".$_SESSION['lang']['lokasitugas']."</td>
                 <td align=center>".$_SESSION['lang']['pt']."</td>
                 <td align=center>".$_SESSION['lang']['noktp']."</td>
                 <td align=center>".$_SESSION['lang']['pendidikan']."</td>
                 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['statuspajak'])."</td>
                 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['statusperkawinan'])."</td>
                 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['jumlahanak'])."</td>
                 <td align=center>".$_SESSION['lang']['tanggalmasuk']."</td>
                 <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['tipekaryawan'])."</td>
                 <td> </td>
           </tr>
         </thead>
         <tbody id=searchplaceresult>
         </tbody>
         <tfoot>
         </tfoot>  	 
         </table>
     </div>";

CLOSE_BOX();
close_body('');
?>