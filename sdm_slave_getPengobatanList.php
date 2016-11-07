<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$periode=$_POST['periode'];
$kodeorg=$_POST['kodeorg'];
$rs=$_POST['rs'];
$method=$_POST['method'];
$optJabatan=makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan');
$optNmRwt=makeOption($dbname,'sdm_5jenisbiayapengobatan','kode,nama');
if($method==1){
   
    $str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, c.lokasitugas as loktug,c.kodejabatan, nama from ".$dbname.".sdm_pengobatanht a left join
        ".$dbname.".sdm_5rs b on a.rs=b.id 
        left join ".$dbname.".datakaryawan c
        on a.karyawanid=c.karyawanid
        left join ".$dbname.".sdm_5diagnosa d
        on a.diagnosa=d.id
        left join ".$dbname.".sdm_karyawankeluarga f
        on a.ygsakit=f.nomor
        where a.periode like '".$periode."%'
        and c.lokasitugas like '".$kodeorg."%'
        and b.namars like '".$rs."%'
        order by a.updatetime desc, a.tanggal desc";

    //	  and a.kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
   	  
    $res=mysql_query($str);
    $no=0;
    while($bar=mysql_fetch_object($res))
    {
        $no+=1;

        $pasien='';
        //get hubungan keluarga
        $stru="select hubungankeluarga from ".$dbname.".sdm_karyawankeluarga 
              where nomor=".$bar->ygsakit;
        $resu=mysql_query($stru);
        while($baru=mysql_fetch_object($resu))
        {
            $pasien=$baru->hubungankeluarga;
        }
        if($pasien=='')$pasien='AsIs';	

        echo"<tr class=rowcontent>
            <td>&nbsp <img src=images/zoom.png title='view' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)></td>
            <td>".$no."</td>
            <td>".$bar->notransaksi."</td>
            <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
            <td>".tanggalnormal($bar->tanggal)."</td>
            <td>".$bar->loktug."</td>
            <td>".$bar->namakaryawan."</td>
            <td>".$optJabatan[$bar->kodejabatan]."</td>
            <td>".$pasien."</td>
            <td>".$bar->nama."</td>
            <td>".$bar->namars."[".$bar->kota."]"."</td>
            <td>".$bar->kodebiaya."</td>
            <td align=right>".number_format($bar->totalklaim,2,'.',',')."</td>
            <td align=right>".number_format($bar->jlhbayar,2,'.',',')."</td>
            <td align=right>".number_format($bar->bebanperusahaan,2,'.',',')."</td>
            <td align=right>".number_format($bar->bebankaryawan,2,'.',',')."</td>
            <td align=right>".number_format($bar->bebanjamsostek,2,'.',',')."</td>     
            <td>".$bar->ketdiag."</td>
            <td>".$bar->keterangan."</td>
        </tr>";	  	
    }    
}

if($method==2){
    $str1="select a.diagnosa, count(*) as kali,d.diagnosa as ketdiag from ".$dbname.".sdm_pengobatanht a 
              left join ".$dbname.".sdm_5diagnosa d
              on a.diagnosa=d.id 
        left join ".$dbname.".datakaryawan c
        on a.karyawanid=c.karyawanid
              
              where a.periode like '".$periode."%'
              and c.lokasitugas like '".$kodeorg."%'
            group by a.diagnosa order by kali desc
        ";
    $res1=mysql_query($str1);    
    $no=0;
//    echo $str1;
    while($bar1=mysql_fetch_object($res1))
    {
        $no+=1;
        echo"<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar1->ketdiag."</td>
            <td align=right>".$bar1->kali."</td>
        </tr>";	  	
//            <td>&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPengobatan1('".$bar->notransaksi."',event)></td>
    }
}

