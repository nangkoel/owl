<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
	
	
	
$method=$_POST['method'];	



$arrstatus=array(''=>'','1'=>'Disetujui','2'=>'Ditolak');
$arrstatusList=array('1'=>$_SESSION['lang']['disetujui'],'2'=>$_SESSION['lang']['ditolak'],'3'=>$_SESSION['lang']['wait_approval']);

$optstatus="<option value='1'>Disetujui</option>";
$optstatus.="<option value='2'>Ditolak</option>";

$setujuKe=$_POST['setujuKe'];

$noKode=$_POST['noKode'];
$apv=$_POST['apv'];

$tgl=tanggalsystem($_POST['tgl']);
$txt=$_POST['txt'];

$status=$_POST['status']; 

$hrini=date('Ymd');



$kode=$_POST['kode'];


switch($method)
{
	
	case'getApvForm'://ind
	//exit("Error:MASUK");
			$tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable width=100%>";
			$tab.="<tr class=rowcontent><td>".$_SESSION['lang']['project']."</td>";
			$tab.="<td>:</td><td><input type='text' id=noKode class=myinputtext value='".$kode."' style=width:150px; disabled />  </td></tr>";
			$tab.="<tr class=rowcontent><td>".$_SESSION['lang']['status']."</td>";
			$tab.="<td>:</td><td><select id=apv style=width:150px;>".$optstatus."</select></td></tr>";
			$tab.="<tr class=rowcontent><td colspan=3 align=center>";
			$tab.="<button class=mybutton onclick=saveApvForm(".$setujuKe.")>".$_SESSION['lang']['save']."</button></td></tr>";
			$tab.="</table>";
			echo $tab;
	break;
	
	
	case'saveApvForm':
			//$setujuKe=persetujuan ke berapa
			$setujuLanjut=$setujuKe+1;
			
			################# khusus untuk persetujuan terakhir (persetujuan ke 7)
			if($setujuKe=='7')
			{
				if($apv=='1')//setuju
				{
					$str="update ".$dbname.".project set stpersetujuan$setujuKe='".$apv."',tglpersetujuan$setujuKe='".$hrini."' where kode='".$noKode."' ";
					
					if(mysql_query($str))
					{
					}
					else
					{
						echo " Gagal,".addslashes(mysql_error($conn));
					}
				}
				else
				{
					$str="update ".$dbname.".project set stpersetujuan$setujuKe='".$apv."',tglpersetujuan$setujuKe='".$hrini."' where kode='".$noKode."' ";
					if(mysql_query($str))
					{
						$i="select persetujuan$setujuKe,updateby from ".$dbname.".project where kode='".$noKode."'";
						$n=mysql_query($i) or die (mysql_error($conn));
						$d=mysql_fetch_assoc($n);
						$to=getUserEmail($d['updateby']);
						$namakaryawan=getNamaKaryawan($d['persetujuan'.$setujuKe]);
						$nmpnlk=getNamaKaryawan($d['updateby']);
						$subject="[Notifikasi]".$_SESSION['lang']['persetujuan']." ".$_SESSION['lang']['project']." dari ".$namakaryawan;
						$body="<html>
								 <head>
								 <body>
								   <dd>Dengan Hormat, Mr./Mrs. ".$nmpnlk."</dd><br>
								   Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." melakukan <b>Penolakan</b> atas ".$_SESSION['lang']['project']." : ".$kode."
								   Regards,<br>
								   Owl-Plantation System.
								 </body>
								 </head>
							   </html>";//exit("Error:$body");
						$kirim=kirimEmail($to,$subject,$body);
					}
					else
					{
						echo " Gagal,".addslashes(mysql_error($conn));
					}
				}
			}
			################# tutup persetujuan terakhir
			
			
			
			
			if($apv=='1')//setuju
			{
				$str="update ".$dbname.".project set stpersetujuan$setujuKe='".$apv."',tglpersetujuan$setujuKe='".$hrini."',stpersetujuan$setujuLanjut='3' where kode='".$noKode."' ";
				if(mysql_query($str))
				{
					$i="select persetujuan$setujuKe,persetujuan$setujuLanjut,updateby from ".$dbname.".project where kode='".$noKode."'";
					$n=mysql_query($i) or die (mysql_error($conn));
					$d=mysql_fetch_assoc($n);
					if($d['stpersetujuan'.$setujuLanjut]!='0000000000')
					{
						$to=getUserEmail($d['persetujuan'.$setujuLanjut]);
						$namakaryawan=getNamaKaryawan($d['persetujuan'.$setujuKe]);
						$nmpnlk=getNamaKaryawan($d['persetujuan'.$setujuLanjut]);
						$subject="[Notifikasi]".$_SESSION['lang']['persetujuan']." ".$_SESSION['lang']['project']." dari ".$namakaryawan;
						$body="<html>
								 <head>
								 <body>
								   <dd>Dengan Hormat, Mr./Mrs. ".$nmpnlk."</dd><br>
								   Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Persertujuan atas ".$_SESSION['lang']['project']." : ".$kode."
								   kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
								   <br>
								   Regards,<br>
								   Owl-Plantation System.
								 </body>
								 </head>
							   </html>";//exit("Error:$body");
						$kirim=kirimEmail($to,$subject,$body);
					}
				}
				else
				{
					echo " Gagal,".addslashes(mysql_error($conn));
				}
			}
			else
			{
				$str="update ".$dbname.".project set stpersetujuan$setujuKe='".$apv."',tglpersetujuan$setujuKe='".$hrini."',stpersetujuan$setujuLanjut='0' where kode='".$noKode."' ";
				//exit("Error:$str");
				if(mysql_query($str))
				{
					$i="select persetujuan$setujuKe,persetujuan$setujuLanjut,updateby from ".$dbname.".project where kode='".$noKode."'";
					$n=mysql_query($i) or die (mysql_error($conn));
					$d=mysql_fetch_assoc($n);
					$to=getUserEmail($d['updateby']);
					$namakaryawan=getNamaKaryawan($d['persetujuan'.$setujuKe]);
					$nmpnlk=getNamaKaryawan($d['updateby']);
					$subject="[Notifikasi]".$_SESSION['lang']['persetujuan']." ".$_SESSION['lang']['project']." dari ".$namakaryawan;
					$body="<html>
							 <head>
							 <body>
							   <dd>Dengan Hormat, Mr./Mrs. ".$nmpnlk."</dd><br>
							   Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." melakukan <b>Penolakan</b> atas ".$_SESSION['lang']['project']." : ".$kode."
							   Regards,<br>
							   Owl-Plantation System.
							 </body>
							 </head>
						   </html>";
					$kirim=kirimEmail($to,$subject,$body);
				}
				else
				{
					echo " Gagal,".addslashes(mysql_error($conn));
				}
			}
		
			
			/*$str="update ".$dbname.".project set stpersetujuan$setujuKe='".$apv."',tglpersetujuan$setujuKe='".$hrini."',stpersetujuan$setujuLanjut='3' where kode='".$noKode."' ";
			//exit("Error:$str");
			if(mysql_query($str))
			{
				$i="select persetujuan$setujuKe,persetujuan$setujuLanjut,updateby from ".$dbname.".project where kode='".$noKode."'";
				$n=mysql_query($i) or die (mysql_error($conn));
				$d=mysql_fetch_assoc($n);
				
				if($apv=='1')//setuju
				{	
					if($d['stpersetujuan'.$setujuLanjut]!='0000000000')
					{
						$to=getUserEmail($d['persetujuan'.$setujuLanjut]);
						$namakaryawan=getNamaKaryawan($d['persetujuan'.$setujuKe]);
						$nmpnlk=getNamaKaryawan($d['persetujuan'.$setujuLanjut]);
						$subject="[Notifikasi]".$_SESSION['lang']['persetujuan']." ".$_SESSION['lang']['project']." dari ".$namakaryawan;
						$body="<html>
								 <head>
								 <body>
								   <dd>Dengan Hormat, Mr./Mrs. ".$nmpnlk."</dd><br>
								   Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Persertujuan atas ".$_SESSION['lang']['project']." : ".$kode."
								   kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
								   <br>
								   Regards,<br>
								   Owl-Plantation System.
								 </body>
								 </head>
							   </html>";//exit("Error:$body");
						$kirim=kirimEmail($to,$subject,$body);
					}
				}
				else //tolak
				{
					$str="update ".$dbname.".project set stpersetujuan$setujuKe='".$apv."',tglpersetujuan$setujuKe='".$hrini."',stpersetujuan$setujuLanjut='0' where kode='".$noKode."' ";
					exit("Error:$str");
					if(mysql_query($str))
					{
						$to=getUserEmail($d['updateby']);
						$namakaryawan=getNamaKaryawan($d['persetujuan$setujuKe']);
						$nmpnlk=getNamaKaryawan($d['updateby']);
						$subject="[Notifikasi]".$_SESSION['lang']['persetujuan']." ".$_SESSION['lang']['project']." dari ".$namakaryawan;
						$body="<html>
								 <head>
								 <body>
								   <dd>Dengan Hormat, Mr./Mrs. ".$nmpnlk."</dd><br>
								   Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." melakukan <b>Penolakan</b> atas ".$_SESSION['lang']['project']." : ".$kode."
								   Regards,<br>
								   Owl-Plantation System.
								 </body>
								 </head>
							   </html>";
						$kirim=kirimEmail($to,$subject,$body);
					}
					else
					{
						echo " Gagal,".addslashes(mysql_error($conn));
					}
				}
			}
			else
			{
				echo " Gagal,".addslashes(mysql_error($conn));
			}*/
	break;
	
		
	
	
	
	
	

	case'loadData'://<table class=sortable cellspacing=1 border=2px style=\"border-collapse:collapse\" cellpadding=5px>

		echo"
		
		<table cellspacing='1' border='0' class='sortable'>
		
			<thead>
				<tr class=rowheader>
					<td align=center rowspan=3>".$_SESSION['lang']['nourut']."</td>
					<td align=center rowspan=3>".$_SESSION['lang']['project']."</td>
					<td align=center rowspan=3>".$_SESSION['lang']['nama']." ".$_SESSION['lang']['project']."</td>
					<td colspan=14 align=center>".$_SESSION['lang']['persetujuan']."</td>
   					<td rowspan=3 align=center>".$_SESSION['lang']['pdf']."</td>
				</tr>
				<tr>";
					for($i=1;$i<=7;$i++)
					{
						echo"
						<td align=center colspan=2>".$i."</td>";
					}
				echo"
				</tr>
				<tr>";
					for($i=1;$i<=7;$i++)
					{
						echo"
						<td align=center>".$_SESSION['lang']['nama']."</td>
						<td align=center>".$_SESSION['lang']['status']."</td>";
					}
				echo"
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
		
		if($txt!='')
			$txt="where kode like '%".$txt."%'";
		else
			$txt="";
			
		$ql2="select count(*) as jmlhrow from ".$dbname.".project  ".$txt." ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		$ha="SELECT * FROM ".$dbname.".project ".$txt." order by kode desc  limit ".$offset.",".$limit."";
		//echo $ha;
		$hi=mysql_query($ha) or die(mysql_error());
		$no=$maxdisplay;
		while($hu=mysql_fetch_assoc($hi))
		{
			$no+=1;
			echo"
			<tr class=rowcontent id=tr_$no>
				<td>".$no."</td>
				<td>".$hu['kode']."</td>
				<td>".$hu['nama']."</td>";
				for($i=1;$i<=7;$i++)//	$rList['kg'.$b]
				{
					echo"<td>".getNamaKaryawan($hu['persetujuan'.$i])."</td>";
					if($hu['persetujuan'.$i]==$_SESSION['standard']['userid'] && $hu['stpersetujuan'.$i]==3 )
					{
						echo"<td><img src=images/icons/arrow_right.png class=resicon height='30' title='Aprove Project: ".$hu['kode']."' onclick=\"getApvForm('".$hu['kode']."','".$i."');\"></td>";	
					}
					else
					{
						if($hu['tglpersetujuan'.$i]=='0000-00-00')
						{
							$tgl='';
						}
						else
						{
							$tgl=tanggalnormal($hu['tglpersetujuan'.$i]);
						}
						echo"<td><b>".$arrstatusList[$hu['stpersetujuan'.$i]]."</b> ".$tgl."</td>";
					}
				}	
			
			echo"<td align=center><img onclick=\"masterPDF('project','".$hu['kode'].",".$hu['updateby']."','','vhc_slave_project_pdf',event);\" title=\"Print\" class=\"resicon\" src=\"images/pdf.jpg\"></td>";	
				
				
			echo"</tr>
			
			";
		}
		echo"
		<tr class=rowheader><td colspan=18 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		echo"</tbody></table>";
    break;
	

	
	
	

	
	default;	
}

?>