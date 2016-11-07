<?php
// file creator: dhyaz sep 20, 2011
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$cekapa=$_POST['cekapa'];
if($cekapa=='')$cekapa=$_GET['cekapa'];

if($cekapa=='saveatas'){

    $tahunbudget=$_POST['tahunbudget'];
    $departemen=$_POST['departemen'];
    $noakun=$_POST['noakun'];
    $keterangan=$_POST['keterangan'];
    $alokasi=$_POST['alokasi'];
    $jumlahbiaya=$_POST['jumlahbiaya'];
    $fisik=$_POST['fisik'];
    if($fisik=='')
        $fisik=0;
    $satuanf=$_POST['satuanf'];
    $str2="select distinct tutup from ".$dbname.".bgt_dept where tahunbudget = '".$tahunbudget."' and departemen ='".$departemen."' ";
    $res2=mysql_query($str2);
    $bar2=mysql_fetch_assoc($res2);
    if($bar2['tutup']!=0)
    {
        exit("Error:  Budget ".$thnBudget." is closed,can not modify");
    }
            
    $str="INSERT INTO ".$dbname.".`bgt_dept` (
    `tahunbudget` ,
    `departemen` ,
    `noakun` ,
    `keterangan` ,
    `alokasibiaya` ,
    `jumlah` ,
    `updateby` ,
    `fisik`,
    `satuanf`
    )
    VALUES (
    '".$tahunbudget."', '".$departemen."', '".$noakun."', '".$keterangan."', '".$alokasi."', '".$jumlahbiaya."', '".$_SESSION['standard']['userid']."',
     ".$fisik.",'".$satuanf."'        
    )";

    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".$str.addslashes(mysql_error($conn));
    }	
    
}

//tampilkan data tab0
if($cekapa=='tab'){

    $pilihtahun0=$_POST['pilihtahun0'];
      $str="select distinct tahunbudget from ".$dbname.".bgt_dept
        where departemen = '".$_SESSION['empl']['bagian']."'
            order by tahunbudget desc
        ";
    $opttahunbudget="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
        $opttahunbudget.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
    }
    $hkef='';
    $hkef.="Budget Department : ".$_SESSION['empl']['bagian']." --- ";
    $hkef.=$_SESSION['lang']['budgetyear']." : <select name=pilihtahun0 id=pilihtahun0 onchange=\"updateTab();\"><option value=''>".$_SESSION['lang']['all']."</option>".$opttahunbudget."</select>";
    $hkef.="<input type=hidden id=hidden0 name=hidden0 value=\"\">";
    $hkef.="<table id=container6 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>No</td>
            <td align=center>".$_SESSION['lang']['budgetyear']."</td>
            <td align=center>".$_SESSION['lang']['namaakun']."</td>
            <td align=center>".$_SESSION['lang']['fisik']."</td>            
            <td align=center>".$_SESSION['lang']['satuan']."</td>                  
            <td align=center>".$_SESSION['lang']['keterangan']."</td>
            <td align=center>".$_SESSION['lang']['alokasibiaya']."</td>
            <td align=center>".$_SESSION['lang']['totalbiaya']."</td>
            <td align=center>Jan</td>
            <td align=center>Feb</td>
            <td align=center>Mar</td>
            <td align=center>Apr</td>
            <td align=center>May</td>
            <td align=center>Jun</td>
            <td align=center>Jul</td>
            <td align=center>Aug</td>
            <td align=center>Sep</td>
            <td align=center>Oct</td>
            <td align=center>Nov</td>
            <td align=center>Dec</td>
            <td align=center>".$_SESSION['lang']['action']."</td>
       </tr>  
     </thead>
     <tbody>";
//pilihan kodeakun    
    if($_SESSION['language']=='EN'){
        $dd='namaakun1 as namaakun';
    }else{
        $dd='namaakun as namaakun';
    }
    $str="select noakun,".$dd." from ".$dbname.".keu_5akun
                    where detail=1 order by noakun
                    ";
    $optnoakun="";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $noakun[$bar->noakun]=$bar->namaakun;
    }
      $str="select * from ".$dbname.".bgt_dept
        where departemen = '".$_SESSION['empl']['bagian']."' and tahunbudget like '%".$pilihtahun0."%'
            order by tahunbudget, noakun, alokasibiaya";
    $res=mysql_query($str);
    $no=1;
    while($bar= mysql_fetch_object($res))
    {
    $hkef.="<tr class=rowcontent>
            <td align=center>".$no."</td>
            <td align=center>".$bar->tahunbudget."</td>
            <td align=left>".$bar->noakun." - ".$noakun[$bar->noakun]."</td>
            <td align=right>".$bar->fisik."</td>
            <td align=left>".$bar->satuanf."</td>    
            <td align=center>".$bar->keterangan."</td>
            <td align=center>".$bar->alokasibiaya."</td>
            <td align=right>".number_format($bar->jumlah)."</td>
            <td align=right>".number_format($bar->d01)."</td>
            <td align=right>".number_format($bar->d02)."</td>
            <td align=right>".number_format($bar->d03)."</td>
            <td align=right>".number_format($bar->d04)."</td>
            <td align=right>".number_format($bar->d05)."</td>
            <td align=right>".number_format($bar->d06)."</td>
            <td align=right>".number_format($bar->d07)."</td>
            <td align=right>".number_format($bar->d08)."</td>
            <td align=right>".number_format($bar->d09)."</td>
            <td align=right>".number_format($bar->d10)."</td>
            <td align=right>".number_format($bar->d11)."</td>
            <td align=right>".number_format($bar->d12)."</td>";
    if($bar->tutup==0)
    {
    $hkef.="<td align=center>
                <input type=\"image\" id=delete src=images/application/application_delete.png class=dellicon title=".$_SESSION['lang']['delete']." onclick=\"deleteRow(".$bar->kunci.")\";>
                <input type=\"image\" id=search src=images/search.png class=dellicon title=".$_SESSION['lang']['sebaran']." onclick=\"sebaran(".$bar->kunci.",event)\";>
            </td>";
    }
    else
    {
        $hkef.="<td>".$_SESSION['lang']['tutup']."</td>";
    }
       $hkef.="</tr>";
    $no+=1;
    }
    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//delete row, all tab berdasarkan kunci