if($method==3){
   
    $str2="select a.karyawanid, sum(totalklaim) as klaim,d.namakaryawan,d.lokasitugas,d.kodegolongan,
    COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',d.tanggallahir)/365.25,1),0) as umur,kodebiaya
    from ".$dbname.".sdm_pengobatanht a 
	  left join ".$dbname.".datakaryawan d
	  on a.karyawanid=d.karyawanid 
          left join ".$dbname.".datakaryawan e
	  on a.karyawanid=e.karyawanid
	  where a.periode like '".$periode."%'
	  and e.lokasitugas='".$kodeorg."'
        group by a.karyawanid,kodebiaya order by klaim desc";
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
    $no=0;
       echo"<table class=sortable cellspacing=1 border=0>
    <thead>
    <tr class=rowheader>
        <td>Rank</td>
        <td>".$_SESSION['lang']['namakaryawan']."</td>
        <td>".$_SESSION['lang']['kodegolongan']."</td>
        <td>".$_SESSION['lang']['umur']."</td>
        <td>".$_SESSION['lang']['lokasitugas']."</td>";
    foreach($kdBiaya as $lsBy){
        echo"<td>".$optNmRwt[$lsBy]."</td>";
    }
    echo"<td>".$_SESSION['lang']['total']."</td>
        <td>*</td>
    </tr>
    </thead>
    <tbody>";
        foreach($idKary as $lstKary){
        $no+=1;
        echo"<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$nmKary[$lstKary]."</td>
            <td>".$kdGol[$lstKary]."</td>
            <td>".$umurKary[$lstKary]."(Yrs)</td>
            <td>".$lksiKary[$lstKary]."(Yrs)</td>";
            foreach($kdBiaya as $lsBy){
                echo"<td align=right>".number_format($jmlhRp[$lstKary.$lsBy])."</td>";
                $total[$lstKary]+=$jmlhRp[$lstKary.$lsBy]; 
                $totPerBy[$lsBy]+=$jmlhRp[$lstKary.$lsBy]; 
            }
            
        echo"<td align=right>".number_format($total[$lstKary])."</td>
               <td>&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPerorang('".$lstKary."',event)></td>
            </tr>";
       }
    echo"<tr class=rowcontent>
              <td></td>
               <td colspan=3 align=right>".$_SESSION['lang']['total']."</td>";
               foreach($kdBiaya as $lsBy){
                   echo"<td align=right>".number_format($totPerBy[$lsBy])."</td>";
                   $totBy+=$totPerBy[$lsBy]; 
               }
     echo"<td>".  number_format($totBy)."</td>
                <td></td></tr></tbody>
    <tfoot>
    </tfoot>
    </table>";
}

if($method==4){
$str3="select a.diagnosa, sum(jlhbayar) as klaim,d.diagnosa as ketdiag from ".$dbname.".sdm_pengobatanht a 
	  left join ".$dbname.".sdm_5diagnosa d
	  on a.diagnosa=d.id 
        left join ".$dbname.".datakaryawan c
        on a.karyawanid=c.karyawanid
              where a.periode like '".$periode."%'
              and c.lokasitugas like '".$kodeorg."%'
        group by a.diagnosa order by klaim desc
    ";
    $res3=mysql_query($str3);    
    $no=0;
//    echo $str1;
    while($bar3=mysql_fetch_object($res3))
    {
        $no+=1;
        echo"<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar3->ketdiag."</td>
            <td align=right>".number_format($bar3->klaim)."</td>
        </tr>";	  	
//            <td>&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPengobatan1('".$bar->notransaksi."',event)></td>
    }
}
if($method==5){
$str3="select  sum(a.jlhbayar) as klaim,a.periode from ".$dbname.".sdm_pengobatanht a 
        left join ".$dbname.".datakaryawan c
        on a.karyawanid=c.karyawanid
              where a.periode like '".$periode."%'
              and c.lokasitugas like '".$kodeorg."%'
        group by periode order by periode
    ";
    $res3=mysql_query($str3);    
    $no=0;
//    echo $str1;
    while($bar3=mysql_fetch_object($res3))
    {
        $no+=1;
        echo"<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar3->periode."</td>
            <td align=right>".number_format($bar3->klaim)."</td>
        </tr>";	  	
    }
}

if($method==6){
    if($_POST['karyawanid']==''){
    $str3="select  sum(jasars) as rs, 
               sum(jasadr) as dr, sum(jasalab) as lab, 
               sum(byobat) as obat, 
               sum(bypendaftaran) administrasi, 
               a.periode, sum(a.totalklaim) as klaim,sum(a.jlhbayar) as bayar from ".$dbname.".sdm_pengobatanht a 
               left join ".$dbname.".datakaryawan c
               on a.karyawanid=c.karyawanid
              where a.periode like '".$periode."%'
             group by periode order by periode";
}
else
        {
    $str3="select  sum(jasars) as rs, 
               sum(jasadr) as dr, sum(jasalab) as lab, 
               sum(byobat) as obat, 
               sum(bypendaftaran) administrasi, 
               a.periode, sum(a.totalklaim) as klaim,sum(a.jlhbayar) as bayar from ".$dbname.".sdm_pengobatanht a 
               left join ".$dbname.".datakaryawan c
               on a.karyawanid=c.karyawanid
              where a.periode like '".$periode."%'
               and c.karyawanid=".$_POST['karyawanid']."
             group by periode order by periode";    
        }
        
    $res3=mysql_query($str3);    
    $no=0;
   $trs=0;
   $tdr=0;
   $tlb=0;
   $tob=0;
   $tad=0;
   $ttl=0;
    while($bar3=mysql_fetch_object($res3))
    {
        $no+=1;
        echo"<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$bar3->periode."</td>
            <td align=right>".number_format($bar3->rs)."</td>
            <td align=right>".number_format($bar3->dr)."</td>
            <td align=right>".number_format($bar3->lab)."</td>
            <td align=right>".number_format($bar3->obat)."</td>
            <td align=right>".number_format($bar3->administrasi)."</td>
            <td align=right>".number_format($bar3->klaim)."</td>
            <td align=right>".number_format($bar3->bayar)."</td>    
        </tr>";	  
         $trs+=$bar3->rs;
         $tdr+=$bar3->dr;
         $tlb+=$bar3->lab;
         $tob+=$bar3->obat;
         $tad+=$bar3->administrasi;
         $ttl+=$bar3->klaim; 
          $byr+=$bar3->bayar; 
    }
        echo"<tr class=rowcontent>
            <td></td>
            <td>".$_SESSION['lang']['total']."</td>
            <td align=right>".number_format($trs)."</td>
            <td align=right>".number_format($tdr)."</td>
            <td align=right>".number_format($tlb)."</td>
            <td align=right>".number_format($tob)."</td>
            <td align=right>".number_format($tad)."</td>
            <td align=right>".number_format($ttl)."</td>
             <td align=right>".number_format($byr)."</td>   
        </tr>";    
}

