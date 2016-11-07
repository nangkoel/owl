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
$optJbtn=makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optNmlowongan=makeOption($dbname, 'sdm_permintaansdm', 'notransaksi,namalowongan');
$optNmKar=makeOption($dbname,'datakaryawan','karyawanid,namakaryawan');
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
        echo"<td>".$_SESSION['lang']['status']."</td>";
	echo"<td>".$_SESSION['lang']['action']."</td>";
	echo"</tr></thead><tbody id=listData>"; 
        $sdt="select distinct * from ".$dbname.".sdm_testcalon where 
              (hasilakhir is null or hasiliview is null) and idpermintaan='".$param['nmLowongan']."' order by email asc";
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
            echo"<td id=emailDt_".$nor." value='".$rdt['email']."'>".$rdt['email']."</td>";
            echo"<td id=namaDt_".$nor.">".$rdt2['namacalon']."</td>";
            echo"<td>".$optPend[$rdt3['levelpendidikan']]."</td>";
            echo"<td>".$rdt['hasiliview']."</td>";
            echo"<td>
            <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"zPdf('sdm_slave_finalDecison','".$adert."','".$nor."','contentData')\">
            <button class='mybutton' onclick='getFormPenilaian(".$nor.")' >Penilaian</button>
            </td>";
            echo"</tr>";
          }
	
	echo"</tbody></table></td><td valign=top><div id=dtForm></div><div id=formPen style='display:none'></div><td></tr></table>";
	 
	break;
        case'getForm':
        $dFrom="<div style=\"background-color:#CCCCCC\">
            <fieldset><legend>".$_SESSION['lang']['form']." ".$_SESSION['lang']['nilai']." </legend>
            <table cellpadding=1 cellspacing=1 border=0>";
            $sdt="select distinct interviewer from ".$dbname.".sdm_interview where email='".$param['emailDt']."'";
            $qDt=mysql_query($sdt) or die(mysql_error($conn));
            while($rdt=  mysql_fetch_assoc($qDt)){
                 $dertkary[]=$rdt['interviewer'];
            }
            
            $jmrow=count($dertkary);
            for($der=0;$der<$jmrow;){
                
            //exit("error:".$idKar);
                $dert="";
            $whered="email='".$param['emailDt']."' and interviewer='".$dertkary[$der]."'";
            $optstat=makeOption($dbname, 'sdm_interview', 'interviewer,stat',$whered); 
             if($optstat[$dertkary[$der]]==1){
                 $dert="checked=checked ";
             }  
                $dFrom.="<tr>";
                if($dertkary[$der]!=''){
                    $dFrom.="<td><input type=radio id='interviewFinal_".$der."' ".$dert." onclick=\"svPenilaian('".$dertkary[$der]."','".$param['idKe']."','".$der."','".$jmrow."')\" title='Keputusan Final Interview' /><a href=# onclick=getFormPen('".$dertkary[$der]."','".$param['idKe']."')>".$optNmKar[$dertkary[$der]]."</a></td>";
                $der+=1;
                }else{
                    $dFrom.="<td>&nbsp;</td>";
                    $der+=1;
                }
                $dert="";
                $whered="email='".$param['emailDt']."' and interviewer='".$dertkary[$der]."'";
                $optstat=makeOption($dbname, 'sdm_interview', 'interviewer,stat',$whered); 
                 if($optstat[$dertkary[$der]]==1){
                     $dert="checked=checked ";
                 }  
                if($dertkary[$der]!=''){
                   $dFrom.="<td><input type=radio id='interviewFinal_".$der."' ".$dert." onclick=\"svPenilaian('".$dertkary[$der]."','".$param['idKe']."','".$der."','".$jmrow."')\" title='Keputusan Final Interview' /><a href=# onclick=getFormPen('".$dertkary[$der]."','".$param['idKe']."')>".$optNmKar[$dertkary[$der]]."</a></td>";
                    $der+=1;
                }else{
                    $dFrom.="<td>&nbsp;|</td>";
                     $der+=1;
                }
                $dert="";
                $whered="email='".$param['emailDt']."' and interviewer='".$dertkary[$der]."'";
                $optstat=makeOption($dbname, 'sdm_interview', 'interviewer,stat',$whered); 
                 if($optstat[$dertkary[$der]]==1){
                     $dert="checked=checked ";
                 }  
                if($dertkary[$der]!=''){
                    $dFrom.="<td><input type=radio id='interviewFinal_".$der."' ".$dert." onclick=\"svPenilaian('".$dertkary[$der]."','".$param['idKe']."','".$der."','".$jmrow."')\" title='Keputusan Final Interview' /><a href=# onclick=getFormPen('".$dertkary[$der]."','".$param['idKe']."')>".$optNmKar[$dertkary[$der]]."</a></td>";
                    $dFrom.="</tr>";
                    $der+=1;
                }else{
                    $dFrom.="<td>&nbsp;</td>";
                    $dFrom.="</tr>";
                    $der+=1;
                }
                $dert="";
                $whered="email='".$param['emailDt']."' and interviewer='".$dertkary[$der]."'";
                $optstat=makeOption($dbname, 'sdm_interview', 'interviewer,stat',$whered); 
                 if($optstat[$dertkary[$der]]==1){
                     $dert="checked=checked ";
                 } 
            
        }
        $dFrom.="<tr><td colspan=3 align=right><button class=mybutton onclick='closeForm()'>".$_SESSION['lang']['tutup']."</button></td></tr>";
        $dFrom.="</table></fieldset></div>";
        $dFrom.="<input type=hidden id=emailDt value='".$param['emailDt']."' />";
        echo $dFrom;
        break;
        case'getForm2':
        $sdtkar="select distinct karyawanid,namakaryawan,kodejabatan from ".$dbname.".datakaryawan 
                 where karyawanid='".$param['karyId']."'
                 order by namakaryawan asc";
        $qdatkar=mysql_query($sdtkar,$conn) or die(mysql_error());
        while($rdatkary=mysql_fetch_assoc($qdatkar)){
            $optKar.="<option value='".$rdatkary['karyawanid']."'>".$rdatkary['namakaryawan']." - ".$optJbtn[$rdatkary['kodejabatan']]."</option>";
        }
        $dert="select distinct * from ".$dbname.".sdm_interview where 
               email='".$param['emailDt']."' and  interviewer='".$param['karyId']."'";
        $qdert=mysql_query($dert) or die(mysql_error($conn));
        $rdert=mysql_fetch_assoc($qdert);
            
        $arrenum=getEnum($dbname,'sdm_interview','hasil');
        foreach($arrenum as $key=>$val)
        {
                $optGoldar.="<option value='".$key."' ".($rdert['hasil']==$key?"selected":"").">".$val."</option>";
        }
        
        $dFrom="<div style=\"background-color:#CCCCCC\">
                <fieldset><legend>".$_SESSION['lang']['form']." ".$_SESSION['lang']['nilai']." </legend>
                <table cellpadding=1 cellspacing=1 border=0>";
        $dFrom.="<tr><td>".$_SESSION['lang']['nama']."</td><td><input type=text class=myinputtext value='".$param['namacalon']."' style='width:150px' disabled />";
        $dFrom.="</td></tr>";
        $dFrom.="<tr><td>".$_SESSION['lang']['interviewer']."</td>";
        $dFrom.="<td><select id=interview style=width:150px;>".$optKar."</select></td</tr>";
        $dFrom.="<tr><td>".$_SESSION['lang']['hasil']."</td>";
        $dFrom.="<td><select id=hasilIntview style=width:150px;>".$optGoldar."</select></td</tr>";
        $dFrom.="<tr><td>".$_SESSION['lang']['catatan'].'</td>';
        $dFrom.="<td><textarea id=catatan>".$rdert['catatan']."</textarea></td></tr>";
        $dFrom.="<tr><td>".$_SESSION['lang']['tanggal'].'</td>';
        $dFrom.="<td><input type=text class=myinputtext id=tglinterview 
                    onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 value='".tanggalnormal($rdert['tanggal'])."' /></td></tr>";
            $dFrom.="</table><button class=mybutton onclick=saveView()>".$_SESSION['lang']['save']."</button></fieldset>
                 <input type=hidden id=emailDt value='".$param['emailDt']."' />";
        $dFrom.="</div>";
        echo $dFrom;
            
        break;
        case'insrData':
            $sdel="delete from ".$dbname.".sdm_interview where email='".$param['emailDt']."' and interviewer='".$param['interviewer']."'";
            if(mysql_query($sdel)){
                $sinsrt="insert into ".$dbname.".sdm_interview (`email`,`interviewer`,`tanggal`,`hasil`,`catatan`) values
                         ('".$param['emailDt']."','".$param['interviewer']."','".tanggalsystem($param['tglInterview'])."','".$param['hasilIntview']."','".$param['cttn']."')";
                 if(!mysql_query($sinsrt)){
                     die(mysql_error($conn))."___".$sinsrt;
                 } 
                     
            }else{
               die(mysql_error($conn))."___".$sdel;
            }
        break;
        case'updateSdmTest':
         $sdt="select distinct * from ".$dbname.".sdm_interview where 
               email='".$param['emailDt']."' and  interviewer='".$_POST['karyId']."'";
         $qdt=mysql_query($sdt) or die(mysql_error($conn));
         $rdt=mysql_fetch_assoc($qdt);
         $sinsrt="update ".$dbname.".sdm_testcalon set tglivew='".$rdt['tanggal']."',hasiliview='".$rdt['hasil']."',
                             keteranganiview='".$rdt['catatan']."' where email='".$param['emailDt']."'
                             and idpermintaan='".$param['idPermintaan']."'";
                    //exit("error:".$sinsrt);
        if(mysql_query($sinsrt)){
            $supdate="update ".$dbname.".sdm_interview set stat=1 where email='".$param['emailDt']."' and  interviewer='".$_POST['karyId']."'";
            //exit("error:".$supdate);
                if(!mysql_query($supdate)){
                die(mysql_error($conn))."___".$supdate;
            }else{
             $supdate="update ".$dbname.".sdm_interview set stat=0 where email='".$param['emailDt']."' and  interviewer<>'".$_POST['karyId']."'";
                if(!mysql_query($supdate)){
                die(mysql_error($conn))."___".$supdate;
            }   
            }
        }else{
            die(mysql_error($conn))."___".$sinsrt;
        }
        break;
         
}
?>