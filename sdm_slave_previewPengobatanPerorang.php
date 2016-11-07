<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, e.lokasitugas as loktug,nama from ".$dbname.".sdm_pengobatanht a left join
      ".$dbname.".sdm_5rs b on a.rs=b.id 
	  left join ".$dbname.".datakaryawan c
	  on a.karyawanid=c.karyawanid
	  left join ".$dbname.".sdm_5diagnosa d
	  on a.diagnosa=d.id
          left join ".$dbname.".datakaryawan e
	  on a.karyawanid=e.karyawanid
        left join ".$dbname.".sdm_karyawankeluarga f
        on a.ygsakit=f.nomor
	  where a.periode like '".$_POST['tahun']."%'
	  and a.karyawanid = ".$_POST['karyawanid']."
          order by a.updatetime desc, a.tanggal desc";
$res=mysql_query($str);
$tab="<table class=sortable cellspacing=1 border=0 width=1200px>
    <thead>
    <tr class=rowheader>
        <td>No</td>
        <td width=30>".$_SESSION['lang']['tanggal']."</td>
        <td width=200>".$_SESSION['lang']['jenis']."</td>            
        <td width=200>".$_SESSION['lang']['namakaryawan']."</td>
        <td>".$_SESSION['lang']['pasien']."</td>
        <td width=150>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['pasien']."</td>
        <td width=150>".$_SESSION['lang']['rumahsakit']."</td>
        <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>
        <td>".$_SESSION['lang']['diagnosa']."</td>
         <td>Obat/Drugs</td>           
    </tr>
    </thead>
    
    <tbody id='container'>";
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
        #ambil obat-obatan
        $str2="select namaobat,jenis from ".$dbname.".sdm_pengobatandt where notransaksi='".$bar->notransaksi."'";
        $resxx=mysql_query($str2);
        while($barxx=mysql_fetch_object($resxx))
        {
            $obat.= $barxx->namaobat." [".$barxx->jenis."]";
        }
        
	if($pasien=='')$pasien='AsIs';				  
        $tab.="<tr class=rowcontent>
            <td>".$no."</td>
            <td>".tanggalnormal($bar->tanggal)."</td>
            <td>".$bar->kodebiaya."</td>
            <td>".$bar->namakaryawan."</td>
            <td>".$pasien."</td>
            <td>".$bar->nama."</td>
            <td>".$bar->namars."[".$bar->kota."]"."</td>
            <td align=right>".number_format($bar->totalklaim,2,'.',',')."</td>
            <td>".$bar->ketdiag."</td>
             <td>".$obat."</td>
        </tr>";	 
    }

$tab.="</tbody>
    <tfoot>
    </tfoot>
    </table>";
echo $tab;	
?>
