<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$regional=$_POST['regional'];
$kdkegiatan=$_POST['kdkegiatan'];
$rp=$_POST['rp'];
$insen=$_POST['insen'];
$konversi=$_POST['konversi'];
$method=$_POST['method'];
if(isset($_GET['method'])){
$method=$_GET['method'];
}


$nmkonv=array('0'=>$_SESSION['lang']['no'],'1'=>$_SESSION['lang']['yes']);
if($_SESSION['language']=='ID'){
    $fld="namaakun";
}else{
    $fld="namaakun1";
}
//$nmid=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');
$nmen=makeOption($dbname,'keu_5akun','noakun,'.$fld.'');
?>

<?php
switch($method)
{
	case 'insert':
            if($kdkegiatan==''){
                exit("error: ".$_SESSION['lang']['kodekegiatan']." is empty!");
            }
            if($_POST['nmKegiatan']==''){
                exit("error: ".$_SESSION['lang']['namakegiatan']." is empty!");
            }
            if($_POST['satuan']==''){
                exit("error: ".$_SESSION['lang']['satuan']." is empty!");
            }
                $whr="kodekegiatan='".$kdkegiatan."'";
                $nmen=makeOption($dbname,'vhc_kegiatan','kodekegiatan,namakegiatan',$whr);
                if($nmen[$kdkegiatan]==''){
                    $i="insert into ".$dbname.".vhc_kegiatan (regional,kodekegiatan,namakegiatan,satuan,noakun,basis,hargasatuan,hargaslebihbasis
                         ,hargaminggu,updateby,auto)
                    values ('".$regional."','".$kdkegiatan."','".$_POST['nmKegiatan']."','".$_POST['satuan']."','".$_POST['noakun']."','".$_POST['basis']."'
                            ,'".$_POST['hrgSatuan']."','".$_POST['hrgLbhBasis']."','".$_POST['hrgHrMngg']."','".$_SESSION['standard']['userid']."','".$_POST['auto']."')";
                    //exit("Error.$sDel2");
                    if(mysql_query($i))
                    echo"";
                    else
                    echo " Gagal,".addslashes(mysql_error($conn));
                }else{
                    exit("error: Data already register");
                }
	break;
	
	case 'update':
	if($_POST['nmKegiatan']==''){
                exit("error: ".$_SESSION['lang']['namakegiatan']." is empty!");
        }
        if($_POST['satuan']==''){
            exit("error: ".$_SESSION['lang']['satuan']." is empty!");
        }
		
		$i="update ".$dbname.".vhc_kegiatan set namakegiatan='".$_POST['nmKegiatan']."',satuan='".$_POST['satuan']."',noakun='".$_POST['noakun']."'
                    ,basis='".$_POST['basis']."',hargasatuan='".$_POST['hrgSatuan']."',hargaslebihbasis='".$_POST['hrgLbhBasis']."'
                    ,updateby='".$_SESSION['standard']['userid']."',auto='".$_POST['auto']."',hargaminggu='".$_POST['hrgHrMngg']."'
		 where kodekegiatan='".$kdkegiatan."' ";
		//exit("Error.$i");
		if(mysql_query($i))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
		
