<?php
// file creator: dhyaz sep 27, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$cekapa=$_POST['cekapa'];

//kamus barang
$str="select kodebarang, namabarang, satuan from ".$dbname.".log_5masterbarang
    ";
$res=mysql_query($str);
while($bar= mysql_fetch_object($res))
{
   $namabarang[$bar->kodebarang]=$bar->namabarang;
   $satuanbarang[$bar->kodebarang]=$bar->satuan;
}

    $str="select kode, kelompok from ".$dbname.".log_5klbarang
                    order by kode 
                    ";
            $artikelompok['']=$_SESSION['lang']['all'];
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $artikelompok[$bar->kode]=$bar->kelompok;
    }

//tampilkan data tab0
if($cekapa=='tab0'){
    $tahunbudget0=$_POST['tahunbudget0'];
    $regional0=$_POST['regional0'];
    $kelompokbarang0=$_POST['kelompokbarang0'];
    $hkef='';
//    $hkef.="<span id=printPanel style='display:none;'>
    $hkef.="<span id=printPanel>
     <img onclick=hargabarangKeExcel(event,'bgt_slave_laporan_harga_barang_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
     <img onclick=hargabarangKePDF(event,'bgt_slave_laporan_harga_barang_PDF.php') src=images/pdf.jpg class=resicon title='PDF'> 
	 </span>";    
        $hkef.="<table><tr>
            <td colspan=2 align=left>".$_SESSION['lang']['budgetyear']."</td>
            <td colspan=4 align=left>: ".$tahunbudget0."</td>
        </tr>
        <tr>
            <td colspan=2 align=left>".$_SESSION['lang']['regional']."</td>
            <td colspan=4 align=left>: ".$regional0."</td>
        </tr>
        <tr>
            <td colspan=2 align=left>".$_SESSION['lang']['kelompokbarang']."</td>
            <td colspan=4 align=left>: ".$kelompokbarang0." ".$artikelompok[$kelompokbarang0]."</td>
        </tr>
        </table>";

    $hkef.="<table id=container00 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td align=left>".$_SESSION['lang']['namabarang']."</td>
            <td align=left>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['hargabudget']."</td>
            <td align=center>".$_SESSION['lang']['hargatahunlalu']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_masterbarang
        where closed = 1 and tahunbudget = '".$tahunbudget0."' and regional = '".$regional0."' and kodebarang like '".$kelompokbarang0."%'";
    $res=mysql_query($str);
    $no=0;
    while($bar= mysql_fetch_object($res))
    {
    $no+=1;
    $hkef.="<tr class=rowcontent>
            <td align=center>".$no."</td>
            <td align=center>".$bar->kodebarang."</td>
            <td align=left>".$namabarang[$bar->kodebarang]."</td>
            <td align=left>".$satuanbarang[$bar->kodebarang]."</td>
            <td align=right>".number_format($bar->hargasatuan,2)."</td>
            <td align=right>".number_format($bar->hargalalu,2)."</td>
       </tr>";
    }
    if($no==0){
    $hkef.="<tr>
            <td colspan= 6 align=center>Data tidak ada atau belum ditutup.</td>
       </tr>";
    }

    $hkef.="</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
    echo $hkef;        
}

//tampilkan data tab0
if($cekapa=='tab1'){
    $tahunbudget1=$_POST['tahunbudget1'];
    $regional1=$_POST['regional1'];
    $namabarang1=$_POST['namabarang1'];
    $hkef='';
//    $hkef.="<span id=printPanel style='display:none;'>
    $hkef.="<span id=printPanel>
     <img onclick=hargabarangKeExcel2(event,'bgt_slave_laporan_harga_barang_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
     <img onclick=hargabarangKePDF2(event,'bgt_slave_laporan_harga_barang_PDF.php') src=images/pdf.jpg class=resicon title='PDF'> 
	 </span>";    
        $hkef.="<table><tr>
            <td colspan=2 align=left>".$_SESSION['lang']['regional']."</td>
            <td colspan=4 align=left>: ".$regional1."</td>
        </tr>
        <tr>
            <td colspan=2 align=left>".$_SESSION['lang']['budgetyear']."</td>
            <td colspan=4 align=left>: ".$tahunbudget1."</td>
        </tr>
        <tr>
            <td colspan=2 align=left>".$_SESSION['lang']['caribarang']."</td>
            <td colspan=4 align=left>: ".$namabarang1."</td>
        </tr>
        </table>";

    $hkef.="<table id=container00 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td align=left>".$_SESSION['lang']['namabarang']."</td>
            <td align=left>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['hargabudget']."</td>
            <td align=center>".$_SESSION['lang']['hargatahunlalu']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select a.* from ".$dbname.".bgt_masterbarang a
        left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang
        where a.tahunbudget = '".$tahunbudget1."' and a.regional = '".$regional1."' and b.namabarang like '%".$namabarang1."%'";
//    echo $str;
    $res=mysql_query($str);
    $no=0;
    while($bar= mysql_fetch_object($res))
    {
    $no+=1;
    $hkef.="<tr class=rowcontent>
            <td align=center>".$no."</td>
            <td align=center>".$bar->kodebarang."</td>
            <td align=left>".$namabarang[$bar->kodebarang]."</td>
            <td align=left>".$satuanbarang[$bar->kodebarang]."</td>
            <td align=right>".number_format($bar->hargasatuan,2)."</td>
            <td align=right>".number_format($bar->hargalalu,2)."</td>
       </tr>";
    }
    if($no==0){
    $hkef.="<tr>
            <td colspan= 6 align=center>Data tidak ada atau belum ditutup.</td>
       </tr>";
    }


    $hkef.="</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
    echo $hkef;        
}


//echo $cekapa;




























//    echo $cekapa;        
