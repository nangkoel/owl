<?php
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<script language=javascript src=js/zMaster.js></script>
<script language=javascript src=js/zSearch.js></script>
<script language=javascript src=js/zTools.js></script>
<script>
   
    /* Function zPreview
 * Fungsi untuk preview sebuah report
 * I : target file, parameter yang akan dilempar, id container
 * O : report dalam bentuk HTML
 */
function zPreview(fileTarget,passParam,idCont) {
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
  // alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var res = document.getElementById(idCont);
                    res.innerHTML = con.responseText;
                    document.getElementById('detailData').style.display='none';
                    document.getElementById('awal').style.display='block';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php?proses=preview', param, respon);

}
function zExcel(ev,tujuan,passParam)
{
	judul='Report Excel';
	//alert(param);
	var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
	param+='&proses=excel';
	//alert(param);
	printFile(param,tujuan,judul,ev)
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param; 
   width='700';
   height='250';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
}

/* Posting Data
 */
function postingData(numRow) {
//    alert("masuk sini");
//    return;
    var notrans = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    var param = "notransaksi="+notrans;

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                   // alert('Posting Berhasil');
                   // javascript:location.reload(true);
                   document.getElementById('rowDt_'+numRow).style.display='none';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    if(confirm('Akan dilakukan posting untuk transaksi '+notrans+
        '\nData tidak dapat diubah setelah ini. Anda yakin?')) {
        post_response_text('kebun_slave_operasional_posting.php', param, respon);
    }
}
function detailData(numRow,ev,tipe)
{
    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    param = "proses=html&tipe="+tipe+"&notransaksi="+notransaksi;
        title="Data Detail";
        showDialog1(title,"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='kebun_slave_operasional_print_detail.php?"+param+"'></iframe>",'800','400',ev);
        var dialog = document.getElementById('dynamic1');
        dialog.style.top = '50px';
        dialog.style.left = '15%';
}
function getPeriode()
{
    var kdOrg = document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    var param = "kdOrg="+kdOrg+'&proses=getPeriode';
    post_response_text('kebun_slave_3postingtransaksi.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                   // alert('Posting Berhasil');
                   // javascript:location.reload(true);
                   document.getElementById('thnId').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }


        
    
}

function postingDat(maxRow)
{
//
	if(confirm('Anda Yakin Ingin Memposting Data ..?'))
	{
		   loopClosingFisik(1,maxRow);
		   // lockForm();
	}
	else
	{
		document.getElementById('revTmbl').disabled=false;
		return;
	}
}

function loopClosingFisik(currRow,maxRow)
{
        notrans = document.getElementById('notransaksi_'+currRow).innerHTML;
        param = "notransaksi="+notrans;
        xtipe=notrans.substr(14,3);
        if(xtipe=='PNN'){
            post_response_text('kebun_slave_panen_posting.php', param, respon);
        }else{
            post_response_text('kebun_slave_operasional_posting.php', param, respon);
        }	
	document.getElementById('rowDt_'+currRow).style.backgroundColor='orange';
       
	//lockScreen('wait');
	function respon(){
		if (con.readyState == 4) {

			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('rowDt_'+currRow).style.backgroundColor='red';
				        currRow+=1;
					if(currRow>maxRow)
					{
						document.getElementById('revTmbl').disabled=false;
						tutupProses('simpan');
					}
					else
					{
						param='';
                                                loopClosingFisik(currRow,maxRow);
					}
				}
				else {
					//alert(con.responseText);
					//return;
                                        document.getElementById('rowDt_'+currRow).style.backgroundColor='green';
                                        currRow+=1;
					if(currRow>maxRow)
					{
						document.getElementById('revTmbl').disabled=false;
						tutupProses('simpan');
					}
					else
					{
						param='';
                                                loopClosingFisik(currRow,maxRow);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
				unlockScreen();
			}
		}
	}

}
function tutupProses(x)
{
	period=document.getElementById('revTmbl');
	if(period.disabled!=true)
	{
		if (x == 'simpan') {
			//unlockScreen();
			alert("Data Telah Terposting");
			//unlockForm();
			document.getElementById('printContainer').innerHTML='';
		}
		else
		{
			unlockScreen();
		}
	}
}

</script>
<?php
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sPeriodeCari="select distinct substr(tanggal,1,7) as periode from ".$dbname.".kebun_aktifitas
//                 where kodeorg='".$_SESSION['empl']['lokasitugas']."' and jurnal=0 order by substr(tanggal,1,7) desc";
////echo $sPeriodeCari;
//$qPeriodeCari=mysql_query($sPeriodeCari) or die(mysql_error());
//while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))
//{
//   $optPeriode.="<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode']."</option>";
//}
$optOrg=$optPeriode;



    $sOrg="select distinct substr(kodeorganisasi,1,4) as kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
      tipe='KEBUN' and kodeorganisasi = '".$_SESSION['empl']['lokasitugas']."' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";
}
$optTrak="<option value=''>".$_SESSION['lang']['all']."</option>";
$trk=array("0"=>"TB","1"=>"BBT","2"=>"TBM","3"=>"TM","4"=>"PNN");
foreach($trk as $dtrk =>$lstr)
{
    $optTrak.="<option value='".$lstr."'>".$lstr."</option>";
}

$optTipe="<option value=''>All</option>";
$optTipe.="<option value='/BBT/'>BBT</option>";
$optTipe.="<option value='/TB/'>TB</option>";
$optTipe.="<option value='/TBM/'>TBM</option>";
$optTipe.="<option value='/TM/'>TM</option>";
$optTipe.="<option value='/PNN/'>PNN</option>";

$arr="##kdOrg##thnId##tipe##tanggal1##tanggal2";
?>

<!--<script language=javascript1.2 src='js/kebun_operasional.js'></script>-->

<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b>Posting Pekerjaan</b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kodeorg']?></label></td><td><select id="kdOrg" name="kdOrg" style="width:150px" onchange="getPeriode()"><?php echo $optOrg?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="thnId" name="thnId" style="width:150px" ><?php echo $optPeriode?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tipe']?></label></td><td><select id="tipe" name="tipe" style="width:150px" ><?php echo $optTipe?></select></td></tr>
    <tr>
        <td><label><?php echo $_SESSION['lang']['tanggalmulai']?></label></td>
        <td><input type="text" class="myinputtext" id="tanggal1" name="tanggal1" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td>
    </tr>
    <tr>
        <td><label><?php echo $_SESSION['lang']['tanggalsampai']?></label></td>
        <td><input type="text" class="myinputtext" id="tanggal2" name="tanggal2" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" /></td>
    </tr>

<!--<tr><td><label><?php echo $_SESSION['lang']['tipe']?></label></td><td><select id="tipeTrk" name="tipeTrk" style="width:150px" ><?php echo $optTrak?></select></td></tr>-->
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('kebun_slave_3postingtransaksi','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button></td></tr>
</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id=awal>
    <div id='printContainer' style='overflow:auto;height:350px;max-width:1220px;'>

    </div>
</div>
<div id=detailData style=display:none>
<div id=isiData>
</div>
</div>
</fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>