if($method==7){
$str3="select  sum(a.jlhbayar) as klaim,a.periode,a.kodebiaya,c.nama from ".$dbname.".sdm_pengobatanht a 
        left join ".$dbname.".sdm_5jenisbiayapengobatan c
        on a.kodebiaya=c.kode
        left join ".$dbname.".datakaryawan b 
        on a.karyawanid=b.karyawanid
              where a.periode like '".$periode."%'
              and b.lokasitugas like '".$kodeorg."%'
        group by kodebiaya,periode order by periode
    ";
    $res3=mysql_query($str3);    
    $no=0;
    while($bar3=mysql_fetch_object($res3))
    {
        $kode[$bar3->kodebiaya][$bar3->periode]=$bar3->klaim;
        $kodex[$bar3->kodebiaya]['nama']=$bar3->nama;
    }
    if(count($kodex)>0){
    foreach($kodex as $key=>$val){
        $no+=1;
        $total=$kode[$key][$periode."-12"]+$kode[$key][$periode."-11"]+$kode[$key][$periode."-10"]+$kode[$key][$periode."-09"]+$kode[$key][$periode."-08"]+$kode[$key][$periode."-07"]+$kode[$key][$periode."-06"]+$kode[$key][$periode."-05"]+$kode[$key][$periode."-04"]+$kode[$key][$periode."-03"]+$kode[$key][$periode."-02"]+$kode[$key][$periode."-01"];
        $gt+=$total;
        echo"<tr class=rowcontent>
            <td>".$no."</td>
            <td>".$kodeorg."</td>
            <td>".$periode."</td>    
            <td>".$kodex[$key]['nama']."</td>                
            <td align=right>".number_format($kode[$key][$periode."-01"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-02"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-03"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-04"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-05"])."</td> 
            <td align=right>".number_format($kode[$key][$periode."-06"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-07"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-08"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-09"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-10"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-11"])."</td>
            <td align=right>".number_format($kode[$key][$periode."-12"])."</td>
            <td align=right>".number_format($total)."</td>    
        </tr>";
        $t01+=$kode[$key][$periode."-01"];
        $t02+=$kode[$key][$periode."-02"];
        $t03+=$kode[$key][$periode."-03"];
        $t04+=$kode[$key][$periode."-04"];
        $t05+=$kode[$key][$periode."-05"];
        $t06+=$kode[$key][$periode."-06"];
        $t07+=$kode[$key][$periode."-07"];
        $t08+=$kode[$key][$periode."-08"];
        $t09+=$kode[$key][$periode."-09"];
        $t10+=$kode[$key][$periode."-10"];
        $t11+=$kode[$key][$periode."-11"];
        $t12+=$kode[$key][$periode."-12"];
    }
    }
        echo"<tr class=rowcontent>
            <td colspan=4>Total</td>                
            <td align=right>".number_format($t01)."</td>
            <td align=right>".number_format($t02)."</td>
            <td align=right>".number_format($t03)."</td>
             <td align=right>".number_format($t04)."</td>
             <td align=right>".number_format($t05)."</td>
             <td align=right>".number_format($t06)."</td>
             <td align=right>".number_format($t07)."</td>
             <td align=right>".number_format($t08)."</td>
             <td align=right>".number_format($t09)."</td>
             <td align=right>".number_format($t10)."</td>
             <td align=right>".number_format($t11)."</td>
             <td align=right>".number_format($t12)."</td>     
            <td align=right>".number_format($gt)."</td>    
        </tr>";   
}
?>
