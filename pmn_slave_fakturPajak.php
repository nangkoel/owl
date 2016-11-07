<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/fpdf.php');
include_once('lib/zLib.php');

$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['komoditi']==''?$komoditi=$_GET['komoditi']:$komoditi=$_POST['komoditi'];
$_POST['kontrak']==''?$kontrak=$_GET['kontrak']:$kontrak=$_POST['kontrak'];
$_POST['kodept']==''?$kodept=$_GET['kodept']:$kodept=$_POST['kodept'];
$_POST['jenispajak']==''?$jenispajak=$_GET['jenispajak']:$jenispajak=$_POST['jenispajak'];
$_POST['tgl']==''?$tgl=$_GET['tgl']:$tgl=$_POST['tgl'];
$tgl=tanggalsystem($tgl); $tgl=substr($tgl,0,4).'-'.substr($tgl,4,2).'-'.substr($tgl,6,2);
$_POST['timbangan']==''?$timbangan=$_GET['timbangan']:$timbangan=$_POST['timbangan'];
$_POST['biaya']==''?$biaya=$_GET['biaya']:$biaya=$_POST['biaya'];
$_POST['dari']==''?$dari=$_GET['dari']:$dari=$_POST['dari'];
$_POST['sd']==''?$sd=$_GET['sd']:$sd=$_POST['sd'];
$dari=tanggalsystem($dari); $dari=substr($dari,0,4).'-'.substr($dari,4,2).'-'.substr($dari,6,2);
$sd=tanggalsystem($sd); $sd=substr($sd,0,4).'-'.substr($sd,4,2).'-'.substr($sd,6,2);
$_POST['curr']==''?$curr=$_GET['curr']:$curr=$_POST['curr'];
$_POST['valas']==''?$valas=$_GET['valas']:$valas=$_POST['valas'];
$_POST['kurs']==''?$kurs=$_GET['kurs']:$kurs=$_POST['kurs'];
$_POST['jml']==''?$jml=$_GET['jml']:$jml=$_POST['jml'];
$_POST['potharga']==''?$potharga=$_GET['potharga']:$potharga=$_POST['potharga'];
$_POST['potum']==''?$potum=$_GET['potum']:$potum=$_POST['potum'];
$_POST['persenppn']==''?$persenppn=$_GET['persenppn']:$persenppn=$_POST['persenppn'];
$_POST['nofaktur']==''?$nofaktur=$_GET['nofaktur']:$nofaktur=$_POST['nofaktur'];
$_POST['customer']==''?$customer=$_GET['customer']:$customer=$_POST['customer'];
$_POST['dasarpajak']==''?$dasarpajak=$_GET['dasarpajak']:$dasarpajak=$_POST['dasarpajak'];
$_POST['ppn']==''?$ppn=$_GET['ppn']:$ppn=$_POST['ppn'];
$_POST['ttd']==''?$ttd=$_GET['ttd']:$ttd=$_POST['ttd'];
$_POST['kodetimbangan']==''?$kodetimbangan=$_GET['kodetimbangan']:$kodetimbangan=$_POST['kodetimbangan'];

