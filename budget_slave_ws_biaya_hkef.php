<?php
// file creator: dhyaz sep 14, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$optNm=makeOption($dbname, 'bgt_kode', 'kodebudget,nama');

$cekapa=$_POST['cekapa'];

//cek hkefektif untuk tahun budget
if($cekapa=='hkef'){
    $tahunbudget=$_POST['tahunbudget'];
    $str="select * from ".$dbname.".bgt_hk
        where tahunbudget = '".$tahunbudget."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res)){
        $hkef=$bar->harisetahun-$bar->hrminggu-$bar->hrlibur+$bar->hrliburminggu;
    }
    $optupah="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $supah="select distinct golongan from ".$dbname.".bgt_upah where 
            kodeorg='".substr($_POST['kodews'],0,4)."' and tahunbudget='".$tahunbudget."'";
    $qupah=mysql_query($supah) or die(mysql_error($conn));
    while($rupah=  mysql_fetch_assoc($qupah)){
        $optupah.="<option value='".$rupah['golongan']."'>".$optNm[$rupah['golongan']]."</option>";
    }
    echo $hkef."#####".$optupah;
}

//cek upah berdasarkan kodebudget0 SDM
if($cekapa=='upah'){
    $kodebudget0=$_POST['kodebudget0'];
    $str="select * from ".$dbname.".bgt_upah
        where closed=1 and golongan = '".$kodebudget0."' and kodeorg = '".substr($_SESSION['empl']['lokasitugas'],0,4)."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef=$bar->jumlah;
    }
    echo $hkef;        
}

//cek regional berdasarkan kodews(4) vs bgt_regional_assignment
if($cekapa=='regional'){
    $kodews=$_POST['kodews'];
    $kodeorg=substr($kodews,0,4);
    $str="select * from ".$dbname.".bgt_regional_assignment
        where kodeunit = '".$kodeorg."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef=$bar->regional;
    }
    echo $hkef;        
}

//harga barang tab1 dan tab2
if($cekapa=='barang'){
    $kodebarang1=$_POST['kodebarang1'];
    $tahunbudget=$_POST['tahunbudget'];
    $regional=$_POST['regional'];
    $str="select * from ".$dbname.".bgt_masterbarang
        where closed=1 and kodebarang = '".$kodebarang1."' and regional ='".$regional."' and tahunbudget ='".$tahunbudget."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef=$bar->hargasatuan;
    }
    echo $hkef;        
}

