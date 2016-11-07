<link rel=stylesheet type=text/css href='style/generic.css'>
<?php
require_once('config/connection.php');

$tanggal = date('d-m-Y', time());
$hariini = date('Y-m-d', time());
$bulan = date('m', time());
$tahun = date('Y', time());

$updatetime=date('d M Y H:i:s', time());

//                $hariini = '2013-06-11';
//                $bulan = '06';
//                $tahun = '2013';

$dt = strtotime($hariini);
$kemarin = date('Y-m-d', $dt-86400);

$str="SELECT kodeorganisasi, namaorganisasi FROM ".$dbname.".organisasi
    WHERE tipe in ('KEBUN','AFDELING')";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $kamuskodeorg[$bar->kodeorganisasi]=$bar->namaorganisasi;
}

// produksi kebun
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, substr(nospb,9,6) as afdeling, sum(beratbersih) as beratbersih FROM ".$dbname.".pabrik_timbangan 
    WHERE substr(tanggal,1,10) = '".$kemarin."' and kodeorg != '' and kodebarang = '40000003'
    GROUP BY substr(nospb,9,6), substr(tanggal,1,10)
    ORDER BY substr(nospb,9,6)";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->afdeling]=$bar->afdeling;
    $kebun[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$kemarin){
        $arey[$bar->afdeling]['kgreamaren']+=$bar->beratbersih;
        $totalsub[$bar->kodeorg]['kgreamaren']+=$bar->beratbersih;        
        $total['kgreamaren']+=$bar->beratbersih;        
    }
}

// taksasi kebun
$str="SELECT substr(afdeling,1,4) as kodeorg, tanggal, afdeling, sum(jjgmasak*bjr) as beratbersih, sum(hkdigunakan) as hk FROM ".$dbname.".kebun_taksasi 
    WHERE tanggal between '".$kemarin."' and '".$hariini."' and afdeling not like '1%'
    GROUP BY afdeling, tanggal
    ORDER BY afdeling";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->afdeling]=$bar->afdeling;
    $kebun[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini){
        $arey[$bar->afdeling]['hktak']+=$bar->hk;
        $totalsub[$bar->kodeorg]['hktak']+=$bar->hk;        
        $total['hktak']+=$bar->hk;        
    }
    if(substr($bar->tanggal,0,10)==$kemarin){
        $arey[$bar->afdeling]['hktakmaren']+=$bar->hk;
        $totalsub[$bar->kodeorg]['hktakmaren']+=$bar->hk;        
        $total['hktakmaren']+=$bar->hk;        
    }    
}

// panen kebun
$str="SELECT unit as kodeorg, tanggal, substr(kodeorg,1,6) as afdeling, sum(hasilkerjakg) as beratbersih, count(*) as hk FROM ".$dbname.".kebun_prestasi_vw
    WHERE tanggal = '".$hariini."'
    GROUP BY afdeling, tanggal
    ORDER BY afdeling";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->afdeling]=$bar->afdeling;
    $kebun[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini){
        $arey[$bar->afdeling]['hkrea']+=$bar->hk;
        $totalsub[$bar->kodeorg]['hkrea']+=$bar->hk;        
        $total['hkrea']+=$bar->hk;        
    }
}

// panen kebun
$str="SELECT unit as kodeorg, tanggal, substr(kodeorg,1,6) as afdeling, sum(hasilkerjakg) as beratbersih, count(*) as hk FROM ".$dbname.".kebun_prestasi_vw
    WHERE tanggal = '".$kemarin."'
    GROUP BY afdeling, tanggal
    ORDER BY afdeling";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->afdeling]=$bar->afdeling;
    $kebun[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$kemarin){
        $arey[$bar->afdeling]['hkreamaren']+=$bar->hk;
        $totalsub[$bar->kodeorg]['hkreamaren']+=$bar->hk;        
        $total['hkreamaren']+=$bar->hk;        
        
        $arey[$bar->afdeling]['kgpanmaren']+=$bar->beratbersih;
        $totalsub[$bar->kodeorg]['kgpanmaren']+=$bar->beratbersih;        
        $total['kgpanmaren']+=$bar->beratbersih;        
    }
}

//@$qwein=$total['kgrea']/1000;
//@$qweintak=$total['kgtak']/1000;
//    $qwe="Taksasi Panen ".$tanggal." = ".number_format($qweintak,2)." ton / Realisasi = ".number_format($qwein,2)." ton";
    $qwe="Taksasi Panen ".$tanggal."";

echo"<table class=sortable cellspacing=1 border=0 width=480px>
    <tr class=rowcontent>
    <td>".$qwe."</td>
    <td align=right width=1% nowrap>".$updatetime."</td>
    </tr>
    </table>";

