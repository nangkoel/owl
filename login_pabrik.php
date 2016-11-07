<link rel=stylesheet type=text/css href='style/generic.css'>
<?php
require_once('config/connection.php');

$what=$_GET['what'];

$tanggal = date('d-m-Y', time());
$hariini = date('Y-m-d', time());
$bulan = date('m', time());
$tahun = date('Y', time());

$updatetime=date('d M Y H:i:s', time());

//                $hariini = '2013-09-13';
//                $bulan = '09';
//                $tahun = '2013';

$dt = strtotime($hariini);
$kemarin = date('Y-m-d', $dt-86400);

$str="SELECT kodeorganisasi, namaorganisasi FROM ".$dbname.".organisasi
    WHERE tipe = 'PABRIK'";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $kamuskodeorg[$bar->kodeorganisasi]=$bar->namaorganisasi;
}

// hari ini produksi cpo
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, oer FROM ".$dbname.".pabrik_produksi
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' 
    GROUP BY kodeorg, substr(tanggal,1,10)
    ORDER BY kodeorg";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini)$arey[$bar->kodeorg]['prodcpo']['hi']+=$bar->oer;
    if(substr($bar->tanggal,0,10)==$kemarin)$arey[$bar->kodeorg]['prodcpo']['maren']+=$bar->oer;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['prodcpo']['bi']+=$bar->oer;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['prodcpo']['sdbi']+=$bar->oer;
    }
}

// hari ini produksi pk
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, oerpk FROM ".$dbname.".pabrik_produksi
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' 
    GROUP BY kodeorg, substr(tanggal,1,10)
    ORDER BY kodeorg";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini)$arey[$bar->kodeorg]['prodpk']['hi']+=$bar->oerpk;
    if(substr($bar->tanggal,0,10)==$kemarin)$arey[$bar->kodeorg]['prodpk']['maren']+=$bar->oerpk;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['prodpk']['bi']+=$bar->oerpk;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['prodpk']['sdbi']+=$bar->oerpk;
    }
}

// hari ini tbs diolah
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, tbsdiolah FROM ".$dbname.".pabrik_produksi
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' 
    GROUP BY kodeorg, substr(tanggal,1,10) 
    ORDER BY kodeorg";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini)$arey[$bar->kodeorg]['tbsdiolah']['hi']+=$bar->tbsdiolah;
    if(substr($bar->tanggal,0,10)==$kemarin)$arey[$bar->kodeorg]['tbsdiolah']['maren']+=$bar->tbsdiolah;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['tbsdiolah']['bi']+=$bar->tbsdiolah;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['tbsdiolah']['sdbi']+=$bar->tbsdiolah;
    }
}

// hari ini oer cpo
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, oer, tbsdiolah FROM ".$dbname.".pabrik_produksi
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' 
    GROUP BY kodeorg, substr(tanggal,1,10)
    ORDER BY kodeorg";
$res=mysql_query($str);
//echo mysql_error($conn);
$kali=array();
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini)@$arey[$bar->kodeorg]['oercpo']['hi']+=($bar->oer)/($bar->tbsdiolah)*100;
    if(substr($bar->tanggal,0,10)==$kemarin)@$arey[$bar->kodeorg]['oercpo']['maren']+=($bar->oer)/($bar->tbsdiolah)*100;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        @$arey[$bar->kodeorg]['oercpoqwe']['bi']+=($bar->oer)/($bar->tbsdiolah)*100;
        if($bar->oer>0)$kali['bi']+=1;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        @$arey[$bar->kodeorg]['oercpoqwe']['sdbi']+=($bar->oer)/($bar->tbsdiolah)*100;
        if($bar->oer>0)$kali['sdbi']+=1;
    }
    @$arey[$bar->kodeorg]['oercpo']['bi']=@$arey[$bar->kodeorg]['oercpoqwe']['bi']/$kali['bi'];
    @$arey[$bar->kodeorg]['oercpo']['sdbi']=@$arey[$bar->kodeorg]['oercpoqwe']['sdbi']/$kali['sdbi'];
}

// hari ini loses cpo
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, (fruitineb+ebstalk+fibre+nut+effluent+soliddecanter) as loses, 
    (fruitinebker+cyclone+ltds+claybath) as losespk FROM ".$dbname.".pabrik_produksi
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' and tbsdiolah>0
    GROUP BY kodeorg, substr(tanggal,1,10)
    ORDER BY kodeorg";
