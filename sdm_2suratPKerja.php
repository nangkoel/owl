<?php //@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src='js/sdm_SPK.js'></script>
<?php
OPEN_BOX();
#kamus tipe karyawan
$str="select id,tipe from ".$dbname.".sdm_5tipekaryawan";
$grr=mysql_query($str);
while($bar=mysql_fetch_object($grr)){
    $tip[$bar->id]=$bar->tipe;
}

// ..lokasi tugas - divisi
if(substr($_SESSION['empl']['lokasitugas'],2,2)=='HO') {
	$sDiv=" select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 order by namaorganisasi";    
} else if($_SESSION['empl']['tipelokasitugas']=='KANWIL'){
	$sDiv=" select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."') order by namaorganisasi";
}else{
    $sDiv=" select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi";    
}

$qDiv=mysql_query($sDiv) or die(mysql_error());
$oDiv="<option value=''>".$_SESSION['lang']['all']."</option>";
while($rDiv=mysql_fetch_object($qDiv)){
    $oDiv.="<option value='".$rDiv->kodeorganisasi."'>".$rDiv->namaorganisasi."</option>";
}

// .. get tipe karyawan
$sTipe="select id,tipe from ".$dbname.".sdm_5tipekaryawan";
$qTipe=mysql_query($sTipe) or die(mysql_error());
$oTipe="<option value=''>".$_SESSION['lang']['all']."</option>";
while($rTipe=mysql_fetch_object($qTipe)){
    $oTipe.="<option value='".$rTipe->id."'>".$rTipe->tipe."</option>";
}

// ..get karyawan
if(substr($_SESSION['empl']['lokasitugas'],2,2)=='HO')
{
  $sKary=" select nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan
       where tanggalkeluar!='0000-00-00' and tipekaryawan=0 order by namakaryawan";	
}
else if($_SESSION['empl']['tipelokasitugas']=='KANWIL')
{
     $sKary=" select nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan
       where tanggalkeluar!='0000-00-00' and left(lokasitugas,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment
       where regional='".$_SESSION['empl']['regional']."') and tipekaryawan in(0,1,2,3,4)";
}
else
{
 $sKary=" select nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan
       where tanggalkeluar!='0000-00-00' and left(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
       and tipekaryawan in(1,2,3,4)
       order by namakaryawan";   
}

	$oKary="<option value=''></option>";
	$qKary=mysql_query($sKary);
	while($rKary=mysql_fetch_object($qKary))
	{
	        $oKary.="<option value='".$rKary->karyawanid."'>".$rKary->nik." | ".$rKary->namakaryawan."</option>";
	}

// .. get karyawan dengan jabatan HRD
if ($_SESSION['empl']['tipelokasitugas']=='HOLDING'){
    if ($_SESSION['empl']['bagian']=='HRD' && $_SESSION['empl']['kodejabatan']=='151'){
        $sHRD="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
              where tipekaryawan=0 and bagian='HRD' and tanggalkeluar='0000-00-00' and 
              karyawanid <>".$_SESSION['standard']['userid']. " and kodejabatan in (21,33) order by namakaryawan";
    } else {
        $sHRD="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
              where tipekaryawan=0 and bagian='HRD' and tanggalkeluar='0000-00-00' and 
              lokasitugas like '%HO' and kodejabatan=151 order by namakaryawan";
    }
} else {
    $sHRD="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
          where tipekaryawan=0 and bagian='HRD' and tanggalkeluar='0000-00-00' and 
          karyawanid <>".$_SESSION['standard']['userid']. " and kodejabatan in (21,33) order by namakaryawan";
}
$qHRD=mysql_query($sHRD) or die(mysql_error());
$oHRD="<option value=''></option>";
while($rHRD=mysql_fetch_object($qHRD))
{
	$oHRD.="<option value='".$rHRD->karyawanid."'>".$rHRD->namakaryawan."</option>";
}


// ..Form
$frm[0].="
     <fieldset>
          <legend>".$_SESSION['lang']['form']."</legend>
     <table>
          <tr> 	 
                <td>
                    <input type=hidden value='insert' id=method>
                    <input type=hidden value='' id=notr>
                    ".$_SESSION['lang']['lokasitugas']."
                </td>
                <td>
                    <select id=lokasitugas onchange=filterK()>".$oDiv."</select>
                </td>
          </tr>
          <tr> 	 
                <td>".$_SESSION['lang']['tipekaryawan']."</td>
                <td>
                    <select id=tipekaryawan onchange=filterK()>".$oTipe."</select>
                </td>
          </tr>          
          <tr> 	 
               <td>".$_SESSION['lang']['karyawan']."</td>
               <td>
                    <select id=karyawanid>".$oKary."</select>
               </td>
          </tr>
          <tr> 	 
               <td>".$_SESSION['lang']['penandatangan']."</td>
               <td>
                    <select id=penandatangan>".$oHRD."</select>
               </td>
          </tr>
          <tr> 	 
                <td>".$_SESSION['lang']['tanggalsurat']."</td>
                <td>
                    <input type=text id=tanggal size=10 maxlength=10 class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this)>
                </td>
          </tr>
     </table>
         <center>
           <button class=mybutton onclick=simpanSPK()>".$_SESSION['lang']['save']."</button>
           <button class=mybutton onclick=window.location.reload()>".$_SESSION['lang']['new']."</button>
         </center>
         </fieldset>";

$frm[1]="<fieldset>
        	<legend>".$_SESSION['lang']['list']."</legend>
          	<fieldset>
          		<legend></legend>
          		".$_SESSION['lang']['caripadanama']."
          		<input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>
          		<button class=mybutton onclick=cariSPK(0)>".$_SESSION['lang']['find']."</button>
          	</fieldset>
          	<table class=sortable cellspacing=1 border=0>
      			<thead>
          			<tr class=rowheader>
			          <td>No.</td>
			          <td>Nomor SKK</td>
			          <td>".$_SESSION['lang']['karyawan']."</td>
			          <td>".$_SESSION['lang']['penandatangan']."</td>
			          <td>".$_SESSION['lang']['dbuat_oleh']."</td>
			          <td>Action</td>
          			</tr>
          		</head>
           		<tbody id=containerlist>
           		</tbody>
           		<tfoot>
           		</tfoot>
           	</table>
         </fieldset>";

$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['list'];

drawTab('FRM',$hfrm,$frm,100,900);
CLOSE_BOX();
echo close_body('');
?>