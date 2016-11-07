function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
    width='600';
    height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2(title,content,width,height,ev); 	
}
function excel(ev,per,kom,org)
{
        param='method=excel'+'&per='+per+'&kom='+kom+'&org='+org;
        //alert(param);
        tujuan='sdm_slave_3plExcel.php';
        judul='Print Excel';		
        printFile(param,tujuan,judul,ev)	
}


function saveDetail()
{
	per=document.getElementById('per').value;
	kom=document.getElementById('kom').value;
	kar=document.getElementById('kar').value;
	jum=document.getElementById('jum').value;
	
	param='method=saveDetail'+'&per='+per+'&kom='+kom+'&kar='+kar+'&jum='+jum;
	//alert(param);
	tujuan='sdm_slave_3pl.php';
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
					else 
					{
						
						//lockHeader();
						//document.getElementById('detailForm').style.display='block';
						loadDataDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}





function cancelFormBarang()
{
	document.getElementById('nobpb').value='';
	document.getElementById('nopo').value='';
	document.getElementById('nopp').value='';
	document.getElementById('kodebarang').value='';
	document.getElementById('kurs').value='';
	document.getElementById('namabarang').value='';
	document.getElementById('jumlah').value='';
	document.getElementById('satuan').value='';
	document.getElementById('matauang').value='IDR';
	document.getElementById('hargasatuan').value='';
	
}

		

function loadDataDetail()
{
	//alert('masuk');
        org=document.getElementById('org').value;
	per=document.getElementById('per').value;
	kom=document.getElementById('kom').value;
	param='method=loadDetail'+'&per='+per+'&kom='+kom+'&org='+org;
	//alert(param);
	tujuan='sdm_slave_3pl.php';
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
							//alert(con.responseText);
							//return;
							//document.getElementById('contentDetail').innerHTML=con.responseText;
							document.getElementById('containList').style.display='block';
							document.getElementById('containList').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
}

		


function cancel()
{
	document.location.reload();
}






function edit(per,kom,org)
{
    //alert(org);
	tabAction(document.getElementById('tabFRM0'),0,'FRM',1);	
	document.getElementById('header').style.display='block';
	document.getElementById('per').value=per;
	document.getElementById('kom').value=kom;
        document.getElementById('org').value=org;
	document.getElementById('displayall').style.display='block';
	//document.getElementById('detailForm').style.display='block';
	lockHeader();
	loadDataDetail();	
}


function saveHeader()
{
        org=document.getElementById('org').value;
	per=document.getElementById('per').value;
	kom=document.getElementById('kom').value;
	param='method=saveHeader'+'&per='+per+'&kom='+kom+'&org='+org;
	tujuan='sdm_slave_3pl.php';
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
					else 
					{
						lockHeader();
						document.getElementById('displayall').style.display='block';
						//document.getElementById('detailForm').style.display='block';
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}


function delHead(per,kom,org)
{
	param='method=delHead'+'&per='+per+'&kom='+kom+'&org='+org;
	tujuan='sdm_slave_3pl.php';
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
					else 
					{
						loadData();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}



function cariBast(num)
{
	
	perSch=document.getElementById('perSch').value;
	
	param='method=loadData'+'&perSch='+perSch+'&page='+num;
     
	tujuan = 'sdm_slave_3pl.php';
	post_response_text(tujuan, param, respog);			
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					//displayList();
					
					document.getElementById('container').innerHTML=con.responseText;
					//loadData();
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}



function loadData () 
{
	
	perSch=document.getElementById('perSch').value;
	param='method=loadData'+'&perSch='+perSch;
	//alert(param);	
	tujuan='sdm_slave_3pl.php';
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




function lockHeader()
{
	document.getElementById('saveHeader').disabled=true;
	document.getElementById('cancelHeader').disabled=true;
	document.getElementById('per').disabled=true;
	document.getElementById('kom').disabled=true;
        document.getElementById('org').disabled=true;
}




function DelDetail(per,kar,kom)
{
	param='method=deleteDetail'+'&kar='+kar+'&per='+per+'&kom='+kom;
	tujuan='sdm_slave_3pl.php';
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
					else 
					{
						loadDataDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}
}



