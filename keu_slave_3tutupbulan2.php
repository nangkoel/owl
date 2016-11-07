<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

$param = $_POST;
$tmpPeriod = explode('-',$param['periode']);
$tahunbulan = implode("",$tmpPeriod);
$proses = $_GET['proses'];
 
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

//#periksa periode sebelumnya
//$prd=explode("-",$param['periode']);
//if($prd[1]-1==0){
//    $prdlalu=($prd[0]-1)."-12";
//}else{
//    $bln=strlen(($prd[1]-1))>1?($prd[1]-1):"0".($prd[1]-1);
//    $prdlalu=$prd[0]."-".$bln;
//}
//$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
//      periode='".$prdlalu."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
//$res=mysql_query($str);
//$beforestart='';
//$beforeend='';
//while($bar=mysql_fetch_object($res))
//{
//    $beforestart=$bar->tanggalmulai;
//    $beforeend=$bar->tanggalsampai;
//}

#periksa apakah sudah diposting semua transaksi kas dan bappp
$str="select tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where 
      periode='".$param['periode']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$res=mysql_query($str);
$currstart='';
$currend='';
while($bar=mysql_fetch_object($res))
{
    $currstart=$bar->tanggalmulai;
    $currend=$bar->tanggalsampai;
}
    
if($currstart=='' or $currend=='')
{
    exit('Error: '.$_SESSION['lang']['accperiodwrong'].' '.$_SESSION['empl']['lokasitugas']);
}
else
{
    #periksa kas
//    $str="select notransaksi,tanggal,jumlah from ".$dbname.".keu_kasbankht where kodeorg='".$_SESSION['empl']['lokasitugas']."'
//          and tanggal between '".$currstart."' and '".$currend."' and posting=0";
//    $res=mysql_query($str);
//    if(mysql_num_rows($res)>0)
//    {
//        echo " There are Cash/Bank transaction that has not been posted:\n";
//        $no=0;
//        while($bar=mysql_fetch_object($res))
//        {
//           $no+=1;
//            echo $no.". No ".$bar->notransaksi.":".tanggalnormal($bar->tanggal)."->Rp. ".number_format($bar->jumlah,0)."\n"; 
//        }
//        exit('Error');
//    }

    #periksa VP
    $str="select novp,tanggal from ".$dbname.".keu_vpht where kodeorg='".$_SESSION['empl']['lokasitugas']."'
          and tanggal between '".$currstart."' and '".$currend."' and posting=0";
    $res=mysql_query($str);
    if(mysql_num_rows($res)>0)
    {
        echo $_SESSION['lang']['errvpnotposted'].":\n";
        $no=0;
        while($bar=mysql_fetch_object($res))
        {
           $no+=1;
            echo $no.". No ".$bar->novp.": ".tanggalnormal($bar->tanggal)."\n"; 
        }
        exit('Error');
    }

    #periksa bapp
    $str="select notransaksi,tanggal,jumlahrealisasi from ".$dbname.".log_baspk where kodeblok like '".$_SESSION['empl']['lokasitugas']."%'
          and tanggal between '".$currstart."' and '".$currend."' and statusjurnal=0";
    $res=mysql_query($str);
    if(mysql_num_rows($res)>0)
    {
        echo $_SESSION['lang']['errbaspknotposted'].":\n";
        $no=0;
        while($bar=mysql_fetch_object($res))
        {
           $no+=1;
            echo $no.". No ".$bar->notransaksi.":".tanggalnormal($bar->tanggal)."->Rp. ".number_format($bar->jumlahrealisasi,0)."\n"; 
        }
        exit('Error');
    }
    #periksa jurnal tidak balance
    $str="select nojurnal,tanggal,debet,kredit from ".$dbname.".keu_jurnal_tidak_balance_vw where kodeorg = '".$_SESSION['empl']['lokasitugas']."'
          and tanggal between '".$currstart."' and '".$currend."'
          and nojurnal not like '%/CLSM/%'";
    $res=mysql_query($str);
    if(mysql_num_rows($res)>0)
    {
        echo $_SESSION['lang']['notbalancejournal'].":\n";
        $no=0;
        while($bar=mysql_fetch_object($res))
        {
           $no+=1;
            echo $no.". No ".$bar->nojurnal.":".tanggalnormal($bar->tanggal)."->(D)Rp. ".number_format($bar->debet,0).":(K)Rp. ".number_format($bar->kredit,0)."\n"; 
        }
        exit('Error');
    }    
    #periksa gudang
    $str="select notransaksi,tanggal, kodegudang from ".$dbname.".log_transaksiht where post=0 and kodegudang like '".$_SESSION['empl']['lokasitugas']."%'
            and tanggal between '".$currstart."' and '".$currend."'";
    $res=mysql_query($str);
    $stm='';
    if(mysql_num_rows($res)>0){
        while($bar=mysql_fetch_object($res))
        {
             $stm.="Gudang:".$bar->kodegudang."->No.>".$bar->notransaksi."->".$bar->tanggal."<br>";
         }
       echo $_SESSION['lang']['errwhnotposted'].":\n".$stm; 
       exit("Error");
    }
	#cek penerimaan barang mutasi tambahan jamhari 23062013
    $scekMut="select * from ".$dbname.".log_transaksiht where kodegudang like '".$_SESSION['empl']['lokasitugas']."%'
              and tanggal between '".$currstart."' and '".$currend."' and tipetransaksi=7 
              and (notransaksireferensi is null or notransaksireferensi='') order by notransaksi asc";
    //exit("error:".$scekMut);
    $qcekMut=mysql_query($scekMut) or die(mysql_error($conn));
    if(mysql_num_rows($qcekMut)>0)
    {
        echo $_SESSION['lang']['errmutationnotreceive'].":\n";
        while($rcekMut=  mysql_fetch_object($qcekMut)){
            echo $rcekMut->notransaksi.": gudang tujuan -> ".$rcekMut->gudangx."\n";
        }
       exit("Error");
    }
   #Periksa BKM
    $str="select notransaksi,tanggal from ".$dbname.".kebun_aktifitas where kodeorg='".$_SESSION['empl']['lokasitugas']."'
          and tanggal between '".$currstart."' and '".$currend."' and jurnal=0";
    $res=mysql_query($str);
    if(mysql_num_rows($res)>0)
    {
        echo $_SESSION['lang']['errbkmnotposted'].":\n";
        $no=0;
        while($bar=mysql_fetch_object($res))
        {
           $no+=1;
            echo $no.". No ".$bar->notransaksi.":".tanggalnormal($bar->tanggal)."\n";
        }
        exit('Error');
    }
   #Periksa TRAKSI
    $str="select notransaksi,tanggal from ".$dbname.".vhc_runht where kodeorg='".$_SESSION['empl']['lokasitugas']."'
          and tanggal between '".$currstart."' and '".$currend."' and posting=0";
    $res=mysql_query($str);
    if(mysql_num_rows($res)>0)
    {
        echo $_SESSION['lang']['errvhcnotposted'].":\n";
        $no=0;
        while($bar=mysql_fetch_object($res))
        {
           $no+=1;
            echo $no.". No ".$bar->notransaksi.":".tanggalnormal($bar->tanggal)."\n";
        }
        exit('Error');
    }    
}   

