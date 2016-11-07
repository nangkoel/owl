<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;
$tmpPeriod = explode('-',$param['periode']);
$tahunbulan = implode("",$tmpPeriod);
if($tmpPeriod[1]==12) {
    $bulanLanjut = 1;
    $tahunLanjut = $tmpPeriod[0]+1;
} else {
    $bulanLanjut = $tmpPeriod[1]+1;
    $tahunLanjut = $tmpPeriod[0];
}

if($_SESSION['language']=='EN'){
    $zz="namaakun1 as namaakun";
}else{
    $zz="namaakun";
}
$nmAkun=makeOption($dbname, "keu_5akun", "noakun,".$zz);
//ambil akun laba tahun berjalan;
$stl="select noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='CLM'";
$rel=mysql_query($stl);
$akunCLM='';
while($bal=mysql_fetch_object($rel))
{
    $akunCLM=$bal->noakundebet;
}
//ambil akun laba ditahan
$stl="select noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='CLY'";
$rel=mysql_query($stl);
$akunCLY='';
while($bal=mysql_fetch_object($rel))
{
    $akunCLY=$bal->noakundebet;
}
//ambil batas bawah akun laba/rugi
$stl="select noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='RAT'";
$rel=mysql_query($stl);
$akunRAT='';
while($bal=mysql_fetch_object($rel))
{
    $akunRAT=$bal->noakundebet;
}
if($akunCLM=='' or $akunCLY=='' or $akunRAT=='')
{
    if($_SESSION['language']=='EN'){
        exit(' Error: Annual income account data, account  retained earnings and account limits profits / losses not yet listed on the parameters of the journal');
    }else{
       exit(' Error: data akun laba tahunan, akun laba ditahan dan batas akun laba/rugi belum terdaftar pada parameter jurnal');
    }
}

$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
      periode='".$param['periode']."' and kodeorg='".$param['kodeorg']."'";
$res=mysql_query($str);
$currstart='';
$currend='';
while($bar=mysql_fetch_object($res))
{
    $currstart=$bar->tanggalmulai;
    $currend=$bar->tanggalsampai;
}
if($currstart=='' or $currend=='') {
    exit('Error: '.$_SESSION['lang']['accperiodwrong'].' '.$param['kodeorg']);
}


