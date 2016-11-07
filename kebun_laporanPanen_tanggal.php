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
    
    // urutin tanggal
    $tanggal=Array();
    if($tanggal2[1]>$tanggal1[1]){ // beda bulan
        for ($i = $tanggal1[0]; $i <= $tanggalterakhir; $i++) {
            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
            $tanggal[$tanggal1[2].'-'.$tanggal1[1].'-'.$ii]=$tanggal1[2].'-'.$tanggal1[1].'-'.$ii;
        }
        for ($i = 1; $i <= $tanggal2[0]; $i++) {
            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
            $tanggal[$tanggal2[2].'-'.$tanggal2[1].'-'.$ii]=$tanggal2[2].'-'.$tanggal2[1].'-'.$ii;
        }
    }else{ // sama bulan
        for ($i = $tanggal1[0]; $i <= $tanggal2[0]; $i++) {
            if(strlen($i)==1)$ii='0'.$i; else $ii=$i;
            $tanggal[$tanggal1[2].'-'.$tanggal1[1].'-'.$ii]=$tanggal1[2].'-'.$tanggal1[1].'-'.$ii;
        }
    }
        
    if($unit=='') // script copy-an dari kebun_laporanPanen.php
    {
        $str="select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,d.bloklama,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from (".$dbname.".kebun_prestasi_vw a
        left join ".$dbname.".organisasi c
        on substr(a.kodeorg,1,4)=c.kodeorganisasi) inner join ".$dbname.".setup_blok d 
                on a.kodeorg=d.kodeorg      
        where c.induk = '".$pt."'  and a.tanggal between '".tanggalsystem($tgl1)."' and '".tanggalsystem($tgl2)."' group by a.tanggal,a.kodeorg";
    }
    else
    {
        $str="select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,d.bloklama,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a 
            inner join ".$dbname.".setup_blok d 
                on a.kodeorg=d.kodeorg     
        where unit = '".$unit."'  and a.tanggal between '".tanggalsystem($tgl1)."' and '".tanggalsystem($tgl2)."' group by a.tanggal, a.kodeorg";
    }
$dzArr=array();
        // ni kemarin buat apa ya?
