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
	$str="select distinct kodeorganisasi,namaorganisasi,tipe from ".$dbname.".organisasi where kodeorganisasi='".$lokasitugas."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
		if($bar->tipe=='PT')//skip PT
		{continue;}
		else
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
		}
//second grade==============================
			$str1="select kodeorganisasi,namaorganisasi,tipe from ".$dbname.".organisasi where induk='".$bar->kodeorganisasi."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
			$res1=mysql_query($str1);
			while($bar1=mysql_fetch_object($res1))
			{
				if($bar1->tipe=='PT')
				{continue;}
				else
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
				}
//third grade==============================
				$str2="select kodeorganisasi,namaorganisasi,tipe from ".$dbname.".organisasi where induk='".$bar1->kodeorganisasi."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
				$res2=mysql_query($str2);
				while($bar2=mysql_fetch_object($res2))
				{
				if($bar2->tipe=='PT')
				{continue;}
				else
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
				}
//forth grade==============================
						$str3="select kodeorganisasi,namaorganisasi,tipe from ".$dbname.".organisasi where induk='".$bar2->kodeorganisasi."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
						$res3=mysql_query($str3);
						while($bar3=mysql_fetch_object($res3))
						{
						if($bar3->tipe=='PT')
						{continue;}
						else
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
							  
						}
//fifth grade==============================
									$str4="select kodeorganisasi,namaorganisasi,tipe from ".$dbname.".organisasi where induk='".$bar3->kodeorganisasi."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
									$res4=mysql_query($str4);
									while($bar4=mysql_fetch_object($res4))
									{
									if($bar4->tipe=='PT')
									{continue;}
									else
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
									}
//sixth grade==============================
										$str5="select kodeorganisasi,namaorganisasi,tipe from ".$dbname.".organisasi where induk='".$bar4->kodeorganisasi."' and tipe not in('BLOK','STENGINE','STATION') order by kodeorganisasi";
										$res5=mysql_query($str5);
										while($bar5=mysql_fetch_object($res5))
										{
										if($bar5->tipe=='PT')
										{continue;}
										else
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
	}


if($returntype=='array')
	return $arr;
else if($returntype=='list')
	return $list;
else
    return $option;
}

function namakaryawan($db,$conn,$userid)
{
        $namakaryawan='';
		$strx="select namakaryawan from ".$db.".datakaryawan where karyawanid=".$userid;
		$resx=mysql_query($strx);
		while($barx=mysql_fetch_object($resx))
		{
			$namakaryawan=$barx->namakaryawan;
		}
	return $namakaryawan;		
}

function ambilUnitPembebananBarang($returntype='array')
{

	global $dbname;
	global $conn;

	$arr=Array();
	$list='';
	$option="";
	$str="select distinct kodeorganisasi,namaorganisasi,tipe from ".$dbname.".organisasi 
	      where length(kodeorganisasi)=4
		  and induk!=''
		  order by namaorganisasi";
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
		if($bar->tipe=='PT')//skip PT
		{
			continue;
		}
		else
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
		}
	}


if($returntype=='array')
	return $arr;
else if($returntype=='list')
	return $list;
else
    return $option;
}
function ambilSubUnit($returntype='array',$induk)
{

	global $dbname;
	global $conn;

	$arr=Array();
	$list='';
	$option="";
	$str="select distinct kodeorganisasi,namaorganisasi,tipe from ".$dbname.".organisasi where induk='".$induk."' order by kodeorganisasi";
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
		if($bar->tipe=='PT')//skip PT
		{
			continue;
		}
		else
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
		}
	}


if($returntype=='array')
	return $arr;
else if($returntype=='list')
	return $list;
else
    return $option;
}


