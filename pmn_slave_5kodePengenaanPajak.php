<?php
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/zLib.php');

$proses=$_POST['proses'];
$kode=$_POST['kode'];
$nama=$_POST['nama'];

switch($proses){
case 'insert':
    $rrr='';

    if($kode=='')$rrr.=" Kode, ";
    if($nama=='')$rrr.=" Nama,";
    if($rrr!=''){
        echo "error: Silakan mengisi ".$rrr.".";
        exit;
    }  
    $s_cek="select * from ".$dbname.".pmn_5fakturkode where kode='".$kode."'";
    $q_cek=mysql_query($s_cek);
    $cek=mysql_num_rows($q_cek);

    if($cek<1){
        $simpan="INSERT INTO ".$dbname.".pmn_5fakturkode(kode,nama)VALUES ('".$kode."','".$nama."')";
        if($hasil=mysql_query($simpan)){}
        else {
            echo " Gagal,".addslashes(mysql_error($conn));
        }
    }
    else{
        $update="UPDATE ".$dbname.".pmn_5fakturkode SET nama='$nama' WHERE kode='".$kode."'";
         if(!mysql_query($update)) {
            echo "DB Error : ".mysql_error();
        }
        else{ 
            echo 'Done.';
        }
    }
break;
case 'loaddata':
    $limit=10;
    $page=0;
    if(isset($_POST['page']))
    {
        $page=$_POST['page'];
        if($page<0)
        $page=0;
    }
    $sCount="select count(*) as jmlhrow from ".$dbname.".pmn_5fakturkode order by kode asc";
    $qCount=mysql_query($sCount) or die(mysql_error());
    while($rCount=mysql_fetch_object($qCount)){
        $jmlbrs= $rCount->jmlhrow;
    }
    $offset=$page*$limit;
    if($jmlbrs<($offset))$page-=1;
    $offset=$page*$limit;
    $no=$offset;

    $sShow="select * from ".$dbname.".pmn_5fakturkode order by kode asc limit ".$offset.",".$limit." ";
    $qShow=mysql_query($sShow) or die(mysql_error());

    while($row=mysql_fetch_assoc($qShow))
    {
        $no+=1;
        $kode=$row['kode'];
        if(strlen($kode)<3){
            $kd="0".$kode;
        }
        else{
            $kd=$kode;
        }    
        echo"<tr class=rowcontent>
        <td id='no'>".$no."</td>
        <td id='kode_".$no."' value='".$row['kode']."' align='center'>".$kd."</td>
        <td id='nama_".$no."' value='".$row['nama']."'>".$row['nama']."</td>
        <td>
        <img src=images/edit.png class=resicon  title='Edit' onclick=\"editRow('".$row['kode']."','".$row['nama']."');\" >
        <img src=images/delete1.jpg class=resicon  title='Delete' onclick=\"delData('".$row['kode']."','".$row['nama']."');\" ></td>";
    }
    echo"
    </tr><tr class=rowheader><td colspan=5 align=center>
    ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jmlbrs."<br />
    <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
    <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
    </td>
    </tr>";
break;
case 'deletedata':
    $where="nama = '".$nama."'";
    $sDel="delete from ".$dbname.".pmn_5fakturkode where ".$where." and kode = '".$kode."'";
    if(mysql_query($sDel))
    echo"";
    else
    echo "DB Error : ".mysql_error($conn);                        
break;

default:
break;	
}
?>