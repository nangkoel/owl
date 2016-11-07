<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		



$kodehead=$_POST['kodehead'];
	
$kodeheadedit=$_POST['kodeheadedit'];	
$matauangheadedit=$_POST['matauangheadedit'];	
$simbolheadedit=$_POST['simbolheadedit'];	
$kodeisoheadedit=$_POST['kodeisoheadedit'];	

$kode=$_POST['kode'];
$kodedetail=$_POST['kodedetail'];
$matauang=$_POST['matauang'];
$simbol=$_POST['simbol'];
$kodeiso=$_POST['kodeiso'];


$kodedetail=$_POST['kodedetail'];
$kodedet=$_POST['kodedet'];


$jm=$_POST['jm'];
$mn=$_POST['mn'];
$jmsavedet=$jm.':'.$mn;
$tgl=tanggalsystem($_POST['tgl']);
$kursdet=$_POST['kursdet'];


$jam=$_POST['jam'];
$daritanggal=tanggalsystem($_POST['daritanggal']);

$kodetambah=$_POST['kodetambah'];
$matauangtambah=$_POST['matauangtambah'];
$simboltambah=$_POST['simboltambah'];
$kodeisotambah=$_POST['kodeisotambah'];
$method=$_POST['method'];

##untuk jam dan menit option			
for($t=0;$t<24;)
{
	if(strlen($t)<2)
	{
		$t="0".$t;
	}
	$jm.="<option value=".$t." ".($t==00?'selected':'').">".$t."</option>";
	$t++;
}
for($y=0;$y<60;)
{
	if(strlen($y)<2)
	{
		$y="0".$y;
	}
	$mnt.="<option value=".$y." ".($y==00?'selected':'').">".$y."</option>";
	$y++;
}	
?>

<?php
switch($method)
{
	case 'insert':
		$str="insert into ".$dbname.".setup_matauang (`kode`,`matauang`,`simbol`,`kodeiso`)
		values ('".$kodetambah."','".$matauangtambah."','".$simboltambah."','".$kodeisotambah."')";
		//exit("Error.$str");
		if(mysql_query($str))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	//daritanggal	jam	kurs
	case 'simpandetail':
		$str="insert into ".$dbname.".setup_matauangrate (`kode`,`daritanggal`,`jam`,`kurs`)
		values ('".$kodedet."','".$tgl."','".$jmsavedet."','".$kursdet."')";
		//exit("Error.$str");
		if(mysql_query($str))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

	case 'edithead':
		$str="update ".$dbname.".setup_matauang set kode='".$kodeheadedit."',matauang='".$matauangheadedit."',simbol='".$simbolheadedit."',kodeiso='".$kodeisoheadedit."'
				where kode='".$kodehead."' ";
		//exit("Error.$str");
		if(mysql_query($str))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	
	
	
		
case'loadData':
		
		//echo"Kode Jurnal : <input type=text maxlength=3 id=kodedet value=".$kode." disabled onkeypress=\"return_tanpa_kutip(event);\" class=myinputtext style=\"width:50px;\">";
		
		echo"
		<table class=sortable cellspacing=1 border=0>
			<thead>
				<tr class=rowheader>
					<td align=center>No.</td>
					<td align=center>Kode</td>
					<td align=center>Tanggal</td>
					<td align=center>Jam</td>
					<td align=center>Kurs</td>
					<td align=center>*</td>
				</tr>
			</thead>
		<tbody>";
		
		$limit=30;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		
		
		$tmbh1='';
                if($thnsort!='')
                {
                    $tmbh1=" and tanggal like '%".$thnsort."%' ";
					//echo $tmbh2;
                }

		
		if ($kode=='')
		{
			$kode=$kodedetail;
		}
		
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".setup_matauangrate where kode='".$kode."' ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$ha="select * from ".$dbname.". setup_matauangrate where kode='".$kode."' order by daritanggal desc limit ".$offset.",".$limit."";
		$hi=mysql_query($ha) or die(mysql_error());
		$no=$maxdisplay;
		while($hu=mysql_fetch_assoc($hi))
		{
				
		$no+=1;
		echo"
		<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$hu['kode']."</td>
			<td>".tanggalnormal($hu['daritanggal'])."</td>
			<td>".$hu['jam']."</td>
			<td>".$hu['kurs']."</td>
			<td>
				<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"deldetail('".$hu['kode']."','".tanggalnormal($hu['daritanggal'])."','".$hu['jam']."');\" >
			</td>
		</tr>
		
		";//<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".tanggalnormal($hu['tanggal'])."','".$hu['kebun']."','".$hu['blok']."','".$hu['kodediagnosa']."','".$hu['saran']."','".$hu['tindakan']."','".$hu['petugas']."','".$hu['updateby']."');\">
		}
		echo"<tr class=rowcontent><td></td>
			<td><input type=text maxlength=3 id=kodedet value=".$kode." disabled onkeypress=\"return_tanpa_kutip(event);\" class=myinputtext style=\"width:50px;\"></td>
			<td><input type='text' class='myinputtext' id='tgl' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='10' maxlength='10' style=width:75px; /></td>
			<td><select id=jm>".$jm."</select>:<select id=mn>".$mnt."</select></td>
			<td><input type=text  id=kursdet onkeypress=\"return_angka_doang(event);\" class=myinputtext style=\"width:50px;\"></td>
			<td><img src=images/application/application_add.png class=resicon  title='Save'  onclick=simpandetail()></td>
		</tr>";	
		echo"
		<tr class=rowheader><td colspan=6 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		echo"</tbody></table>";
    break;

	case 'delhead':
	//exit("Error:hahaha");delhead(kode,matauang,simbol,kodeiso)
		$str="delete from ".$dbname.".setup_matauang where kode='".$kode."' and matauang='".$matauang."' and simbol='".$simbol."' and kodeiso='".$kodeiso."'";
		//exit("Error.$str");
		if(mysql_query($str))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	case 'deldetail':
	//exit("Error:hahaha");delhead(kode,matauang,simbol,kodeiso)
		$str="delete from ".$dbname.".setup_matauangrate where kode='".$kode."' and daritanggal='".$daritanggal."' and jam='".$jam."'";
		//exit("Error.$str");
		if(mysql_query($str))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

default:
}
?>