echo"<table class=sortable cellspacing=1 border=0 width=480px>
    <thead>
    <tr class=rowtitle>
        <td align=center rowspan=2 style='width:60px;'>Unit</td>
        <td align=center colspan=2>Hari Ini</td>
        <td align=center colspan=5>Kemarin</td>
    </tr>  
    <tr class=rowtitle>
        <td align=center style='width:60px;'>HK Taks.</td>
        <td align=center style='width:60px;'>HK Real.</td>
        <td align=center style='width:60px;'>HK Taks.</td>
        <td align=center style='width:60px;'>HK Real.</td>
        <td align=center style='width:60px;'>KG Pabrik (T)</td>
        <td align=center style='width:60px;'>KG Kebun (T)</td>
        <td align=center style='width:60px;'>KG Selisih (T)</td>
        <!--<td align=center>Restan</td>-->
    </tr>  
    </thead>
    <tbody></tbody></table>";

echo"<marquee height=150 onmouseout=this.start() onmouseover=this.stop() scrolldelay=20 scrollamount=1 behavior=scroll direction=up>
    <table class=sortable cellspacing=1 border=0 width=480px>
    <tbody>";

if(!empty($kebun))foreach($kebun as $buu){
    echo"<tr class=rowtitle>";
    echo"<td style='width:60px;'>".$buu."</td>";
    @$qwein=$totalsub[$buu]['hktak'];
    echo"<td align=right style='width:60px;'>".number_format($qwein)."</td>";
    @$qwein=$totalsub[$buu]['hkrea'];
    echo"<td align=right style='width:60px;'>".number_format($qwein)."</td>";
    
    @$qwein=$totalsub[$buu]['hktakmaren'];
    echo"<td align=right style='width:60px;'>".number_format($qwein)."</td>";
    @$qwein=$totalsub[$buu]['hkreamaren'];
    echo"<td align=right style='width:60px;'>".number_format($qwein)."</td>";

    @$qwein=$totalsub[$buu]['kgreamaren']/1000;
    echo"<td align=right style='width:60px;'>".number_format($qwein,2)."</td>";
    @$qwein=$totalsub[$buu]['kgpanmaren']/1000;
    echo"<td align=right style='width:60px;'>".number_format($qwein,2)."</td>";
    @$qwein=($totalsub[$buu]['kgreamaren']-$totalsub[$buu]['kgpanmaren'])/1000;
    echo"<td align=right style='width:60px;'>".number_format($qwein,2)."</td>";
    @$qwein=$totalsub[$buu]['kgres']/1000;
    echo"<!--<td align=right style='width:60px;'>".number_format($qwein,2)."</td>-->";
    echo"</tr>";
    if(!empty($unit))foreach($unit as $uun){
        if(substr($uun,0,4)==$buu){
        echo"<tr class=rowcontent>";
        echo"<td>&nbsp; &nbsp;".$uun."</td>";
        @$qwein=$arey[$uun]['hktak'];
        echo"<td align=right>".number_format($qwein)."</td>";
        @$qwein=$arey[$uun]['hkrea'];
        echo"<td align=right>".number_format($qwein)."</td>";
        
        @$qwein=$arey[$uun]['hktakmaren'];
        echo"<td align=right>".number_format($qwein)."</td>";
        @$qwein=$arey[$uun]['hkreamaren'];
        echo"<td align=right>".number_format($qwein)."</td>";

        @$qwein=$arey[$uun]['kgreamaren']/1000;
        echo"<td align=right>".number_format($qwein,2)."</td>";
        @$qwein=$arey[$uun]['kgpanmaren']/1000;
        echo"<td align=right>".number_format($qwein,2)."</td>";
        @$qwein=($arey[$uun]['kgreamaren']-$arey[$uun]['kgpanmaren'])/1000;
        echo"<td align=right>".number_format($qwein,2)."</td>";
        @$qwein=$arey[$uun]['kgres']/1000;
        echo"<!--<td align=right>".number_format($qwein,2)."</td>-->";
        echo"</tr>";
        }
    }
}

echo"<tr class=rowtitle>";
echo"<td>Total</td>";
@$qwein=$total['hktak'];
echo"<td align=right>".number_format($qwein)."</td>";
@$qwein=$total['hkrea'];
echo"<td align=right>".number_format($qwein)."</td>";

@$qwein=$total['hktakmaren'];
echo"<td align=right>".number_format($qwein)."</td>";
@$qwein=$total['hkreamaren'];
echo"<td align=right>".number_format($qwein)."</td>";

@$qwein=$total['kgreamaren']/1000;
echo"<td align=right>".number_format($qwein,2)."</td>";
@$qwein=$total['kgpanmaren']/1000;
echo"<td align=right>".number_format($qwein,2)."</td>";
@$qwein=($total['kgreamaren']-$total['kgpanmaren'])/1000;
echo"<td align=right>".number_format($qwein,2)."</td>";
@$qwein=$total['kgres']/1000;
echo"<!--<td align=right>".number_format($qwein,2)."</td>-->";
echo"</tr>";
echo"</tbody>
    </table>
    * sumber data: taksasi + panen + timbangan
    </marquee>";
?>