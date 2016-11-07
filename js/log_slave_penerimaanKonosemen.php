<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

	
$method=$_POST['method'];	
$notran=$_POST['notran'];
$txt=$_POST['txt'];
$hrini=date('Ymd');
$jumlahditerima=$_POST['jumlahditerima'];
$kodebarang=$_POST['kodebarang'];

$nmFranco=makeOption($dbname,'setup_franco','id_franco,franco_name');
$nmBarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');

switch($method)
{
	case'posting':
		$sekarang=date('Y-m-d');
		$i="update  ".$dbname.".log_konosemenht set posting=1,postingby='".$_SESSION['standard']['userid']."',tanggaltiba='".$sekarang."' where nokonosemen='".$notran."'";
		//exit("Error:$i");
		if(mysql_query($i))
		{
		}
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
		
		
	case'savePenerimaan':
	//exit("Error:MASUK");
		$i="update ".$dbname.".`log_konosemendt`  set jumlahditerima='".$jumlahditerima."' where nokonosemen='".$notran."' and kodebarang='".$kodebarang."'";
	//exit("Error:$i");	
		if(mysql_query($i))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}
		echo $notran;
		//echo $notran.'##'.$_SESSION['lang']['find'].'##event';
		
		
		
	break;
	
	case'getIsi':
		echo"
			
			<fieldset style=float:left><legend>Posting</legend>
			<button class=mybutton onclick=posting('".$notran."')>".$_SESSION['lang']['posting']."</button>
			</fieldset>
			<br />
			<fieldset style=float:left><legend>".$_SESSION['lang']['list']."</legend>
			<table cellspacing=1 border=0 class='sortable'>
			<thead>
				<tr class=rowheader>
					<td align=center>".$_SESSION['lang']['nourut']."</td>
					<td align=center>".$_SESSION['lang']['nokonosemen']."</td>
					<td align=center>".$_SESSION['lang']['kodept']."</td>
					<td align=center>".$_SESSION['lang']['kodebarang']."</td>
					<td align=center>".$_SESSION['lang']['namabarang']."</td>
					<td align=center>".$_SESSION['lang']['jenis']."</td>
					<td align=center>".$_SESSION['lang']['jumlah']." PO</td>
					<td align=center>".$_SESSION['lang']['diterima']."</td>
					<td align=center>".$_SESSION['lang']['satuan']."</td>
					<td align=center>".$_SESSION['lang']['action']."</td>
				</tr>
		</thead>
		</tbody>";
	
		$i="select * from ".$dbname.".log_konosemendt where nokonosemen='".$notran."' and postingkirim=1";	
		$n=mysql_query($i) or die (mysql_error($conn));
		while ($d=mysql_fetch_assoc($n))
		{
			$no+=1;
			echo"<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$d['nokonosemen']."</td>
				<td>".$d['kodept']."</td>
				<td align=right>".$d['kodebarang']."</td>
				<td>".$nmBarang[$d['kodebarang']]."</td>
				<td>".$d['jenis']."</td>
				<td id=jumlah".$no.">".$d['jumlah']."</td>
				<td><input type=text id=jumlahditerima".$no." value=".$d['jumlahditerima']." onkeypress=\"return angka_doang(event);\" class=myinputtextnumber style=\"width:50px;\"></td>
				<td>".$d['satuanpo']."</td>
				<td><img src=images/icons/Grey/PNG/save.png class=resicon  title='update' onclick=\"savePenerimaan('".$d['nokonosemen']."','".$d['kodebarang']."',".$no.");\" ></td>

			</tr>
		";
		}
		echo"</fieldset>";		
	break;
	
	
	
	
	
	case'loadData'://<table class=sortable cellspacing=1 border=2px style=\"border-collapse:collapse\" cellpadding=5px>

		echo"
		
		<table cellspacing='1' border='0' class='sortable'>
		
			<thead>
				<tr class=rowheader>
					<td align=center>".$_SESSION['lang']['nourut']."</td>
					<td align=center>".$_SESSION['lang']['nokonosemen']."</td>
					<td align=center>".$_SESSION['lang']['nokonosemen']." EXP</td>
					<td align=center>".$_SESSION['lang']['kodept']."</td>
					<td align=center>".$_SESSION['lang']['tanggal']."</td>
					<td align=center>".$_SESSION['lang']['tanggalberangkat']."</td>
					<td align=center>".$_SESSION['lang']['shipper']."</td>
					<td align=center>".$_SESSION['lang']['vessel']."</td>
					<td align=center>".$_SESSION['lang']['franco']."</td>
					<td align=center>".$_SESSION['lang']['asalbarang']."</td>
					<td align=center>".$_SESSION['lang']['daftar']."</td>

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
			$txt="where nokonosemen like '%".$txt."%'";
		else
			$txt="";
			
		$ql2="select count(*) as jmlhrow from ".$dbname.".log_konosemenht  ".$txt."  order by tanggal desc";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		$ha="SELECT * FROM ".$dbname.".log_konosemenht ".$txt." order by tanggal desc  limit ".$offset.",".$limit."";
		//echo $ha;
		$hi=mysql_query($ha) or die(mysql_error());
		$no=$maxdisplay;
		while($hu=mysql_fetch_assoc($hi))
		{
			$no+=1;
			echo"
			<tr class=rowcontent>
				<td>".$no."</td>
				<td>".$hu['nokonosemen']."</td>
				<td>".$hu['nokonosemenexp']."</td>
				<td>".$hu['kodept']."</td>
				<td>".$hu['tanggal']."</td>
				<td>".tanggalnormal($hu['tanggalberangkat'])."</td>
				<td>".$hu['shipper']."</td>
				<td>".$hu['vessel']."</td>
				<td>".$nmFranco[$hu['franco']]."</td>
				<td>".$hu['asalbarang']."</td>";
				if($hu['posting']=='0')
				{
						$post="<td align=center><img src=images/zoom.png title='".$_SESSION['lang']['find']."' id=a class=resicon onclick=listBarang('".$hu['nokonosemen']."','".$_SESSION['lang']['find']."',event)></td>";
								
						
				}
				else
				{
						$post="<td align=center>".$_SESSION['lang']['posting']."</td>";
				}
					
				echo $post;	
			echo"</tr>";
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