<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

// ..filterK()
  // ..tipekaryawan
    if (isset($_POST['tipekaryawan'])) {
      $sTip = "select id,tipe from ".$dbname.".sdm_5tipekaryawan";
      $qTip = mysql_query($sTip) or die(mysql_error());
      while ($rTip = mysql_fetch_object($qTip)) {
        $tkar[$rTip->id]=$rTip->tipe;
      }

      $tip = $_POST['tipekaryawan'];
      $lok = $_POST['lokasitugas'];

      // ..get datakaryawan !=0000-00-00
      if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') {
            $sKary = "select nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan where tanggalkeluar!='0000-00-00' and tipekaryawan=0 and lokasitugas='".$lok."' order by namakaryawan";
      } else if ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') {
            if ($tip == '')
              $tip = 'tipekaryawan in(1,2,3,6,4)';
            else if($tip == '0')
              exit("Error : You don't have permission");
            else {
              $tip = "tipekaryawan='".$tip."'";
            }

            if ($lok == '')
              $lok = "left(lokasitugas,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')";
            else
              $lok = "lokasitugas='".$lok."'";
              $dimanakah = $tip." and ".$lok;
              $sKary = "select nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan where ".$dimanakah." and tanggalkeluar!='0000-00-00'";
      } else {
        if($tip=='')
             $tip='tipekaryawan in(1,2,3,6,4)';
         else if($tip=='0')
              exit("Error: you don`t have permission");
         else {
             $tip="tipekaryawan='".$tip."'";
         }

//         $str=" select nik,karyawanid,namakaryawan from ".$dbname.".datakaryawan where left(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."' and ".$tip." and tanggalkeluar
//         !='0000-00-00' order by namakaryawan";
      }
      //exit('error'.$sKary); 
      $qKary = mysql_query($sKary) or die(mysql_error());
      while ($rKary2 = mysql_fetch_object($qKary)) {
         $oKary .= "<option value='".$rKary2->karyawanid."'>".$rKary2->nik." | ".$rKary2->namakaryawan."</option>";
      }
      echo $oKary;
      exit(); 
    }
// ..end of filterK() =====

// ..List Data
    // ..limit list
    $limit = 20;
    // ..page begin
    $page = 0;

// ..get jumlah baris dalam tahun ini
    if (isset($_POST['tex'])) {
      $notransaksi.=$_POST['tex'];
    }

// ..get data karyawan --- sdm_suratperingatan == datakaryawan
    if(substr($_SESSION['empl']['lokasitugas'],2,2)=='HO') {
      $sjKary="select count(*) as jlhbrs from ".$dbname.".sdm_pengalamankerja a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.namakaryawan like '%".$notransaksi."%' order by jlhbrs desc";
    } else if($_SESSION['empl']['tipelokasitugas']=='KANWIL') {
      $sjKary="select count(*) as jlhbrs from ".$dbname.".sdm_pengalamankerja a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.namakaryawan like '%".$notransaksi."%' and b.lokasitugas in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')  order by jlhbrs desc";
    } else {
      $sjKary="select count(*) as jlhbrs from ".$dbname.".sdm_pengalamankerja a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.namakaryawan like '%".$notransaksi."%' and left(a.notransaksi,4)='".$_SESSION['empl']['lokasitugas']."' and b.tipekaryawan in(1,2,3,6) order by jlhbrs desc";
    }

    // ..echo sjKary
      $qjKary = mysql_query($sjKary) or die(mysql_error());
      while ($rjKary = mysql_fetch_object($qjKary)) {
        $jlhbrs = $rjKary->jlhbrs;
      }

        if (isset($_POST['page'])) {
          $page = $_POST['page'];
          if($page<0)
            $page = 0;
        }

        $offset = $page*$limit;

    if(substr($_SESSION['empl']['lokasitugas'],2,2)=='HO') {
      $sjTip="select a.*,b.tipekaryawan from ".$dbname.".sdm_pengalamankerja a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.namakaryawan like '%".$notransaksi."%' limit ".$offset.",20";
    } else if($_SESSION['empl']['tipelokasitugas']=='KANWIL') {
      $sjTip="select a.*,b.tipekaryawan from ".$dbname.".sdm_pengalamankerja a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.namakaryawan like '%".$notransaksi."%' and b.lokasitugas in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')   limit ".$offset.",20";
    } else {
      $sjTip="select a.*,b.tipekaryawan from ".$dbname.".sdm_pengalamankerja a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.namakaryawan like '%".$notransaksi."%' and left(a.notransaksi,4)='".$_SESSION['empl']['lokasitugas']."' and b.tipekaryawan in(1,2,3,6,4)  limit ".$offset.",20";
    } 

    $qjTip = mysql_query($sjTip) or die(mysql_error());
    $no = $page*$limit;
    while ($rjTip = mysql_fetch_object($qjTip)) {
      $no+=1;

      $namakaryawan='';
      // ..get namakaryawan
      $strx = "select namakaryawan from ".$dbname.".datakaryawan where karyawanid=".$rjTip->karyawanid;
      $qtrx = mysql_query($strx) or die(mysql_error());
      while ($rtrx=mysql_fetch_object($qtrx)) {
        $namakaryawan=$rtrx->namakaryawan;
      }

      // ..get penandatangan->namakaryawan
      $penandatangan='';
      $strz = "select namakaryawan from ".$dbname.".datakaryawan where karyawanid=".$rjTip->penandatangan;
      $qtrz = mysql_query($strz) or die(mysql_error());
      while ($rtrz = mysql_fetch_object($qtrz)) {
        $penandatangan = $rtrz->namakaryawan;
      }
      // ..get updateby->namakaryawan
      $namapembuat='';
      $stry = "select namakaryawan from ".$dbname.".datakaryawan where karyawanid=".$rjTip->updateby;
      $qtry = mysql_query($stry) or die(mysql_error());
      while ($rtry = mysql_fetch_object($qtry)) {
        $namapembuat = $rtry->namakaryawan;
      }

      // ..echo data list
        echo "<tr class=rowcontent>
                <td>".$no."</td>
                <td>".$rjTip->notransaksi."</td>
                <td>".$namakaryawan."</td>
                <td>".$penandatangan."</td>
                <td>".$namapembuat."</td>
                <td align=center>";
                  if($_SESSION['empl']['tipelokasitugas']=='KANWIL' && $bar->tipekaryawan=='0'){
                     echo"<img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewSPK('".$rjTip->notransaksi."',event);\">";
                      } else{
                        echo"<img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewSPK('".$rjTip->notransaksi."',event);\"> 
                            &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delSPK('".$rjTip->notransaksi."','".$rjTip->karyawanid."');\">
                            &nbsp <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editSPK('".$rjTip->notransaksi."','".$rjTip->karyawanid."');\">";
                  }
        echo "  </td>
              </tr>
              ";
    }
    echo"<tr>
          <td colspan=11 align=center>
            ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
            <br>
            <button class=mybutton onclick=cariSPK(".($page-1).");>".$_SESSION['lang']['pref']."</button>
            <button class=mybutton onclick=cariSPK(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
          </td>
        </tr>";

?>