#PERIKSA akun transit yang belum nol=============================
$str="select sum(debet)-sum(kredit) as saldo FROM ".$dbname.".keu_jurnalsum_vw where  periode ='".$param['periode']."' 
          and kodeorg='".$_SESSION['empl']['lokasitugas']."' AND noakun like '4%'";
$res=mysql_query($str);
$transit=0;
if(mysql_num_rows($res)>0){
        while($bar=mysql_fetch_object($res))
        {
            $transit=abs($bar->saldo);
        }
}
if($transit>10 && $transit!='')#lebih dari  10 rupiah
{
    exit(" Error: ".$_SESSION['lang']['errtransitnotalloc']."\n".$_SESSION['lang']['remain'].": ".$transit);
}
#---------------------------------------==================================

if(substr($_SESSION['empl']['lokasitugas'],2,2)!='HO' and substr($_SESSION['empl']['lokasitugas'],2,2)!='RO'){
	$arrExcep=array("H16E"=>"1","H17E"=>"1","H19E"=>"1","H20E"=>"1","H21E"=>"1","H22E"=>"1","H23E"=>"1","H24E"=>"1","H25E"=>"1","H26E"=>"1");
    #PERIKSA apakah sudah ada gaji=============================
	if($arrExcep[$_SESSION['empl']['lokasitugas']]!="1"){
		$str="select nojurnal FROM ".$dbname.".keu_jurnalht where  tanggal like '".$param['periode']."%'
              and nojurnal like '%".$_SESSION['empl']['lokasitugas']."/KBN%'";
		$res=mysql_query($str);
		if(mysql_num_rows($res)>0){
                    
		}else{
                    exit(" Error: ".$_SESSION['lang']['gajinotrun']);
		}
	}
    
    #---------------------------------------==================================
}


