<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

//periksa apakah sudah ada
$str="select posting from ".$dbname.".sdm_catu where kodeorg='".$_POST['kodeorg']."' 
        and periodegaji='".$_POST['periode']."' and posting=1 order by posting desc 
        limit 1";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    if($bar->posting==1)
       $stat='1';
    else
        $stat='';
}

if($stat!='')
{
    exit($stat);
}
switch ($_POST['aksi']){
    
    case 'display':
        display($_POST['kodeorg'],$_POST['periode'],$_POST['harga'],$dbname,$conn);
    break;
    case 'simpan':
        display($_POST['kodeorg'],$_POST['periode'],$_POST['harga'],$dbname,$conn);
    break;    
    case 'replace':
        display($_POST['kodeorg'],$_POST['periode'],$_POST['harga'],$dbname,$conn);        
    break;
    case 'posting':
        posting($_POST['kodeorg'],$_POST['periode'],$_POST['jumlah'],$dbname,$conn);
    break;
}

function display($kodeorg,$periode,$harga,$dbname,$conn)
{
   $tgl1='';
   $tgl2='';
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where kodeorg='".$kodeorg."'
           and periode='".$periode."' and jenisgaji='H'"; 
    $res=mysql_query($str);
    while($bar=  mysql_fetch_object($res))
    {
        $tgl1= str_replace("-","",$bar->tanggalmulai);
        $tgl2=str_replace("-","",$bar->tanggalsampai);
    }
    
    if($tgl1=='' or $tgl2=='')
    {
        exit(" Error: Periode penggajian Harian tidak ditemukan/ Daily base payrol period not found");
    }
    else
    {   
            //ambil kamus karyawan
          $str="select a.karyawanid,a.namakaryawan,a.kodecatu,a.subbagian,b.tipe,c.keterangan,a.kodecatu,a.tipekaryawan,a.kodejabatan,d.namajabatan
                  from ".$dbname.".datakaryawan a left join ".$dbname.".sdm_5tipekaryawan b on a.tipekaryawan=b.id                  
                  left join ".$dbname.".sdm_5catuporsi c on a.kodecatu=c.kode
                  left join ".$dbname.".sdm_5jabatan d on a.kodejabatan=d.kodejabatan    
                  where a.lokasitugas='".$kodeorg."' and tipekaryawan!=0 and (a.tanggalkeluar>'".$_POST['periode']."-01' or a.tanggalkeluar='0000-00-00')";
          $res=mysql_query($str);
          $kamusKar=Array();
          while($bar=mysql_fetch_object($res))
          {
              if($bar->tipe!='KHL'){
              $kamusKar[$bar->karyawanid]['id']=$bar->karyawanid;
              $kamusKar[$bar->karyawanid]['nama']=$bar->namakaryawan;
              $kamusKar[$bar->karyawanid]['kodecatu']=$bar->kodecatu;
              $kamusKar[$bar->karyawanid]['subbagian']=$bar->subbagian;
              $kamusKar[$bar->karyawanid]['tipekaryawan']=$bar->tipekaryawan;
              $kamusKar[$bar->karyawanid]['namatipe']=$bar->tipe;              
              $kamusKar[$bar->karyawanid]['kelompok']=$bar->keterangan;
              $kamusKar[$bar->karyawanid]['kode']=$bar->kodecatu;
              $kamusKar[$bar->karyawanid]['jabatan']=$bar->namajabatan; 
              }
          }
    }
    
    //ambil subbagian untuk pengurutan perafdeling
    $str="select kodeorganisasi from ".$dbname.".organisasi where induk='".$kodeorg."' order by kodeorganisasi";
    $res=mysql_query($str);

    $subbagian=Array();
    while($bar=mysql_fetch_object($res))
    {
        array_push($subbagian,$bar->kodeorganisasi); 
    }
    //ambil dari perawatan
          $sKehadiran="select absensi,tanggal,karyawanid from ".$dbname.".kebun_kehadiran_vw 
                            where tanggal between  '".$tgl1."' and '".$tgl2."' and unit='".$kodeorg."'";
          $res=  mysql_query($sKehadiran);
          while($bar= mysql_fetch_object($res))
          {
              $tgl=str_replace("-","",$bar->tanggal);
              $kehadiran[$bar->karyawanid][$tgl]=$bar->absensi;
          }         
    //ambil Panen
        $sPrestasi="select b.tanggal,a.jumlahhk,a.nik from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi 
                            where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4)='".$kodeorg."' and b.tanggal between '".$tgl1."' and '".$tgl2."'";
        $res=  mysql_query($sPrestasi);
          while($bar= mysql_fetch_object($res))
          {
              $tgl=str_replace("-","",$bar->tanggal);
              $kehadiran[$bar->nik][$tgl]='H';
          }         
          
    // ambil pengawas                        
        $dzstr="SELECT tanggal,nikmandor FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid
            where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeorg."%' and c.namakaryawan is not NULL
            union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid
            where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeorg."%' and c.namakaryawan is not NULL";
        $dzres=mysql_query($dzstr);
        while($bar=mysql_fetch_object($dzres))
        {
              $tgl=str_replace("-","",$bar->tanggal);
              $kehadiran[$bar->nikmandor][$tgl]='H';
        }

    // ambil administrasi                       
        $dzstr="SELECT tanggal,nikasisten as nikmandor FROM ".$dbname.".kebun_aktifitas a
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.nikasisten=c.karyawanid
            where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeorg."%' and c.namakaryawan is not NULL
            union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a 
            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi
            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid
            where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeorg."%' and c.namakaryawan is not NULL";
        $dzres=mysql_query($dzstr);
        while($bar=mysql_fetch_object($dzres))
        {
              $tgl=str_replace("-","",$bar->tanggal);
              $kehadiran[$bar->nikmandor][$tgl]='H';
        }
    // ambil dari traksi                      
        $dzstr="SELECT tanggal,idkaryawan FROM ".$dbname.".vhc_runhk
            where tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '".$kodeorg."%'";
        $dzres=mysql_query($dzstr);
        while($bar=mysql_fetch_object($dzres))
        {
              $tgl=str_replace("-","",$bar->tanggal);
              $kehadiran[$bar->idkaryawan][$tgl]='H';
        }
        
    //ambil dari absensi. jika sudah diabsen di atas, dan di sini diabsen juga namun catu = 0, maka akan dioverride tidak dapat catu.
          $sAbsn="select absensi,tanggal,karyawanid,catu from ".$dbname.".sdm_absensidt 
                        where tanggal between  '".$tgl1."' and '".$tgl2."' and kodeorg like '".$kodeorg."%'
                         and left(absensi,1)='H'";
          $res=  mysql_query($sAbsn);
          $kehadiran=Array();
          while($bar=  mysql_fetch_object($res))
          {
              $tgl=str_replace("-","",$bar->tanggal);
              if($bar->catu==1){
                  $kehadiran[$bar->karyawanid][$tgl]=$bar->absensi;                  
              }else{
                  unset($kehadiran[$bar->karyawanid][$tgl]);
              }
          }                   
        
        //buang hari minggu 
        $hari = dates_inbetween($tgl1, $tgl2);
            foreach($hari as $ar => $isi)
            {
                    $qwe=date('D', strtotime($isi));
                    $tglini=date('Ymd', strtotime($isi));
                    if($qwe=='Sun'){
                        
                        foreach($kehadiran as $key=>$val){                            
                            $sCek="select distinct catu from ".$dbname.".sdm_absensidt 
                                   where karyawanid='".$key."' and tanggal='".$tglini."'";
                            $qCek=mysql_query($sCek) or die(mysql_error($conn));
                            $rCek=mysql_fetch_assoc($qCek);
                            if($rCek['catu']==0)
                            {
                                unset($kehadiran[$key][$tglini]);
                            }
                        }
                    }
            }
            
 
            
      //jumlahkan hk masing-masing orang
        $jumlahHK=Array();    
            foreach($kehadiran as $key=>$val){                            
               $jumlahHK[$key]=count($kehadiran[$key]);
            }  
    //ambil jumlah porsi catu masing-masing orang
    $str="select kelompok, jumlah as porsi from ".$dbname.".sdm_5catu where kodeorg='".$kodeorg."' and tahun=".substr($periode,0,4);
    $porsi=Array();
    $res=mysql_query($str);
    if(mysql_num_rows($res)==0)
    {
        if($_SESSION['language']=='ID'){
        exit("Error:Setup->Natura untuk tahun ".substr($periode,0,4)." belum ada, silahkan isi terlebih dahulu");
        }else{
          exit("Error:Setup->Natura for year ".substr($periode,0,4)." not defined, please define first");          
        }
    }
    while($bar=mysql_fetch_object($res))
    {
        $porsi[$bar->kelompok]=$bar->porsi;
    }
    
    //bentuk rupiah catu masing-masing orang
    $rupiahCatu=Array();
    foreach ($jumlahHK as $key=>$val)
    {
        $rupiahCatu[$key]=$jumlahHK[$key]*$porsi[$kamusKar[$key]['kode']]*$harga;
    }

 if($_POST['aksi']=='display'){   
        //print
            echo"<font color=red>Scroll down to save</font>
                    <table class=sortable border=0 cellspacing=1>
                    <thead>
                    <tr class=rowheader>
                    <td>No.</td>
                    <td>".$_SESSION['lang']['kodeorg']."</td>
                    <td>".$_SESSION['lang']['subbagian']."</td>
                    <td>".$_SESSION['lang']['periode']."</td>
                    <td>".$_SESSION['lang']['namakaryawan']."</td>
                    <td>".$_SESSION['lang']['tipe']."</td>
                    <td>".$_SESSION['lang']['jabatan']."</td>
                    <td>".$_SESSION['lang']['status']."</td>
                    <td>Ltr/Hk</td>
                    <td>".$_SESSION['lang']['jumlah']." HK</td>
                    <td>".$_SESSION['lang']['hargasatuan']."</td>
                    <td>".$_SESSION['lang']['total']." (Rp)</td>
                    </tr>
                    </thead>
                    <tbody>";
            $no=0;
            $ttl=0;
            foreach ($subbagian as $unit=>$sub){
                  $SUBTTL=0;
                foreach ( $kamusKar as $key=>$val)
                {
                    if($kamusKar[$key]['subbagian']==$sub){
                        $no+=1;
                        echo "<tr class=rowcontent>
                                <td>".$no."</td>
                                <td>".$kodeorg."</td>
                                <td>".$kamusKar[$key]['subbagian']."</td>
                                <td>".$periode."</td>
                                <td>".$kamusKar[$key]['nama']."</td>
                                <td>".$kamusKar[$key]['namatipe']."</td>
                                <td>".$kamusKar[$key]['jabatan']."</td>
                                <td>".$kamusKar[$key]['kode']."</td>
                                <td>".number_format($porsi[$kamusKar[$key]['kode']],2,'.',',')."</td>
                                <td align=right>".number_format($jumlahHK[$key],0,'.',',')."</td>
                                <td align=right>".number_format($harga,0,'.',',')."</td>     
                                <td align=right>".number_format($rupiahCatu[$key],0,'.',',')."</td></tr>     
                                ";
                    $ttl+=$rupiahCatu[$key];  
                    $SUBTTL+=$rupiahCatu[$key];
                    }
                }
                //print subtotal per afdeling    
                echo "<tr class=rowcontent>
                            <td colspan=11>Sub Total ".$sub."</td>     
                            <td align=right>".number_format($SUBTTL,0,'.',',')."</td></tr>     
                            ";                                 
                }  
               //khusus karyawan kantor
                $SUBTTL=0;
                foreach ( $kamusKar as $key=>$val)
                {
                    if($kamusKar[$key]['subbagian']=='' or $kamusKar[$key]['subbagian']=='0'){
                        $no+=1;
                        echo "<tr class=rowcontent>
                                <td>".$no."</td>
                                <td>".$kodeorg."</td>
                                <td>".$kamusKar[$key]['subbagian']."</td>
                                <td>".$periode."</td>
                                <td>".$kamusKar[$key]['nama']."</td>
                                <td>".$kamusKar[$key]['namatipe']."</td>
                                <td>".$kamusKar[$key]['jabatan']."</td>
                                <td>".$kamusKar[$key]['kode']."</td>
                                <td>".number_format($porsi[$kamusKar[$key]['kode']],2,'.',',')."</td>
                                <td align=right>".number_format($jumlahHK[$key],0,'.',',')."</td>
                                <td align=right>".number_format($harga,0,'.',',')."</td>     
                                <td align=right>".number_format($rupiahCatu[$key],0,'.',',')."</td></tr>     
                                ";
                    $ttl+=$rupiahCatu[$key];  
                    $SUBTTL+=$rupiahCatu[$key];
                    }
                }
                //print subtotal per afdeling    
                echo "<tr class=rowcontent>
                            <td colspan=11>Sub Total Kantor</td>     
                            <td align=right>".number_format($SUBTTL,0,'.',',')."</td></tr>";                 
                echo "<tr class=rowheader>
                        <td colspan=11>TOTAL</td>     
                        <td align=right>".number_format($ttl,0,'.',',')."</td></tr>     
                        ";    
            echo"</tbody>
                    <tfoot>
                    </tfoot>
                    </table>
                    <button onclick=simpanCatu()>".$_SESSION['lang']['save']."</button>"; 
 }   
 else if($_POST['aksi']=='simpan' or $_POST['aksi']=='replace'){
           if($_POST['aksi']=='simpan')
           {
               //periksa dulu apakah sudah ada atau sdah posting
                    $str="select posting from ".$dbname.".sdm_catu where kodeorg='".$kodeorg."' 
                            and periodegaji='".$periode."'  order by posting desc 
                            limit 1";
                    $res=mysql_query($str);
                    while($bar=mysql_fetch_object($res))
                    {
                        if($bar->posting=='1')
                          $stat='1';
                        elseif($bar->posting=='0')
                           $stat='0';                        
                        else
                            $stat='';
                    }

                    if($stat!='')
                    {
                        exit($stat);
                    }              
           }     
            $ttl=0;
            $stsimpan='';
            foreach ( $kamusKar as $key=>$val)
            {  
               if($rupiahCatu[$key]>0){
                    if($no==0){
                        $stsimpan="              
                            insert into ".$dbname.".sdm_catu(
                            kodeorg, 
                            subbagian,
                            periodegaji, 
                            karyawanid, 
                            hargacatu, 
                            jumlahhk, 
                            catuperhk, 
                            totalcatu, 
                            jumlahrupiah, 
                            posting, 
                            updateby)
                            values(
                            '".$kodeorg."',
                            '".$kamusKar[$key]['subbagian']."',    
                            '".$periode."',
                            ".$key.", 
                            ".$harga.",
                            ".$jumlahHK[$key].",   
                            ".$porsi[$kamusKar[$key]['kode']].",
                            ".($jumlahHK[$key]*$porsi[$kamusKar[$key]['kode']]).", 
                            ".$rupiahCatu[$key].",
                                0,
                            ".$_SESSION['standard']['userid']."    
                            )";
                    }else{
                        $stsimpan.=",(
                            '".$kodeorg."',
                            '".$kamusKar[$key]['subbagian']."',     
                            '".$periode."',
                            ".$key.", 
                            ".$harga.",
                            ".$jumlahHK[$key].",   
                            ".$porsi[$kamusKar[$key]['kode']].",
                            ".($jumlahHK[$key]*$porsi[$kamusKar[$key]['kode']]).", 
                            ".$rupiahCatu[$key].",
                                0,
                            ".$_SESSION['standard']['userid']."    
                            )";
                    }
                 $no+=1;   
               }
            }
            $str="delete from ".$dbname.".sdm_catu where kodeorg='".$kodeorg."' and periodegaji='".$periode."'";
            mysql_query($str);//hapus dulu yang ada
            if(mysql_query($stsimpan))
            {}//do nothing
            else
            {
                echo " Error: ".mysql_error($conn).$stsimpan;
            }  
 }
}

