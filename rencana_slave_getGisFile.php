<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
$param=$_POST;

if($param['kodeorg']!='' and $param['periode']!='')
{
$str1="select a.*,b.namakaryawan from ".$dbname.".rencana_gis_file a
            left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
            where unit='".$param['kodeorg']."' and tanggal like '".$param['periode']."%' 
            and kode='".$param['kode']."' and a.karyawanid='".$_SESSION['standard']['userid']."'     
            order by a.lastupdate  desc";    
} 
else if($param['kodeorg']!='' and $param['periode']==''){
$str1="select a.*,b.namakaryawan from ".$dbname.".rencana_gis_file a
            left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
            where unit='".$param['kodeorg']."' and kode='".$param['kode']."' and a.karyawanid='".$_SESSION['standard']['userid']."'
            order by a.lastupdate  desc"; 
}
else if($param['kodeorg']=='' and $param['periode']!=''){
$str1="select a.*,b.namakaryawan from ".$dbname.".rencana_gis_file a
            left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
            where  tanggal like '".$param['periode']."%' and 
            kode='".$param['kode']."' and a.karyawanid='".$_SESSION['standard']['userid']."'
            order by a.lastupdate  desc"; 
}
else if($param['kodeorg']=='' and $param['periode']=='' and $param['kode']=='' ){
$str1="select a.*,b.namakaryawan from ".$dbname.".rencana_gis_file a
            left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where a.karyawanid='".$_SESSION['standard']['userid']."' 
            order by a.lastupdate  desc limit 100"; 
}
else
{
$str1="select a.*,b.namakaryawan from ".$dbname.".rencana_gis_file a
            left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid 
            where   kode='".$param['kode']."' and a.karyawanid='".$_SESSION['standard']['userid']."'   
            order by a.lastupdate  desc";     
}   
$res1=mysql_query($str1);
    $no=0;
    while($bar1=mysql_fetch_object($res1))
    {
            $no+=1;
            echo"<tr class=rowcontent>
               <td>".$no."</td>
                <td>".$bar1->unit."</td>
                    <td>".$bar1->kode."</td>
                    <td>".tanggalnormal($bar1->tanggal)."</td>
                    <td>".$bar1->namakaryawan."</td>
                    <td>".$bar1->lastupdate."</td>
                    <td>".$bar1->keterangan."</td>
                    <td>".$bar1->namafile."</td>
                    <td align=right>".$bar1->ukuran."</td>
                    <td>".$bar1->namakaryawan."</td>
                    <td>";
            if($bar1->karyawanid==$_SESSION['standard']['userid']){
            echo"<img class=zImgBtn src=images/skyblue/delete.png   title='Edit' onclick=\"delFile('".$bar1->unit."','".$bar1->kode."','".$bar1->namafile."');\"> &nbsp  &nbsp  &nbsp"; 
            }                
            echo "<img class=zImgBtn src=images/skyblue/save.png   title='Save' onclick=\"download('".$bar1->namafile."');\"></td></tr>";
    }
?>