<?php
    require_once('master_validation.php');
    require_once('config/connection.php');
    include_once('lib/zLib.php');
	
    $proses=$_POST['proses'];
    switch($proses){
    case 'loaddata':
        $limit=10;
        $page=0;
        if(isset($_POST['page']))
        {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
        }
       
        $sCount="select count(*) as jmlhrow from ".$dbname.".owl_help_en order by kode asc";
        $qCount=mysql_query($sCount) or die(mysql_error());
        while($rCount=mysql_fetch_object($qCount)){
            $jlhbrs= $rCount->jmlhrow;
        }
        
        $offset=$page*$limit;
        if($jlhbrs<($offset))$page-=1;
        $offset=$page*$limit;
        $no=$offset;
        
        $sShow="select * from ".$dbname.".owl_help_en order by kode asc,tentang,modul,isi limit ".$offset.",".$limit." ";
        $qShow=mysql_query($sShow) or die(mysql_error());
        while($row=mysql_fetch_assoc($qShow))
        {
            $no+=1;
            echo"<tr class=rowcontent>
            <td id='no'>".$no."</td>
            <td id='index_".$row['kode']."' value='".$row['kode']."' align='center'>".$row['kode']."</td>
            <td id='modul_".$row['kode']."' value='".$row['modul']."'>".$row['modul']."</td>
            <td id='tentang_".$row['kode']."' value='".$row['tentang']."'>".$row['tentang']."</td>
            <td><img onclick=\"detailHelp_en(event,'".str_replace(" ","",$row['kode'])."','".$row['modul']."');\" title=\"Detail Help\" class=\"resicon\" src=\"images/zoom.png\"></td>";
        }
        echo"
        </tr><tr class=rowheader><td colspan=5 align=center>
        ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
        <button class=mybutton onclick=cariBast_en(".($page-1).");>".$_SESSION['lang']['pref']."</button>
        <button class=mybutton onclick=cariBast_en(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
        </td>
        </tr>";
    break;
    case 'cariindex':
        $indexfind=$_POST['cariindex'];
//        $limit=10;
//        $page=0;
//        if(isset($_POST['page']))
//        {
//            $page=$_POST['page'];
//            if($page<0)
//            $page=0;
//        }
//       
//        $sCount="select count(*) as jmlhrow from ".$dbname.".owl_help_en where (kode like '%".$indexfind."%') or (tentang like '%".$indexfind."%') or (modul like '%".$indexfind."%')";
//        $qCount=mysql_query($sCount) or die(mysql_error());
//        while($rCount=mysql_fetch_object($qCount)){
//            $jmlbrs= $rCount->jmlhrow;
//        }
//        
//        $offset=$page*$limit;
//        if($jmlbrs<($offset))$page-=1;
//        $offset=$page*$limit;
//        $no=$offset;
                
       	$str="select * from ".$dbname.".owl_help_en where (kode like '%".$indexfind."%') or (tentang like '%".$indexfind."%') or (modul like '%".$indexfind."%')  ";
	if($res=mysql_query($str))
	{
            $no=0;	 
            while($bar=mysql_fetch_object($res))
            {
                $no+=1;
		echo"<tr class=rowcontent>
                    <td id='no'>".$no."</td>
                    <td id='index_".$bar->kode."' value='".$bar->kode."' align='center'>".$bar->kode."</td>
                    <td id='modul_".$bar->kode."' value='".$bar->modul."'>".$bar->modul."</td>
                    <td id='tentang_".$bar->kode."' value='".$bar->tentang."'>".$bar->tentang."</td>
                    <td><img onclick=\"detailHelp_en(event,'".str_replace(" ","",$bar->kode)."','".$bar->modul."');\" title=\"Detail Help\" class=\"resicon\" src=\"images/zoom.png\"></td>
                    </tr>";
             }	 
            
        }	
        else
	{
            echo " Gagal,".addslashes(mysql_error($conn));
	}
//        echo"
//            </tr><tr class=rowheader><td colspan=5 align=center>
//            ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jmlbrs."<br />
//            <button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
//            <button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
//            </td>
//            </tr>";
     break;
     default:
        break;	
    }
?>