function posting($kodeorg,$periode,$jumlah,$dbname,$conn)
{
   $tgl1='';
   $tgl2='';
    $str="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where kodeorg='".$kodeorg."'
           and periode='".$periode."' and jenisgaji='H'"; 
    $res=mysql_query($str);
    while($bar=  mysql_fetch_object($res))
    {
        $tgl1= str_replace("-","",$bar->tanggalmulai);
        $tgl2=str_replace("-","",$bar->tanggalsampai);
    }
    
    if($tgl1=='' or $tgl2=='')
    {
        exit(" Error: Periode penggajian Harian tidak ditemukan/ Daily base payrol period not found");
    }  
 //periksa periode akuntansi
    $str="select * from ".$dbname.".setup_periodeakuntansi where kodeorg='".$kodeorg."' 
             and periode='".$periode."' and tutupbuku=0";
    $res=mysql_query($str);
    if(mysql_num_rows($res)==0)
    {
        exit(" Error: Sorry, accounting period is not active on choson period");
    }
    
 //periksa periode penggajian unit untuk memastikan apakah sudah selesai inputan BKM,KKD,ABSENSI
    $str="select sudahproses from ".$dbname.".sdm_5periodegaji where kodeorg='".$kodeorg."' 
             and periode='".$periode."' and sudahproses=0";
    $res=mysql_query($str);
    if(mysql_num_rows($res)>0)
    {
        exit(" Error: Sorry, input for presence, CARLOG and Foreman daoly book not yet close, please make sure for those transaction by confirmation via Setu->Periode Penggajian unit");
    }
   
//periksa tipe organisasi
    $str="select tipe from ".$dbname.".organisasi where kodeorganisasi='".$kodeorg."'";
    $res=mysql_query($str);
    $tipe='KANWIL';
    while($bar=mysql_fetch_object($res))
    {
        $tipe=$bar->tipe;
    }
    
        if($tipe=='KEBUN'){
            //ambil noakun dari parameter jurnal
            $debet='';
            $kredit='';
            $nojurnal=str_replace("-","",$periode)."28/".$kodeorg."/CT01/001";
            $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal where jurnalid='CT01'";
            $res=mysql_query($str);
            while($bar=mysql_fetch_object($res))
            {
                $debet=$bar->noakundebet;
                $kredit=$bar->noakunkredit;
            }
            if($debet=='' or $kredit=='')
            {
                exit('Error: Journal parameter for CT01 not defined, contact administrator');
            }
            $kodejurnal='CT01';
            //ambil porsi biaya umum
                $byumum=0;
                $str="select sum(jumlahrupiah) as byumum from ".$dbname.".sdm_catu where periodegaji='".$periode."' 
                        and kodeorg='".$kodeorg."' and subbagian=''";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                    $byumum=$bar->byumum;
                }
                $bytanaman=$jumlah-$byumum;
          //prepare jurnal
                # Prep Header
             $dataRes=Array();   
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodejurnal,
                    'tanggal'=>str_replace("-","",$periode)."28",
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>'1',
                    'totaldebet'=>$jumlah,
                    'totalkredit'=>($jumlah*-1),
                    'amountkoreksi'=>'0',
                    'noreferensi'=>'CT01',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'
                );

                # Data Detail
                $noUrut = 1;
                //jika biaya umum>0
                if($byumum>0){ 
                    # Debet
                    $dataRes['detail'][] = array(
                        'nojurnal'=>$nojurnal,
                        'tanggal'=>str_replace("-","",$periode)."28",
                        'nourut'=>$noUrut,
                        'noakun'=>$debet,
                        'keterangan'=>'Catu Beras -'.$periode,
                        'jumlah'=>$byumum,
                        'matauang'=>'IDR',
                        'kurs'=>'1',
                        'kodeorg'=>$kodeorg,
                        'kodekegiatan'=>'',
                        'kodeasset'=>'',
                        'kodebarang'=>'',
                        'nik'=>'',
                        'kodecustomer'=>'',
                        'kodesupplier'=>'',
                        'noreferensi'=>'CT01',
                        'noaruskas'=>'',
                        'kodevhc'=>'',
                        'nodok'=>'',
                        'kodeblok'=>'',
                    'revisi'=>'0'
                    );
                    $noUrut++;
                }
                
            //ambil kodeblok dan kegiatan untuk melengkapi sisi debet
                #1 ambil noakun panen
                $akunpanen='';
                $str="select  noakundebet from ".$dbname.".keu_5parameterjurnal where jurnalid='PNN01'";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                    $akunpanen=$bar->noakundebet;
                }
                if($akunpanen=='')
                {
                    exit(" Error: Account for harvesting not defined in journal parameter PNN01");
                }
               #2 Ambil blok panen
                    $sAbsn="select distinct kodeorg from ".$dbname.".kebun_prestasi_vw 
                                  where tanggal between  '".$tgl1."' and '".$tgl2."' and unit ='".$kodeorg."'";
                    $respanen=mysql_query($sAbsn);
                    $jml_baris=  mysql_num_rows($respanen);
                #3 ambil noakun dan blok perawatan
                    $sAbsn="select distinct noakun,kodeorg,kodekegiatan from ".$dbname.".kebun_perawatan_vw 
                                  where tanggal between  '".$tgl1."' and '".$tgl2."' and unit ='".$kodeorg."'";
                    $resrawat=mysql_query($sAbsn);
                    $jml_baris+=  mysql_num_rows($resrawat);
               #4 dibagi per masing-masing baris     
                    if($jml_baris==0 and $bytanaman>0)
                    {
                        #jika tidak ada pekerjaan lapangan
                        #kembalikan ke biaya umum
                            $dataRes['detail'][] = array(
                                'nojurnal'=>$nojurnal,
                                'tanggal'=>str_replace("-","",$periode)."28",
                                'nourut'=>$noUrut,
                                'noakun'=>$debet,
                                'keterangan'=>'Catu Beras -'.$periode,
                                'jumlah'=>$bytanaman,
                                'matauang'=>'IDR',
                                'kurs'=>'1',
                                'kodeorg'=>$kodeorg,
                                'kodekegiatan'=>'',
                                'kodeasset'=>'',
                                'kodebarang'=>'',
                                'nik'=>'',
                                'kodecustomer'=>'',
                                'kodesupplier'=>'',
                                'noreferensi'=>'CT01',
                                'noaruskas'=>'',
                                'kodevhc'=>'',
                                'nodok'=>'',
                                'kodeblok'=>'',
                    'revisi'=>'0'
                            ); 
                            $noUrut++;                   
                    }
                    else
                    {    
                         $biayaperblok=$bytanaman/$jml_baris;
                    }
                    if($biayaperblok>0 and $jml_baris>0)
                    {
                    #5 Bentuk detail jurnal pelengkap disisi debet     
                            while($bar=mysql_fetch_object($respanen))
                            {
                                    # Debet
                                    $dataRes['detail'][] = array(
                                        'nojurnal'=>$nojurnal,
                                        'tanggal'=>str_replace("-","",$periode)."28",
                                        'nourut'=>$noUrut,
                                        'noakun'=>$akunpanen,
                                        'keterangan'=>'Catu Beras -'.$periode,
                                        'jumlah'=>$biayaperblok,
                                        'matauang'=>'IDR',
                                        'kurs'=>'1',
                                        'kodeorg'=>$kodeorg,
                                        'kodekegiatan'=>$akunpanen."01",
                                        'kodeasset'=>'',
                                        'kodebarang'=>'',
                                        'nik'=>'',
                                        'kodecustomer'=>'',
                                        'kodesupplier'=>'',
                                        'noreferensi'=>'CT01',
                                        'noaruskas'=>'',
                                        'kodevhc'=>'',
                                        'nodok'=>'',
                                        'kodeblok'=>$bar->kodeorg,
                    'revisi'=>'0'
                                    );
                                    $noUrut++;
                            } 

                            while($bar=mysql_fetch_object($resrawat))
                            {
                                # Debet
                                    $dataRes['detail'][] = array(
                                        'nojurnal'=>$nojurnal,
                                        'tanggal'=>str_replace("-","",$periode)."28",
                                        'nourut'=>$noUrut,
                                        'noakun'=>$bar->noakun,
                                        'keterangan'=>'Catu Beras -'.$periode,
                                        'jumlah'=>$biayaperblok,
                                        'matauang'=>'IDR',
                                        'kurs'=>'1',
                                        'kodeorg'=>$kodeorg,
                                        'kodekegiatan'=>$bar->kodekegiatan,
                                        'kodeasset'=>'',
                                        'kodebarang'=>'',
                                        'nik'=>'',
                                        'kodecustomer'=>'',
                                        'kodesupplier'=>'',
                                        'noreferensi'=>'CT01',
                                        'noaruskas'=>'',
                                        'kodevhc'=>'',
                                        'nodok'=>'',
                                        'kodeblok'=>$bar->kodeorg,
                    'revisi'=>'0'
                                    );
                                    $noUrut++;
                            }                                                   
                  }                
                # Kredit (Kreditnya cukup satu saja)
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>str_replace("-","",$periode)."28",
                    'nourut'=>$noUrut,
                    'noakun'=>$kredit,
                    'keterangan'=>'Catu Beras -'.$periode,
                    'jumlah'=>-1*$jumlah,
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$kodeorg,
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>'',
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'CT01',
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'revisi'=>'0'
                );
                  $noUrut++;                     
        }
       else if($tipe=='TRAKSI')  
       {
            $debet='';
            $kredit='';
            $nojurnal=str_replace("-","",$periode)."28/".$kodeorg."/CT03/001";
            $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal where jurnalid='CT03'";
            $res=mysql_query($str);
            while($bar=mysql_fetch_object($res))
            {
                $debet=$bar->noakundebet;
                $kredit=$bar->noakunkredit;
            }
            if($debet=='' or $kredit=='')
            {
                exit('Error: Journal parameter for CT03 (Traksi) not defined, contact administrator');
            }
            $kodejurnal='CT03';       
            
            #1 Ambil semua kendaraan yang bekerja di bulan ini
            $str="select distinct kodevhc from ".$dbname.".vhc_runht where tanggal between  '".$tgl1."' and '".$tgl2."' 
                     and kodeorg ='".$kodeorg."'";
            $res=mysql_query($str);
            $jml_baris=  mysql_num_rows($res);
             //prepare jurnal
                # Prep Header
             $dataRes=Array();   
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodejurnal,
                    'tanggal'=>str_replace("-","",$periode)."28",
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>'1',
                    'totaldebet'=>$jumlah,
                    'totalkredit'=>($jumlah*-1),
                    'amountkoreksi'=>'0',
                    'noreferensi'=>'CT03',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'
                );

                # Data Detail
                $noUrut = 1;
                if($jml_baris==0){//jika tidak ada pekerjaan kendaraan
                        # Debet
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>str_replace("-","",$periode)."28",
                            'nourut'=>$noUrut,
                            'noakun'=>$debet,
                            'keterangan'=>'Catu Beras -'.$periode,
                            'jumlah'=>$jumlah,
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>$kodeorg,
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>'',
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>'CT03',
                            'noaruskas'=>'',
                            'kodevhc'=>'',
                            'nodok'=>'',
                            'kodeblok'=>'',
                    'revisi'=>'0'
                        );
                        $noUrut++;
                }
                else
                {
                    $byperkendaraan=$jumlah/$jml_baris;
                  while($bar=mysql_fetch_object($res))
                            {
                                # Debet
                                    $dataRes['detail'][] = array(
                                        'nojurnal'=>$nojurnal,
                                        'tanggal'=>str_replace("-","",$periode)."28",
                                        'nourut'=>$noUrut,
                                        'noakun'=>$debet,
                                        'keterangan'=>'Catu Beras -'.$periode,
                                        'jumlah'=>$byperkendaraan,
                                        'matauang'=>'IDR',
                                        'kurs'=>'1',
                                        'kodeorg'=>$kodeorg,
                                        'kodekegiatan'=>'',
                                        'kodeasset'=>'',
                                        'kodebarang'=>'',
                                        'nik'=>'',
                                        'kodecustomer'=>'',
                                        'kodesupplier'=>'',
                                        'noreferensi'=>'CT03',
                                        'noaruskas'=>'',
                                        'kodevhc'=>$bar->kodevhc,
                                        'nodok'=>'',
                                        'kodeblok'=>'',
                    'revisi'=>'0'
                                    );
                                    $noUrut++;
                            }  
                }
                # Kredit (Kreditnya cukup satu saja)
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>str_replace("-","",$periode)."28",
                    'nourut'=>$noUrut,
                    'noakun'=>$kredit,
                    'keterangan'=>'Catu Beras -'.$periode,
                    'jumlah'=>-1*$jumlah,
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$kodeorg,
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>'',
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'CT03',
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'revisi'=>'0'
                );
                  $noUrut++;            
            
       }
       else if($tipe=='PABRIK')  
       {
            $debet='';
            $kredit='';
            $nojurnal=str_replace("-","",$periode)."28/".$kodeorg."/CT04/001";
            $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal where jurnalid='CT04'";
            $res=mysql_query($str);
            while($bar=mysql_fetch_object($res))
            {
                $debet=$bar->noakundebet;
                $kredit=$bar->noakunkredit;
            }
            if($debet=='' or $kredit=='')
            {
                exit('Error: Journal parameter  CT04 (PKS) not defined');
            }
            $kodejurnal='CT04';       
            
            //ambil porsi biaya umum
                $byumum=0;
                $str="select sum(jumlahrupiah) as byumum from ".$dbname.".sdm_catu where periodegaji='".$periode."' 
                        and kodeorg='".$kodeorg."' and subbagian=''";
                $res=mysql_query($str);
                while($bar=mysql_fetch_object($res))
                {
                    $byumum=$bar->byumum;
                }
                $bystasiun=$jumlah-$byumum;
                
            
            #1 Ambil semua statiun yang ada dalam pks
            $str="select kodeorganisasi from ".$dbname.".organisasi where tipe='STATION' 
                     and induk ='".$kodeorg."'";

            $res=mysql_query($str);
            $jml_baris=  mysql_num_rows($res);      
             //prepare jurnal
                # Prep Header
             $dataRes=Array();   
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodejurnal,
                    'tanggal'=>str_replace("-","",$periode)."28",
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>'1',
                    'totaldebet'=>$jumlah,
                    'totalkredit'=>($jumlah*-1),
                    'amountkoreksi'=>'0',
                    'noreferensi'=>'CT04',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0'
                );

                # Data Detail
                $noUrut = 1;
                if($jml_baris==0){//jika tidak ada pekerjaan kendaraan
                        # Debet
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>str_replace("-","",$periode)."28",
                            'nourut'=>$noUrut,
                            'noakun'=>$debet,
                            'keterangan'=>'Catu Beras -'.$periode,
                            'jumlah'=>$jumlah,
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>$kodeorg,
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>'',
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>'CT04',
                            'noaruskas'=>'',
                            'kodevhc'=>'',
                            'nodok'=>'',
                            'kodeblok'=>'',
                    'revisi'=>'0'
                        );
                        $noUrut++;
                }
                else
                {
                  //biaya umum masuk dulu
                   if($byumum>0){ 
                    $dataRes['detail'][] = array(
                                        'nojurnal'=>$nojurnal,
                                        'tanggal'=>str_replace("-","",$periode)."28",
                                        'nourut'=>$noUrut,
                                        'noakun'=>$debet,
                                        'keterangan'=>'Catu Beras -'.$periode,
                                        'jumlah'=>$byumum,
                                        'matauang'=>'IDR',
                                        'kurs'=>'1',
                                        'kodeorg'=>$kodeorg,
                                        'kodekegiatan'=>'',
                                        'kodeasset'=>'',
                                        'kodebarang'=>'',
                                        'nik'=>'',
                                        'kodecustomer'=>'',
                                        'kodesupplier'=>'',
                                        'noreferensi'=>'CT04',
                                        'noaruskas'=>'',
                                        'kodevhc'=>'',
                                        'nodok'=>'',
                                        'kodeblok'=>'',
                    'revisi'=>'0'
                                    );
                                    $noUrut++;
                   }               
                  //bagi biaya station ke setiap station
                   $byperstasiun=$bystasiun/$jml_baris;
                  while($bar=mysql_fetch_object($res))
                            {
                                # Debet
                                    $dataRes['detail'][] = array(
                                        'nojurnal'=>$nojurnal,
                                        'tanggal'=>str_replace("-","",$periode)."28",
                                        'nourut'=>$noUrut,
                                        'noakun'=>$debet,
                                        'keterangan'=>'Catu Beras -'.$periode,
                                        'jumlah'=>$byperstasiun,
                                        'matauang'=>'IDR',
                                        'kurs'=>'1',
                                        'kodeorg'=>$kodeorg,
                                        'kodekegiatan'=>'',
                                        'kodeasset'=>'',
                                        'kodebarang'=>'',
                                        'nik'=>'',
                                        'kodecustomer'=>'',
                                        'kodesupplier'=>'',
                                        'noreferensi'=>'CT04',
                                        'noaruskas'=>'',
                                        'kodevhc'=>'',
                                        'nodok'=>'',
                                        'kodeblok'=>$bar->kodeorganisasi,
                    'revisi'=>'0'
                                    );
                                    $noUrut++;
                            }  
                }
                # Kredit (Kreditnya cukup satu saja)
                $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>str_replace("-","",$periode)."28",
                    'nourut'=>$noUrut,
                    'noakun'=>$kredit,
                    'keterangan'=>'Catu Beras -'.$periode,
                    'jumlah'=>-1*$jumlah,
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$kodeorg,
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>'',
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'CT04',
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'revisi'=>'0'
                );
                  $noUrut++;            
            
       }
       else//jika bukan pks,kebun atau traksi maka masuh biaya umum
       {
            $debet='';
            $kredit='';
            $nojurnal=str_replace("-","",$periode)."28/".$kodeorg."/CT01/001";
            $str="select noakundebet,noakunkredit from ".$dbname.".keu_5parameterjurnal where jurnalid='CT01'";
            $res=mysql_query($str);
            while($bar=mysql_fetch_object($res))
            {
                $debet=$bar->noakundebet;
                $kredit=$bar->noakunkredit;
            }
            if($debet=='' or $kredit=='')
            {
                exit('Error: Journal parameter CT01 (Kebun) not defined');
            }
            $kodejurnal='CT01';      
           //prepare jurnal
                # Prep Header
             $dataRes=Array();   
                $dataRes['header'] = array(
                    'nojurnal'=>$nojurnal,
                    'kodejurnal'=>$kodejurnal,
                    'tanggal'=>str_replace("-","",$periode)."28",
                    'tanggalentry'=>date('Ymd'),
                    'posting'=>'1',
                    'totaldebet'=>$jumlah,
                    'totalkredit'=>($jumlah*-1),
                    'amountkoreksi'=>'0',
                    'noreferensi'=>'CT01',
                    'autojurnal'=>'1',
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'revisi'=>'0',
                );

                # Data Detail
                $noUrut = 1;
                        # Debet
                        $dataRes['detail'][] = array(
                            'nojurnal'=>$nojurnal,
                            'tanggal'=>str_replace("-","",$periode)."28",
                            'nourut'=>$noUrut,
                            'noakun'=>$debet,
                            'keterangan'=>'Catu Beras -'.$periode,
                            'jumlah'=>$jumlah,
                            'matauang'=>'IDR',
                            'kurs'=>'1',
                            'kodeorg'=>$kodeorg,
                            'kodekegiatan'=>'',
                            'kodeasset'=>'',
                            'kodebarang'=>'',
                            'nik'=>'',
                            'kodecustomer'=>'',
                            'kodesupplier'=>'',
                            'noreferensi'=>'CT01',
                            'noaruskas'=>'',
                            'kodevhc'=>'',
                            'nodok'=>'',
                            'kodeblok'=>'',
                    'revisi'=>'0'
                        );
                        $noUrut++;
           $dataRes['detail'][] = array(
                    'nojurnal'=>$nojurnal,
                    'tanggal'=>str_replace("-","",$periode)."28",
                    'nourut'=>$noUrut,
                    'noakun'=>$kredit,
                    'keterangan'=>'Catu Beras -'.$periode,
                    'jumlah'=>-1*$jumlah,
                    'matauang'=>'IDR',
                    'kurs'=>'1',
                    'kodeorg'=>$kodeorg,
                    'kodekegiatan'=>'',
                    'kodeasset'=>'',
                    'kodebarang'=>'',
                    'nik'=>'',
                    'kodecustomer'=>'',
                    'kodesupplier'=>'',
                    'noreferensi'=>'CT01',
                    'noaruskas'=>'',
                    'kodevhc'=>'',
                    'nodok'=>'',
                    'kodeblok'=>'',
                    'revisi'=>'0'
                );
                  $noUrut++;
       }
   #execute

        #========================== Proses Insert dan Update ==========================
        #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Header
        $headErr = '';
        $insHead = insertQuery($dbname,'keu_jurnalht',$dataRes['header']);
        if(!mysql_query($insHead)) {
            $headErr .= 'Insert Header Error : '.mysql_error()."\n";
        }

        if($headErr=='') {
            #>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Insert Detail
            $detailErr = '';
            foreach($dataRes['detail'] as $row) {
                $insDet = insertQuery($dbname,'keu_jurnaldt',$row);
                if(!mysql_query($insDet)) {
                    $detailErr .= "Insert Detail Error : ".mysql_error()."\n";
                    break;
                }
            }

            if($detailErr=='') {
                #update sdm_catu status posting
                $str="update ".$dbname.".sdm_catu set posting=1 where kodeorg='".$kodeorg."' and periodegaji='".$periode."'";
                mysql_query($str);
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
}

function dates_inbetween($date1, $date2){

    $day = 60*60*24;

    $date1 = strtotime($date1);
    $date2 = strtotime($date2);

    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between

    $dates_array = array();
    $dates_array[] = date('Y-m-d',$date1);
   
    for($x = 1; $x < $days_diff; $x++){
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }

    $dates_array[] = date('Y-m-d',$date2);
    if($date1==$date2){
        $dates_array = array();
        $dates_array[] = date('Y-m-d',$date1);        
    }
    return $dates_array;
}
?>