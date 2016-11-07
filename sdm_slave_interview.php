<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include('lib/zFunction.php');
require_once('lib/fpdf.php');

$param=$_POST;
$optPend=makeOption($dbname,'sdm_5pendidikan','levelpendidikan,pendidikan');
$optNmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
$optJbtn=makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optNmlowongan=makeOption($dbname, 'sdm_permintaansdm', 'notransaksi,namalowongan');

switch($param['proses'])
{
    case'getData':        
	$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sprd="select distinct idpermintaan,namalowongan from ".$dbname.".`sdm_testcalon` 
               where periodetest='".$_POST['periodeTest']."' order by periodetest desc";
	$qprd=mysql_query($sprd) or die(mysql_error($conn));
	while($rprd=mysql_fetch_assoc($qprd)){
            $optPeriode.="<option value='".$rprd['idpermintaan']."'>".$rprd['namalowongan']."</option>";
	}
        echo $optPeriode;
    break;
    case'loadData':
        $saks="select distinct * from ".$dbname.".setup_remotetimbangan 
               where lokasi='HRDJKRT'";			
        $qaks=mysql_query($saks) or die(mysql_error($conn));
        $jaks=mysql_fetch_assoc($qaks);
        $uname2=$jaks['username'];
        $passwd2=$jaks['password'];
        $dbserver2=$jaks['ip'];
        $dbport2=$jaks['port'];
        $dbdt=$jaks['dbname'];
      
        //$conn2=mysql_connect($dbserver2.":".$dbport2,$uname2,$passwd2) or die("Error/Gagal :Unable to Connect to database ".$dbserver2);
        //$conn2=mysql_connect('192.168.1.204','root','dbdev');
        $conn2=mysql_connect($dbserver2,$uname2,$passwd2);
        if (!$conn2)
          {
          die('Could not connect: ' . mysql_error());
          }

	if(($param['periodeTest']=='')||($param['nmLowongan']=='')){
            exit("error: Semua Field Tidak Boleh Kosong");
	}
	echo"<input type=hidden id=nopermintaan value='".$param['nmLowongan']."' />";
	echo"
        <table border=0><tr><td valign=top>    
        <table cellpadding=2 cellspacing=1 border=0 class=sortable>
               <thead>
	       <tr class=rowheader>";
	echo"<td>No</td>";
	echo"<td>".$_SESSION['lang']['email']."</td>";
        echo"<td>".$_SESSION['lang']['nama']."</td>";
	echo"<td>".$_SESSION['lang']['pendidikan']."</td>";
	echo"<td>".$_SESSION['lang']['action']."</td>";
	echo"</tr></thead><tbody id=listData>"; 
        $sdt="select distinct * from ".$dbname.".sdm_testcalon where 
              hasilakhir is null and hasiliview is null and idpermintaan='".$param['nmLowongan']."' order by email asc";
        $qdt=mysql_query($sdt,$conn) or die(mysql_error($conn));
        while($rdt=  mysql_fetch_assoc($qdt)){
            $nor+=1;
            $sdt2="select distinct namacalon from ".$dbdt.".datacalon where email='".$rdt['email']."'";
            $qdt2=mysql_query($sdt2,$conn2) or die(mysql_error($conn2));
            $rdt2=mysql_fetch_assoc($qdt2);
            
            $sdt3="select distinct levelpendidikan from ".$dbdt.".pendidikan where email='".$rdt['email']."'  order by levelpendidikan desc ";
            $qdt3=mysql_query($sdt3,$conn2) or die(mysql_error($conn2));
            $rdt3=mysql_fetch_assoc($qdt3);
             $adert="##nmLowongan##emailDt_".$nor."";
            echo"<tr class=rowcontent>";
            echo"<td>".$nor."</td>";
            echo"<td id=emailDt_".$nor."  value='".$rdt['email']."'>".$rdt['email']."</td>";
            echo"<td id=namaDt_".$nor.">".$rdt2['namacalon']."</td>";
            echo"<td>".$optPend[$rdt3['levelpendidikan']]."</td>";
            echo"<td>
            <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"zPdf('sdm_slave_finalDecison','".$adert."','".$nor."','contentData')\">
            <button class='mybutton' onclick='getFormPenilaian(".$nor.")' >".$_SESSION['lang']['interviewer']."</button>
            </td>";
            echo"</tr>";
          }
	
	echo"</tbody></table></td><td valign=top><div id=dtForm></div><td></tr></table>";
	 
	break;
        case'getForm':
            $sdt="select distinct tglinterview from ".$dbname.".sdm_interview where email='".$param['emailDt']."'";
            //echo $sdt;
            $qrdt=mysql_query($sdt) or die(mysql_error($conn));
            $rdt=mysql_fetch_assoc($qrdt);
        $dFrom="<div style=\"background-color:#CCCCCC\">
                <fieldset><legend>".$_SESSION['lang']['form']." ".$_SESSION['lang']['pilih']." ".$_SESSION['lang']['interviewer']."</legend>
                ".$_SESSION['lang']['tanggal']." Interview : <input type=text class=myinputtext id=tglInter onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 value='".tanggalnormal($rdt['tglinterview'])."' />
                <table cellpadding=1 cellspacing=1 border=0>";
        $sdtkar="select distinct karyawanid from ".$dbname.".datakaryawan 
                 where tipekaryawan='0' and tanggalkeluar>".$_SESSION['org']['period']['start']."
                 and kodejabatan in ('2','3','4','5','7','8','11','97','124','142','179','186','187','195','197','198','199','200','217')
                 order by namakaryawan asc";
        $qdatkar=mysql_query($sdtkar) or die(mysql_error());
        
        while($rdatkary= mysql_fetch_assoc($qdatkar)){
           $dertkary[]=$rdatkary['karyawanid'];
        }
        $der=0;
        $jmrow=count($dertkary);
 
        for($der=0;$der<$jmrow;){
            //exit("error:".$idKar);
            if($dertkary[$der]!='')
            {
                $erct="";
                $sdt="select distinct email from ".$dbname.".sdm_interview where interviewer='".$dertkary[$der]."' and email='".$param['emailDt']."'";
                $qrdt=mysql_query($sdt) or die(mysql_error($conn));
                $rdt=mysql_num_rows($qrdt);
                if($rdt==1){
                    $erct="checked";
                }
                $dFrom.="<tr>";
                //$optKar.="<option value='".$rdatkary['karyawanid']."'>".$rdatkary['namakaryawan']." - ".$optJbtn[$rdatkary['kodejabatan']]."</option>";
                $dFrom.="<td><input type=checkbox id='interview_".$der."' onclick='saveView(".$der.")' ".$erct." >".$optNmKar[$dertkary[$der]]." <input type=hidden id=karyId_".$der." value='".$dertkary[$der]."' />";
                $dFrom.="</td>";
                $der+=1;
                $erct="";
                $sdt="select distinct email from ".$dbname.".sdm_interview where interviewer='".$dertkary[$der]."' and email='".$param['emailDt']."'";
                $qrdt=mysql_query($sdt) or die(mysql_error($conn));
                $rdt=mysql_num_rows($qrdt);
                if($rdt==1){
                    $erct="checked";
                }
                $dFrom.="<td><input type=checkbox id='interview_".$der."' onclick='saveView(".$der.")' ".$erct." >".$optNmKar[$dertkary[$der]]." <input type=hidden id=karyId_".$der." value='".$dertkary[$der]."' />";
                $dFrom.="</td>";
                $der+=1;
                $erct="";
                $sdt="select distinct email from ".$dbname.".sdm_interview where interviewer='".$dertkary[$der]."' and email='".$param['emailDt']."'";
                $qrdt=mysql_query($sdt) or die(mysql_error($conn));
                $rdt=mysql_num_rows($qrdt);
                if($rdt==1){
                    $erct="checked";
                }
                $dFrom.="<td><input type=checkbox id='interview_".$der."' onclick='saveView(".$der.")' ".$erct." >".$optNmKar[$dertkary[$der]]."  <input type=hidden id=karyId_".$der." value='".$dertkary[$der]."' />";
                $dFrom.="</td>";
                $dFrom.="</tr>";
                $der+=1;
                $erct="";
                $sdt="select distinct email from ".$dbname.".sdm_interview where interviewer='".$dertkary[$der]."' and email='".$param['emailDt']."'";
                $qrdt=mysql_query($sdt) or die(mysql_error($conn));
                $rdt=mysql_num_rows($qrdt);
                if($rdt==1){
                    $erct="checked";
                }
                
            }
        }
        $dFrom.="<tr><td colspan=3 align=right><button class=mybutton onclick='closeForm()'>".$_SESSION['lang']['tutup']."</button></td></tr>";
        $dFrom.="</table></fieldset></div>";
//        $arrenum=getEnum($dbname,'sdm_interview','hasil');
//        foreach($arrenum as $key=>$val)
//        {
//                $optGoldar.="<option value='".$key."'>".$val."</option>";
//        }
//        $dFrom="<div style=\"background-color:#CCCCCC\"><table cellpadding=1 cellspacing=1 border=0>";
//        $dFrom.="<tr><td>".$_SESSION['lang']['nama']."</td><td><input type=text class=myinputtext value='".$param['nmcalon']."' style='width:150px' disabled />";
//        $dFrom.="</td></tr>";
//        $dFrom.="<tr><td>".$_SESSION['lang']['interviewer']."</td>";
//        $dFrom.="<td><select id=interview style=width:150px;>".$optKar."</select></td</tr>";
//        $dFrom.="<tr><td>".$_SESSION['lang']['hasil']."</td>";
//        $dFrom.="<td><select id=hasilIntview style=width:150px;>".$optGoldar."</select></td</tr>";
//        $dFrom.="<tr><td>".$_SESSION['lang']['catatan'].'</td>';
//          $dFrom.="<td><textarea id=catatan></textarea></td></tr></table><input type=hidden id=emailDt value='".$param['emailDt']."' />";
          $dFrom.="<input type=hidden id=emailDt value='".$param['emailDt']."' />";
//        $dFrom.="<button class=mybutton onclick=saveView()>".$_SESSION['lang']['save']."</button></div>";
        echo $dFrom;
            
        break;
        case'insrData':
            if($param['tglInterv']==''){
                exit("Error:Tanggal Interview Kosong!!");
            }
            $dtgl=explode("-",$param['tglInterv']);
            $prdtest=$dtgl['2']."-".$dtgl['1'];
            if($param['periode']!=$prdtest){
                exit("error: Tanggal interview di luar periode");
            }
            
            $sdel="delete from ".$dbname.".sdm_interview where email='".$param['emailDt']."' and interviewer='".$param['karyId']."'";
            if(mysql_query($sdel)){
                $sinsrt="insert into ".$dbname.".sdm_interview (`email`,`interviewer`,`tglinterview`) values
                         ('".$param['emailDt']."','".$param['karyId']."','".tanggalsystem($param['tglInterv'])."')";
                //exit("error:".$sinsrt);
                 if(!mysql_query($sinsrt)){
                     die(mysql_error($conn))."___".$sinsrt;
                 }else{
                     #email ke user
                     #send an email to incharge person
                        $to=getUserEmail($param['karyId']);
                        $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
                        if($_SESSION['language']=='EN'){    
                        $subject="[Notifikasi] PR Submission for approval, submitted by: ".$namakaryawan;
                        $body="<html>
                                 <head>
                                 <body>
                                   <dd>Dear Sir/Madam,</dd><br>
                                   <br>
                                   Today,  ".date('d-m-Y').",  on behalf of ".$namakaryawan." submit a PR, requesting for your approval. To follow up, please follow the link below.
                                   <br>
                                   <br>
                                   <br>
                                   Regards,<br>
                                   Owl-Plantation System.
                                 </body>
                                 </head>
                               </html>
                               ";
                        }else{
                        $subject="[Notifikasi]Undangan Hadir Wawancara PP a/n ".$namakaryawan;
                        $body="<html>
                                 <head>
                                 <body>
                                   <dd>Dengan Hormat,</dd><br>
                                   <br>
                                   Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengundang
                                   kepada bapak/ibu. Untuk menghadiri wawancara karyawan baru pada tanggal ".$param['tglInterv']."
                                   <br>
                                   <br>
                                   <br>
                                   Regards,<br>
                                   Owl-Plantation System.
                                 </body>
                                 </head>
                               </html>
                               ";                                            
                        }
                        $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;
                 }
            }else{
               die(mysql_error($conn))."___".$sdel;
            }
        break;
        case'delData':
            $sdel="delete from ".$dbname.".sdm_interview where email='".$param['emailDt']."' and interviewer='".$param['karyId']."'";
            if(!mysql_query($sdel)){
               die(mysql_error($conn))."___".$sdel;
            }
        break;
}
?>