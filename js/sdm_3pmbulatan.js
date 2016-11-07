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
    post_response_text('kebun_slave_3updategajibjr.php', param, respon);
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

function getPeriodeGaji()
{
    var kdOrg = document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    var param = "kdOrg="+kdOrg+'&proses=getPeriodeGaji';
    post_response_text('sdm_slave_3pembulatangaji.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                   document.getElementById('periodeGaji').innerHTML=con.responseText;
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
	if(confirm('Anda Yakin Ingin Menyimpan Semua Data ..?'))
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

function loopClosingFisik(currRow,maxRow){
    
            //pph=trim(document.getElementById('pph_'+currRow).innerHTML);
        karyId = document.getElementById('karyId_'+currRow).value;
        simpnan = document.getElementById('simpanan_'+currRow).value;
        tmbhn = document.getElementById('penambah_'+currRow).value;
        prd=document.getElementById('periodeGaji');
        prd=prd.options[prd.selectedIndex].value;
        kdorg=document.getElementById('kdOrg');
        kdorg=kdorg.options[kdorg.selectedIndex].value;
        param ="karyId="+karyId+'&proses=updateData'+'&penambah='+tmbhn;
        param+="&simpanan="+simpnan+"&periodeGaji="+prd+"&kdOrg="+kdorg;
        
         //alert(param);return;
        
        post_response_text('sdm_slave_3pembulatangaji.php', param, respon);
	document.getElementById('rowDt_'+currRow).style.display='none';
       
       
    
	//lockScreen('wait');
	function respon(){
		if (con.readyState == 4) {

			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('rowDt_'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					//alert(con.responseText);
					//return;
                                        
                                        err=con.responseText;
                                        if(err=="1"){
                                            document.getElementById('rowDt_'+currRow).style.backgroundColor='red';
                                        }else{
                                            document.getElementById('rowDt_'+currRow).style.display='none';
                                        }
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
			alert("Data Telah Tersimpan");
			//unlockForm();
			document.getElementById('printContainer').innerHTML='';
		}
		else
		{
			unlockScreen();
		}
	}
}