<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
require_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src='js/sdm_pengobatan.js'></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?php
OPEN_BOX('',$_SESSION['lang']['adm_peng']);
$optJabatan=makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan');
$optNmRwt=makeOption($dbname,'sdm_5jenisbiayapengobatan','kode,nama');
$optthn="<option value=''>".$_SESSION['lang']['all']."</option>";
for($x=-1;$x<10;$x++)
{
    if($x==0)$qwe='selected = "selected"'; else $qwe='';
	$mk=mktime(0,0,0,1,15,date('Y')-$x);
	$optthn.="<option value='".(date('Y',$mk))."' ".$qwe.">".(date('Y',$mk))."</option>";
}
$optkodeorg="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4' 
    and tipe in ('KEBUN', 'PABRIK', 'KANWIL', 'TRAKSI','HOLDING')  
    order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
//    and tipe in ('kebun', 'pabrik', 'kanwil')
while($rOrg=mysql_fetch_assoc($qOrg))
{
    if(substr($_SESSION['empl']['lokasitugas'],0,4)==$rOrg['kodeorganisasi'])$qwe='selected = "selected"'; else $qwe='';
    $optkodeorg.="<option value=".$rOrg['kodeorganisasi']." ".$qwe.">".$rOrg['namaorganisasi']."</option>";
}

//ambil daftar rumah sakit
$str="select a.*, b.*from ".$dbname.".sdm_pengobatanht a left join
      ".$dbname.".sdm_5rs b on a.rs=b.id 
          order by b.namars";
$res1=mysql_query($str);
//echo $str;
$optrs="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res1))
{
    if($qwe[$bar->namars]=='')
            $optrs.="<option value='".$bar->namars."'>".$bar->namars." [".$bar->kota."]</option>";
            $qwe[$bar->namars]=$bar->namars;
}

#ambil data karyawn
$optKaryawan="<option value=''>Seluruhnya</option>";
$str="Select distinct a.karyawanid,b.namakaryawan,b.lokasitugas from ".$dbname.".sdm_pengobatanht a left join
          ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid order by namakaryawan";
$res=  mysql_query($str);
while($bar=mysql_fetch_object($res))
{
      $optKaryawan.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."[".$bar->lokasitugas."]</option>";
}

if($_SESSION['empl']['tipelokasitugas']!='HOLDING')$lokasi=substr($_SESSION['empl']['lokasitugas'],0,4); else $lokasi='';

$frm[0].="<fieldset>
    <legend>".$_SESSION['lang']['list']."</legend>
    ".$_SESSION['lang']['thnplafon'].":
    <select id=optplafon onchange=loadPengobatanPrint()>".$optthn."</select>
    <img src=images/excel.jpg onclick=printKlaim() class=resicon><br>
    ".$_SESSION['lang']['kodeorganisasi'].":
    <select id=optkodeorg onchange=loadPengobatanPrint()>".$optkodeorg."</select>
    ".$_SESSION['lang']['rumahsakit'].":
    <select id=optrs onchange=loadPengobatanPrint()>".$optrs."</select>
    <iframe id=frmku frameborder=0 style='width:0px;height:0px;'></iframe>
    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>
    <thead>
    <tr class=rowheader>
        <td width=50></td>
        <td>No</td>
        <td width=100>".$_SESSION['lang']['notransaksi']."</td>
        <td width=50>".$_SESSION['lang']['periode']."</td>
        <td width=30>".$_SESSION['lang']['tanggal']."</td>
        <td width=200>".$_SESSION['lang']['lokasitugas']."</td>
        <td width=200>".$_SESSION['lang']['namakaryawan']."</td>
        <td width=200>".$_SESSION['lang']['jabatan']."</td>
        <td>".$_SESSION['lang']['pasien']."</td>
        <td width=150>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['pasien']."</td>
        <td width=150>".$_SESSION['lang']['rumahsakit']."</td>
        <td width=50>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
        <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>
        <td>".$_SESSION['lang']['dibayar']."</td>
        <td width=90>".$_SESSION['lang']['perusahaan']."</td>
        <td width=90>".$_SESSION['lang']['karyawan']."</td>
        <td width=90>Jamsostek</td>      
        <td>".$_SESSION['lang']['diagnosa']."</td>
        <td>".$_SESSION['lang']['keterangan']."</td>
    </tr>
    </thead>
    
    <tbody id='container'>";


