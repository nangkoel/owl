<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    require_once('lib/nangkoelib.php');

    // ambil yang dilempar javascript
    $pt=$_POST['pt'];
    $unit=$_POST['unit'];
    $tgl1=$_POST['tgl1'];
    $tgl2=$_POST['tgl2'];
    
    // olah tanggal
    $tanggal1=explode('-',$tgl1);
    $tanggal2=explode('-',$tgl2);
    $date1=$tanggal1[2].'-'.$tanggal1[1].'-'.$tanggal1[0];
    $tanggalterakhir=date(t, strtotime($date1));

    // kamus blok
    $sdakar="select kodeorg,tahuntanam from ".$dbname.".setup_blok";
    $qdakar=mysql_query($sdakar) or die(mysql_error($conn));
    while($rdakar=  mysql_fetch_assoc($qdakar))
    {
        $belok[$rdakar['kodeorg']]=$rdakar['tahuntanam'];
    }
    
    if($unit=='') // script copy-an dari kebun_laporanPanen.php
    {
        $str="select a.blok,a.tanggal,a.nospb,a.notiket,a.nokendaraan,a.jjg,a.kgwb,a.bjr,a.kgbjr
        from ".$dbname.".kebun_spb_vw a
        left join ".$dbname.".organisasi c
        on substr(a.kodeorg,1,4)=c.kodeorganisasispb_vw
        where c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." 
        order by a.blok, a.tanggal";
    }
    else
    {
        $str="select a.blok,a.tanggal,a.nospb,a.notiket,a.nokendaraan,a.jjg,a.kgwb,a.bjr,a.kgbjr
        from ".$dbname.".kebun_spb_vw a
        where blok like '".$unit."%'  and a.tanggal between ".tanggalsystem($tgl1)." and ".tanggalsystem($tgl2)." 
        order by a.blok, a.tanggal";
    }	
//    echo $str;

    // header
    echo"<thead> 
        <tr>
            <td align=center>No.</td>
            <td align=center>".$_SESSION['lang']['afdeling']."</td>
            <td align=center>".$_SESSION['lang']['blok']."</td>
            <td align=center>".$_SESSION['lang']['tahuntanam']."</td>
            <td align=center>".$_SESSION['lang']['tanggal']."</td>
            <td align=center>".$_SESSION['lang']['nospb']."</td>
            <td align=center>".$_SESSION['lang']['noTiket']."</td>
            <td align=center>".$_SESSION['lang']['kendaraan']."</td>
            <td align=center>".$_SESSION['lang']['jjg']."</td>
            <td align=center>"."KG ".$_SESSION['lang']['kebun']."</td>    
            <td align=center>".$_SESSION['lang']['kgwb']."</td>
            <td align=center>".$_SESSION['lang']['bjr']." ".$_SESSION['lang']['aktual']."</td>
            <td align=center>".$_SESSION['lang']['bjr']." Sensus</td>
            <td align=center>%</td>
        </tr></thead>
	<tbody>";    
    
    $res=mysql_query($str);
    $no=0;
    if(mysql_num_rows($res)<1){
        $jukol=12;
        echo"<tr class=rowcontent><td colspan=".$jukol.">".$_SESSION['lang']['tidakditemukan']."</td></tr>";
    }else{
        while($bar=mysql_fetch_object($res)){
        // content
        $no+=1;
        @$aktual=$bar->kgwb/$bar->jjg;
        echo"<tr class='rowcontent'>
            <td align=center>".$no."</td>
            <td align=left>".substr($bar->blok,0,6)."</td>
            <td align=center>".$bar->blok."</td>
            <td align=center>".$belok[$bar->blok]."</td>
            <td align=center>".$bar->tanggal."</td>
            <td align=center>".$bar->nospb."</td>";
            $notiket=$bar->notiket;
            if($notiket!='')
            echo"<td align=right>".$notiket."</td>";else{
                echo"<td bgcolor=red title='Belum Masuk PKS' align=right>".$notiket."</td>";
            }
            echo"<td align=center>".$bar->nokendaraan."</td>
            <td align=right>".$bar->jjg."</td>";
            echo "<td align=right>".number_format($bar->kgbjr,2)."</td>";
            $kgwb=$bar->kgwb;
            if($kgwb!=0){
                echo"<td align=right>".number_format($kgwb,2)."</td>";
                $beda=$kgwb-$bar->kgbjr;
                @$persen=($beda/$bar->kgbjr)*100;
            }
            else{
                echo"<td bgcolor=red title='SPB Belum Diinput' align=right>".number_format($kgwb,2)."</td>";
                $persen=0;
            }
            echo"<td align=right>".number_format($aktual,2)."</td>
            <td align=right>".$bar->bjr."</td>";
            echo"<td align=right>".number_format($persen,2)."</td>";
            echo "</tr>";
            $totalbarjjg+=$bar->jjg;
            $totalbarkgbjr+=$bar->kgbjr;
            $totalbarkgwb+=$bar->kgwb;
        }
        echo"<tr class='rowcontent'>
            <td align=center></td>
            <td align=left></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center></td>
            <td align=center>Total</td>";
//            $notiket=$bar->notiket;
//            if($notiket!='')
            echo"<td align=right></td>";
//            else{
//                echo"<td bgcolor=red title='Belum Masuk PKS' align=right>".$notiket."</td>";
//            }
            echo"<td align=center></td>
            <td align=right>".number_format($totalbarjjg)."</td>";
            echo "<td align=right>".number_format($totalbarkgbjr,2)."</td>";
//            $kgwb=$bar->kgwb;
//            if($kgwb!=0){
                echo"<td align=right>".number_format($totalbarkgwb,2)."</td>";
                $beda=$totalbarkgwb-$totalbarkgbjr;
                @$persen=($beda/$totalbarkgbjr)*100;
//            }
//            else{
//                echo"<td bgcolor=red title='SPB Belum Diinput' align=right>".number_format($kgwb,2)."</td>";
//                $persen=0;
//            }
        @$aktual=$totalbarkgwb/$totalbarjjg;
            echo"<td align=right>".number_format($aktual,2)."</td>
            <td align=right></td>";
            echo"<td align=right>".number_format($persen,2)."</td>";
            echo "</tr>";        
    } 
    echo"</tbody>
        <tfoot>
        </tfoot>";		 

?>