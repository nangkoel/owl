<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

$zel=makeOption($dbname,'datakaryawan','karyawanid,lokasitugas');

//##thn##pilInp##karyawanId##idKomponen##jmlhDt##method##tpKary
$method=$_POST['method'];
$tpKary=$_POST['tpKary'];
$optThn=$_POST['optThn'];
$pilInp=$_POST['pilInp'];
$karyawanId=$_POST['karyawanId'];
$idKomponen=$_POST['idKomponen'];
$jmlhDt=$_POST['jmlhDt'];
$thn=$_POST['thn'];


$kdUnit=$_POST['kdUnit'];

$optUnit=makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas');
$optTip=makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$optNmKar=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optTipe=makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan');
$optKomponen=makeOption($dbname, 'sdm_ho_component', 'id,name');
        switch($method)
        {
			
			case'getKar':
			$karyPdf="karyawanid in (";
				$optTipe2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
				$i="select * from ".$dbname.".datakaryawan where lokasitugas='".$kdUnit."' and tipekaryawan='".$tpKary."' ";
				
				//exit("Error:$i");
				$n=mysql_query($i) or die (mysql_error($conn));
				while($d=mysql_fetch_assoc($n))
				{  
					$ader+=1;
					$optTipe2.="<option value='".$d['karyawanid']."'>".$d['namakaryawan']."</option>";
					 if($ader==1){
						$karyPdf.=$d['karyawanid'];
					}else{
						 $karyPdf.=",".$d['karyawanid'];
					}
				}
				
			$karyPdf.=") and tahun=".date('Y')."";	
			echo $optTipe2."###".$karyPdf;
			
			break;
			
			
			
			
                case'insert':
if($tpKary==''){
    echo "Error: silakan pilih tipe karyawan";
    exit;
}
if($idKomponen==''){
    echo "Error: Component is obligatory";
    exit;
}
if($jmlhDt==''){
    echo "Error: Please fill amoun(jumlah)";
    exit;
}

                    if($pilInp==0){
                        $sIns="insert into ".$dbname.".sdm_5gajipokok
                              values ('".$thn."','".$karyawanId."','".$idKomponen."','".$jmlhDt."')";
                        if(!mysql_query($sIns))
                        {
                                echo"Gagal".mysql_error($conn);
                        }
                    }else{
						
						
                        $sdata="select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$kdUnit."'
                                and tipekaryawan='".$tpKary."' and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")";
                        $qData=mysql_query($sdata) or die(mysql_error($conn));
                        while($rdata=  mysql_fetch_assoc($qData)){
                            $sdel="delete from ".$dbname.".sdm_5gajipokok where karyawanid='".$rdata['karyawanid']."'
                                   and idkomponen='".$idKomponen."' and tahun='".$thn."'";
                            if(mysql_query($sdel)){
                                 $sIns="insert into ".$dbname.".sdm_5gajipokok
                                        values ('".$thn."','".$rdata['karyawanid']."','".$idKomponen."','".$jmlhDt."')";
                                if(!mysql_query($sIns))
                                {
                                        echo"Gagal".$sIns."____".mysql_error($conn);
                                }
                            }else{
                                        echo"Gagal".$sdel."____".mysql_error($conn);
                            }
                        }
                    }
                break;
                case'loadData':
				
				
				
				$kdUnitList='';
				$i="select * from ".$dbname.".organisasi where induk='".$_SESSION['empl']['kodeorganisasi']."' and  tipe!='HOLDING' ";
				$n=mysql_query($i) or die (mysql_error($conn));
				while($d=mysql_fetch_assoc($n))
				{
					if($kdUnitList!='')
					{
						$kdUnitList.=",";
					}
					$kdUnitList.="'".$d['kodeorganisasi']."'";
				}
				
				
					 if($_POST['kdUnit']!=''){
                        $whrd.=" and karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$kdUnit."')";
                    }
					
				
					
                    if($_POST['namaKary']!=''){
                        $whrd.=" and namakaryawan like '%".$_POST['namaKary']."%'";
                    }
                    if($_POST['tpKaryCr']!=''){
                        $whrd.=" and tipekaryawan = '".$_POST['tpKaryCr']."'";
                    }
                    if($_POST['idKomponenCr']!=''){
                        $whr=" and idkomponen='".$_POST['idKomponenCr']."'";
                    }
					
					 if($_POST['kdUnitCr']!=''){
                       // $whr.=" and karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$kdUnit."')";
					   $whr.=" and karyawanid in (select karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_POST['kdUnitCr']."')";
                    }
					
                $limit=30;
                $page=0;
                if(isset($_POST['page']))
                {
                $page=$_POST['page'];
                if($page<0)
                $page=0;
                }
                $offset=$page*$limit;
                $no=0;	 
                $str="select * from ".$dbname.".sdm_5gajipokok where tahun='".$optThn."'
                      and karyawanid in (select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas in ($kdUnitList) ".$whrd.") ".$whr."";
            //  echo $str;
			    //exit("error".$str);
                $res=mysql_query($str);
                $oow=mysql_num_rows($res);
                if($oow==0){
                    echo"<tr class=rowcontent><td colspan=6>".$_SESSION['lang']['dataempty']."</td></tr>";
                }
                else{
                    while($bar=mysql_fetch_assoc($res))
                    {
						
                    echo"<tr class=rowcontent>
                    <td>".$bar['tahun']."</td>   
					<td>".$optUnit[$bar['karyawanid']]."</td>
                    <td>".$optNmKar[$bar['karyawanid']]."</td>
                    <td>".$optTip[$optTipe[$bar['karyawanid']]]."</td>
                    <td>".$optKomponen[$bar['idkomponen']]."</td>  
                    <td align=right>".number_format($bar['jumlah'],0)."</td>  
                    <td align=center>
                              <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['tahun']."','".$bar['karyawanid']."','".$optTipe[$bar['karyawanid']]."','".$bar['idkomponen']."','".$bar['jumlah']."','".$zel[$bar['karyawanid']]."','".$optNmKar[$bar['karyawanid']]."');\">
                              <img src=images/application/application_delete.png class=resicon  title='Delete Data' onclick=\"delData('".$bar['tahun']."','".$bar['karyawanid']."','".$bar['idkomponen']."');\">
                      </td>
                    </tr>";	
                    }
                   /* echo"<tr class=rowheader><td colspan=6 align=center>
                ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $oow."<br />
                <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
                <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
                </td>
                </tr>";*/
                }
                break;
                case'updateData':
                if($pilInp==0){
                    $sdel="delete from ".$dbname.".sdm_5gajipokok where karyawanid='".$karyawanId."'
                                   and idkomponen='".$idKomponen."' and tahun='".$thn."'";
                       if(mysql_query($sdel)){
                        $sIns="insert into ".$dbname.".sdm_5gajipokok
                              values ('".$thn."','".$karyawanId."','".$idKomponen."','".$jmlhDt."')";
                        if(!mysql_query($sIns))
                        {
                                echo"Gagal".mysql_error($conn);
                        }
                       }
                    }else{
                        $sdata="select distinct karyawanid from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."'
                                and tipekaryawan='".$tpKary."' and (tanggalkeluar = '0000-00-00' or tanggalkeluar > ".$_SESSION['org']['period']['start'].")";
                        $qData=mysql_query($sdata) or die(mysql_error($conn));
                        while($rdata=  mysql_fetch_assoc($qData)){
                            $sdel="delete from ".$dbname.".sdm_5gajipokok where karyawanid='".$rdata['karyawanid']."'
                                   and idkomponen='".$idKomponen."' and tahun='".$thn."'";
                            if(mysql_query($sdel)){
                                 $sIns="insert into ".$dbname.".sdm_5gajipokok
                                        values ('".$thn."','".$rdata['karyawanid']."','".$idKomponen."','".$jmlhDt."')";
                                if(!mysql_query($sIns))
                                {
                                        echo"Gagal".$sIns."____".mysql_error($conn);
                                }
                            }else{
                                        echo"Gagal".$sdel."____".mysql_error($conn);
                            }
                        }
                    }
                break;
                case'delData':
                $sdel="delete from ".$dbname.".sdm_5gajipokok where karyawanid='".$_POST['karyawanId']."'
                                   and idkomponen='".$_POST['idKomponen']."' and tahun='".$_POST['optThn']."'";
                if(!mysql_query($sdel)){
                     echo"Gagal".$sdel."____".mysql_error($conn);
                }
                break;
        }
?>