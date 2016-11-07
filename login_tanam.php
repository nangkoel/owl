<link rel=stylesheet type=text/css href='style/generic.css'>
<?php
require_once('config/connection.php');

$what=$_GET['what'];

$noakuntanam = '1260505';

$tanggal = date('d-m-Y', time());
$hariini = date('Y-m-d', time());
$bulan = date('m', time());
$tahun = date('Y', time());

$updatetime=date('d M Y H:i:s', time());

//                $hariini = '2012-12-10';
//                $bulan = '12';
//                $tahun = '2012';

$dt = strtotime($hariini);
$kemarin = date('Y-m-d', $dt-86400);

$str="SELECT kodeorganisasi, namaorganisasi FROM ".$dbname.".organisasi
    WHERE tipe = 'KEBUN'";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $kamuskodeorg[$bar->kodeorganisasi]=$bar->namaorganisasi;
}

// hari ini
$str="SELECT a.kodekegiatan, sum(a.hasilkerja) as hasilkerja, b.tanggal, b.kodeorg FROM ".$dbname.".kebun_prestasi a
    LEFT JOIN ".$dbname.".kebun_aktifitas b on a.notransaksi = b.notransaksi
    WHERE substr(b.tanggal,1,10) like '".$hariini."%' and a.kodekegiatan like '".$noakuntanam."%' and b.jurnal=1
    GROUP BY b.kodeorg
    ORDER BY b.kodeorg";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    $arey[$bar->kodeorg]['hi']=$bar->hasilkerja;
    $total['hi']+=$bar->hasilkerja;
}

// kemarin
$str="SELECT a.kodekegiatan, sum(a.hasilkerja) as hasilkerja, b.tanggal, b.kodeorg FROM ".$dbname.".kebun_prestasi a
    LEFT JOIN ".$dbname.".kebun_aktifitas b on a.notransaksi = b.notransaksi
    WHERE substr(tanggal,1,10) like '".$kemarin."%' and a.kodekegiatan like '".$noakuntanam."%' and b.jurnal=1
    GROUP BY b.kodeorg
    ORDER BY b.kodeorg";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    $arey[$bar->kodeorg]['maren']=$bar->hasilkerja;
    $total['maren']+=$bar->hasilkerja;
}

// bulan ini
$str="SELECT a.kodekegiatan, sum(a.hasilkerja) as hasilkerja, b.tanggal, b.kodeorg FROM ".$dbname.".kebun_prestasi a
    LEFT JOIN ".$dbname.".kebun_aktifitas b on a.notransaksi = b.notransaksi
    WHERE substr(tanggal,1,10) between '".$tahun."-".$bulan."-01' and '".$hariini."' and a.kodekegiatan like '".$noakuntanam."%' and b.jurnal=1
    GROUP BY b.kodeorg
    ORDER BY b.kodeorg";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    $arey[$bar->kodeorg]['bi']=$bar->hasilkerja;
    $total['bi']+=$bar->hasilkerja;
}

// sd bulan ini
$str="SELECT a.kodekegiatan, sum(a.hasilkerja) as hasilkerja, b.tanggal, b.kodeorg FROM ".$dbname.".kebun_prestasi a
    LEFT JOIN ".$dbname.".kebun_aktifitas b on a.notransaksi = b.notransaksi
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' and '".$hariini."' and a.kodekegiatan like '".$noakuntanam."%' and b.jurnal=1
    GROUP BY b.kodeorg
    ORDER BY b.kodeorg";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    $arey[$bar->kodeorg]['sdbi']=$bar->hasilkerja;
    $total['sdbi']+=$bar->hasilkerja;
}

echo"<table class=sortable cellspacing=1 border=0 width=480px>
    <tr class=rowcontent>
    <td>Tanam ".$tanggal." = ".number_format($total['hi'])." pokok</td>
    <td align=right width=1% nowrap>".$updatetime."</td>
    </tr>
    </table>";

echo"<table class=sortable cellspacing=1 border=0 width=480px>
    <thead>
    <tr class=rowtitle>
        <td align=center rowspan=2 style='width:140px;'>Unit</td>
        <td align=center colspan=2 style='width:80x;'>Hari Ini</td>
        <td align=center colspan=2 style='width:80x;'>Kemarin</td>
        <td align=center colspan=2 style='width:90x;'>Bulan Ini</td>
        <td align=center colspan=2 style='width:100x;'>sd Bulan Ini</td>
    </tr>  
    <tr class=rowtitle>
        <td align=center style='width:40x;'>Pkk</td>
        <td align=center style='width:40x;'>Ha</td>
        <td align=center style='width:40x;'>Pkk</td>
        <td align=center style='width:40x;'>Ha</td>
        <td align=center style='width:45x;'>Pkk</td>
        <td align=center style='width:45x;'>Ha</td>
        <td align=center style='width:50x;'>Pkk</td>
        <td align=center style='width:50x;'>Ha</td>
    </tr>  
    </thead>
    <tbody></tbody></table>";

echo"<marquee height=100 onmouseout=this.start() onmouseover=this.stop() scrolldelay=20 scrollamount=1 behavior=scroll direction=up>
    <table class=sortable cellspacing=1 border=0 width=480px>
    <tbody>";

if(!empty($unit))foreach($unit as $uun){
    echo"<tr class=rowcontent>";
    echo"<td style='width:140px;'>".$kamuskodeorg[$uun]."</td>";
    echo"<td align=right style='width:40x;'>".number_format($arey[$uun]['hi'])."</td>";
    @$qwein=$arey[$uun]['hi']/143;
    echo"<td align=right style='width:40x;'>".number_format($qwein,2)."</td>";
    echo"<td align=right style='width:40x;'>".number_format($arey[$uun]['maren'])."</td>";
    @$qwein=$arey[$uun]['maren']/143;
    echo"<td align=right style='width:40x;'>".number_format($qwein,2)."</td>";
    echo"<td align=right style='width:45x;'>".number_format($arey[$uun]['bi'])."</td>";
    @$qwein=$arey[$uun]['bi']/143;
    echo"<td align=right style='width:45x;'>".number_format($qwein,2)."</td>";
    echo"<td align=right style='width:50x;'>".number_format($arey[$uun]['sdbi'])."</td>";
    @$qwein=$arey[$uun]['sdbi']/143;
    echo"<td align=right style='width:50x;'>".number_format($qwein,2)."</td>";
    echo"</tr>";
}

echo"<tr class=rowtitle>";
echo"<td>Total</td>";
echo"<td align=right>".number_format($total['hi'])."</td>";
@$qwein=$total['hi']/143;
echo"<td align=right>".number_format($qwein,2)."</td>";
echo"<td align=right>".number_format($total['maren'])."</td>";
@$qwein=$total['maren']/143;
echo"<td align=right>".number_format($qwein,2)."</td>";
echo"<td align=right>".number_format($total['bi'])."</td>";
@$qwein=$total['bi']/143;
echo"<td align=right>".number_format($qwein,2)."</td>";
echo"<td align=right>".number_format($total['sdbi'])."</td>";
@$qwein=$total['sdbi']/143;
echo"<td align=right>".number_format($qwein,2)."</td>";
echo"</tr>";
echo"</tbody>
    </table>
    * sumber data: BKM yang telah terposting untuk kegiatan tanam
    </marquee>";
?>