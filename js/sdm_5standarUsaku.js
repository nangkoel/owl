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
						//cancelIsi();
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
        thnBudget=document.getElementById('thnBudgetHead').options[document.getElementById('thnBudgetHead').selectedIndex].value;
        kdGol=document.getElementById('kdGOlHead').options[document.getElementById('kdGOlHead').selectedIndex].value;
        
        if(thnBudget!='')
        {
        param+='&thnBudget='+thnBudget;
        }
        if(kdGol!='')
        {
        param+='&kdGol='+kdGol;
        }
        
	tujuan='sdm_slave_5standardUsaku';
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
                                          cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function fillField(thnbdget,kdgol)
{
	
	param='method=getData'+'&thnBudget='+thnbdget+'&kdGol='+kdgol;
	tujuan='sdm_slave_5standardUsaku';
        post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //$arr="##thnBudget##kdGol##region##tktPes##tksi##airport##visa##byaLain##method";
					ar=con.responseText.split("###");
                                    document.getElementById('thnBudget').value=ar[0];
                                    l=document.getElementById('kdGol');
                                    
                                    for(a=0;a<l.length;a++)
                                    {
                                        if(l.options[a].value==ar[1])
                                        {
                                        l.options[a].selected=true;
                                        }
                                    }

					document.getElementById('ungSaku').value=ar[2];
					document.getElementById('ungMkn').value=ar[3];
                                        document.getElementById('htel').value=ar[4];
					document.getElementById('method').value="update";
					document.getElementById('thnBudget').disabled=true;
                                        document.getElementById('kdGol').disabled=true;
                                       
					 // document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function cancelIsi()
{
    
	document.getElementById('kdGol').value='';
	document.getElementById('ungSaku').value='0';
	document.getElementById('htel').value='0';
	document.getElementById('ungMkn').value='0';
        document.getElementById('thnBudget').value='';
	document.getElementById('method').value="insert";
	document.getElementById('thnBudget').disabled=false;
	document.getElementById('kdGol').disabled=false;
        
}
function delData(thnbdget,kdgol)
{
	param='method=delData';
        param+='&thnBudget='+thnbdget+'&kdGol='+kdgol;
	tujuan='sdm_slave_5standardUsaku';
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
function cariPage(num)
{
                
		param='method=loadData';
		param+='&page='+num;
                thnBudget=document.getElementById('thnBudgetHead').options[document.getElementById('thnBudgetHead').selectedIndex].value;
                kdGol=document.getElementById('kdGOlHead').options[document.getElementById('kdGOlHead').selectedIndex].value;
                
                if(thnBudget!='')
                {
                    param+='&thnBudget='+thnBudget;
                }
                if(kdGol!='')
                {
                    param+='&kdGol='+kdGol;
                }
              
                tujuan='sdm_slave_5standardUsaku.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('container').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}