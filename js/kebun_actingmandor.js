// JavaScript Document
function saveFranco(fileTarget,passParam) {
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
	//alert(param);
  //alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
						loadData();
						cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php', param, respon);

}
function loadData()
{
	param='method=loadData';
	tujuan='kebun_slave_actingmandor';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
					  document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function fillField(prd,kary,nikkar,afd){
    l=document.getElementById('periode');
    l.disabled=true;
    for(a=0;a<l.length;a++){
        if(l.options[a].value==prd)
            {
                l.options[a].selected=true;
            }
    }
    lw=document.getElementById('nikMandor');
    for(a=0;a<lw.length;a++){
        if(lw.options[a].value==kary)
            {
                lw.options[a].selected=true;
            }
    }
    lw.disabled=true;
    lw2=document.getElementById('nikMandorAct');
    for(a=0;a<lw2.length;a++){
        if(lw2.options[a].value==nikkar)
            {
                lw2.options[a].selected=true;
            }
    }
	lw3=document.getElementById('afdId');
    for(a=0;a<lw3.length;a++){
        if(lw3.options[a].value==afd)
            {
                lw3.options[a].selected=true;
            }
    }
}

function cancelIsi()
{
	document.getElementById('nikMandor').value='';
	document.getElementById('nikMandorAct').value='';
	document.getElementById('method').value="insert";
	document.getElementById('periode').disabled=false;
	document.getElementById('nikMandor').disabled=false;
}
function delData(prd,kary)
{
	param='method=delData'+'&periode='+prd+'&nikMandor='+kary;
	tujuan='kebun_slave_actingmandor';
	if(confirm("Anda yakin ingin menghapus"))
    {
		post_response_text(tujuan+'.php', param, respon);
	}
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
					  loadData();
					  cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}