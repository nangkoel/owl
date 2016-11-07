<?php
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

	$kodeorg	  =$_POST['kodeorg'];
        $tanggal	  =tanggalsystem($_POST['tanggal']);
	$sisatbskemarin=$_POST['sisatbskemarin'];
	$tbsmasuk     =$_POST['tbsmasuk'];
	$tbsdiolah    =$_POST['tbsdiolah'];
	$sisahariini  =$_POST['sisahariini'];
	
	$oer     	  =$_POST['oer'];
	$kadarair     =$_POST['kadarair'];
	$ffa     	  =$_POST['ffa'];
	$dirt     	  =$_POST['dirt'];

	$oerpk     	  =$_POST['oerpk'];
	$kadarairpk   =$_POST['kadarairpk'];
	$ffapk     	  =$_POST['ffapk'];
	$dirtpk       =$_POST['dirtpk'];
	$intipecah=$_POST['intipecah'];

        $usbbefore     	  =$_POST['usbbefore'];
        $usbafter     	  =$_POST['usbafter'];
        $oildiluted       =$_POST['oildiluted'];
        $oilin    	  =$_POST['oilin'];
        $oilinheavy    	  =$_POST['oilinheavy'];
        $caco     	  =$_POST['caco'];
   
        //cpo loses
        $fruitineb     	  =$_POST['fruitineb'];
        $ebstalk     	  =$_POST['ebstalk'];
        $fibre            =$_POST['fibre'];
        $nut    	  =$_POST['nut'];
        $effluent    	  =$_POST['effluent'];
        $soliddecanter    =$_POST['soliddecanter'];
    

        //kernel loses
        $fruitinebker     =$_POST['fruitinebker'];
        $cyclone    	  =$_POST['cyclone'];
        $claybath   	  =$_POST['claybath'];
        $ltds             =$_POST['ltds'];

        $dobi   	  =$_POST['dobi'];
        $batu             =$_POST['batu'];
     
        $method           =$_POST['method'];
	
        switch($method)
        {
        case 'delete':	
	  {
            $strx="delete from ".$dbname.".pabrik_produksi 
                   where kodeorg='".$kodeorg."' 
                       and tanggal='".$_POST['tanggal']."'";   
            break;
	  }
        case 'update':	
	  {
            $strx="update ".$dbname.".pabrik_produksi set sisatbskemarin='".$sisatbskemarin."',tbsmasuk='".$tbsmasuk."',
                tbsdiolah='".$tbsdiolah."',sisahariini='".$sisahariini."',oer='".$oer."',ffa='".$ffa."',
                kadarair='".$kadarair."',kadarkotoran='".$dirt."',oerpk='".$oerpk."',ffapk='".$ffapk."',
                kadarairpk='".$kadarairpk."',kadarkotoranpk='".$dirtpk."',karyawanid='".$_SESSION['standard']['userid']."',
                fruitineb='".$fruitineb."',ebstalk='".$ebstalk."',fibre='".$fibre."',nut='".$nut."',
                effluent='".$effluent."',soliddecanter='".$soliddecanter."',fruitinebker='".$fruitinebker."',
                cyclone='".$cyclone."',ltds='".$ltds."',claybath='".$claybath."',usbbefore='".$usbbefore."',
                usbafter='".$usbafter."',oildiluted='".$oildiluted."',oilin='".$oilin."',oilinheavy='".$oilinheavy."',
                caco='".$caco."',intipecah='".$intipecah."',dobi='".$dobi."',batu='".$batu."'
                   where kodeorg='".$kodeorg."' 
                       and tanggal='".$_POST['tanggal']."'";   
                //exit("Error:$strx");
            break;
	  }
        case 'insert':	
	  {

			$strx="insert into ".$dbname.".pabrik_produksi
                   (kodeorg,tanggal,sisatbskemarin,
				    tbsmasuk,tbsdiolah,sisahariini,
				    oer,ffa,kadarair,kadarkotoran,
					oerpk,ffapk,kadarairpk,kadarkotoranpk,
					karyawanid,fruitineb, ebstalk, fibre, nut, 
                                        effluent, soliddecanter, fruitinebker, cyclone, 
                                        ltds, claybath, usbbefore, usbafter, oildiluted, oilin, 
                                        oilinheavy, caco,intipecah,dobi,batu)
					values('".$kodeorg."',".$tanggal.",".$sisatbskemarin.",
					".$tbsmasuk.",".$tbsdiolah.",".$sisahariini.",
					".$oer.",".$ffa.",".$kadarair.",".$dirt.",
					".$oerpk.",".$ffapk.",".$kadarairpk.",".$dirtpk.",
					".$_SESSION['standard']['userid'].",".$fruitineb.",".$ebstalk.",
                                        ".$fibre.",".$nut.",".$effluent.",".$soliddecanter.",".$fruitinebker.",".$cyclone.",
                                        ".$ltds.",".$claybath.",".$usbbefore.",".$usbafter.",
                                        ".$oildiluted.",".$oilin.",".$oilinheavy.",".$caco.",".$intipecah.",".$dobi.",".$batu.")";
                                          //exit("Error:$strx");
	  }
        default:
           break;					
        }
  if(mysql_query($strx))
  {
	
			$str="select a.* from ".$dbname.".pabrik_produksi a where a.kodeorg='".$_SESSION['empl']['lokasitugas']."'
			      order by a.tanggal desc limit 20";
			$res=mysql_query($str);
			while($bar=mysql_fetch_object($res))
			{
		// echo"<tr class=rowcontent onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\" style='cursor:pointer'>
		  echo"<tr class=rowcontent>
		   <!--<td onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kodeorg."</td>-->
		   <td onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".tanggalnormal($bar->tanggal)."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->sisatbskemarin,0,'.',',')."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->tbsmasuk,0,'.',',')."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->tbsdiolah,0,'.',',.')."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->sisahariini,0,'.',',')."</td>
		   
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->oer,2,'.',',')."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".(@number_format($bar->oer/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->ffa."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarkotoran."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarair."</td>
		   
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".number_format($bar->oerpk,2,'.',',')."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".(@number_format(@$bar->oerpk/$bar->tbsdiolah*100,2,'.',','))."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->ffapk."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarkotoranpk."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->kadarairpk."</td>
		   <td align=right onclick=\"previewDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">".$bar->intipecah."</td>	   
		   <td><img src=images/application/application_add.png class=resicon  title='add detail ".tanggalnormal($bar->tanggal)."' onclick=\"addDetail('".$bar->tanggal."','".$bar->kodeorg."',event);\">
                     <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar->kodeorg."','".$bar->tanggal."','".$bar->sisatbskemarin."','".$bar->tbsmasuk."','".$bar->tbsdiolah."','".$bar->sisahariini."','".$bar->oer."','".$bar->kadarkotoran."','".$bar->kadarair."','".$bar->ffa."','".$bar->oerpk."','".$bar->kadarkotoranpk."','".$bar->kadarairpk."','".$bar->ffapk."','".$bar->intipecah."','".$bar->dobi."','".$bar->batu."','".$bar->usbbefore."','".$bar->usbafter."','".$bar->oildiluted."','".$bar->oilin."','".$bar->oilinheavy."','".$bar->caco."','".$bar->fruitineb."','".$bar->ebstalk."','".$bar->fibre."','".$bar->nut."','".$bar->effluent."','".$bar->soliddecanter."','".$bar->fruitinebker."','".$bar->cyclone."','".$bar->ltds."','".$bar->claybath."');\">
		     <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delProduksi('".$bar->kodeorg."','".$bar->tanggal."','".$bar->kodebarang."');\">
		   </td>
		  </tr>";	
}
}	
  else
	{
		echo " Gagal,".addslashes(mysql_error($conn));
	}	
	
?>
