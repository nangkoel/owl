<?php
	require_once('master_validation.php');
	include('lib/nangkoelib.php');
	require_once('config/connection.php');
	include_once('lib/zLib.php');
	$method=$_POST['method'];
        $txtSearch=$_POST['txtSearch'];
        $tglCari=tanggalsystem($_POST['tglCari']);
        $kdGudang=$_POST['kdGudang'];
        $nmBrg=$_POST['nmBrg'];
//        param='txtSearch='+txtsearch+'&tglCari='+tgl_cari+'&method='+met;
//        param+='&kdGudang='+kdGudang+'&nmBrg='+nmBrg;
        $optNma=makeOption($dbname,'organisasi', 'kodeorganisasi,namaorganisasi');
	switch($method)
	{
        case 'list_new_data':		
        $limit=20;
        $page=0;
        if(isset($_POST['page']))
        {
        $page=$_POST['page'];
        if($page<0)
        $page=0;
        }
        $offset=$page*$limit;
        if($nmBrg!='')
        {
            $where.=" and b.kodebarang in (select  kodebarang from ".$dbname.".log_5masterbarang where namabarang like '%".$nmBrg."%')";
        }
        if($kdGudang!='')
        {
            $where.=" and a.kodegudang='".$kdGudang."'";
        }
        if($tglCari!='')
        {
            $where.=" and a.tanggal='".$tglCari."'";
        }
         
        if($_POST['nopp']!='')
        {
            if(strlen($_POST['nopp'])<7)
            {
                exit("error: masukan nopp min: 001/12 (6 karakter)");
            }
            else
            {
                $where.=" and a.nopo in (select distinct nopo from ".$dbname.".log_podt where nopp like '".$_POST['nopp']."%')";
            }
        }
        else
        {
            if($txtSearch!='')
            {
              $where.=" and a.nopo like '%".$txtSearch."%'";
            }
        }

        $sql2="select distinct count(*) as jmlhrow from ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt b on a.notransaksi=b.notransaksi
            where tipetransaksi=1 ".$where."   ORDER BY a.notransaksi DESC";
       //exit("Error".$sql2);
        $query2=mysql_query($sql2) or die(mysql_error());
        while($jsl=mysql_fetch_object($query2)){
        $jlhbrs= $jsl->jmlhrow;
        }
	 // stat_release='1'
		$str="SELECT distinct a.notransaksi,tanggal,a.nopo,kodegudang FROM ".$dbname.".log_transaksiht a left join ".$dbname.".log_transaksidt b on a.notransaksi=b.notransaksi
                    where tipetransaksi=1  ".$where."  ORDER BY a.notransaksi DESC limit ".$offset.",".$limit."";
	//exit("Error".$str);
           //    echo $str;
	  if($res=mysql_query($str))
	  {
              $row=mysql_num_rows($res);
              if($row!=0)
              {
              $jlhbrs=$row;
		while($bar=mysql_fetch_assoc($res))
		{
			$kodeorg=$bar['kodeorg'];
			$no+=1;
			echo"<tr class=rowcontent >
				  <td>".$no."</td>
				  <td id=td_".$no.">".$bar['notransaksi']."</td>
				  <td>".tanggalnormal($bar['tanggal'])."</td>
				  <td>".$bar['nopo']."</td>
                                  <td>".$optNma[$bar['kodegudang']]."</td>";
	
					  ?>
					 <td>			
					 <button class=mybutton onclick="previewBapb('<?php  echo $bar['notransaksi']?>',event);" ><?php echo $_SESSION['lang']['print'] ?>
					 </button>
					 </td>
	
				 <?php
				
				 echo"</tr>";
		}	 	 	echo"
				 <tr><td colspan=8 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariPage(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariPage(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />";   
              }
              else
              {
                  echo"<tr class=rowcontent><td colspan=6>".$_SESSION['lang']['dataempty']."</td></tr>";
              }
	  }	
	  else
		{
			echo " Gagal,".(mysql_error($conn));
		}
                                
                        

		break;
        case 'loadData':
		$limit=20;
			$page=0;
			if(isset($_POST['page']))
			{
			$page=$_POST['page'];
			if($page<0)
			$page=0;
			}
			$offset=$page*$limit;
			
			$sql2="select distinct count(*) as jmlhrow from ".$dbname.".log_transaksiht where tipetransaksi=1   ORDER BY notransaksi DESC";
			$query2=mysql_query($sql2) or die(mysql_error());
			while($jsl=mysql_fetch_object($query2)){
			$jlhbrs= $jsl->jmlhrow;
			}
	 // stat_release='1'
		$str="SELECT distinct * FROM ".$dbname.".log_transaksiht where tipetransaksi=1   ORDER BY notransaksi DESC limit ".$offset.",".$limit."";
			//echo $str;
	  if($res=mysql_query($str))
	  {
		while($bar=mysql_fetch_assoc($res))
		{
			$kodeorg=$bar['kodeorg'];
			$no+=1;
			echo"<tr class=rowcontent >
				  <td>".$no."</td>
				  <td id=td_".$no.">".$bar['notransaksi']."</td>
				  <td>".tanggalnormal($bar['tanggal'])."</td>
				  <td>".$bar['nopo']."</td>
                                  <td>".$optNma[$bar['kodegudang']]."</td>";
	
					  ?>
					 <td>			
					 <button class=mybutton onclick="previewBapb('<?php  echo $bar['notransaksi']?>',event);" ><?php echo $_SESSION['lang']['print'] ?>
					 </button>
					 </td>
	
				 <?php
				
				 echo"</tr>";
		}	 	 	echo"
				 <tr><td colspan=8 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />";   	
	  }	
	  else
		{
			echo " Gagal,".(mysql_error($conn));
		}	
        break;

	default:
	break;
	}
?>