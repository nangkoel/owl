<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
	
	
	
$method=$_POST['method'];	
$txt=$_POST['txt'];
$hrini=date('Ymd');

$nmFranco=makeOption($dbname,'setup_franco','id_franco,franco_name');


switch($method)
{
        case 'excel':
            
            break;
	case'loadData'://<table class=sortable cellspacing=1 border=2px style=\"border-collapse:collapse\" cellpadding=5px>

		echo"
		<img onclick=toexcel(event,'log_slave_asuransi.php') src=images/excel.jpg class=resicon title='Export to Excel'>
		<table cellspacing='1' border='0' class='sortable'>
		
			<thead>
				<tr class=rowheader>
					<td align=center>".$_SESSION['lang']['nourut']."</td>
					<td align=center>".$_SESSION['lang']['nokonosemen']."</td>
					<td align=center>".$_SESSION['lang']['nokonosemen']."</td>
					<td align=center>".$_SESSION['lang']['kodept']."</td>
					<td align=center>".$_SESSION['lang']['tanggal']."</td>
					<td align=center>".$_SESSION['lang']['tanggalberangkat']."</td>
					<td align=center>".$_SESSION['lang']['shipper']."</td>
					<td align=center>".$_SESSION['lang']['vessel']."</td>
					<td align=center>".$_SESSION['lang']['franco']."</td>
					<td align=center>".$_SESSION['lang']['asalbarang']."</td>
					<td align=center>".$_SESSION['lang']['pdf']."</td>

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
			<tr class=rowcontent id=tr_$no>
				<td>".$no."</td>
				<td>".$hu['nokonosemen']."</td>
				<td>".$hu['nokonosemenexp']."</td>
				<td>".$hu['kodept']."</td>
				<td>".$hu['tanggal']."</td>
				<td>".tanggalnormal($hu['tanggalberangkat'])."</td>
				<td>".$hu['shipper']."</td>
				<td>".$hu['vessel']."</td>
				<td>".$nmFranco[$hu['franco']]."</td>
				<td>".$hu['asalbarang']."</td>
				

				<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_konosemenht','".$hu['nokonosemen']."','','log_slave_asuransi_pdf',event)\"></td>
			</tr>";
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