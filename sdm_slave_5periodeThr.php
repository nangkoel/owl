<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$tahun=$_POST['tahun'];
$agama=$_POST['agama'];
$perBayar=$_POST['perBayar'];
$perMulai=$_POST['perMulai'];
$perSampai=$_POST['perSampai'];
$tgl=  tanggalsystem($_POST['tgl']);
$method=$_POST['method'];
?>

<?php
switch($method)
{
	

    case 'insert':
            $i="insert into ".$dbname.".sdm_5periodethr (regional,tahun,periodemulai,periodesampai,updateby,periodebayar,agama,tanggalcutoff)
            values ('".$_SESSION['empl']['regional']."','".$tahun."','".$perMulai."','".$perSampai."','".$_SESSION['standard']['userid']."',"
            . " '".$perBayar."','".$agama."','".$tgl."')";
           // exit("Error.$i");
            if(mysql_query($i))
            echo"";
            else
            echo " Gagal,".addslashes(mysql_error($conn));
    break;

    case 'update':
    //exit("Error:ASD");
            $i="update ".$dbname.".sdm_5periodethr set periodemulai='".$perMulai."',periodesampai='".$perSampai."',"
            . " updateby='".$_SESSION['standard']['userid']."',periodebayar='".$perBayar."',tanggalcutoff='".$tgl."'
             where tahun='".$tahun."' and regional='".$_SESSION['empl']['regional']."' and agama='".$agama."'";
            //exit("Error.$i");
            if(mysql_query($i))
            echo"";
            else
            echo " Gagal,".addslashes(mysql_error($conn));
    break;
	
		
    case'loadData':
	echo"
	<div id=container>
		<table class=sortable cellspacing=1 border=0>
	     <thead>
			 <tr class=rowheader>
			 	 <td align=center>".$_SESSION['lang']['nourut']."</td>
                                     <td align=center>".$_SESSION['lang']['agama']."</td>
				 <td align=center>".$_SESSION['lang']['tahun']."</td>
				 <td align=center>".$_SESSION['lang']['periode']." Mulai</td>
				 <td align=center>".$_SESSION['lang']['periode']." Selesai</td>
                                     <td align=center>".$_SESSION['lang']['periode']." Bayar</td>
                                         <td align=center>Tanggal Cut Off</td>
				 <td align=center>".$_SESSION['lang']['action']."</td>
			 </tr>
		</thead>
		<tbody>";
		
		
		
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_5periodethr where regional='".$_SESSION['empl']['regional']."'";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".sdm_5periodethr where regional='".$_SESSION['empl']['regional']."' order by tahun asc  limit ".$offset.",".$limit."";
		
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
	
                    $no+=1;
                    echo "<tr class=rowcontent>";
                    echo "<td align=center>".$no."</td>";
                     echo "<td align=left>".$d['agama']."</td>";
                    echo "<td align=left>".$d['tahun']."</td>";
                    echo "<td align=left>".$d['periodemulai']."</td>";
                    echo "<td align=left>".$d['periodesampai']."</td>";
                    echo "<td align=left>".$d['periodebayar']."</td>";
                     echo "<td align=left>".tanggalnormal($d['tanggalcutoff'])."</td>";
                    echo "<td align=center>
                            <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"edit('".$d['tahun']."','".$d['periodemulai']."',"
                            . "'".$d['periodesampai']."','".$d['agama']."','".$d['periodebayar']."','".tanggalnormal($d['tanggalcutoff'])."');\">
                            </td>";
                    echo "</tr>";//<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['kode']."');\">
		}
		/*echo"
		<tr class=rowheader><td colspan=18 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";*/
		echo"</tbody></table>";
    break;

	case 'delete':
	//exit("Error:hahaha");
		$i="delete from ".$dbname.".kebun_5dendapengawas where kode='".$kode."'";
		//exit("Error.$str");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;

default:
}
?>
