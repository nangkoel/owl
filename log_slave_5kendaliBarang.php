<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
?>	

<?php		
$kdOrg=$_POST['kdOrg'];
$kdBarang=$_POST['kdBarang'];
$method=$_POST['method'];
$noBarang=$_POST['noBarang'];

?>

<?php
switch($method)
{
    case'goCariBarang':
        echo"
        <table cellspacing=1 border=0 class=data>
            <thead>
                <tr class=rowheader>
                    <td>No</td>
                    <td>".$_SESSION['lang']['kodebarang']."</td>
                    <td>".$_SESSION['lang']['namabarang']."</td>
                    <td>".$_SESSION['lang']['satuan']."</td>
                </tr>
            </thead>
        </tbody>";

        $i="select * from ".$dbname.".log_5masterbarang where kodebarang like '%".$noBarang."%' 
                or namabarang like '%".$noBarang."%'  and inactive=0 ";
        //echo $i;
        $n=mysql_query($i) or die (mysql_error($conn));
        while ($d=mysql_fetch_assoc($n))
        {
            $no+=1;
            echo"
                <tr class=rowcontent  style='cursor:pointer;' title='Click It' onclick=goPickBarang('".$d['kodebarang']."')>
                    <td>".$no."</td>
                    <td>".$d['kodebarang']."</td>
                    <td>".$d['namabarang']."</td>
                    <td>".$d['satuan']."</td>
                </tr>
            ";
        }
    break;

    case 'insert':
            
            $i="INSERT INTO ".$dbname.".`log_5kendalibarang` (`kodegudang`, `kodebarang`, `updateby`)
            values ('".$kdOrg."','".$kdBarang."','".$_SESSION['standard']['userid']."')";
            //exit("Error.$sDel2");
            if(mysql_query($i))
            echo"";
            else
            echo " Gagal,".addslashes(mysql_error($conn));
    break;
	
    case 'update':
        $i="update ".$dbname.".log_5kendalibarang set nokartu='".$noKartu."',updateby='".$_SESSION['standard']['userid']."'
         where kodegudang='".$kdOrg."' and kodebarang='".$kdBarang."'";
        //exit("Error.$str");
        if(mysql_query($i))
        echo"";
        else
        echo " Gagal,".addslashes(mysql_error($conn));
    break;
    
case 'delete':
        $i="delete from ".$dbname.".log_5kendalibarang where kodegudang='".$kdOrg."' and kodebarang='".$kdBarang."' ";
    //exit("Error:$i");
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
                                 <td align=center colspan=2>".$_SESSION['lang']['gudang']."</td>
				 <td align=center>".$_SESSION['lang']['kodebarang']."</td>
				 <td align=center>".$_SESSION['lang']['namabarang']."</td>
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
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".log_5kendalibarang where kodegudang in (select kodeorganisasi from ".$dbname.".organisasi "
                . " where tipe in ('GUDANG','GUDANGTEMP') and SUBSTR(kodeorganisasi,1,4)='".$_SESSION['empl']['lokasitugas']."') ";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
                    $jlhbrs= $jsl->jmlhrow;
		}
		$i="select * from ".$dbname.".log_5kendalibarang where kodegudang in (select kodeorganisasi from ".$dbname.".organisasi "
                . " where tipe in ('GUDANG','GUDANGTEMP') and SUBSTR(kodeorganisasi,1,4)='".$_SESSION['empl']['lokasitugas']."')   limit ".$offset.",".$limit."";
		$n=mysql_query($i) or die(mysql_error());
		$no=$maxdisplay;
		while($d=mysql_fetch_assoc($n))
		{
                    $whBrg="kodebarang='".$d['kodebarang']."'";
                    $nmBarang=  makeOption($dbname,'log_5masterbarang', 'kodebarang,namabarang',$whBrg);
                    
                    $whOrg="kodeorganisasi='".$d['kodegudang']."'";
                    $nmOrg=  makeOption($dbname,'organisasi', 'kodeorganisasi,namaorganisasi',$whOrg);
                    
                    $no+=1;
                    echo "<tr class=rowcontent>";
                    echo "<td align=center>".$no."</td>";
                    echo "<td align=left>".$d['kodegudang']."</td>";
                    echo "<td align=left>".$nmOrg[$d['kodegudang']]."</td>";
                    echo "<td align=left>".$d['kodebarang']."</td>";
                    echo "<td align=left>".$nmBarang[$d['kodebarang']]."</td>";
                    echo "<td align=center>
                            <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['kodegudang']."','".$d['kodebarang']."');\"></td>";
                    echo "</tr>";
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

    

default:
}
?>