$res=mysql_query($str);
//echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini){
        $arey[$bar->kodeorg]['loscpo']['hi']+=$bar->loses;
         $arey[$bar->kodeorg]['lospk']['hi']+=$bar->losespk;
        $counthi++;
    }
    
    if(substr($bar->tanggal,0,10)==$kemarin){
        $arey[$bar->kodeorg]['loscpo']['maren']+=$bar->loses;
        $arey[$bar->kodeorg]['lospk']['maren']+=$bar->losespk;
       $countmaren++;
    }
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['loscpo']['bi']+=$bar->loses;
        $arey[$bar->kodeorg]['lospk']['bi']+=$bar->losespk;
        $countbi++;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['loscpo']['sdbi']+=$bar->loses;
        $arey[$bar->kodeorg]['lospk']['sdbi']+=$bar->losespk;
        $countsdbi++;
    }
}
foreach($unit as $un =>$kdun){
    @$arey[$kdun]['loscpo']['hi']=$arey[$kdun]['loscpo']['hi']/$counthi;
    @$arey[$kdun]['loscpo']['maren']=$arey[$kdun]['loscpo']['maren']/$countmaren;
    @$arey[$kdun]['loscpo']['bi']=$arey[$kdun]['loscpo']['bi']/$countbi;
    @$arey[$kdun]['loscpo']['sdbi']=$arey[$kdun]['loscpo']['sdbi']/$countsdbi;
 
    @$arey[$kdun]['lospk']['hi']=$arey[$kdun]['lospk']['hi']/$counthi;
    @$arey[$kdun]['lospk']['maren']=$arey[$kdun]['lospk']['maren']/$countmaren;
    @$arey[$kdun]['lospk']['bi']=$arey[$kdun]['loscpo']['bi']/$countbi;
    @$arey[$kdun]['lospk']['sdbi']=$arey[$kdun]['lospk']['sdbi']/$countsdbi;    
}

// hari ini ffa cpo
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, ffa FROM ".$dbname.".pabrik_produksi
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' 
    GROUP BY kodeorg, substr(tanggal,1,10)
    ORDER BY kodeorg";
$res=mysql_query($str);
//echo mysql_error($conn);
$kali=array();
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini)$arey[$bar->kodeorg]['ffacpo']['hi']+=$bar->ffa;
    if(substr($bar->tanggal,0,10)==$kemarin)$arey[$bar->kodeorg]['ffacpo']['maren']+=$bar->ffa;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['ffacpoqwe']['bi']+=$bar->ffa;
        if($bar->ffa>0)$kali['bi']+=1;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['ffacpoqwe']['sdbi']+=$bar->ffa; 
        if($bar->ffa>0)$kali['sdbi']+=1;
    }
    @$arey[$bar->kodeorg]['ffacpo']['bi']=@$arey[$bar->kodeorg]['ffacpoqwe']['bi']/$kali['bi'];
    @$arey[$bar->kodeorg]['ffacpo']['sdbi']=@$arey[$bar->kodeorg]['ffacpoqwe']['sdbi']/$kali['sdbi'];
}

// hari ini oer pk
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, oerpk, tbsdiolah FROM ".$dbname.".pabrik_produksi
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' 
    GROUP BY kodeorg, substr(tanggal,1,10)
    ORDER BY kodeorg";
$res=mysql_query($str);
//echo mysql_error($conn);
$kali=array();
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini)@$arey[$bar->kodeorg]['oerpk']['hi']+=($bar->oerpk)/($bar->tbsdiolah)*100;
    if(substr($bar->tanggal,0,10)==$kemarin)@$arey[$bar->kodeorg]['oerpk']['maren']+=($bar->oerpk)/($bar->tbsdiolah)*100;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['oerpk1']['bi']+=$bar->oerpk;
        $arey[$bar->kodeorg]['oerpk2']['bi']+=$bar->tbsdiolah;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['oerpk1']['sdbi']+=$bar->oerpk;
        $arey[$bar->kodeorg]['oerpk2']['sdbi']+=$bar->tbsdiolah;
    }
    @$arey[$bar->kodeorg]['oerpk']['bi']=($arey[$bar->kodeorg]['oerpk1']['bi']/$arey[$bar->kodeorg]['oerpk2']['bi'])*100;
    @$arey[$bar->kodeorg]['oerpk']['sdbi']=($arey[$bar->kodeorg]['oerpk1']['sdbi']/$arey[$bar->kodeorg]['oerpk2']['sdbi'])*100;
}

