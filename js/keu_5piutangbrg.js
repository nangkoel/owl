//JS 
function simpan(){
	compId=document.getElementById('idcom');
	compId=compId.options[compId.selectedIndex].value;
	kdBrg=document.getElementById('kdbrg');
	kdBrg=kdBrg.options[kdBrg.selectedIndex].value;
 	method=document.getElementById('method').value;
 	
	param='kdbrg='+kdBrg+'&idcom='+compId+'&method='+method;
	if(method=='update'){
 		oldDtid=document.getElementById('oldId').value;
        oldDtbrg=document.getElementById('oldBrgId').value;
        param+='&oldId='+oldDtid+'&oldBrgId='+oldDtbrg;
 	}
	tujuan='keu_slave_5piutangbrg.php';
    post_response_text(tujuan, param, respog);		
	function respog(){
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							cancel();

						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}
					


function cancel(){
	//document.location.reload();
	l=document.getElementById('idcom');
    for(a=0;a<l.length;a++){
            if(l.options[a].value=='')
                {
                    l.options[a].selected=true;
                }
    }
    l2=document.getElementById('kdbrg');
    for(a=0;a<l2.length;a++){
            if(l2.options[a].value=='')
                {
                    l2.options[a].selected=true;
                }
    }
    document.getElementById('method').value='insert';
    document.getElementById('oldId').value='';
    document.getElementById('oldBrgId').value='';
    loadData(0);
}

function loadData (num) {	 
	param='method=loadData';
    param+='&page='+num;
	tujuan='keu_slave_5piutangbrg.php';
    post_response_text(tujuan, param, respog);
	function respog()
	{
              if(con.readyState==4)
              {
                    if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                   // alert(con.responseText);
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

function edit(idcom,kdbrg){
	l=document.getElementById('idcom');
    for(a=0;a<l.length;a++){
            if(l.options[a].value==idcom)
                {
                    l.options[a].selected=true;
                }
    }
    l2=document.getElementById('kdbrg');
    for(a=0;a<l2.length;a++){
            if(l2.options[a].value==kdbrg)
                {
                    l2.options[a].selected=true;
                }
    }
    document.getElementById('oldId').value=idcom;
    document.getElementById('oldBrgId').value=kdbrg;
    document.getElementById('method').value='update';
}
 


function del(idcom,kdbrg){
	param='method=delete';
	param+='&kdbrg='+kdbrg+'&idcom='+idcom;
	//alert(param);
	tujuan='keu_slave_5piutangbrg.php';
	if(confirm("Anda yaking menghapus data ini?")){
		post_response_text(tujuan, param, respog);		
	}
	function respog()
	{
		  if(con.readyState==4)
		  {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else 
					{
						loadData(0);
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

} 