if($cekapa=='delete'){
    $kunci=$_POST['kunci'];
    $str="delete from ".$dbname.".bgt_dept 
    where kunci='".$kunci."'";
    if(mysql_query($str))
    {
    }
    else
    {echo " Gagal3,".addslashes(mysql_error($conn));}
}



//tampilkan data tab4
if($cekapa=='sebaran'){
    $kunci=$_GET['kunci'];
//kamus kodeakun    
    $str="select noakun,namaakun from ".$dbname.".keu_5akun
                    where detail=1  order by noakun
                    ";
    $res=mysql_query($str);
    while($bar=mysql_fetch_object($res))
    {
            $noakun[$bar->noakun]=$bar->namaakun;
    }
    
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<script language=javascript1.2 src="js/generic.js"></script>
<script language=javascript1.2 src="js/bgt_departemen.js"></script>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?php
    
    $hkef='';

    $str="select * from ".$dbname.".bgt_dept
        where kunci = '".$kunci."'";
    $res=mysql_query($str);
    $no=1;
    $res=mysql_query($str);
    $no=1;
    $df=0;
    while($bar= mysql_fetch_object($res))
    {
        $rp01=$bar->d01;
        $rp02=$bar->d02;
        $rp03=$bar->d03;
        $rp04=$bar->d04;
        $rp05=$bar->d05;
        $rp06=$bar->d06;
        $rp07=$bar->d07;
        $rp08=$bar->d08;
        $rp09=$bar->d09;
        $rp10=$bar->d10;
        $rp11=$bar->d11;
        $rp12=$bar->d12;
        $df=$rp01+$rp02+$rp03+$rp04+$rp05+$rp06+$rp07+$rp08+$rp09+$rp10+$rp11+$rp12;
        $total=$bar->jumlah;
        if($df==0){
            $rp01=number_format(($bar->jumlah/12),2,'.','');
            $rp02=number_format(($bar->jumlah/12),2,'.','');
            $rp03=number_format(($bar->jumlah/12),2,'.','');
            $rp04=number_format(($bar->jumlah/12),2,'.','');
            $rp05=number_format(($bar->jumlah/12),2,'.','');
            $rp06=number_format(($bar->jumlah/12),2,'.','');
            $rp07=number_format(($bar->jumlah/12),2,'.','');
            $rp08=number_format(($bar->jumlah/12),2,'.','');
            $rp09=number_format(($bar->jumlah/12),2,'.','');
            $rp10=number_format(($bar->jumlah/12),2,'.','');
            $rp11=number_format(($bar->jumlah/12),2,'.','');
            $rp12=number_format(($bar->jumlah/12),2,'.','');
        }
    }
   $hkef.="<table id=container5 class=sortable cellspacing=1 border=0 width=100%>
     <thead>
        <tr>
            <td align=center>".$_SESSION['lang']['bulan']."</td><td>%</td><td align=right>".number_format($total,2,'.',',')."</td></tr>
       </tr>  
     </thead>
     <tbody>";

       for($x=1;$x<13;$x++){
           $z=str_pad($x, 2, "0", STR_PAD_LEFT);
          $hkef.="<tr class=rowcontent><td>".$z."</td>
                <td><input type=text class=myinputtextnumber onkeypress=\"return angka_doang(event);\" id=persen".$x." size=3 onblur=ubahNilai(".$total.") value=".number_format((${"rp".$z}/$total*100),2,'.','')."></td>
                <td><input id=rupiah".$x." type=text class=myinputtextnumber onkeypress=\"return angka_doang(event)\" value='".${"rp".$z}."' size=15></td></tr>";
       }    
                
       $hkef.="<tr class=rowcontent><td align=center colspan=3>
                <input type=hidden id=total4 name=total4 value=\"".$total."\">
                <input type=hidden id=progress name=progress value=\"\">    
                <input type=\"image\" id=search src=images/save.png class=dellicon title=".$_SESSION['lang']['save']." onclick=\"simpansebaran(".$kunci.",".$total.",event)\";>
            </td></tr>
           </tbody>
     <tfoot>
     </tfoot>		 
     </table><br><br>
       <center><button class=mybutton id=tutup name=tutup onclick=parent.closeDialog()>".$_SESSION['lang']['close']."</button>
       ";

    echo $hkef;        


    echo "</tbody>
     <tfoot>
     </tfoot>		 
     </table>";
}