$frm[0].="</tbody>
    <tfoot>
    </tfoot>
    </table></div>
    </fieldset> 	 
    ";	 

//ambil daftar tab 1
$str1="select a.diagnosa, count(*) as kali,d.diagnosa as ketdiag from ".$dbname.".sdm_pengobatanht a 
	  left join ".$dbname.".sdm_5diagnosa d
	  on a.diagnosa=d.id 
          left join ".$dbname.".datakaryawan e
	  on a.karyawanid=e.karyawanid
	  where a.periode like '".date('Y')."%'
	  and e.lokasitugas='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
        group by a.diagnosa order by kali desc
    ";
//echo $str1;
$res1=mysql_query($str1);

$frm[1].="<fieldset>
    <legend>Ranking ".$_SESSION['lang']['diagnosa']."</legend>
    ".$_SESSION['lang']['thnplafon'].":
    <select id=optplafon1 onchange=loadPengobatanPrint1()>".$optthn."</select>
    <img src=images/excel.jpg onclick=printKlaim1() class=resicon><br>
    ".$_SESSION['lang']['kodeorganisasi'].":
    <select id=optkodeorg1 onchange=loadPengobatanPrint1()>".$optkodeorg."</select>
    <iframe id=frmku1 frameborder=0 style='width:0px;height:0px;'></iframe>
    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>
    <thead>
    <tr class=rowheader>
        <td>Rank</td>
        <td>Diagnose</td>
        <td>Number of visit</td>
    </tr>
    </thead>
    <tbody id='container1'>";
//        <td width=50></td>
    $no=0;
    while($bar1=mysql_fetch_object($res1))
    {
        $no+=1;
        $frm[1].="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar1->ketdiag."</td>
            <td align=right>".$bar1->kali."</td>
        </tr>";	  	
//            <td>&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPengobatan1('".$bar->notransaksi."',event)></td>
    }
$frm[1].="</tbody>
    <tfoot>
    </tfoot>
    </table></div>
    </fieldset> 	 
    ";	

//ambil daftar tab 2
$str2="select a.karyawanid, sum(totalklaim) as klaim,d.namakaryawan,d.lokasitugas,d.kodegolongan,
    COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',d.tanggallahir)/365.25,1),0) as umur,kodebiaya
    from ".$dbname.".sdm_pengobatanht a 
	  left join ".$dbname.".datakaryawan d
	  on a.karyawanid=d.karyawanid 
          left join ".$dbname.".datakaryawan e
	  on a.karyawanid=e.karyawanid
	  where a.periode like '".date('Y')."%'
	  and e.lokasitugas='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
        group by a.karyawanid,kodebiaya order by klaim desc
    ";
 
$res2=mysql_query($str2);
while($bar2=mysql_fetch_object($res2)){
    $kdBiaya[$bar2->kodebiaya]=$bar2->kodebiaya;
    $idKary[$bar2->karyawanid]=$bar2->karyawanid;
    $jmlhRp[$bar2->karyawanid.$bar2->kodebiaya]=$bar2->klaim;
    $umurKary[$bar2->karyawanid]=$bar2->umur;
    $nmKary[$bar2->karyawanid]=$bar2->namakaryawan;
    $kdGol[$bar2->karyawanid]=$bar2->kodegolongan;
    $lksiKary[$bar2->karyawanid]=$bar2->lokasitugas;
}

$frm[2].="<fieldset>
    <legend>Ranking  ".$_SESSION['lang']['biaya']."/".$_SESSION['lang']['karyawan']."</legend>
    ".$_SESSION['lang']['thnplafon'].":
    <select id=optplafon2 onchange=loadPengobatanPrint2()>".$optthn."</select>
    <img src=images/excel.jpg onclick=printKlaim2() class=resicon><br>
    ".$_SESSION['lang']['kodeorganisasi'].":
    <select id=optkodeorg2 onchange=loadPengobatanPrint2()>".$optkodeorg."</select>
    <iframe id=frmku2 frameborder=0 style='width:0px;height:0px;'></iframe>
    <div style=\"width:100%;height:350px;overflow:scroll\" id=container2>
    

   <table class=sortable cellspacing=1 border=0>
    <thead>
    <tr class=rowheader>
        <td>Rank</td>
        <td>".$_SESSION['lang']['namakaryawan']."</td>
        <td>".$_SESSION['lang']['kodegolongan']."</td>
        <td>".$_SESSION['lang']['umur']."</td>
        <td>".$_SESSION['lang']['lokasitugas']."</td>";
