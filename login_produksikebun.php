<link rel=stylesheet type=text/css href='style/generic.css'>
<?php
require_once('config/connection.php');

$tanggal = date('d-m-Y', time());
$hariini = date('Y-m-d', time());
$bulan = date('m', time());
$tahun = date('Y', time());

$updatetime=date('d M Y H:i:s', time());

//                $hariini = '2012-12-12';
//                $bulan = '12';
//                $tahun = '2012';

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
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' and kodeorg != '' and kodebarang = '40000003'
    GROUP BY substr(nospb,9,6), substr(tanggal,1,10)
    ORDER BY substr(nospb,9,6)";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->afdeling]=$bar->afdeling;
    $kebun[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini){
        $arey[$bar->afdeling]['hi']+=$bar->beratbersih;
        $totalsub[$bar->kodeorg]['hi']+=$bar->beratbersih;        
        $total['hi']+=$bar->beratbersih;        
    }
    if(substr($bar->tanggal,0,10)==$kemarin){
        $arey[$bar->afdeling]['maren']+=$bar->beratbersih;
        $totalsub[$bar->kodeorg]['maren']+=$bar->beratbersih;        
        $total['maren']+=$bar->beratbersih;        
    }
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->afdeling]['bi']+=$bar->beratbersih;
        $totalsub[$bar->kodeorg]['bi']+=$bar->beratbersih;        
        $total['bi']+=$bar->beratbersih;        
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->afdeling]['sdbi']+=$bar->beratbersih;
        $totalsub[$bar->kodeorg]['sdbi']+=$bar->beratbersih;        
        $total['sdbi']+=$bar->beratbersih;        
    }
}

// taksasi kebun
$str="SELECT substr(afdeling,1,4) as kodeorg, tanggal, afdeling, sum(jjgmasak*bjr) as beratbersih FROM ".$dbname.".kebun_taksasi 
    WHERE tanggal between '".$tahun."-01-01' and '".$hariini."' and afdeling not like '1%'
    GROUP BY afdeling, tanggal
    ORDER BY afdeling";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->afdeling]=$bar->afdeling;
    $kebun[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini){
        $areytak[$bar->afdeling]['hi']+=$bar->beratbersih;
        $totalsubtak[$bar->kodeorg]['hi']+=$bar->beratbersih;        
        $totaltak['hi']+=$bar->beratbersih;        
    }
    if(substr($bar->tanggal,0,10)==$kemarin){
        $areytak[$bar->afdeling]['maren']+=$bar->beratbersih;
        $totalsubtak[$bar->kodeorg]['maren']+=$bar->beratbersih;        
        $totaltak['maren']+=$bar->beratbersih;        
    }
}

@$qwein=$total['hi']/1000;
@$qweintak=$totaltak['hi']/1000;
    $qwe="Produksi Kebun ".$tanggal." = ".number_format($qwein,2)." ton / Taksasi = ".number_format($qweintak,2)." ton";

echo"<table class=sortable cellspacing=1 border=0 width=480px>
    <tr class=rowcontent>
    <td>".$qwe."</td>
    <td align=right width=1% nowrap>".$updatetime."</td>
    </tr>
    </table>";

echo"<table class=sortable cellspacing=1 border=0 width=480px>
    <thead>
    <tr class=rowtitle>
        <td align=center rowspan=2 style='width:80px;'>Unit</td>
        <td align=center colspan=2 style='width:120px;'>Hari Ini (T)</td>
        <td align=center colspan=2 style='width:120px;'>Kemarin (T)</td>
        <td align=center rowspan=2 style='width:80px;'>Bulan Ini (T)</td>
        <td align=center rowspan=2 style='width:100px;'>sd Bulan Ini (T)</td>
    </tr>  
    <tr class=rowtitle>
        <td align=center>Taks.</td>
        <td align=center>Real.</td>
        <td align=center>Taks.</td>
        <td align=center>Real.</td>
    </tr>  
    </thead>
    <tbody></tbody></table>";

echo"<marquee height=180 onmouseout=this.start() onmouseover=this.stop() scrolldelay=20 scrollamount=1 behavior=scroll direction=up>
    <table class=sortable cellspacing=1 border=0 width=480px>
    <tbody>";

if(!empty($kebun))foreach($kebun as $buu){
    echo"<tr class=rowtitle>";
    echo"<td style='width:80px;'>".$buu."</td>";
    @$qweintak=$totalsubtak[$buu]['hi']/1000;
    @$qwein=$totalsub[$buu]['hi']/1000;
    echo"<td align=right style='width:60px;'>".number_format($qweintak,2)."</td>";
    echo"<td align=right style='width:60px;'>".number_format($qwein,2)."</td>";
    @$qweintak=$totalsubtak[$buu]['maren']/1000;
    @$qwein=$totalsub[$buu]['maren']/1000;
    echo"<td align=right style='width:60px;'>".number_format($qweintak,2)."</td>";
    echo"<td align=right style='width:60px;'>".number_format($qwein,2)."</td>";
    @$qwein=$totalsub[$buu]['bi']/1000;
    echo"<td align=right style='width:80px;'>".number_format($qwein,2)."</td>";
    @$qwein=$totalsub[$buu]['sdbi']/1000;
    echo"<td align=right style='width:100px;'>".number_format($qwein,2)."</td>";
    echo"</tr>";
    if(!empty($unit))foreach($unit as $uun){
        if(substr($uun,0,4)==$buu){
        echo"<tr class=rowcontent>";
        echo"<td>&nbsp; &nbsp;".$uun."</td>";
        @$qweintak=$areytak[$uun]['hi']/1000;
        @$qwein=$arey[$uun]['hi']/1000;
        echo"<td align=right>".number_format($qweintak,2)."</td>";
        echo"<td align=right>".number_format($qwein,2)."</td>";
        @$qweintak=$areytak[$uun]['maren']/1000;
        @$qwein=$arey[$uun]['maren']/1000;
        echo"<td align=right>".number_format($qweintak,2)."</td>";
        echo"<td align=right>".number_format($qwein,2)."</td>";
        @$qwein=$arey[$uun]['bi']/1000;
        echo"<td align=right>".number_format($qwein,2)."</td>";
        @$qwein=$arey[$uun]['sdbi']/1000;
        echo"<td align=right>".number_format($qwein,2)."</td>";
        echo"</tr>";
        }
    }
}

echo"<tr class=rowtitle>";
echo"<td>Total</td>";
@$qweintak=$totaltak['hi']/1000;
@$qwein=$total['hi']/1000;
echo"<td align=right>".number_format($qweintak,2)."</td>";
echo"<td align=right>".number_format($qwein,2)."</td>";
@$qweintak=$totaltak['maren']/1000;
@$qwein=$total['maren']/1000;
echo"<td align=right>".number_format($qweintak,2)."</td>";
echo"<td align=right>".number_format($qwein,2)."</td>";
@$qwein=$total['bi']/1000;
echo"<td align=right>".number_format($qwein,2)."</td>";
@$qwein=$total['sdbi']/1000;
echo"<td align=right>".number_format($qwein,2)."</td>";
echo"</tr>";
echo"</tbody>
    </table>
    * sumber data: timbangan + taksasi kebun
    </marquee>";
?>