//tampilkan data tab0
if($cekapa=='tab0'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $kodews=$_POST['kodews'];
    $hkef='';
    $hkef.="<table id=container9 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['index']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['volume']."</td>
            <td align=center>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['jumlah']."</td>
            <td align=center>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_budget
        where kodebudget like 'SDM%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$kodews."'";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
    $hkef.="<tr class=rowcontent>
            <td align=center>".$bar->kunci."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->tipebudget."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=right>".number_format($bar->volume)."</td>
            <td align=left>".$bar->satuanv."</td>
            <td align=right>".number_format($bar->jumlah)."</td>
            <td align=left>".$bar->satuanj."</td>
            <td align=right>".number_format($bar->rupiah)."</td>";
            if($bar->tutup==0)
            $hkef.="<td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(0,".$bar->kunci.")\" title=\"Hapus\"></td>";
            else
            $hkef.="<td align=center>&nbsp;</td>";
       $hkef.="</tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//tampilkan data tab1
if($cekapa=='tab1'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $kodews=$_POST['kodews'];
//kamus namabarang
    $strJ="select kodebarang, namabarang from ".$dbname.".log_5masterbarang";
    $resJ=mysql_query($strJ,$conn);
    while($barJ=mysql_fetch_object($resJ))
    {
        $barang[$barJ->kodebarang]=$barJ->namabarang;
    }

    $hkef='';
    $hkef.="<table id=container8 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['index']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td align=center>".$_SESSION['lang']['namabarang']."</td>
            <td align=center>".$_SESSION['lang']['jumlah']."</td>
            <td align=center>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_budget
        where kodebudget like 'M%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$kodews."'";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
    $hkef.="<tr class=rowcontent>
            <td align=center>".$bar->kunci."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->tipebudget."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=right>".$bar->kodebarang."</td>
            <td align=left>".$barang[$bar->kodebarang]."</td>
            <td align=right>".number_format($bar->jumlah)."</td>
            <td align=left>".$bar->satuanj."</td>
            <td align=right>".number_format($bar->rupiah)."</td>";
            if($bar->tutup==0)
            $hkef.="
            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(1,".$bar->kunci.")\" title=\"Hapus\"></td>";
            else
            $hkef.="<td align=center>&nbsp;</td>";
       $hkef.="
       </tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//tampilkan data tab2
if($cekapa=='tab2'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $kodews=$_POST['kodews'];
//kamus namabarang
    $strJ="select kodebarang, namabarang from ".$dbname.".log_5masterbarang";
    $resJ=mysql_query($strJ,$conn);
    while($barJ=mysql_fetch_object($resJ))
    {
        $barang[$barJ->kodebarang]=$barJ->namabarang;
    }

    $hkef='';
    $hkef.="<table id=container7 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['index']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td align=center>".$_SESSION['lang']['namabarang']."</td>
            <td align=center>".$_SESSION['lang']['jumlah']."</td>
            <td align=center>".$_SESSION['lang']['satuan']."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_budget
        where kodebudget like 'TOOL%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$kodews."'";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
    $hkef.="<tr class=rowcontent>
            <td align=center>".$bar->kunci."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->tipebudget."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=right>".$bar->kodebarang."</td>
            <td align=left>".$barang[$bar->kodebarang]."</td>
            <td align=right>".number_format($bar->jumlah)."</td>
            <td align=left>".$bar->satuanj."</td>
            <td align=right>".number_format($bar->rupiah)."</td>";
            if($bar->tutup==0)
            $hkef.="
            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(2,".$bar->kunci.")\" title=\"Hapus\"></td>";
            else
            $hkef.="<td align=center>&nbsp;</td>";
       $hkef.="
       </tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//tampilkan data tab3
if($cekapa=='tab3'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $kodews=$_POST['kodews'];
//kamus namaakun
    $strJ="select * from ".$dbname.".keu_5akun where tipeakun='Biaya' and detail=1";
    $resJ=mysql_query($strJ,$conn);
    while($barJ=mysql_fetch_object($resJ))
    {
        $akun[$barJ->noakun]=$barJ->namaakun;
    }

    $hkef='';
    $hkef.="<table id=container6 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['index']."</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['noakun']."</td>
            <td align=center>".$_SESSION['lang']['namaakun']."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_budget
        where kodebudget like 'TRANSIT%' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$kodews."'";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
    $hkef.="<tr class=rowcontent>
            <td align=center>".$bar->kunci."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->tipebudget."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=right>".$bar->noakun."</td>
            <td align=left>".$akun[$bar->noakun]."</td>
            <td align=right>".number_format($bar->rupiah)."</td>";
            if($bar->tutup==0)
            $hkef.="
            <td align=center><img id=\"delRow\" class=\"zImgBtn\" src=\"images/application/application_delete.png\" onclick=\"deleteRow(3,".$bar->kunci.")\" title=\"Hapus\"></td>";
            else
            $hkef.="<td align=center>&nbsp;</td>";
       $hkef.="
       </tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//tampilkan data tab4
if($cekapa=='tab4'){
    $tipebudget=$_POST['tipebudget'];
    $tahunbudget=$_POST['tahunbudget'];
    $kodews=$_POST['kodews'];
//kamus namaakun
//    $strJ="select * from ".$dbname.".keu_5akun where tipeakun='Biaya' and detail=1";
//    $resJ=mysql_query($strJ,$conn);
//    while($barJ=mysql_fetch_object($resJ))
//    {
//        $akun[$barJ->noakun]=$barJ->namaakun;
//    }

    $hkef=''; $no=1;
    $hkef.="<table id=container6 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr id=baris_0 name=baris_0>
            <td align=center>No.</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['kodeorg']."</td>
            <td align=center>".$_SESSION['lang']['tipeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['kodeanggaran']."</td>
            <td align=center>".$_SESSION['lang']['noakun']."</td>
            <td align=center>".$_SESSION['lang']['volume']."</td>
            <td align=center>".$_SESSION['lang']['kodebarang']."</td>
            <td align=center>".$_SESSION['lang']['jumlah']."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
       </tr>  
     </thead>
     <tbody>";
    $str="select * from ".$dbname.".bgt_budget
        where tutup = '0' and tipebudget = '".$tipebudget."' and tahunbudget = '".$tahunbudget."' and kodeorg = '".$kodews."' order by kodebudget, noakun, kodebarang";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
    $hkef.="<tr id=baris_".$no." class=rowcontent>
            <td align=center><input type=hidden id=kunci_".$no." name=kunci_".$no." value=".$bar->kunci.">".$no."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=center>".$bar->kodeorg."</td>
            <td align=center>".$bar->tipebudget."</td>
            <td align=center>".$bar->kodebudget."</td>
            <td align=right>".$bar->noakun."</td>
            <td align=right>".number_format($bar->volume)."</td>
            <td align=right>".$bar->kodebarang."</td>
            <td align=right>".$bar->jumlah."</td>
            <td align=right>".number_format($bar->rupiah)."</td>
       </tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//delete row, all tab berdasarkan kunci
if($cekapa=='delete0'){
    $kunci=$_POST['kunci'];
    $str="delete from ".$dbname.".bgt_budget 
    where kunci='".$kunci."'";
    if(mysql_query($str))
    {}
    else
    {echo " Gagal3,".addslashes(mysql_error($conn));}
}