foreach($kdBiaya as $lsBy){
    $frm[2].="<td>".$optNmRwt[$lsBy]."</td>";
}
            
$frm[2].="<td>".$_SESSION['lang']['total']."</td>
        <td>*</td>
    </tr>
    </thead>
    <tbody>";
//        <td width=50></td>
    $no=0;
    foreach($idKary as $lstKary){
        $no+=1;
        $frm[2].="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$nmKary[$lstKary]."</td>
            <td>".$kdGol[$lstKary]."</td>
            <td>".$umurKary[$lstKary]."(Yrs)</td>
            <td>".$lksiKary[$lstKary]."(Yrs)</td>";
            foreach($kdBiaya as $lsBy){
                $frm[2].="<td align=right>".number_format($jmlhRp[$lstKary.$lsBy])."</td>";
                $total[$lstKary]+=$jmlhRp[$lstKary.$lsBy]; 
                $totPerBy[$lsBy]+=$jmlhRp[$lstKary.$lsBy]; 
            }
            
        $frm[2].="<td align=right>".number_format($total[$lstKary])."</td>
               <td>&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPerorang('".$lstKary."',event)></td>
            </tr>";
       }
    $frm[2].="<tr class=rowcontent>
              <td></td>
               <td colspan=3 align=right>".$_SESSION['lang']['total']."</td>";
               foreach($kdBiaya as $lsBy){
                   $frm[2].="<td align=right>".number_format($totPerBy[$lsBy])."</td>";
                   $totBy+=$totPerBy[$lsBy]; 
               }
     $frm[2].="<td>".  number_format($totBy)."</td>
                <td></td></tr>";       
$frm[2].="</tbody>
    <tfoot>
    </tfoot>
    </table></div>
    </fieldset> 	 
    ";	 

//ambil daftar tab 3
$str3="select a.diagnosa, sum(totalklaim) as klaim,d.diagnosa as ketdiag from ".$dbname.".sdm_pengobatanht a 
	  left join ".$dbname.".sdm_5diagnosa d
	  on a.diagnosa=d.id 
          left join ".$dbname.".datakaryawan e
	  on a.karyawanid=e.karyawanid
	  where a.periode like '".date('Y')."%'
	  and e.lokasitugas='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
        group by a.diagnosa order by klaim desc
    ";
//echo $str2;
$res3=mysql_query($str3);

$frm[3].="<fieldset>
    <legend>Ranking ".$_SESSION['lang']['biaya']."/".$_SESSION['lang']['diagnosa']."</legend>
    ".$_SESSION['lang']['thnplafon'].":
    <select id=optplafon3 onchange=loadPengobatanPrint3()>".$optthn."</select>
    <img src=images/excel.jpg onclick=printKlaim3() class=resicon><br>
    ".$_SESSION['lang']['kodeorganisasi'].":
    <select id=optkodeorg3 onchange=loadPengobatanPrint3()>".$optkodeorg."</select>
    <iframe id=frmku3 frameborder=0 style='width:0px;height:0px;'></iframe>
    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>
    <thead>
    <tr class=rowheader>
        <td>Rank</td>
        <td>Diagnose</td>
        <td>".$_SESSION['lang']['jumlah']."</td>
    </tr>
    </thead>
    <tbody id='container3'>";
//        <td width=50></td>
    $no=0;
    while($bar3=mysql_fetch_object($res3))
    {
        $no+=1;
        $frm[3].="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar3->ketdiag."</td>
            <td align=right>".number_format($bar3->klaim)."</td>
        </tr>";	  	
//            <td>&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPengobatan1('".$bar->notransaksi."',event)></td>
    }
$frm[3].="</tbody>
    <tfoot>
    </tfoot>
    </table></div>
    </fieldset> 	 
    ";	

