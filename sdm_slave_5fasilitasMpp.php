<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

//$arr="##thnBudget##kdJabatan##kdBarang##hrgSat##sat##jmlhBrng##method";
$method=$_POST['method'];
$thnBudget=$_POST['thnBudget'];
$kdJabatan=$_POST['kdJabatan'];
$kdBarang=$_POST['kdBarang'];
$hrgSat=$_POST['hrgSat'];
$sat=$_POST['sat'];
$jmlhBrng=$_POST['jmlhBrng'];
$totBrg=$_POST['totBrg'];
$nmBrg=$_POST['nmBrg'];
$oldKdBrg=$_POST['oldKdBrg'];
$optBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optJbtn=makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$where=" tahunbudget='".$thnBudget."' and jabatan='".$kdJabatan."' and kodebarang='".$oldKdBrg."'";

	switch($method)
	{
		case'insert':
		if(($thnBudget=='')||($kdJabatan=='')||($kdBarang=='')||($hrgSat=='')||($hrgSat==0)||($jmlhBrng=='')||($jmlhBrng==0))
		{
			echo"warning:Field tidak boleh kosong";
			exit();
		}
		$sCek="select tahunbudget from ".$dbname.".sdm_5kebutuhanmpp where ".$where."";
		$qCek=mysql_query($sCek) or die(mysql_error($conn));
		$rCek=mysql_num_rows($qCek);
		if($rCek>0)
		{
			echo"warning:Data sudah ada";
			exit();
		}
		else
		{
                    $sIns="insert into ".$dbname.".sdm_5kebutuhanmpp (tahunbudget, jabatan, kodebarang, hargasatuan, satuan, jumlah, total) values 
                        ('".$thnBudget."','".$kdJabatan."','".$kdBarang."','".$hrgSat."','".$sat."','".$jmlhBrng."','".$totBrg."')";
                    if(!mysql_query($sIns))
                    {
                            echo"Gagal".mysql_error($conn);
                    }

		}
		break;
		case'loadData':
                $limit=20;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
		$no=0;	 
                if($thnBudget!='')
                {
                    $addKond.=" and tahunbudget='".$thnBudget."'";
                }
                if($kdJabatan!='')
                {
                    $addKond.=" and jabatan='".$kdJabatan."'";
                }
              
                $sql2="SELECT count(*) as jmlhrow FROM ".$dbname.".sdm_5kebutuhanmpp where tahunbudget!='' ".$addKond." order by tahunbudget desc ";	 

                //echo "warning:".$strx;exit();
                $query2=mysql_query($sql2) or die(mysql_error());
                while($jsl=mysql_fetch_object($query2)){
                $jlhbrs= $jsl->jmlhrow;
                }
                if($jlhbrs!=0)
                {
		$str="select * from ".$dbname.".sdm_5kebutuhanmpp where tahunbudget!='' ".$addKond." order by tahunbudget desc limit ".$offset.",".$limit."";
		$res=mysql_query($str);
		while($bar=mysql_fetch_assoc($res))
		{
		$no+=1;	
		echo"<tr class=rowcontent>
		<td>".$no."</td>
		<td>".$bar['tahunbudget']."</td>
		<td>".$optJbtn[$bar['jabatan']]."</td>
		<td>".$optBrg[$bar['kodebarang']]."</td>
		<td align=right>".number_format($bar['hargasatuan'],2)."</td>
                <td>".$bar['satuan']."</td>
		<td align=right>".number_format($bar['jumlah'],2)."</td>
                <td align=right>".number_format($bar['total'],2)."</td>
		<td>
			  <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['tahunbudget']."','".$bar['jabatan']."','".$bar['kodebarang']."');\"> 
			  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['tahunbudget']."','".$bar['jabatan']."','".$bar['kodebarang']."');\">
		  </td>
		</tr>";	
		}     
                 echo"
                <tr><td colspan=10 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
                <button class=mybutton onclick=cariPage(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariPage(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";
                }
                else
                {
                    echo"<tr class=rowcontent><td colspan=10>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
		break;
		case'update':
		if(($thnBudget=='')||($kdJabatan=='')||($kdBarang=='')||($hrgSat=='')||($hrgSat==0)||($jmlhBrng=='')||($jmlhBrng==0))
		{
			echo"warning:Field tidak boleh kosong";
			exit();
		}
		else
		{
                
                    //ticket, taxiboat, airporttax, visa, bylain
			$sUpd="update ".$dbname.".sdm_5kebutuhanmpp set `kodebarang`='".$kdBarang."',`hargasatuan`='".$hrgSat."',`satuan`='".$sat."',`jumlah`='".$jmlhBrng."',`total`='".$totBrg."' where ".$where."";
			if(!mysql_query($sUpd))
			{
				echo"Gagal".mysql_error($conn);
			}
		}
		break;
		case'delData':
		$sDel="delete from ".$dbname.".sdm_5kebutuhanmpp  where ".$where."";
		if(!mysql_query($sDel))
		{
			echo"Gagal".mysql_error($conn);
		}
		break;
		case'getData':
		$sDt="select * from ".$dbname.".sdm_5kebutuhanmpp where ".$where."";
		$qDt=mysql_query($sDt) or die(mysql_error($conn));
		$rDet=mysql_fetch_assoc($qDt);
                //tahunbudget, golongan, tujuan, ticket, taxiboat, airporttax, visa, bylain
		echo $rDet['tahunbudget']."###".$rDet['jabatan']."###".$rDet['kodebarang']."###".$rDet['hargasatuan']."###".$rDet['satuan']."###".$rDet['jumlah']."###".$rDet['total'];
		break;
            case'getBarang':
              $tab="<fieldset><legend>".$_SESSION['lang']['result']."</legend>
                        <div style=\"overflow:auto;height:295px;width:455px;\">
                        <table cellpading=1 border=0 class=sortbale>
                        <thead>
                        <tr class=rowheader>
                        <td>No.</td>
                        <td>".$_SESSION['lang']['kodebarang']."</td>
                        <td>".$_SESSION['lang']['namabarang']."</td>
                        <td>".$_SESSION['lang']['satuan']."</td>
                        </tr><tbody>
                        ";
            
            $sLoad="select kodebarang,namabarang,satuan from ".$dbname.".log_5masterbarang where kodebarang like '%".$nmBrg."%'
            or namabarang like '%".$nmBrg."%'";
        //   echo $sLoad;
        $qLoad=mysql_query($sLoad) or die(mysql_error($conn));
        while($res=mysql_fetch_assoc($qLoad))
        {
            $no+=1;
            $tab.="<tr class=rowcontent onclick=\"setData('".$res['kodebarang']."','".$res['satuan']."')\">";
            $tab.="<td>".$no."</td>";
            $tab.="<td>".$res['kodebarang']."</td>";
            $tab.="<td>".$res['namabarang']."</td>";
            $tab.="<td>".$res['satuan']."</td>";
            $tab.="</tr>";
        }
        echo $tab;
            break;
            case'getSatuan':
            $sSatuan="select distinct satuan from ".$dbname.".log_5masterbarang where kodebarang='".$kdBarang."'";
            $qSatuan=mysql_query($sSatuan) or die(mysql_error($sSatuan));
            $rSatuan=mysql_fetch_assoc($qSatuan);
            echo $rSatuan['satuan'];
            break;
		default:
		break;
	}
?>