// hari ini jam olah
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, sum(jamdinasbruto-jamstagnasi) as jamolah FROM ".$dbname.".pabrik_pengolahan
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' 
    GROUP BY kodeorg, substr(tanggal,1,10)
    ORDER BY kodeorg";
$res=mysql_query($str);
//echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini)$arey[$bar->kodeorg]['jamolah']['hi']+=$bar->jamolah;
    if(substr($bar->tanggal,0,10)==$kemarin)$arey[$bar->kodeorg]['jamolah']['maren']+=$bar->jamolah;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['jamolah']['bi']+=$bar->jamolah;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['jamolah']['sdbi']+=$bar->jamolah;
    }
}

// hari ini kapasitas olah
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, sum(tbsdiolah) as tbsdiolah FROM ".$dbname.".pabrik_pengolahan
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' 
    GROUP BY kodeorg, substr(tanggal,1,10)
    ORDER BY kodeorg";
$res=mysql_query($str);
//echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini)$arey[$bar->kodeorg]['kapolah']['hi']+=$bar->tbsdiolah;
    if(substr($bar->tanggal,0,10)==$kemarin)$arey[$bar->kodeorg]['kapolah']['maren']+=$bar->tbsdiolah;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['kapolah']['bi']+=$bar->tbsdiolah;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['kapolah']['sdbi']+=$bar->tbsdiolah;
    }
}

if(!empty($unit))foreach($unit as $uun){
    @$arey[$uun]['kapolah']['hi']=$arey[$uun]['kapolah']['hi']/$arey[$uun]['jamolah']['hi'];
    @$arey[$uun]['kapolah']['maren']=$arey[$uun]['kapolah']['maren']/$arey[$uun]['jamolah']['maren'];
    @$arey[$uun]['kapolah']['bi']=$arey[$uun]['kapolah']['bi']/$arey[$uun]['jamolah']['bi'];
    @$arey[$uun]['kapolah']['sdbi']=$arey[$uun]['kapolah']['sdbi']/$arey[$uun]['jamolah']['sdbi'];
}


// hari ini stok cpo
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, sum(kuantitas) as kuantitas FROM ".$dbname.".pabrik_masukkeluartangki
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' 
    GROUP BY kodeorg, substr(tanggal,1,10)
    ORDER BY kodeorg";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini)$arey[$bar->kodeorg]['stokcpo']['hi']+=$bar->kuantitas;
    if(substr($bar->tanggal,0,10)==$kemarin)$arey[$bar->kodeorg]['stokcpo']['maren']+=$bar->kuantitas;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['stokcpo']['bi']+=$bar->kuantitas;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['stokcpo']['sdbi']+=$bar->kuantitas;
    }
}

// hari ini stok pk
$str="SELECT kodeorg, substr(tanggal,1,10) as tanggal, sum(kernelquantity) as kernelquantity FROM ".$dbname.".pabrik_masukkeluartangki
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' 
    GROUP BY kodeorg, substr(tanggal,1,10)
    ORDER BY kodeorg";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->kodeorg]=$bar->kodeorg;
    if(substr($bar->tanggal,0,10)==$hariini)$arey[$bar->kodeorg]['stokpk']['hi']+=$bar->kernelquantity;
    if(substr($bar->tanggal,0,10)==$kemarin)$arey[$bar->kodeorg]['stokpk']['maren']+=$bar->kernelquantity;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['stokpk']['bi']+=$bar->kernelquantity;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->kodeorg]['stokpk']['sdbi']+=$bar->kernelquantity;
    }
}

// hari ini internal
$str="SELECT millcode, substr(tanggal,1,10) as tanggal, sum(beratbersih) as beratbersih FROM ".$dbname.".pabrik_timbangan 
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' and kodeorg != '' and kodebarang = '40000003'
    GROUP BY millcode, substr(tanggal,1,10)
    ORDER BY millcode";