$frm[4].="<fieldset>
    <legend>Trend ".$_SESSION['lang']['biaya']."</legend>
    ".$_SESSION['lang']['thnplafon'].":
    <select id=optplafon4 onchange=loadPengobatanPrint4()>".$optthn."</select>
    <img src=images/excel.jpg onclick=printKlaim4() class=resicon><br>
    ".$_SESSION['lang']['kodeorganisasi'].":
    <select id=optkodeorg4 onchange=loadPengobatanPrint4()>".$optkodeorg."</select>
    <iframe id=frmku4 frameborder=0 style='width:0px;height:0px;'></iframe>
    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>
    <thead>
    <tr class=rowheader>
        <td>No</td>
        <td>Period</td>
        <td>".$_SESSION['lang']['jumlah']."</td>
    </tr>
    </thead>
    <tbody id='container4'>";
$frm[4].="</tbody>
    <tfoot>
    </tfoot>
    </table></div>
    </fieldset>"; 
$frm[5].="<fieldset>
    <legend>Trend ".$_SESSION['lang']['biaya']."</legend>
    ".$_SESSION['lang']['thnplafon'].":
    <select id=optplafon5 onchange=loadPengobatanPrint5()>".$optthn."</select>
    <img src=images/excel.jpg onclick=printKlaim5() class=resicon><br>
    ".$_SESSION['lang']['nama'].":
    <select id=karyawanid onchange=loadPengobatanPrint5()>".$optKaryawan."</select>
    <iframe id=frmku5 frameborder=0 style='width:0px;height:0px;'></iframe>
    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>
    <thead>
    <tr class=rowheader>
        <td>No</td>
        <td>Period</td>
        <td>".$_SESSION['lang']['biayars']."</td>
        <td>".$_SESSION['lang']['biayadr']."</td>
        <td>".$_SESSION['lang']['biayalab']."</td>
        <td>".$_SESSION['lang']['biayaobat']."</td>
        <td>".$_SESSION['lang']['biayapendaftaran']."</td>
        <td>".$_SESSION['lang']['nilaiklaim']."</td>
        <td>".$_SESSION['lang']['dibayar']."</td>
    </tr>
    </thead>
    <tbody id='container5'>";
$frm[5].="</tbody>
    <tfoot>
    </tfoot>
    </table></div>
    </fieldset>"; 


$frm[6].="<fieldset>
    <legend>Per Jenis Perawatan</legend>
    ".$_SESSION['lang']['thnplafon'].":
    <select id=optplafon6 onchange=loadPengobatanPrint6()>".$optthn."</select>
    <img src=images/excel.jpg onclick=printKlaim6() class=resicon><br>
    ".$_SESSION['lang']['kodeorganisasi'].":
    <select id=optkodeorg6 onchange=loadPengobatanPrint6()>".$optkodeorg."</select>
    <iframe id=frmku6 frameborder=0 style='width:0px;height:0px;'></iframe>
    <div style=\"width:100%;height:350px;overflow:scroll\"><table class=sortable cellspacing=1 border=0>
    <thead>
    <tr class=rowheader>
        <td>No</td>
        <td>".$_SESSION['lang']['kodeorg']."</td>
        <td>".$_SESSION['lang']['tahun']."</td>            
        <td>Treatment Type</td>
        <td  align=center>Jan</td>
        <td  align=center>Feb</td>
        <td  align=center>Mar</td>
        <td  align=center>Apr</td>
        <td  align=center>Mei</td>
        <td  align=center>Jun</td>
        <td  align=center>Jul</td>
        <td  align=center>Aug</td>
        <td  align=center>Sep</td>
        <td  align=center>Oct</td>
        <td  align=center>Nov</td>
        <td  align=center>Dec</td>
        <td>".$_SESSION['lang']['total']."</td>
    </tr>
    </thead>
    <tbody id='container6'>";
$frm[6].="</tbody>
    <tfoot>
    </tfoot>
    </table></div>
    </fieldset>"; 
//========================
$hfrm[0]=$_SESSION['lang']['detail'];
$hfrm[1]="Rank ".$_SESSION['lang']['diagnosa'];
$hfrm[2]="Rank ".$_SESSION['lang']['biaya']."/".$_SESSION['lang']['karyawan'];
$hfrm[3]="Rank ".$_SESSION['lang']['biaya']."/".$_SESSION['lang']['diagnosa'];
$hfrm[4]="Monthly Trend";
$hfrm[5]="By cost type";
$hfrm[6]="By Treatment type";
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================
CLOSE_BOX();
echo close_body();
?>