switch($proses){
case 'kodetimb':
    $s_timb="select kodetimbangan from ".$dbname.".pmn_4customer where kodecustomer='".$customer."' ";
    $q_timb=mysql_query($s_timb) or die(mysql_error($conn));
    $r_cust=mysql_fetch_assoc($q_timb);
    $kodetimbangan=$r_cust['kodetimbangan'];
    echo $kodetimbangan;
break;
case 'loadkontrak':
    $optkontrak="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $s_kontrak="select nokontrak from ".$dbname.".pmn_kontrakjual where kodebarang='".$komoditi."' order by nokontrak desc";
    $q_kontrak=mysql_query($s_kontrak) or die(mysql_error($conn));
    while($r_kontrak=mysql_fetch_assoc($q_kontrak))
    {
        $optkontrak.="<option value='".$r_kontrak['nokontrak']."'>".$r_kontrak['nokontrak']."</option>";  
        
    }
    echo $optkontrak;
break;
case 'loadcurr':
    $s_curr="select matauang from ".$dbname.".pmn_kontrakjual where nokontrak='".$kontrak."'";
    $q_curr=mysql_query($s_curr) or die(mysql_error($conn));
    $r_curr=mysql_fetch_assoc($q_curr);
    $curr=$r_curr['matauang'];
    echo $curr;
break;
case 'loadfaktur':
    $s_npwp="select right(npwp,3) as npwp from ".$dbname.".setup_org_npwp where kodeorg='".$kodept."'";
    $q_npwp=mysql_query($s_npwp) or die (mysql_error($conn));
    $r_npwp=mysql_fetch_assoc($q_npwp);
    $npwp=$r_npwp['npwp'];
    if(strlen($jenispajak)<3){
            $jp="0".$jenispajak;
        }
    else{
            $jp=$jenispajak;
        }    
    $thnSkrng=date("Y");
    $thn=substr($thnSkrng,2,2);
    $nofaktur=$jp.".".$npwp.".".$thn;
    $s_data="select distinct nofaktur from ".$dbname.".pmn_faktur where nofaktur like '%".$nofaktur."%' order by nofaktur desc ";
    //exit("error".$s_data);
    $q_data=mysql_query($s_data) or die (mysql_error($conn));
    $r_data=mysql_fetch_assoc($q_data);
    $thndata=substr($r_data['nofaktur'],8,2);
    $urut=intval(substr($r_data['nofaktur'],11,8));
    //exit("error".$nofaktur);
    if($thn!=$thndata)
    {
        $urut=1;
    }
    else
    {
        $urut++;
    }
    $counter=addZero($urut,8);
    $nofaktur=$jp.".".$npwp.".".$thn.".".$counter;
    echo $nofaktur;
break;
case 'loadvol':  
    #Volume: 
    # Jika atas biaya uang muka maka vol diambil dari kuantitas kontrak, jika bukan maka 
    #   jika timbangan sendiri ambil sum(berat bersih) dari kg timbangan dari tanggal s/d tanggal, 
    #   jika timbangan pembeli maka sum(kgpembeli) dari kg timbangan dari tanggal s/d tanggal dari table pabrik timbangan
    if($biaya=='Uang Muka'){
        $s_qtykontrak = "select kuantitaskontrak from ".$dbname.".pmn_kontrakjual
                         where nokontrak='".$kontrak."' ";
        $q_qtykontrak = mysql_query($s_qtykontrak) or die(mysql_error($conn));
        $r_qtykontrak = mysql_fetch_assoc($q_qtykontrak);
        $vol=$r_qtykontrak['kuantitaskontrak'];
    }
    else{
        if($timbangan=='Sendiri'){
            $s_wb="select sum(a.beratbersih) as total from ".$dbname.".pabrik_timbangan a
                   left join ".$dbname.".pmn_4customer b on a.kodecustomer=b.kodetimbangan
                   where a.kodebarang = '".$komoditi."' and b.kodecustomer='".$customer."'
                   and b.kodetimbangan = '".$kodetimbangan."'
                   and substr(a.tanggal,1,10) between '".$dari."' and '".$sd."' ";
//            exit("error: ".$s_wb);
            $q_wb=mysql_query($s_wb) or die(myswl_error($conn));
            $r_wb=mysql_fetch_assoc($q_wb);
            $vol=$r_wb['total'];
        }
        else{
            $s_wb="select sum(a.kgpembeli) as total from ".$dbname.".pabrik_timbangan a
                   left join ".$dbname.".pmn_4customer b on a.kodecustomer=b.kodetimbangan
                   where a.kodebarang = '".$komoditi."' and b.kodecustomer='".$customer."'
                   and b.kodetimbangan = '".$kodetimbangan."'
                   and substr(a.tanggal,1,10) between '".$dari."' and '".$sd."' ";
            $q_wb=mysql_query($s_wb) or die(myswl_error($conn));
            $r_wb=mysql_fetch_assoc($q_wb);
            $vol=$r_wb['total'];
        }
    }
    #Jumlah Rp: 
    #  jika mata uang asing maka valas kali kurs, 
    #  jika RP maka diisi dari harga satuan kontrak dikali volume
    if($curr!='IDR'){
        $jml=$valas*$kurs;
    }
    else{
        $s_harga="select hargasatuan from ".$dbname.".pmn_kontrakjual where nokontrak='".$kontrak."'";
        $q_harga=mysql_query($s_harga) or die(mysql_error($conn));
        $r_harga=mysql_fetch_assoc($q_harga);
        $harga=$r_harga['hargasatuan'];
        $jml=$harga*$vol;
    }
    #Pot uang Muka: 
    # Jika atas biaya bukan uang muka maka sum(jumlah) dari table ditambah jumlah dari form 
    # dikurang uang muka dari table, 
    #  jika hasilnya minus, maka dikali -1 dan masukkan ke field, 
    #  jika hasilnya plus maka isi field dengan 0
    if($biaya!='Uang Muka'){
        $s_uangmuka="select sum(jumlah) as jml,sum(potumuka) as potumuka from ".$dbname.".pmn_faktur 
                     where nokontrak='".$kontrak."' and kodept = '".$kodept."' ";
//        exit("error: ".$s_uangmuka);
        $q_uangmuka=mysql_query($s_uangmuka) or die(mysql_error($conn));
        $r_uangmuka=mysql_fetch_assoc($q_uangmuka);
        $tjumlah=$r_uangmuka['jml'];
        $uangmuka=$r_uangmuka['potumuka'];
        $potongan=$tjumlah+$jml-$uangmuka;
        
        if($potongan<0){
            $potum=$potongan*(-1);
        }
        else{
            $potum=0;
        }
    }
echo number_format($vol,0)."###".number_format($jml,2)."###".number_format($potum,2);
break;
case 'ppn':
    #Dasar P.pajak:
    #Jumlah-Pot.harga-Pot.Umuka,jika plus maka isikan ke Dasar P.Pajak, Jika minus, maka isikan 0
    if($jenispajak=='10'){
        $pajak=0;
    }
    
    else{
        $pajak=$jml-$potharga-$potum;
    }
    
    //    exit("error:"."=".$jml."-".$potharga."-".$potum);
    if($pajak>0){
        $dasarpajak=$pajak;
    }
    else{
        $dasarpajak=0;
    }
    #PPn(Rp): Dasar P.Pajak*persen PPn/100
    @$ppn=($dasarpajak*$persenppn)/100;
    echo number_format($dasarpajak,2)."###".number_format($ppn,2);
break;
case 'insert':
    $tglfaktur=$_POST['tgl'];
    $tgldari=$_POST['dari'];
    $tglsd=$_POST['sd'];
    $warning='';
    if($kodept=='')$warning.=" ".$_SESSION['lang']['kodept'].", ";
    if($customer=='')$warning.=" ".$_SESSION['lang']['Pembeli'].",";
    if($tglfaktur=='')$warning.=" ".$_SESSION['lang']['tglfaktur'].",";
    if($komoditi=='')$warning.=" ".$_SESSION['lang']['komoditi'].",";
    if($kontrak=='')$warning.=" ".$_SESSION['lang']['kontrak'].",";
    if($jenispajak=='')$warning.=" ".$_SESSION['lang']['jenispajak'].",";
    if($timbangan=='')$warning.=" ".$_SESSION['lang']['timbangan'].",";
    if($biaya=='')$warning.=" ".$_SESSION['lang']['atasbiaya'].",";
    if($tgldari=='')$warning.=" ".$_SESSION['lang']['tgldari'].",";
    if($tglsd=='')$warning.=" ".$_SESSION['lang']['tanggalsampai'].",";
    if($potharga=='')$warning.=" ".$_SESSION['lang']['potharga'].",";
    if($ttd=='')$warning.=" ".$_SESSION['lang']['penandatangan'].",";
    if($warning!=''){
        echo "error: Please fill ".$warning.".";
        exit;
    }  
    $s_harga="select hargasatuan from ".$dbname.".pmn_kontrakjual where nokontrak='".$kontrak."'";
    $q_harga=mysql_query($s_harga) or die(mysql_error($conn));
    $r_harga=mysql_fetch_assoc($q_harga);
    $harga=$r_harga['hargasatuan'];
    
    $s_cek="select distinct nofaktur from ".$dbname.".pmn_faktur where nofaktur='".$nofaktur."'";
    $q_cek=mysql_query($s_cek) or die(mysql_error($conn));
    $r_cek=mysql_num_rows($q_cek);
    if($r_cek<1)
    {
        $insert="insert into ".$dbname.".pmn_faktur
                (nofaktur,kodept,partner,tanggalfaktur,nokontrak,matauang,daritanggal,sdtanggal,atasbiaya,persenppn,
                 hargaasing,hargarupiah,jumlah,potongan,potumuka,dasarpajak,rupiahppn,penandatangan,kurs,post)
                 values('".$nofaktur."','".$kodept."','".$customer."','".$tgl."','".$kontrak."','".$curr."',
                 '".$dari."','".$sd."','".$biaya."','10',".$valas.",'".$harga."','".$jml."','".$potharga."',
                 '".$potum."','".$dasarpajak."','".$ppn."','".$ttd."','".$kurs."','0')";
//        exit("error:".$insert);
        if(!mysql_query($insert)) 
        {
            echo " Gagal,".addslashes(mysql_error($conn));
        }
    }
break;
case 'loaddata':
        $limit=10;
        $page=0;
        if(isset($_POST['page']))
        {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
        }
       
        $sCount="select count(*) as jmlhrow from ".$dbname.".pmn_faktur order by nofaktur asc";
        $qCount=mysql_query($sCount) or die(mysql_error());
        while($rCount=mysql_fetch_object($qCount)){
            $jlhbrs= $rCount->jmlhrow;
        }
        
        $offset=$page*$limit;
        if($jlhbrs<($offset))$page-=1;
        $offset=$page*$limit;
        $no=$offset;
        
        $sShow="select a.nofaktur as nofaktur,a.tanggalfaktur as tanggalfaktur,a.partner as partner,
                a.nokontrak as nokontrak,c.namabarang as komoditi,a.jumlah as jumlah,a.rupiahppn as rupiahppn,
                a.post as post
                from ".$dbname.".pmn_faktur a
                left join ".$dbname.".pmn_kontrakjual b on a.nokontrak=b.nokontrak
                left join ".$dbname.".log_5masterbarang c on b.kodebarang=c.kodebarang
                order by a.nofaktur asc limit ".$offset.",".$limit." ";
//        exit("error:".$sShow);
        $qShow=mysql_query($sShow) or die(mysql_error());
        echo "<table cellspacing=1 border=0 class='sortable'>
              <thead>
                <tr class=rowcontent>
                    <td align=center>No.</td>
                    <td align=center>".$_SESSION['lang']['nofp']."</td>
                    <td align=center>".$_SESSION['lang']['tanggal']."</td>
                    <td align=center>".$_SESSION['lang']['Pembeli']."</td>
                    <td align=center>".$_SESSION['lang']['kontrak']."</td>
                    <td align=center>".$_SESSION['lang']['komoditi']."</td>
                    <td align=center>".$_SESSION['lang']['jumlah']."</td>
                    <td align=center>".$_SESSION['lang']['ppn']." (Rp)</td>
                    <td align=center colspan=3>".$_SESSION['lang']['action']."</td>
                 </tr></thead>";
        while($row=mysql_fetch_assoc($qShow))
        {
            $no+=1;
            echo"<tr class=rowcontent>
            <td id='no' align='center'>".$no."</td>
            <td id='nofaktur_".$no."' align='left'>".$row['nofaktur']."</td>
            <td id='tgl_".$no."' align='left'>".$row['tanggalfaktur']."</td>
            <td id='rekanan_".$no."' align='left'>".$row['partner']."</td>
            <td id='nokontrak_".$no."' align='left'>".$row['nokontrak']."</td>
            <td id='komoditi_".$no."' align='left'>".$row['komoditi']."</td>
            <td id='jml_".$no."' align='right'>".number_format($row['jumlah'],0)."</td>
            <td id='ppn_".$no."' align='right'>".number_format($row['rupiahppn'],0)."</td>";
            if($row['post']>0){
                echo "<td colspan=3 align=center><img onclick=\"printPDF(event,'".$no."');\" title=\"PDF\" class=\"zImgBtn\" src=\"images/skyblue/pdf.jpg\"></td>";
            }
            else{
                echo "<td align=center><img onclick=\"posting('".$no."');\" title=\"Posting\" class=\"zImgBtn\" src=\"images/skyblue/posting.png\"></td>
                <td align=center><img onclick=\"deletefaktur('".$no."');\" title=\"Delete\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>
                <td align=center><img onclick=\"printPDF(event,'".$no."');\" title=\"PDF\" class=\"zImgBtn\" src=\"images/skyblue/pdf.jpg\"></td>";
            }
            echo"</tr>";
        }
        echo"<tr class=rowheader><td colspan=9 align=center>
        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
        <button class=mybutton onclick=pages(".($page-1).");>".$_SESSION['lang']['pref']."</button>
        <button class=mybutton onclick=pages(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
        </td>
        </tr></table>";
break;
case 'daftarfaktur':
    /*$limit=10;
    $page=0;
    if(isset($_POST['page']))
    {
        $page=$_POST['page'];
        if($page<0)
        $page=0;
    }

    $sCount="select count(*) as jmlhrow from ".$dbname.".pmn_faktur 
             where nokontrak like '%".$_POST['nokontrak']."%' 
             and substr(tanggalfaktur,1,7) like '%".$_POST['bulan']."%' 
             and partner like '%".$_POST['rekanan']."%' order by nofaktur asc";
    $qCount=mysql_query($sCount) or die(mysql_error());
    while($rCount=mysql_fetch_object($qCount)){
        $jlhbrs= $rCount->jmlhrow;
    }

    $offset=$page*$limit;
    if($jlhbrs<($offset))$page-=1;
    $offset=$page*$limit;
    $no=$offset;
   */     
    $s_daftar="select a.nofaktur as nofaktur,a.tanggalfaktur as tanggalfaktur,a.partner as partner,
               a.nokontrak as nokontrak,c.namabarang as komoditi,a.jumlah as jumlah,a.rupiahppn as rupiahppn,
               a.post as post
               from ".$dbname.".pmn_faktur a
               left join ".$dbname.".pmn_kontrakjual b on a.nokontrak=b.nokontrak 
               left join ".$dbname.".log_5masterbarang c on b.kodebarang=c.kodebarang
               where a.nokontrak like '%".$_POST['nokontrak']."%' 
               and substr(tanggalfaktur,1,7) like '%".$_POST['bulan']."%' 
               and partner like '%".$_POST['rekanan']."%' order by a.nofaktur asc";
    $q_daftar=mysql_query($s_daftar) or die(mysql_error());
    echo "<table cellspacing=1 border=0 class='sortable'>
              <thead>
                <tr class=rowcontent>
                    <td align=center>No.</td>
                    <td align=center>No. Faktur</td>
                    <td align=center>Tanggal</td>
                    <td align=center>Rekanan</td>
                    <td align=center>No. Kontrak</td>
                    <td align=center>Komoditi</td>
                    <td align=center>Jumlah (Rp)</td>
                    <td align=center>PPn (Rp)</td>
                    <td align=center colspan=3>Aksi</td>
                 </tr></thead><tbody id=contain>";
    while($row=mysql_fetch_assoc($q_daftar)){
        $no+=1;
        echo"<tr class=rowcontent>
        <td id='no' align='center'>".$no."</td>
        <td id='nofaktur_".$no."' align='left'>".$row['nofaktur']."</td>
        <td id='tgl_".$no."' align='left'>".$row['tanggalfaktur']."</td>
        <td id='rekanan_".$no."' align='left'>".$row['partner']."</td>
        <td id='nokontrak_".$no."' align='left'>".$row['nokontrak']."</td>
        <td id='komoditi_".$no."' align='left'>".$row['komoditi']."</td>
        <td id='jml_".$no."' align='right'>".number_format($row['jumlah'],0)."</td>
        <td id='ppn_".$no."' align='right'>".number_format($row['rupiahppn'],0)."</td>";
        if($row['post']>0){
            echo "<td colspan=3 align=center><img onclick=\"printPDF(event,'".$no."');\" title=\"PDF\" class=\"zImgBtn\" src=\"images/skyblue/pdf.jpg\"></td>";
        }
        else{
            echo "<td align=center><img onclick=\"posting('".$no."');\" title=\"Posting\" class=\"zImgBtn\" src=\"images/skyblue/posting.png\"></td>
            <td align=center><img onclick=\"deletefaktur('".$no."');\" title=\"Delete\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\"></td>
            <td align=center><img onclick=\"printPDF(event,'".$no."');\" title=\"PDF\" class=\"zImgBtn\" src=\"images/skyblue/pdf.jpg\"></td>";
        }
        echo"</tr></tbody>";
   }
   echo "</table>";
   /*echo"<tr class=rowheader><td colspan=9 align=center>
        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
        <button class=mybutton onclick=pages2(".($page-1).");>".$_SESSION['lang']['pref']."</button>
        <button class=mybutton onclick=pages2(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
        </td>
        </tr></table>";
   */
break;
case 'posting':
    $s_cekpost="select * from ".$dbname.".pmn_faktur where nofaktur='".$nofaktur."' and post=0";
    $q_cekpost=mysql_query($s_cekpost) or die(mysql_error());
    $r_cekpost=mysql_num_rows($q_cekpost);
    if($r_cekpost>0)
    {
        $s_posting="update ".$dbname.".pmn_faktur set post='1' where nofaktur='".$nofaktur."'";
        if(!mysql_query($s_posting)){
            echo "DB Error : ".mysql_error($conn);
            exit();
        }
    }
    else{
        
        echo"warning:Sudah Terposting";
        exit();
    }
break;
case 'delete':
    $s_delete="delete from ".$dbname.".pmn_faktur where nofaktur='".$nofaktur."'";
    if(!mysql_query($s_delete)){
            echo "DB Error : ".mysql_error($conn);
            exit();
    }
break;
case 'pdf':
    $s_pdf="select a.nofaktur as nofaktur,a.partner as namapartner,f.namaorganisasi as namapt,d.alamatnpwp as alamatpt,
            d.npwp as npwppt,e.alamat as alamatpartner,e.npwp as npwppartner,c.namabarang as namabarang,
            a.nokontrak as kontrak,g.noorder as do,a.hargaasing as valas, a.jumlah as jml,a.potongan as potharga,
            a.potumuka as potum,a.dasarpajak as pajak,a.rupiahppn as ppn,h.namakaryawan as ttd,d.kota as kota,
            i.namajabatan as jabatan
            from ".$dbname.".pmn_faktur a
            left join ".$dbname.".pmn_kontrakjual b on a.nokontrak=b.nokontrak 
            left join ".$dbname.".log_5masterbarang c on b.kodebarang=c.kodebarang
            left join ".$dbname.".setup_org_npwp d on a.kodept=d.kodeorg
            left join ".$dbname.".pmn_4customer e on a.partner=e.namacustomer
            left join ".$dbname.".organisasi f on a.kodept=f.kodeorganisasi
            left join ".$dbname.".pmn_doht g on a.nokontrak=g.noorder
            left join ".$dbname.".datakaryawan h on a.penandatangan=h.karyawanid
            left join ".$dbname.".sdm_5jabatan i on h.kodejabatan=i.kodejabatan
            where a.nofaktur like '%".$nofaktur."%'";
//    exit("error:".$s_pdf);
    $q_pdf=mysql_query($s_pdf) or die(mysql_error($conn));
    while($r_pdf=mysql_fetch_assoc($q_pdf)){
        $no+=1;
        $nofaktur=$r_pdf['nofaktur'];
        $namapt=$r_pdf['namapt'];
        $alamatpt=$r_pdf['alamatpt'];
//        $kotattd=substr($alamatpt,38,9);
        $npwppt=$r_pdf['npwppt'];
        $namapartner=$r_pdf['namapartner'];
        $alamatpartner=$r_pdf['alamatpartner'];
        $npwppartner=$r_pdf['npwppartner'];
        $barang=$r_pdf['namabarang'];
        $kontrak=$r_pdf['kontrak'];
        $valas=$r_pdf['valas'];
        $do=$r_pdf['do'];
        $jml=$r_pdf['jml'];
        $potharga=$r_pdf['potharga'];
        $potum=$r_pdf['potum'];
        $pajak=$r_pdf['pajak'];
        $ppn=$r_pdf['ppn'];
        $ttd=$r_pdf['ttd'];
        $kotattd=$r_pdf['kota'];
        $jabatan=$r_pdf['jabatan'];
    }
//    exit("error:".$namapt);
    class PDF extends FPDF {
    function Header() {
        global $dbname;
        global $luas;
        global $wkiri, $wlain;
        global $luasbudg, $luasreal;
            $width = $this->w - $this->lMargin - $this->rMargin;
        
        $height = 12;
        $this->SetFillColor(255,255,255);
        $this->SetFont('Arial','',6.5);
        
        $this->Cell(($width-30)/2,$height,'',NULL,0,'L',1);
        $this->Cell(($width-30)/2,$height,'Lembar ke-1: Untuk Pembeli BKP/Penerima JKP Sebagai Bukti Pajak Masukan',NULL,0,'L',1);
        $this->Ln();
        $this->Cell(($width-30)/2,$height,'',NULL,0,'L',1);
        $this->Cell(($width-30)/2,$height,'Lembar ke-2: Untuk BKP yang MENERBITKAN Faktur Pajak Standar Sebagai Bukti Pajak Keluar',NULL,0,'L',1);
        $this->Ln();
        $this->Cell(($width-30)/2,$height,'',NULL,0,'L',1);
        $this->Cell(($width-30)/2,$height,'Lembar ke-3: Untuk KPP dalam hal Penyerahan BKP/JKP dilakukan kepada Pemungut PPN',NULL,0,'L',1);
        $this->Ln();$this->Ln();$this->Ln();
  
        $height = 20;
        $this->SetFillColor(220,220,220);
        $this->SetFont('Arial','B',12);
        
        $this->Cell($width,$height,'FAKTUR PAJAK',NULL,0,'C',1);
        $this->Ln();
    }
    function Footer()
    {
//        $this->SetY(-15);
//        $this->SetFont('Arial','I',8);
//        $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
    }
}
    //================================
    $pdf=new PDF('P','pt','A4');
    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
    $height = 15;
    $pdf->AddPage();
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',9);
    
    $no=1;
// pdf array content =========================================================================
    $pdf->Cell(150,$height,'Kode dan Nomor Seri Faktur Pajak',TBLR,0,'L',1);	
    $pdf->Cell(10,$height,':',TB,0,'L',1);	
    $pdf->Cell(379,$height,$nofaktur,TBR,0,'L',1);
    $pdf->Ln();
    $pdf->Cell($width,$height,'Pengusaha Kena Pajak',TBLR,0,'L',1);
    $pdf->Ln();
    $pdf->Cell(150,$height,'Nama',TL,0,'L',1);	
    $pdf->Cell(10,$height,':',T,0,'L',1);	
    $pdf->Cell(379,$height,$namapt,TR,0,'L',1);
    $pdf->Ln();
    $pdf->Cell(150,$height,'Alamat',L,0,'L',1);	
    $pdf->Cell(10,$height,':',0,0,'L',1);	
    $pdf->Cell(379,$height,$alamatpt,R,0,'L',1);
    $pdf->Ln();
    $pdf->Cell(150,$height,'N.P.W.P',LB,0,'L',1);	
    $pdf->Cell(10,$height,':',B,0,'L',1);	
    $pdf->Cell(379,$height,$npwppt,RB,0,'L',1);
    $pdf->Ln();
    $pdf->Cell($width,$height,'Pembeli Barang Kena Pajak/Penerima Jasa Kena Pajak',TBLR,0,'L',1);
    $pdf->Ln();
    $pdf->Cell(150,$height,'Nama',TL,0,'L',1);	
    $pdf->Cell(10,$height,':',T,0,'L',1);	
    $pdf->Cell(379,$height,$namapartner,TR,0,'L',1);
    $pdf->Ln();
    $pdf->Cell(150,$height,'Alamat',L,0,'L',1);	
    $pdf->Cell(10,$height,':',0,0,'L',1);	
    $pdf->Cell(379,$height,$alamatpartner,R,0,'L',1);
    $pdf->Ln();
    $pdf->Cell(150,$height,'N.P.W.P',LB,0,'L',1);	
    $pdf->Cell(10,$height,':',B,0,'L',1);	
    $pdf->Cell(379,$height,$npwppartner,RB,0,'L',1);
    $pdf->Ln();
    $pdf->Cell(25,$height,'No.',TLR,0,'C',1);
    $pdf->Cell(214,$height,'Nama Barang Kena Pajak/Jasa Kena Pajak',TBLR,0,'C',1);
    $pdf->Cell(300,$height,'Harga Jual/Penggantian/Uang Muka/Termin',TLR,0,'C',1);
    $pdf->Ln();
    $pdf->Cell(25,$height,'Urut',BLR,0,'C',1);
    $pdf->Cell(214,$height,'',BLR,0,'C',1);
    $pdf->Cell(150,$height,'Valas *)',TBLR,0,'C',1);
    $pdf->Cell(150,$height,'(Rp)',TBLR,0,'C',1);
    $pdf->Ln();
    $pdf->Cell(25,$height,$no,TLR,0,'C',1);
    $pdf->Cell(214,$height,$barang,TLR,0,'L',1);
    $pdf->Cell(150,$height,'',TLR,0,'R',1);
    $pdf->Cell(150,$height,'',TLR,0,'R',1);
    $pdf->Ln();
    $pdf->Cell(25,$height,'',LR,0,'C',1);
    $pdf->Cell(214,$height,'Kontrak No.'.$kontrak,LR,0,'L',1);
    $pdf->Cell(150,$height,number_format($valas,0),LR,0,'R',1);
    $pdf->Cell(150,$height,number_format($jml,0),LR,0,'R',1);
    $pdf->Ln();
    $pdf->Cell(25,$height,'',BLR,0,'C',1);
    $pdf->Cell(214,$height,'DO No.'.$do,BLR,0,'L',1);
    $pdf->Cell(150,$height,'',BLR,0,'R',1);
    $pdf->Cell(150,$height,'',BLR,0,'R',1);
    $pdf->Ln();
    $pdf->Cell(239,$height,'Harga Jual/ Uang Muka',TBLR,0,'L',1);
    $pdf->Cell(150,$height,'',TBLR,0,'R',1);
    $pdf->Cell(150,$height,number_format($jml,0),TBLR,0,'R',1);
    $pdf->Ln();
    $pdf->Cell(239,$height,'Dikurangi Potongan Harga',TBLR,0,'L',1);
    $pdf->Cell(150,$height,'',TBLR,0,'R',1);
    $pdf->Cell(150,$height,number_format($potharga,0),TBLR,0,'R',1);
    $pdf->Ln();
    $pdf->Cell(239,$height,'Dikurangi Uang Muka Yang Telah Diterima',TBLR,0,'L',1);
    $pdf->Cell(150,$height,'',TBLR,0,'R',1);
    $pdf->Cell(150,$height,number_format($potum,0),TBLR,0,'R',1);
    $pdf->Ln();
    $pdf->Cell(239,$height,'Dasar Pengenaan Pajak',TBLR,0,'L',1);
    $pdf->Cell(150,$height,'',TBLR,0,'R',1);
    $pdf->Cell(150,$height,number_format($pajak,0),TBLR,0,'R',1);
    $pdf->Ln();
    $pdf->Cell(239,$height,'PPN=10%xDasar Pengenaan Pajak',TBLR,0,'L',1);
    $pdf->Cell(150,$height,'',TBLR,0,'R',1);
    $pdf->Cell(150,$height,number_format($ppn,0),TBLR,0,'R',1);
    $pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
    $pdf->Cell(239,$height,'',0,0,'L',1);
    $pdf->Cell(150,$height,'',0,0,'R',1);
    $date=date('Ymd');
    $pdf->Cell(150,$height,$kotattd.", ".substr($date,6,2)." ".numToMonth(substr($date,4,2),$lang='I',$format='long')." ".substr($date,0,4) ,0,0,'C',1);
    $pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
    $pdf->Cell(239,$height,'',0,0,'L',1);
    $pdf->Cell(150,$height,'',0,0,'R',1);
    $pdf->Cell(150,$height,$ttd,B,0,'C',1);
    $pdf->Ln();
    $pdf->Cell(239,$height,'',0,0,'L',1);
    $pdf->Cell(150,$height,'',0,0,'R',1);
    $pdf->Cell(150,$height,$jabatan,T,0,'C',1);
    $pdf->Output();	
break;
default:
break;	
}
?>