$res=mysql_query($str);
//echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->millcode]=$bar->millcode;
    if(substr($bar->tanggal,0,10)==$hariini)$arey[$bar->millcode]['internal']['hi']+=$bar->beratbersih;
    if(substr($bar->tanggal,0,10)==$kemarin)$arey[$bar->millcode]['internal']['maren']+=$bar->beratbersih;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->millcode]['internal']['bi']+=$bar->beratbersih;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->millcode]['internal']['sdbi']+=$bar->beratbersih;
    }
}

// hari ini eksternal
$str="SELECT millcode, substr(tanggal,1,10) as tanggal, sum(beratbersih) as beratbersih FROM ".$dbname.".pabrik_timbangan 
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' and kodeorg = '' and kodebarang = '40000003'
    GROUP BY millcode, substr(tanggal,1,10)
    ORDER BY millcode";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->millcode]=$bar->millcode;
    if(substr($bar->tanggal,0,10)==$hariini)$arey[$bar->millcode]['eksternal']['hi']+=$bar->beratbersih;
    if(substr($bar->tanggal,0,10)==$kemarin)$arey[$bar->millcode]['eksternal']['maren']+=$bar->beratbersih;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->millcode]['eksternal']['bi']+=$bar->beratbersih;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->millcode]['eksternal']['sdbi']+=$bar->beratbersih;
    }
}

// hari ini cpo keluar
$str="SELECT millcode, substr(tanggal,1,10) as tanggal, sum(beratbersih) as beratbersih, kodebarang FROM ".$dbname.".pabrik_timbangan 
    WHERE substr(tanggal,1,10) between '".$tahun."-01-01' and '".$hariini."' and kodeorg = '' and kodebarang in ('40000001', '40000004', '40000002', '40000005')
        and beratkeluar>beratmasuk
    GROUP BY millcode, substr(tanggal,1,10), kodebarang
    ORDER BY millcode";
$res=mysql_query($str);
echo mysql_error($conn);
while($bar=mysql_fetch_object($res))
{ 
    $unit[$bar->millcode]=$bar->millcode;
    
    if($bar->kodebarang=='40000001')$barangnya='outcpo';
    if($bar->kodebarang=='40000004')$barangnya='outjjk';
    if($bar->kodebarang=='40000002')$barangnya='outker';
    if($bar->kodebarang=='40000005')$barangnya='outckg';
    
    if(substr($bar->tanggal,0,10)==$hariini)$arey[$bar->millcode][$barangnya]['hi']+=$bar->beratbersih;
    if(substr($bar->tanggal,0,10)==$kemarin)$arey[$bar->millcode][$barangnya]['maren']+=$bar->beratbersih;
    if((substr($bar->tanggal,0,7)==($tahun."-".$bulan))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->millcode][$barangnya]['bi']+=$bar->beratbersih;
    }
    if((substr($bar->tanggal,0,4)==($tahun))and(substr($bar->tanggal,0,10)<=$hariini)){
        $arey[$bar->millcode][$barangnya]['sdbi']+=$bar->beratbersih;
    }
}

echo"<table class=sortable cellspacing=1 border=0 width=480px>
    <tr class=rowcontent>
    <td>Produksi Pabrik ".$tanggal."</td>
    <td align=right width=1% nowrap>".$updatetime."</td>
    </tr>
    </table>";

echo"<table class=sortable cellspacing=1 border=0 width=480px>
    <thead>
    <tr class=rowtitle>
        <td align=center style='width:100px;'>Unit</td>
        <td align=center style='width:110px;'>Keterangan</td>
        <td align=center style='width:60px;'>Hari Ini</td>
        <td align=center style='width:60px;'>Kemarin</td>
        <td align=center style='width:70px;'>Bulan Ini</td>
        <td align=center style='width:80px;'>sd Bulan Ini</td>
    </tr>  
    </thead>
    <tbody></tbody></table>";

echo"<marquee height=130 onmouseout=this.start() onmouseover=this.stop() scrolldelay=20 scrollamount=1 behavior=scroll direction=up>
    <table class=sortable cellspacing=1 border=0 width=480px>
    <tbody>";