function getVhcCode($returntype='array',$kodeunit)
{

	global $dbname;
	global $conn;
	$arr=Array();
	$list='';
	$option="";
    $str="select * from ".$dbname.".vhc_5master where kodeorg='".$kodeunit."'
	 or kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$kodeunit."')
	 order by kodevhc";
	$res=mysql_query($str);
//echo $str.mysql_error($conn);
	$no=0;
	while($bar1=mysql_fetch_object($res))
	{
		$no+=1;
		$str="select namajenisvhc from ".$dbname.".vhc_5jenisvhc where jenisvhc='".$bar1->jenisvhc."'";
                //echo $str;
		$res1=mysql_query($str);
		$namabarang='';
		while($bar=mysql_fetch_object($res1))
		{
			$namabarang=$bar->namajenisvhc;
		}
			if($returntype=='array')
			  array_push($arr,$bar1->kodevhc);
			else if($list=='' and $returntype=='list')
			  {
			  	$list=$bar1->kodevhc;
			  }
			else if($list!='' and $returntype=='list') 
			 {
			 	$list.="|".$bar1->kodevhc;
			 } 
			 else
			 {
			 	$option.="<option value='".$bar1->kodevhc."'>[".$bar1->kodevhc."]-".$namabarang."</option>";
			 } 
	}	
if($returntype=='array')
	return $arr;
else if($returntype=='list')
	return $list;
else
    return $option;	
}

function getGudangPT($returntype='array',$gudang)
{
	global $dbname;
	global $conn;
	$arr=Array();
	$list='';
	$option="";
    $str="select distinct kodeorg from ".$dbname.".log_5masterbarangdt where kodegudang='".$gudang."' 
	      order by kodeorg";
	$res=mysql_query($str);
//echo $str.mysql_error($conn);
	$no=0;
	while($bar1=mysql_fetch_object($res))
	{
		$no+=1;
		$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$bar1->kodeorg."'";
		$res1=mysql_query($str);
		while($bar=mysql_fetch_object($res1))
		{
			$namapt=$bar->namaorganisasi;
		}
			if($returntype=='array')
			  array_push($arr,$bar1->kodeorg);
			else if($list=='' and $returntype=='list')
			  {
			  	$list=$bar1->kodeorg;
			  }
			else if($list!='' and $returntype=='list') 
			 {
			 	$list.="|".$bar1->kodeorg;
			 } 
			 else
			 {
			 	$option.="<option value='".$bar1->kodeorg."'>[".$bar1->kodeorg."]-".$namapt."</option>";
			 } 
	}	
if($returntype=='array')
	return $arr;
else if($returntype=='list')
	return $list;
else
    return $option;		
}

function getKegiatanBlok($returntype='array',$blok)
{
	global $dbname;
	global $conn;
	$arr=Array();
	$list='';
	$option="";
    $str="select statusblok from ".$dbname.".setup_blok where kodeorg='".$blok."'";
	$res=mysql_query($str);
//echo $str.mysql_error($conn);
	$no=0;
	while($bar1=mysql_fetch_object($res))
	{
		$no+=1;
                if($bar1->statusblok=='TM')
		     $str="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan where (kelompok='TM' or kelompok='PNN') order by kelompok,namakegiatan";
		else
                {
                    $str="select kodekegiatan,kelompok,namakegiatan from ".$dbname.".setup_kegiatan where kelompok='".$bar1->statusblok."' order by kelompok,namakegiatan"; 
                } 
                $res1=mysql_query($str);
		while($bar=mysql_fetch_object($res1))
		{
			if($returntype=='array')
			  array_push($arr,$bar1->kodekegiatan);
			else if($list=='' and $returntype=='list')
			  {
			  	$list=$bar1->kodekegiatan;
			  }
			else if($list!='' and $returntype=='list') 
			 {
			 	$list.="|".$bar1->kodekegiatan;
			 } 
			 else
			 {
			 	$option.= "<option value='".$bar->kodekegiatan."'>[".$bar->kelompok."]-".$bar->namakegiatan."</option>";
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

function ambilSeluruhGudang($returntype='array',$kecuali)
{
	global $dbname;
	global $conn;
	$arr=Array();
	$list='';
	$option="";
	$no=0;
		$no+=1;
		$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
		      where tipe='GUDANG' and kodeorganisasi<>'".$kecuali."' order by namaorganisasi";
		$res1=mysql_query($str);
		while($bar=mysql_fetch_object($res1))
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
			 	$option.= "<option value='".$bar->kodeorganisasi."'>[".$bar->kodeorganisasi."]-".$bar->namaorganisasi."</option>";
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