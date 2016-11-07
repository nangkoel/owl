<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//	$pt=$_POST['pt']; source: log_laporanHutangSupplier.php
        $unit=$_POST['unit'];
        $periode=$_POST['periode'];

if($periode==''){
        echo "Warning: Period is oblogatory"; exit;
}

$str="select induk from ".$dbname.".organisasi
      where kodeorganisasi ='".$unit."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $induk=$bar->induk;
        $hasil.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}
$str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
      where kodeorg ='".$unit."' and periode='".$periode."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
        $tanggalmulai=$bar->tanggalmulai;
        $tanggalsampai=$bar->tanggalsampai;
}

if($_SESSION['language']=='EN'){
    $zz=' b.namaakun1';
}
else{
    $zz='b.namaakun';
}
        $str="select a.nojurnal as nojurnal, a.tanggal as tanggal, a.keterangan as keterangan, a.noakun as noakun, ".$zz." as namaakun, a.debet as debet, a.kredit as kredit, a.kodeblok as kodeorg, a.noreferensi as kodevhc  
                  from ".$dbname.".keu_jurnaldt_vw a
                  left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
                  where a.tanggal>='".$tanggalmulai."' and a.tanggal<='".$tanggalsampai."' and (a.noreferensi in ('ALK_WS_GYMH','ALK_TRK_GYMH','ALK_WAS','ALK_GAJI') or nojurnal like '%M0%' or nojurnal like '%PNN%') 
                  and a.kodeorg = '".$unit."'  order by a.tanggal";

        $res=mysql_query($str);
        $no=0;
        if(mysql_num_rows($res)<1)
        {
                echo"<tr class=rowcontent><td colspan=17>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
        }
        else
        {
                while($bar=mysql_fetch_object($res))
                {
                        $no+=1; $total=0;
                        echo"<tr class=rowcontent>
                                  <td align=right>".$no."</td>
                                  <td>".$bar->nojurnal."</td>
                                  <td align=right>".tanggalnormal($bar->tanggal)."</td>
                                  <td>".$bar->keterangan."</td>
                                  <td align=right>".$bar->noakun."</td>
                                  <td>".$bar->namaakun."</td>
                                  <td align=right>".number_format($bar->debet)."</td>
                                  <td align=right>".number_format($bar->kredit)."</td>
                                  <td>".$bar->kodeorg."</td>
                                  <td>".$bar->kodevhc."</td>
                                </tr>";
                $td+=$bar->debet;
                 $tk+= $bar->kredit;                              
                }
               echo"<tr class=rowcontent>
                                  <td colspan=6>Total</td>
                                  <td align=right>".number_format($td)."</td>
                                  <td align=right>".number_format($tk)."</td>
                                  <td></td>
                                  <td></td>
                        </tr>"; 	                
}
?>