if(!empty($unit))foreach($unit as $uun){
    echo"<tr class=rowcontent>";
    echo"<td rowspan=23 style='width:100px;' valign=top>".$kamuskodeorg[$uun]."</td>";
    echo"<td style='width:110px;'>Produksi CPO (T)</td>";
    @$qwein=$arey[$uun]['prodcpo']['hi']/1000;
    echo"<td align=right style='width:60px;'>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['prodcpo']['maren']/1000;
    echo"<td align=right style='width:60px;'>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['prodcpo']['bi']/1000;
    echo"<td align=right style='width:70px;'>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['prodcpo']['sdbi']/1000;
    echo"<td align=right style='width:80px;'>".number_format($qwein,2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>Produksi PK (T)</td>";
    @$qwein=$arey[$uun]['prodpk']['hi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['prodpk']['maren']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['prodpk']['bi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['prodpk']['sdbi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>TBS Diolah (T)</td>";
    @$qwein=$arey[$uun]['tbsdiolah']['hi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['tbsdiolah']['maren']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['tbsdiolah']['bi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['tbsdiolah']['sdbi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>TBS Internal (T)</td>";
    @$qwein=$arey[$uun]['internal']['hi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['internal']['maren']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['internal']['bi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['internal']['sdbi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>TBS Eksternal (T)</td>";
    @$qwein=$arey[$uun]['eksternal']['hi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['eksternal']['maren']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['eksternal']['bi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['eksternal']['sdbi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td colspan=5></td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>OER CPO (%)</td>";
    echo"<td align=right>".number_format($arey[$uun]['oercpo']['hi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['oercpo']['maren'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['oercpo']['bi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['oercpo']['sdbi'],2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>Loses CPO (%)</td>";
    echo"<td align=right>".number_format($arey[$uun]['loscpo']['hi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['loscpo']['maren'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['loscpo']['bi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['loscpo']['sdbi'],2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>FFA CPO (%)</td>";
    echo"<td align=right>".number_format($arey[$uun]['ffacpo']['hi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['ffacpo']['maren'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['ffacpo']['bi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['ffacpo']['sdbi'],2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td colspan=5></td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>OER PK (%)</td>";
    echo"<td align=right>".number_format($arey[$uun]['oerpk']['hi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['oerpk']['maren'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['oerpk']['bi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['oerpk']['sdbi'],2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>Loses PK (%)</td>";
    echo"<td align=right>".number_format($arey[$uun]['lospk']['hi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['lospk']['maren'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['lospk']['bi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['lospk']['sdbi'],2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td colspan=5></td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>Jam Olah</td>";
    echo"<td align=right>".number_format($arey[$uun]['jamolah']['hi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['jamolah']['maren'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['jamolah']['bi'],2)."</td>";
    echo"<td align=right>".number_format($arey[$uun]['jamolah']['sdbi'],2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>Kap. Olah (T)</td>";
    @$qwein=$arey[$uun]['kapolah']['hi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['kapolah']['maren']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['kapolah']['bi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['kapolah']['sdbi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td colspan=5></td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>Stok CPO (T)</td>";
    @$qwein=$arey[$uun]['stokcpo']['hi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['stokcpo']['maren']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['stokcpo']['bi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['stokcpo']['sdbi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>Stok PK (T)</td>";
    @$qwein=$arey[$uun]['stokpk']['hi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['stokpk']['maren']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['stokpk']['bi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['stokpk']['sdbi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    echo"</tr>";
    
    echo"<tr class=rowcontent>";
    echo"<td colspan=5></td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>Angkut CPO (T)</td>";
    @$qwein=$arey[$uun]['outcpo']['hi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outcpo']['maren']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outcpo']['bi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outcpo']['sdbi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>Angkut JJK (T)</td>";
    @$qwein=$arey[$uun]['outjjk']['hi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outjjk']['maren']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outjjk']['bi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outjjk']['sdbi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    echo"</tr>";    
    echo"<tr class=rowcontent>";
    echo"<td>Keluar PK (T)</td>";
    @$qwein=$arey[$uun]['outker']['hi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outker']['maren']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outker']['bi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outker']['sdbi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    echo"</tr>";
    echo"<tr class=rowcontent>";
    echo"<td>Keluar Ckng (T)</td>";
    @$qwein=$arey[$uun]['outckg']['hi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outckg']['maren']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outckg']['bi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    @$qwein=$arey[$uun]['outckg']['sdbi']/1000;
    echo"<td align=right>".number_format($qwein,2)."</td>";
    echo"</tr>";    
}else{
    echo"<tr class=rowtitle>";
    echo"<td style='width:480px;'>N/A</td>";
    echo"</tr>";
}

echo"</tbody>
    </table>
    * sumber data: timbangan + inputan pabrik
    </marquee>";
?>