$kmrn=strtotime ('-1 day',strtotime ($date1));
$kmrn=date ('Y-m-d', $kmrn );
if($unit=='') // script copy-an dari kebun_laporanPanen.php
    {
        $str2="select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,d.bloklama,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from (".$dbname.".kebun_prestasi_vw a
        left join ".$dbname.".organisasi c
        on substr(a.kodeorg,1,4)=c.kodeorganisasi) inner join ".$dbname.".setup_blok d 
                on a.kodeorg=d.kodeorg     
        where c.induk = '".$pt."'  and a.tanggal between '".$kmrn."' and '".tanggalsystem($tgl2)."' group by a.tanggal,a.kodeorg";
    }
    else
    {
        $str2="select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,d.bloklama,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,
        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a 
        inner join ".$dbname.".setup_blok d 
                on a.kodeorg=d.kodeorg         
        where unit = '".$unit."'  and a.tanggal between '".$kmrn."' and '".tanggalsystem($tgl2)."' group by a.tanggal, a.kodeorg";
    }
    //echo $str2;
    $qKmrn=mysql_query($str2) or die(mysql_error($conn));
    while($rKmr=mysql_fetch_object($qKmrn))
    {
            $dzArrk[$rKmr->kodeorg][$rKmr->tanggal.'j']=$rKmr->jjg;
    }

    // isi array
    $jumlahhari=count($tanggal);
    $res=mysql_query($str);
    
    if(mysql_num_rows($res)<1){
        $jukol=($jumlahhari*3)+5;
        echo"<tr class=rowcontent><td colspan=".$jukol.">".$_SESSION['lang']['tidakditemukan']."</td></tr>";
    }else{
        while($bar=mysql_fetch_object($res)){
            $dzArr[$bar->kodeorg][$bar->tanggal]=$bar->tanggal;
            $dzArr[$bar->kodeorg]['kodeorg']=$bar->kodeorg;
            $dzArr[$bar->kodeorg]['bloklama']=$bar->bloklama;
            $dzArr[$bar->kodeorg]['tahuntanam']=$bar->tahuntanam;
            $dzArr[$bar->kodeorg][$bar->tanggal.'j']=$bar->jjg;
            $dzArr[$bar->kodeorg][$bar->tanggal.'k']=$bar->berat;
            $dzArr[$bar->kodeorg][$bar->tanggal.'h']=$bar->jumlahhk;
        }	
    } 
    if(!empty($dzArr)) { // list isi data on kodeorg
        foreach($dzArr as $c=>$key) { // list tanggal
            $sort_kodeorg[] = $key['kodeorg'];
            $sort_tahuntanam[] = $key['tahuntanam'];
        }
        array_multisort($sort_kodeorg, SORT_ASC, $sort_tahuntanam, SORT_ASC, $dzArr); // urut kodeorg, terus tahun tanam
    }
        
    // header
    echo"<thead>
        <tr>
            <td rowspan=2 align=center>No.</td>
            <td rowspan=2 align=center>".$_SESSION['lang']['afdeling']."</td>
            <td rowspan=2 align=center>".$_SESSION['lang']['kodeblok']."</td>
            <td rowspan=2 align=center>".$_SESSION['lang']['bloklama']."</td>    
            <td rowspan=2 align=center>".$_SESSION['lang']['tahuntanam']."</td>";    
    foreach($tanggal as $tang){
        $ting=explode('-',$tang);
        $qwe=date('D', strtotime($tang));
        echo"<td colspan=3 align=center>";
        if($qwe=='Sun')echo"<font color=red>".$ting[2]."</font>"; else echo $ting[2]; 
        echo"</td>";
    }
    echo"<td colspan=3 align=center>Total</td><td align=center>Rata2</td></tr><tr>";  
    foreach($tanggal as $tang){
        echo"<td align=center>".$_SESSION['lang']['jjg']."</td>
            <td align=center>".$_SESSION['lang']['kg']."</td>
            <td align=center>".$_SESSION['lang']['jumlahhk']."</td>";    
    }
    echo"<td align=center>".$_SESSION['lang']['jjg']."</td>
        <td align=center>".$_SESSION['lang']['kg']."</td>
        <td align=center>".$_SESSION['lang']['jumlahhk']."</td><td align=center>".$_SESSION['lang']['jjg']."</td></tr>  
        </thead>
	<tbody>";

    // content
    $no=0;
    foreach($dzArr as $arey){ // list isi data on kodeorg
        $no+=1;
        echo"<tr class='rowcontent'>
            <td align=center>".$no."</td>
            <td align=center>".substr($arey['kodeorg'],0,6)."</td>
            <td align=center>".$arey['kodeorg']."</td>
            <td align=center>".$arey['bloklama']."</td>    
            <td align=center>".$arey['tahuntanam']."</td>";    
        $totalj=0;
        $totalk=0;
        $totalh=0;
        $totaltanpanol=0;
        $jumlahtanpanol=0;
        foreach($tanggal as $tang){ // list tanggal
            $dbg="";
            $tglkmrn=strtotime ('-1 day',strtotime ($tang));
            $tglkmrn2=date ('Y-m-d', $tglkmrn );
//            if(($arey[$tglkmrn2.'j']!=0)&&($arey[$tang.'j']!=0))
            if(($dzArrk[$arey['kodeorg']][$tglkmrn2.'j']!=0)&&($arey[$tang.'j']!=0))
            {
                $dbg="bgcolor=red";
            }
            $qwe=date('D', strtotime($tang));
            if($qwe=='Sun'){
                echo"<td align=right ".$dbg."><font color=red>".number_format($arey[$tang.'j'])."</font></td>";
                echo"<td align=right ><font color=red>".number_format($arey[$tang.'k'])."</font></td>";
                echo"<td align=right ><font color=red>".number_format($arey[$tang.'h'])."</font></td>";
            }else{
                echo"<td align=right ".$dbg.">".number_format($arey[$tang.'j'])."</td>";
                echo"<td align=right >".number_format($arey[$tang.'k'])."</td>";
                echo"<td align=right >".number_format($arey[$tang.'h'])."</td>";
            }
//            echo"</td>";
            $total[$tang.'j']+=$arey[$tang.'j']; // tambahin total bawah
            $total[$tang.'k']+=$arey[$tang.'k']; // tambahin total bawah
            $total[$tang.'h']+=$arey[$tang.'h']; // tambahin total bawah
            
            $totalj+=$arey[$tang.'j']; // tambahin total kanan
            $totalk+=$arey[$tang.'k']; // tambahin total kanan
            $totalh+=$arey[$tang.'h']; // tambahin total kanan
            
            if($arey[$tang.'j']>0){
                $totaltanpanol+=$arey[$tang.'j'];
                $jumlahtanpanol+=1;
            }
        }
        @$rataj=$totaltanpanol/$jumlahtanpanol;
        echo"<td align=right>".number_format($totalj)."</td>
            <td align=right>".number_format($totalk)."</td>
            <td align=right>".number_format($totalh)."</td><td align=right>".number_format($rataj)."</td></tr>";
    }
    
    // tampilin total
    echo"<tr class='rowcontent'>
        <td colspan=5 align=center>Total</td>";
    $totalj=0;
    $totalk=0;
    $totalh=0;
    foreach($tanggal as $tang){ // list tanggal
        echo"<td align=right>".number_format($total[$tang.'j'])."</td>";   
        echo"<td align=right>".number_format($total[$tang.'k'])."</td>";    
        echo"<td align=right>".number_format($total[$tang.'h'])."</td>";    
        $totalj+=$total[$tang.'j']; // tambahin total kanan
        $totalk+=$total[$tang.'k']; // tambahin total kanan
        $totalh+=$total[$tang.'h']; // tambahin total kanan
    }
    echo"<td align=right>".number_format($totalj)."</td>
        <td align=right>".number_format($totalk)."</td>
        <td align=right>".number_format($totalh)."</td><td></td></tr>";
    echo"</tbody>
        <tfoot>
        </tfoot>";		 

?>