//save sebaran
if($cekapa=='simpansebaran'){

    $kunci=$_POST['kunci'];
    $d01=$_POST['d01'];
    $d02=$_POST['d02'];
    $d03=$_POST['d03'];
    $d04=$_POST['d04'];
    $d05=$_POST['d05'];
    $d06=$_POST['d06'];
    $d07=$_POST['d07'];
    $d08=$_POST['d08'];
    $d09=$_POST['d09'];
    $d10=$_POST['d10'];
    $d11=$_POST['d11'];
    $d12=$_POST['d12'];

    $str="select * from ".$dbname.".bgt_dept
        where kunci = '".$kunci."'";
    $res=mysql_query($str);
    //$no=1;
    $hkef='';
    while($bar= mysql_fetch_object($res))
    {
        $hkef.=$bar->kunci;
    }
    if($hkef!=''){
        $hkef='Data sudah ada : '.$hkef;
    }
    
    $str="select * from ".$dbname.".bgt_dept
        where kunci = '".$kunci."'";
    $res=mysql_query($str);
    //$no=1;
    $cektotal='';
    while($bar= mysql_fetch_object($res))
    {
        $totalah=($d01)+($d02)+($d03)+($d04)+($d05)+($d06)+($d07)+($d08)+($d09)+($d10)+($d11)+($d12);
        if($totalah>$bar->jumlah)$cektotal.=number_format($totalah)." > ".number_format($bar->jumlah);
    }
    if($cektotal!=''){
        $cektotal='Total sebaran melebihi tahunan. '.$cektotal;
        echo $cektotal;
        exit;
    }
   
    $str="UPDATE ".$dbname.".`bgt_dept` SET `d01` = '".$d01."', `d02` = '".$d02."', `d03` = '".$d03."', `d04` = '".$d04."', `d05` = '".$d05."', `d06` = '".$d06."', `d07` = '".$d07."', `d08` = '".$d08."', `d09` = '".$d09."', `d10` = '".$d10."', `d11` = '".$d11."', `d12` = '".$d12."' WHERE kunci = '".$kunci."'";
    if(mysql_query($str))
    {} else
    {
        echo " Gagal,".$str.addslashes(mysql_error($conn));
    }	
    
}

if($cekapa=='updatetahun'){

    $str="select distinct tahunbudget from ".$dbname.".bgt_dept
        where departemen = '".$_SESSION['empl']['bagian']."'
            order by tahunbudget desc
    ";
    $res=mysql_query($str);
    $hkef="<option value=''>".$_SESSION['lang']['all']."</option>";
    while($bar= mysql_fetch_object($res))
    {
//        $hkef.=$bar->tahunbudget." ".$bar->tahunbudget." ";
        $hkef.="<option value='".$bar->tahunbudget."'>".$bar->tahunbudget."</option>";
    }
        echo $hkef;
}
if($cekapa=='closeBudget')
{
   // exit("Error".);
    $tahunbudget=$_POST['tahunbudget'];
     if($tahunbudget=='')
        {
            exit("Error: Tahun Budget Tidak Boleh Kosong");
        }
                $sQl="select distinct tutup from ".$dbname.".bgt_dept where departemen = '".$_SESSION['empl']['bagian']."' and tahunbudget='".$tahunbudget."' and tutup=1";
               // exit("error".$sQl);
                $qQl=mysql_query($sQl) or die(mysql_error($conn));
                $row=mysql_num_rows($qQl);
                if($row!=1)
                {
                    $sUpdate="update ".$dbname.".bgt_dept set tutup=1 where departemen = '".$_SESSION['empl']['bagian']."' and tahunbudget='".$tahunbudget."'";
                    //exit("error".$sUpdate);
                    if(mysql_query($sUpdate))
                        echo"";
                    else
                         echo " Gagal,_".$sUpdate."__".(mysql_error($conn));
                }
                else
                {
                    exit("Error:Sudah di Tutup");
                }
}
if($cekapa=='getThnBudget')
{
    $optThnTtp="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $sThn="select distinct tahunbudget from ".$dbname.".bgt_dept where departemen like '%".$_SESSION['empl']['bagian']."%' and tutup=0 order by tahunbudget desc";
    //echo $sThn;
    $qThn=mysql_query($sThn) or die(mysql_error($conn));
    while($rThn=mysql_fetch_assoc($qThn))
    {
    $optThnTtp.="<option value='".$rThn['tahunbudget']."'>".$rThn['tahunbudget']."</option>";
    }
    echo $optThnTtp;
}