switch($proses) {
    case 'tutupBuku':
        #==================== Prep Periode ====================================
        # Prep Tahun Bulan untuk periode selanjutnya
        if($tmpPeriod[1]==12) {
            $bulanLanjut = 1;
            $tahunLanjut = $tmpPeriod[0]+1;
        } else {
            $bulanLanjut = $tmpPeriod[1]+1;
            $tahunLanjut = $tmpPeriod[0];
        }
        
        # Prep Hari untuk periode selanjutnya
        $jmlHari = cal_days_in_month(CAL_GREGORIAN,$bulanLanjut,$tahunLanjut);
        $tglAwal = $tahunLanjut.'-'.addZero($bulanLanjut,2).'-01';
        $tglAkhir = $tahunLanjut.'-'.addZero($bulanLanjut,2).'-'.addZero($jmlHari,2);
        $d=date_parse($currend);
        if ($d['day']<addZero($jmlHari,2)){
            $tglAwal=  nambahHari($currend, 1, 1);
            $tglAkhir = $tahunLanjut.'-'.addZero($bulanLanjut,2).'-'.$d['day'];
        }
//        if (date_parse($beforeend))
        #==================== /Prep Periode ===================================
        
        #==================== Prep Jurnal =====================================
        #=== Extract Data ====
        # Get PT
        $pt = getPT($dbname,$param['kodeorg']);
        if($pt==false) {
            $pt = getHolding($dbname,$param['kodeorg']);
        }
        
        # Tanggal dan Kode Jurnal
        $tgl = $tmpPeriod[0].$tmpPeriod[1].
            cal_days_in_month(CAL_GREGORIAN,$tmpPeriod[1],$tmpPeriod[0]);
        $kodejurnal = 'CLSM';
        
        
        #==================== Journal Counter ==================
        $nojurnal = $tgl."/".$param['kodeorg'].
            "/".$kodejurnal."/999";
        #==================== Journal Counter ==================
        
        # Cek apakah tahun sudah ditutup
        $qCek = selectQuery($dbname,'keu_jurnalht','*',
            "nojurnal='".$nojurnal."'");
//        echo "error:".$qCek;
//        exit;
        $resCek = fetchData($qCek);
        if(!empty($resCek)) {
            echo ' Error : This period has been closed(Before).';
            exit;
        }
        
         $query = "select count(*) as x from ".$dbname.".keu_jurnaldt_vw where 
                   tanggal between '".$currstart."' and '".$currend."' and substr(nojurnal,10,4)='".$param['kodeorg']."'";
//         exit("error: ".$query);
        $res=mysql_query($query);
        
       if(mysql_num_rows($res)==0) {
            echo 'Warning : No data found for this unit';
            exit;
        }
        
        # Get Sum dari Jurnal
        $query = selectQuery($dbname,'keu_jurnaldt_vw','substr(nojurnal,10,4) as kodeorg,sum(jumlah) as jumlah',
            "substr(nojurnal,10,4)='".$param['kodeorg']."' and tanggal between '".$currstart."' and '".$currend."'
             and noakun>='".$akunRAT."'").
            "group by substr(nojurnal,10,4)";
        $data = fetchData($query);

        
        # Get Akun
        #+++++++++++++++++++++++++
        //tambahan ginting
        $noakun=$akunCLM;//akun laba tahun berjalan
        #++++++++++++++++++++++++++
		if($data[0]['jumlah']==''){
			$data[0]['jumlah']=0;
		}
        if($data[0]['jumlah']>0) {
            # Rugi
            $debetH=$data[0]['jumlah'];
            $kreditH=0;
        } else {
            # Laba
            $debetH=0;
            $kreditH=$data[0]['jumlah'];            
        }

        # Prep Header
        $dataRes['header'] = array(
            'nojurnal'=>$nojurnal,
            'kodejurnal'=>$kodejurnal,
            'tanggal'=>$tgl,
            'tanggalentry'=>date('Ymd'),
            'posting'=>'0',
            'totaldebet'=>$debetH,
            'totalkredit'=>$kreditH,
            'amountkoreksi'=>'0',
            'noreferensi'=>'TUTUP/'.$param['kodeorg'].'/'.$tahunbulan,
            'autojurnal'=>'1',
            'matauang'=>'IDR',
            'kurs'=>'1',
            'revisi'=>'0'
        );
        
        # Data Detail
        $noUrut = 1;
        
        # Debet
        $dataRes['detail'][] = array(
            'nojurnal'=>$nojurnal,
            'tanggal'=>$tgl,
            'nourut'=>$noUrut,
            'noakun'=>$noakun,
            'keterangan'=>'Tutup Bulan '.$tahunbulan.' Unit '.$param['kodeorg'],
            'jumlah'=>$data[0]['jumlah'],
            'matauang'=>'IDR',
            'kurs'=>'1',
            'kodeorg'=>$param['kodeorg'],
            'kodekegiatan'=>'',
            'kodeasset'=>'',
            'kodebarang'=>'',
            'nik'=>'',
            'kodecustomer'=>'',
            'kodesupplier'=>'',
            'noreferensi'=>'',
            'noaruskas'=>'',
            'kodevhc'=>'',
            'nodok'=>'',
            'kodeblok'=>'',
           'revisi'=>'0'            
        );
        $noUrut++;
 /*    kredit tidak perlu untuk laba rugi tahun berjalan   
        # Kredit
        $dataRes['detail'][] = array(
            'nojurnal'=>$nojurnal,
            'tanggal'=>$tgl,
            'nourut'=>$noUrut,
            'noakun'=>$akunKredit,
            'keterangan'=>'Tutup Bulan '.$tahunbulan.' Unit '.$param['kodeorg'],
            'jumlah'=>-1*$data[0]['jumlah'],
            'matauang'=>'IDR',
            'kurs'=>'1',
            'kodeorg'=>$pt['kode'],
            'kodekegiatan'=>'',
            'kodeasset'=>'',
            'kodebarang'=>'',
            'nik'=>'',
            'kodecustomer'=>'',
            'kodesupplier'=>'',
            'noreferensi'=>'',
            'noaruskas'=>'',
            'kodevhc'=>'',
            'nodok'=>'',
            'kodeblok'=>''
            
        );
  *        $noUrut++; 
  * 
  */

       #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Header
        $headErr = '';
        $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
        if(!mysql_query($insHead)) {
            $headErr .= 'Insert Header Error : '.mysql_error()."\n".$insHead;
        }
        
        if($headErr=='') {
            #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
            $detailErr = '';
            foreach($dataRes['detail'] as $row) {
                $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                if(!mysql_query($insDet)) {
                    $detailErr .= "Insert Detail Error : ".mysql_error()."\n".$insDet;
                    break;
                }
                else
                {

                }    
            }
            
            if($detailErr=='') {
                    #==================== /Prep Jurnal ====================================
                    createSaldoAwal($param['periode'],$tahunLanjut.'-'.addZero($bulanLanjut,2),$param['kodeorg']);
                    #========================== Proses Insert dan Update ==========================
 
                # Header and Detail inserted
                # Update Status Tutup Buku
                $queryUpd = updateQuery($dbname,'setup_periodeakuntansi',array('tutupbuku'=>1),
                    "kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'");
                if(!mysql_query($queryUpd)) {
                    echo 'Error Update : '.mysql_error();
                    exit;
                } else {
                    # Insert periode baru
                    $dataIns = array(
                        'kodeorg'=>$param['kodeorg'],
                        'periode'=>$tahunLanjut.'-'.addZero($bulanLanjut,2),
                        'tanggalmulai'=>$tglAwal,
                        'tanggalsampai'=>$tglAkhir,
                        'tutupbuku'=>0
                    );
                    $queryIns = insertQuery($dbname,'setup_periodeakuntansi',$dataIns);
                    echo '1';
                    if(!mysql_query($queryIns)) {
                        # Rollback
                        echo 'Error Insert : '.mysql_error();
                        $queryRB = updateQuery($dbname,'setup_periodeakuntansi',array('tutupbuku'=>0),
                            "kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'");
                        if(!mysql_query($queryRB)) {
                            echo 'Error Rollback Update : '.mysql_error();
                            exit;
                        }
                    }
                    else{
                            //update history tutup buku
                            $str="delete from ".$dbname.".keu_setup_watu_tutup where periode='".$param['periode']."'. and kodeorg='".$param['kodeorg']."'";
                            mysql_query($str);
                            $str="insert into ".$dbname.".keu_setup_watu_tutup(kodeorg,periode,username) values(
                                  '".$param['kodeorg']."','".$param['periode']."','".$_SESSION['standard']['username']."')";
                            mysql_query($str);                              
                        }                    
                }
            } else {
                echo $detailErr;
                # Rollback, Delete Header
                $RBDet = deleteQuery($dbname,'keu_jurnalht',"nojurnal='".$nojurnal."'");
                if(!mysql_query($RBDet)) {
                    echo "Rollback Delete Header Error : ".mysql_error();
                    exit;
                }
            }
        } else {
            echo $headErr;
            exit;
        }
        
        
        break;
    default:
}