if ($param['metode']=='getList'){
    $sawal=Array();
    $mtdebet=Array();
    $mtkredit=Array();
    $salak=Array();
    #ambil saldoawal bulan berjalan
    $str="select awal".substr($param['periode'],5,2).",noakun,debet".substr($param['periode'],5,2)." as debet,kredit".substr($param['periode'],5,2)." as kredit from ".$dbname.".keu_saldobulanan
          where periode='".str_replace("-", "", $param['periode'])."' and kodeorg='".$param['kodeorg']."'";
    //exit('error'.$str);
    $res=mysql_query($str);
    while($bar=mysql_fetch_array($res))
    {
        $sawal[$bar[1]]=$bar[0];
        $mtdebet[$bar[1]]=0;
        $mtkredit[$bar[1]]=0;
        $salak[$bar[1]]=$bar[0];
        
        $debet[$bar[1]]=$bar[2];
        $kredit[$bar[1]]=$bar[3];
    }
    #ambil saldoawal bulan berikutnya sebagai perbandingan
    $str="select awal".addZero($bulanLanjut,2).",noakun from ".$dbname.".keu_saldobulanan
          where periode='".$tahunLanjut.addZero($bulanLanjut,2)."' and kodeorg='".$param['kodeorg']."'";
    //exit('error'.$str);
    $res=mysql_query($str);
    while($bar=mysql_fetch_array($res))
    {
        $sawal2[$bar[1]]=$bar[0];
    }
    #ambil transaksi transaksi bln berjalan
    $str="select noakun,sum(debet) as debet,sum(kredit) as kredit from ".$dbname.".keu_jurnaldt_vw 
          where tanggal between '".$currstart."' and '".$currend."' and kodeorg='".$param['kodeorg']."' group by noakun";
    //exit('error'.$str);
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        if ($sawal[$bar->noakun]=='') $sawal[$bar->noakun]=0;
        if ($tmpPeriod[1]==12 and $bar->noakun==$akunCLM){
            $mtdebet[$bar->noakun]=0;
            $mtkredit[$bar->noakun]=0;
        } else {
            $mtdebet[$bar->noakun]=$bar->debet;
            $mtkredit[$bar->noakun]=$bar->kredit;
        }
        $salak[$bar->noakun]=$mtdebet[$bar->noakun]+$sawal[$bar->noakun]-$mtkredit[$bar->noakun];
    }
    //echo"<pre>";
    //print_r($ass);
    //echo"</pre>";

    echo"<button class=mybutton onclick=prosesRekalkulasi(1) id=btnproses>Process</button>&nbsp;
         <i>(Hanya untuk Data yang berwarna merah)</i><br>
            <div style='width:1200px;display:fixed;'>
            <table class=sortable cellspacing=1 border=0 style='width:100%'>
            <thead>
            <tr class=rowheader>
            <td rowspan=2 align=center width=50>No Akun</td>
            <td rowspan=2 align=center>Nama Akun</td>
            <td rowspan=2 align=center width=110>Saldo awal</td>
            <td colspan=3 align=center width=300>Sebelum Rekalkulasi</td>
            <td colspan=3 align=center width=300>Setelah Rekalkulasi</td>
            </tr>
            <tr class=rowheader>
            <td align=center width=110>Debet</td>
            <td align=center width=110>Kredit</td>
            <td align=center width=110>Saldo Akhir</td>
            <td align=center width=110>Debet</td>
            <td align=center width=110>Kredit</td>
            <td align=center width=110>Saldo Akhir</td>
            </tr>
            </thead>
            <tbody></tbody></table></div>";

    $no=0;
    $adasalah=false;
    echo "<div style='overflow:scroll;height:320px;width:1215px;display:fixed;'>
         <table cellspacing=1 border=0 class=sortable style='width:100%'>
         <thead class=rowheader></thead><tbody>";

    foreach($sawal as $noak=>$val)
    { 
            $no+=1;
            if (round($debet[$noak],2)!=round($mtdebet[$noak],2)){
                $merahdebet="style=\"background-color:red; color:#fff;\"";
                $adasalah=true;
            } else {
                $merahdebet="";
            }
            if (round($kredit[$noak],2)!=round($mtkredit[$noak],2)){
                $merahkredit="style=\"background-color:red; color:#fff;\"";
                $adasalah=true;
            } else {
                $merahkredit="";
            }
            if (round($sawal2[$noak],2)!=round($salak[$noak],2)){
                $merahsakhir="style=\"background-color:red; color:#fff;\"";
                $adasalah=true;
            } else {
                $merahsakhir="";
            }

            echo"<tr class=rowcontent id='row".$no."'>
            <td width=50 id='noakun".$no."'>".$noak."</td>
            <td>".$nmAkun[$noak]."</td>
            <td align=right width=110 id='awal".$no."'>".number_format($val,2,'.','')."</td>
            <td align=right width=110 id='debetprev".$no."' ".$merahdebet.">".number_format($debet[$noak],2,'.','')."</td>
            <td align=right width=110 id='kreditprev".$no."' ".$merahkredit.">".number_format($kredit[$noak],2,'.','')."</td>
            <td align=right width=110 id='akhirprev".$no."' ".$merahsakhir.">".number_format($sawal2[$noak],2,'.','')."</td>
            <td align=right width=110 id='debet".$no."' ".$merahdebet.">".number_format($mtdebet[$noak],2,'.','')."</td>
            <td align=right width=110 id='kredit".$no."' ".$merahkredit.">".number_format($mtkredit[$noak],2,'.','')."</td>
            <td align=right width=110 id='akhir".$no."' ".$merahsakhir.">".number_format($salak[$noak],2,'.','')."</td>
            </tr>";
    }
        
    echo"</tbody><tfoot></tfoot></table></div>####";
    if ($adasalah) echo "salah";
} else if ($param['metode']=='akhirtahun'){
        $query = selectQuery($dbname,'keu_jurnaldt_vw','sum(jumlah) as jumlah',
            "kodeorg='".$param['kodeorg']."' and tanggal between '".$currstart."' and '".$currend."'
             and noakun>='".$akunRAT."'");
        $data = fetchData($query);
        # Get Akun
        #+++++++++++++++++++++++++
        $noakun=$akunCLM;//akun laba tahun berjalan
        #++++++++++++++++++++++++++
        if($data[0]['jumlah']==''){
                $data[0]['jumlah']=0;
        }
        if($data[0]['jumlah']>0) { # Rugi
            $debetH=$data[0]['jumlah'];
            $kreditH=0;
        } else { # Laba
            $debetH=0;
            $kreditH=$data[0]['jumlah'];            
        }
        $tgl = $tmpPeriod[0].$tmpPeriod[1].cal_days_in_month(CAL_GREGORIAN,$tmpPeriod[1],$tmpPeriod[0]);
        $nojurnal = $tgl."/".$param['kodeorg']."/CLSM/999";
        
        $temp="update ".$dbname.".keu_jurnalht 
             set totaldebet=".$debetH.",
             totalkredit=".$kreditH."
             where nojurnal='".$nojurnal."';";
        if(!mysql_query($temp))
        {
            exit("Error update jurnal laba tahun berjalan ".mysql_error($conn));
        }   
        $temp="update ".$dbname.".keu_jurnaldt 
             set jumlah=".$data[0]['jumlah']."
             where nojurnal='".$nojurnal."' and nourut=1;";
        if(!mysql_query($temp))
        {
            exit("Error update jurnal laba tahun berjalan ".mysql_error($conn));
        }   
    
} else { // Update data yang salah
    if (round($param['debet'],2)==round($param['debet2'],2) and round($param['kredit'],2)==round($param['kredit2'],2) and round($param['sakhir'],2)==round($param['sakhir2'],2)){
        //exit('error:Data sudah sama');
    } else {
        // cek datanya ada atau tidak
        $qCek = selectQuery($dbname,'keu_saldobulanan','*',
            "kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and periode='".str_replace("-", "", $param['periode'])."'");
        $resCek = fetchData($qCek);
        if(!empty($resCek)){
            $temp="update ".$dbname.".keu_saldobulanan 
                 set debet".substr($param['periode'],5,2)."=".$param['debet'].",
                 kredit".substr($param['periode'],5,2)."=".$param['kredit']."
                 where periode='".str_replace("-", "", $param['periode'])."'
                 and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."';";
            if(!mysql_query($temp))
            {
                exit("Error proses rekalkulasi ".mysql_error($conn));
            }   
        } else {
                $temp="insert into  ".$dbname.".keu_saldobulanan (kodeorg,periode,noakun,
                      debet".substr($param['periode'],5,2).",kredit".substr($param['periode'],5,2).")values('". 
                       $param['kodeorg']."','".str_replace("-", "", $param['periode'])."','".$param['noakun']."',".$param['debet'].",".$param['kredit'].")";
                if(!mysql_query($temp))
                {
                    exit("Error insert saldo awal ".mysql_error($conn).":".$temp);
                }  
        }
        // cek data bulan selanjutnya
        if (addZero($bulanLanjut,2)!='01'){ // jika berikutnya bukan Januari
            $qCek = selectQuery($dbname,'keu_saldobulanan','*',
                "kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and periode='".$tahunLanjut.addZero($bulanLanjut,2)."'");
            $resCek = fetchData($qCek);
            if(!empty($resCek)){
               $temp2="update ".$dbname.".keu_saldobulanan 
                    set awal".addZero($bulanLanjut,2)."=".$param['sakhir']."
                    where periode='".$tahunLanjut.addZero($bulanLanjut,2)."'
                    and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."';";
               if(!mysql_query($temp2))
               {
                   exit("Error proses rekalkulasi ".mysql_error($conn));
               }   
            } else {
                    $temp="insert into  ".$dbname.".keu_saldobulanan (kodeorg,periode,noakun,
                          awal".addZero($bulanLanjut,2).")values('". 
                           $param['kodeorg']."','".$tahunLanjut.addZero($bulanLanjut,2)."','".$param['noakun']."',".$param['sakhir'].")";
                    if(!mysql_query($temp))
                    {
                        exit("Error insert saldo awal ".mysql_error($conn).":".$temp);
                    }  
            }
        } else { // jika berikutnya adalah Januari
            if ($param['noakun']<$akunRAT){
                if($param['noakun']!=$akunCLY){
                    $sakhir=($key==$akunCLM)?0:$param['sakhir'];
                    $qCek = selectQuery($dbname,'keu_saldobulanan','*',
                        "kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and periode='".$tahunLanjut.addZero($bulanLanjut,2)."'");
                    $resCek = fetchData($qCek);
                    if(!empty($resCek)){
                        $temp2="update ".$dbname.".keu_saldobulanan 
                             set awal".addZero($bulanLanjut,2)."=".$sakhir."
                             where periode='".$tahunLanjut.addZero($bulanLanjut,2)."'
                             and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."';";
                        if(!mysql_query($temp2))
                        {
                            exit("Error proses rekalkulasi ".mysql_error($conn));
                        }   
                    } else {
                        $temp="insert into  ".$dbname.".keu_saldobulanan (kodeorg,periode,noakun,
                              awal".addZero($bulanLanjut,2).")values('". 
                               $param['kodeorg']."','".$tahunLanjut.addZero($bulanLanjut,2)."','".$param['noakun']."',".$sakhir.")";
                        if(!mysql_query($temp))
                        {
                            exit("Error insert saldo awal ".mysql_error($conn).":".$temp);
                        }  
                    }
                }
            }
        }
    }
}
?>