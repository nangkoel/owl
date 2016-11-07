<?php
//ambil kode organisasi dari lokasitugas sampai afdeling
//return type: Array,List,Option
function ambilLokasiTugasDanTurunannya($returntype='array',$lokasitugas)
{

	global $dbname;
	global $conn;

	$arr=Array();
	$list='';
	$option="";
	$str="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$lokasitugas."' and tipe<>'BLOK' order by kodeorganisasi";
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
		if($returntype=='array')
		  array_push($arr,$bar->kodeorganisasi);
		else if($list=='' and $returntype=='list')
		  {
		  	$list=$bar->kodeorganisasi;
		  }
		else if($list!='' and $returntype=='list') 
		 {
		 	$list.="|".$bar->kodeorganisasi;
		 } 
		 else
		 {
		 	$option.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
		 }
//second grade==============================
			$str1="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$bar->kodeorganisasi."' and tipe<>'BLOK' order by kodeorganisasi";
			$res1=mysql_query($str1);
			while($bar1=mysql_fetch_object($res1))
			{
				if($returntype=='array')
				  array_push($arr,$bar1->kodeorganisasi);
				else if($list=='' and $returntype=='list')
				  {
				  	$list=$bar1->kodeorganisasi;
				  }
				else if($list!='' and $returntype=='list') 
				 {
				 	$list.="|".$bar1->kodeorganisasi;
				 } 
				 else
				 {
				 	$option.="<option value='".$bar1->kodeorganisasi."'>".$bar1->namaorganisasi."</option>";
				 }
					  
//third grade==============================
				$str2="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$bar1->kodeorganisasi."' and tipe<>'BLOK' order by kodeorganisasi";
				$res2=mysql_query($str2);
				while($bar2=mysql_fetch_object($res2))
				{
					if($returntype=='array')
					  array_push($arr,$bar2->kodeorganisasi);
					else if($list=='' and $returntype=='list')
					  {
					  	$list=$bar2->kodeorganisasi;
					  }
					else if($list!='' and $returntype=='list') 
					 {
					 	$list.="|".$bar2->kodeorganisasi;
					 } 
					 else
					 {
					 	$option.="<option value='".$bar2->kodeorganisasi."'>".$bar2->namaorganisasi."</option>";
					 }

//forth grade==============================
						$str3="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$bar2->kodeorganisasi."' and tipe<>'BLOK' order by kodeorganisasi";
						$res3=mysql_query($str3);
						while($bar3=mysql_fetch_object($res3))
						{
							if($returntype=='array')
							  array_push($arr,$bar3->kodeorganisasi);
							else if($list=='' and $returntype=='list')
							  {
							  	$list=$bar3->kodeorganisasi;
							  }
							else if($list!='' and $returntype=='list') 
							 {
							 	$list.="|".$bar3->kodeorganisasi;
							 } 
							 else
							 {
							 	$option.="<option value='".$bar3->kodeorganisasi."'>".$bar3->namaorganisasi."</option>";
							 }
							  
							
//fifth grade==============================
									$str4="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$bar3->kodeorganisasi."' and tipe<>'BLOK' order by kodeorganisasi";
									$res4=mysql_query($str4);
									while($bar4=mysql_fetch_object($res4))
									{
										if($returntype=='array')
										  array_push($arr,$bar4->kodeorganisasi);
										else if($list=='' and $returntype=='list')
										  {
										  	$list=$bar4->kodeorganisasi;
										  }
										else if($list!='' and $returntype=='list') 
										 {
										 	$list.="|".$bar4->kodeorganisasi;
										 } 
										 else
										 {
										 	$option.="<option value='".$bar4->kodeorganisasi."'>".$bar4->namaorganisasi."</option>";
										 }

//sixth grade==============================
										$str5="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$bar4->kodeorganisasi."' and tipe<>'BLOK' order by kodeorganisasi";
										$res5=mysql_query($str5);
										while($bar5=mysql_fetch_object($res5))
										{
											if($returntype=='array')
											  array_push($arr,$bar5->kodeorganisasi);
											else if($list=='' and $returntype=='list')
											  {
											  	$list=$bar5->kodeorganisasi;
											  }
											else if($list!='' and $returntype=='list') 
											 {
											 	$list.="|".$bar5->kodeorganisasi;
											 } 
											 else
											 {
											 	$option.="<option value='".$bar5->kodeorganisasi."'>".$bar5->namaorganisasi."</option>";
											 }
											  
										}										  
							     }		
				      }
									  
			  }	
		}
	}


if($returntype=='array')
	return $arr;
else if($returntype=='list')
	return $list;
else
    return $option;
}
?>