function createSaldoAwal($dariperiode,$keperiode,$kodeorg)
{
    global $conn;
    global $dbname;
    global $akunRAT;
    global $akunCLM;
    global $akunCLY;
    $sawal=Array();
    $mtdebet=Array();
    $mtkredit=Array();
    $salak=Array();
    #ambil saldoawal bulan berjalan
    $str="select awal".substr($dariperiode,5,2).",noakun from ".$dbname.".keu_saldobulanan
          where periode='".str_replace("-", "", $dariperiode)."' and kodeorg='".$kodeorg."'";
    $res=mysql_query($str);
    while($bar=mysql_fetch_array($res))
    {
        $sawal[$bar[1]]=$bar[0];
        $mtdebet[$bar[1]]=0;
        $mtkredit[$bar[1]]=0;
        $salak[$bar[1]]=$bar[0];
    }
    #ambil transaksi transaksi bln berjalan
    $str="select debet,kredit,noakun from ".$dbname.".keu_jurnalsum_vw 
          where periode='".$dariperiode."' and kodeorg='".$kodeorg."'";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $mtdebet[$bar->noakun]=$bar->debet;
        $mtkredit[$bar->noakun]=$bar->kredit;
        $salak[$bar->noakun]=$mtdebet[$bar->noakun]+$sawal[$bar->noakun]-$mtkredit[$bar->noakun];
    }
    #ambil semu nomor akun
    $str="select noakun from ".$dbname.".keu_5akun where length(noakun)=7";
    $res=mysql_query($str);
    $temp='';
    while($bar=mysql_fetch_object($res))
    {
        #create string update current
       
        if($sawal[$bar->noakun]!='')
        {  
         #jika sudah ada di database maka update
            if($mtdebet[$bar->noakun]=='')
                $mtdebet[$bar->noakun]=0;
           if($mtkredit[$bar->noakun]=='')
                $mtkredit[$bar->noakun]=0;
           
           $temp="update ".$dbname.".keu_saldobulanan 
                set debet".substr($dariperiode,5,2)."=".$mtdebet[$bar->noakun].",
                kredit".substr($dariperiode,5,2)."=".$mtkredit[$bar->noakun]."
                where periode='".str_replace("-", "", $dariperiode)."'
                and kodeorg='".$kodeorg."' and noakun='".$bar->noakun."';";
           if(!mysql_query($temp))
           {
               exit("Error update mutasi bulanan ".mysql_error($conn));
           }   
        }
        else
        {
           #jika belum ada maka insert
         if($sawal[$bar->noakun]!='' or $mtdebet[$bar->noakun]!='' or  $mtkredit[$bar->noakun]!=''){
            if($mtdebet[$bar->noakun]=='')
                $mtdebet[$bar->noakun]=0;
           if($mtkredit[$bar->noakun]=='')
                $mtkredit[$bar->noakun]=0;
           $temp="insert into  ".$dbname.".keu_saldobulanan (kodeorg,periode,noakun,
                  awal".substr($dariperiode,5,2).",debet".substr($dariperiode,5,2).",
                  kredit".substr($dariperiode,5,2).")values('". 
                   $kodeorg."','".str_replace("-", "", $dariperiode)."','".$bar->noakun."',0,".
                   $mtdebet[$bar->noakun].",".$mtkredit[$bar->noakun].");";
           if(!mysql_query($temp))
           {
               exit("Error insert mutasi bulanan ".mysql_error($conn));
           }  
         }
        }   
    } 
    #delete saldo awal bulan selanjutnya;
    $str="delete from ".$dbname.".keu_saldobulanan where periode='".str_replace("-", "", $keperiode)."'
          and kodeorg='".$kodeorg."';";
    if(mysql_query($str))
    {
        $saldoditahan=0;
        foreach($salak as $key=>$val){
            if($salak[$key]!=''){
              
                $temp="insert into  ".$dbname.".keu_saldobulanan (kodeorg,periode,noakun,
                      awal".substr($keperiode,5,2).")values('". 
                       $kodeorg."','".str_replace("-", "", $keperiode)."','".$key."',".$salak[$key].")";
               if(substr($keperiode,5,2)!='01')#jika bukan awal tahun
               {      
                   if(!mysql_query($temp))
                   {
                       exit("Error insert saldo awal ".mysql_error($conn).":".$temp);
                   }  
               }
               else #jika bulan 12
               {                     
                   if($key<$akunRAT){#jika awal tahun maka hanya akan membawa aktiva saja ke bulan selanjutnya
                #++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++                       
                   #deteksi jika saldo ditahan
                   #sudah mengakomodasi tutup akhir tahun    
                    if($key==$akunCLY)
                        $saldoditahan+=$salak[$key];
                    else{                    
                            if($key==$akunCLM){
                                $saldoditahan+=$salak[$key];#tampung laba tahun berjalan ke laba ditahan
                                $salak[$key]=0;
                            }
                            $temp1="insert into  ".$dbname.".keu_saldobulanan (kodeorg,periode,noakun,
                                  awal".substr($keperiode,5,2).")values('". 
                                   $kodeorg."','".str_replace("-", "", $keperiode)."','".$key."',".$salak[$key].")";

                       #++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++                       

                           if(!mysql_query($temp1))
                           {
                               exit("Error insert saldo awal ".mysql_error($conn));
                           } 
                    }                   
                  }
               }
            }   
        }
      //masukkan saldo laba ditahan
     if(substr($keperiode,5,2)=='01'){//hanya pada bulan 12                           
        $temp2="insert into  ".$dbname.".keu_saldobulanan (kodeorg,periode,noakun,
          awal".substr($keperiode,5,2).")values
           ('".$kodeorg."','".str_replace("-", "", $keperiode)."','".$akunCLY."',".$saldoditahan.")";
       if(!mysql_query($temp2))
       {
           exit("Error insert laba ditahan pada saldo awal ".mysql_error($conn));
       }  
     }
    }   
}   
?>