case'loadData':
	echo"
	<div id=container>
	<img onclick=\"dataKeExcel(event,'vhc_slave_5jenisKegiatan.php')\" src=\"images/excel.jpg\" class=\"resicon\" title=\"MS.Excel\">
		<table class=sortable cellspacing=1 border=0>
	     <thead>
			 <tr class=rowheader>
				 <td align=center>".$_SESSION['lang']['nourut']."</td>
				 <td align=center>".$_SESSION['lang']['regional']."</td>
				 <td align=center>".$_SESSION['lang']['kodekegiatan']."</td>
				 <td align=center>".$_SESSION['lang']['namakegiatan']."</td>
				 <td align=center>".$_SESSION['lang']['satuan']."</td>
				 <td align=center>".$_SESSION['lang']['noakun']."</td>
				 <td align=center>".$_SESSION['lang']['basis']."</td>
                                 <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
                                 <td align=center>".$_SESSION['lang']['hargalbhbasis']."</td>
                                 <td align=center>".$_SESSION['lang']['hargaHariMinggu']."</td>
                                 <td align=center>".$_SESSION['lang']['isiauto']."</td>
				 <td align=center>".$_SESSION['lang']['action']."</td>
			 </tr>
		</thead>
		<tbody>";
 
                if($_POST['nmKegiatanCr']!=''){
                    $whr.=" and (namakegiatan like '%".$_POST['nmKegiatanCr']."%' or kodekegiatan like '%".$_POST['nmKegiatanCr']."%')";
                }
                if($_POST['noakunCr']!=''){
                    $whr.=" and noakun='".$_POST['noakunCr']."'";
                }
                if($_POST['autoCr']!=''){
                    $whr.=" and auto='".$_POST['autoCr']."'";
                }
				if($_POST['satuanCr']!=''){
					$whr.=" and satuan like '%".$_POST['satuanCr']."%'";
				}
		
		
		$limit=15;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		$maxdisplay=($page*$limit);
		
		


		
		$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_kegiatan  
                       where regional='".$_SESSION['empl']['regional']."' ".$whr." order by kodekegiatan asc ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".vhc_kegiatan 
                    where regional='".$_SESSION['empl']['regional']."' ".$whr." 
                     order by kodekegiatan asc  limit ".$offset.",".$limit."";
		//echo $i;
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n)){
			$no+=1;
			echo "<tr class=rowcontent>";
			echo "<td align=center>".$no."</td>";
			echo "<td align=left>".$d['regional']."</td>";
			echo "<td>".$d['kodekegiatan']."</td>";
                        echo "<td align=left>".$d['namakegiatan']."</td>";
			echo "<td>".$d['satuan']."</td>";
			echo "<td>".$nmen[$d['noakun']]."</td>";
                        echo "<td align=right>".$d['basis']."</td>";
                        echo "<td align=right>".number_format($d['hargasatuan'],2)."</td>";
                        echo "<td align=right>".number_format($d['hargaslebihbasis'],2)."</td>";
                        echo "<td align=right>".number_format($d['hargaminggu'],2)."</td>";
			echo "<td align=right>".$d['auto']."</td>";
			echo "<td align=center>
			<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"edit('".$d['kodekegiatan']."','".$d['namakegiatan']."','".$d['satuan']."','".$d['noakun']."','".$d['basis']."','".$d['hargaslebihbasis']."','".$d['hargaminggu']."','".$d['auto']."','".$d['hargasatuan']."');\">";
			echo "</tr>";
		}
		echo"</tbody><tfoot>
		<tr class=rowheader><td colspan=18 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=loadData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=loadData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		echo"</tfoot></table>";
    break;

	
        case'upGradeData':
            $sData="select * from ".$dbname.".vhc_kegiatan where regional='".$_SESSION['empl']['regional']."'";
            //exit("error:masuk___".$sData);
            $qData=mysql_query($sData) or die(mysql_error($conn));
            if(mysql_num_rows($qData)==0){
                exit("error:Data Kosong");
            }
            while($rData=  mysql_fetch_assoc($qData)){
                @$basis=$rData['basis']+($rData['basis']*$_POST['bsisPrsn']/100);
                @$hrgsat=$rData['hargasatuan']+($rData['hargasatuan']*$_POST['hrgStnPrsn']/100);
                @$hrgLbh=$rData['hargaslebihbasis']+($rData['hargaslebihbasis']*$_POST['hrgLbhBsisPrsn']/100);
                @$hrgming=$rData['hargaminggu']+($rData['hargaminggu']*$_POST['hrgMnggPrsn']/100);
                $supdate="update ".$dbname.".vhc_kegiatan set basis='".$basis."',hargasatuan='".$hrgsat."'
                          ,hargaslebihbasis='".$hrgLbh."',hargaminggu='".$hrgming."',updateby='".$_SESSION['standard']['userid']."'
                          where regional='".$_SESSION['empl']['regional']."'";
                //exit("error:".$supdate);
                if(!mysql_query($supdate)){
                    exit("error: db gagal ".mysql_error($conn)."__".$supdate);
                }
            }
        break;
		case'excelData':
	    $tab.="<table class=sortable cellspacing=1 border=1>
	     <thead>
			 <tr class=rowheader>
				 <td align=center>".$_SESSION['lang']['nourut']."</td>
				 <td align=center>".$_SESSION['lang']['regional']."</td>
				 <td align=center>".$_SESSION['lang']['kodekegiatan']."</td>
				 <td align=center>".$_SESSION['lang']['namakegiatan']."</td>
				 <td align=center>".$_SESSION['lang']['satuan']."</td>
				 <td align=center>".$_SESSION['lang']['noakun']."</td>
				 <td align=center>".$_SESSION['lang']['basis']."</td>
				 <td align=center>".$_SESSION['lang']['hargasatuan']."</td>
				 <td align=center>".$_SESSION['lang']['hargalbhbasis']."</td>
				 <td align=center>".$_SESSION['lang']['hargaHariMinggu']."</td>
				 <td align=center>".$_SESSION['lang']['isiauto']."</td>
			 </tr>
		</thead>
		<tbody>";
		if($_GET['nmKegiatanCr']!=''){
                    $whr.=" and namakegiatan like '%".$_GET['nmKegiatanCr']."%'";
		}
		if($_GET['noakunCr']!=''){
			$whr.=" and noakun='".$_GET['noakunCr']."'";
		}
		if($_GET['autoCr']!=''){
			$whr.=" and auto='".$_GET['autoCr']."'";
		}
		$i="select * from ".$dbname.".vhc_kegiatan 
                    where regional='".$_SESSION['empl']['regional']."' ".$whr." 
                     order by kodekegiatan asc  ";
		//echo $i;
		$n=mysql_query($i) or die(mysql_error());
		while($d=mysql_fetch_assoc($n)){
			$no+=1;
			$tab.="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=left>".$d['regional']."</td>";
			$tab.="<td>".$d['kodekegiatan']."</td>";
			$tab.= "<td align=left>".$d['namakegiatan']."</td>";
			$tab.="<td>".$d['satuan']."</td>";
			$tab.="<td>".$nmen[$d['noakun']]."</td>";
			$tab.="<td align=right>".$d['basis']."</td>";
			$tab.="<td align=right>".number_format($d['hargasatuan'],2)."</td>";
			$tab.="<td align=right>".number_format($d['hargaslebihbasis'],2)."</td>";
			$tab.="<td align=right>".number_format($d['hargaminggu'],2)."</td>";
			$tab.= "<td align=right>".$d['auto']."</td>";
			$tab.= "</tr>";
		}
		$tab.="</tfoot></table>";
		$tab.="<br>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
		$nop_="masterkegiatan__".date('YmdHis');
		if(strlen($tab)>0)
		{
			if ($handle = opendir('tempExcel')) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						@unlink('tempExcel/'.$file);
					}
				}	
				closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$tab))
			{
			echo "<script language=javascript1.2>
				parent.window.alert('Can't convert to excel format');
				</script>";
			exit;
			}
			else
			{
				echo "<script language=javascript1.2>
					window.location='tempExcel/".$nop_.".xls';
					</script>";
			}
			closedir($handle);
		}       
    break;

}
?>

