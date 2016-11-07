	function batal()
	{
		document.getElementById('kodeorgLap').value='';
		document.getElementById('tglLap').value='';	
		document.getElementById('produkLap').value='';	
		document.getElementById('printContainer').innerHTML='';	
	}


function cancel()
{
	document.location.reload();
}

function getForm()
{
	document.getElementById('editForm').style.display='none';
	kodeorg=document.getElementById('kodeorg').value;
	produk=document.getElementById('produk').value;
	
	
	if(produk=='')
	{
		//document.getElementById('form').style.display='none';
		cancel();
	}
	
		/*document.getElementById('merah').style.display='none';
		document.getElementById('kuning').style.display='none';
		document.getElementById('hijau').style.display='none';*/
	
	param='method=getForm'+'&kodeorg='+kodeorg+'&produk='+produk;
	tujuan='pabrik_slave_kelengkapanloses.php';
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
						document.getElementById('form').style.display='block';
						document.getElementById('isi').innerHTML=con.responseText;
						//document.getElementById('kar').innerHTML=							
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);  
                }
      }	
     } 
}





function saveAll(maxRow)
{     
	maxf=maxRow;
	loopsave(1,maxRow);
}

function loopsave(currRow,maxRow)
{
	kodeorg=document.getElementById('kodeorg').value;
	tgl=document.getElementById('tgl').value;
	//produk=document.getElementById('produk').value;
	inp=document.getElementById('inp'+currRow).value;
	id=document.getElementById('id'+currRow).value;
	//method=document.getElementById('method').value;
	//imp=trim(document.getElementById('premi'+currRow).innerHTML);
	
	if(kodeorg=='' || tgl=='' || produk=='')
	{
		alert("Field Empty");return;
	}	
    else
	{  
	    param='kodeorg='+kodeorg+'&tgl='+tgl+'&inp='+inp+'&id='+id;
		param+="&method=savedata";
		
		//alert(param);
		tujuan = 'pabrik_slave_kelengkapanloses.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('row'+currRow).style.backgroundColor='cyan';
		//lockScreen('wait');
	}
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					document.getElementById('row'+currRow).style.display='none';
                    currRow+=1;
					sekarang=currRow;
                    if(currRow>maxRow)
					{
						//alert('Done');
						//unlockScreen();
						cancel();
						//document.getElementById('infoDisplay').innerHTML='';
					}  
					else
					{
						loopsave(currRow,maxRow);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
                               // document.getElementById('lanjut').style.display='';
				//unlockScreen();
			}
		}
	}		
	
}



function del(kodeorg,tgl,id)
{
	
	param='method=delete'+'&kodeorg='+kodeorg+'&tgl='+tgl+'&id='+id;
	tujuan='pabrik_slave_kelengkapanloses.php';
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




function edit(kodeorgEdit,tglEdit,produkEdit,barangEdit,inpEdit,idEdit)
{
	document.getElementById('tgl').value='';
	document.getElementById('produk').value='';
	document.getElementById('editForm').style.display='block';
	document.getElementById('form').style.display='none';
	document.getElementById('kodeorgEdit').value=kodeorgEdit;
	document.getElementById('tglEdit').value=tglEdit;
	document.getElementById('produkEdit').value=produkEdit;
	document.getElementById('barangEdit').value=barangEdit;
	document.getElementById('inpEdit').value=inpEdit;
	document.getElementById('idEdit').value=idEdit;
}

function saveEdit ()
{
	
	kodeorgEdit=document.getElementById('kodeorgEdit').value;
	tglEdit=document.getElementById('tglEdit').value;
	produkEdit=document.getElementById('produkEdit').value;
	barangEdit=document.getElementById('barangEdit').value;
	inpEdit=document.getElementById('inpEdit').value;
	idEdit=document.getElementById('idEdit').value;
	
	param='method=update'+'&kodeorgEdit='+kodeorgEdit+'&tglEdit='+tglEdit+'&idEdit='+idEdit+'&inpEdit='+inpEdit;
	tujuan='pabrik_slave_kelengkapanloses.php';
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





function cariBast(num)
{
		tglsch=document.getElementById('tglsch').value;
		kodeorg=document.getElementById('kodeorg').value;
		param='method=loadData'+'&tglsch='+tglsch+'&kodeorg='+kodeorg+'&page='+num;
		tujuan = 'pabrik_slave_kelengkapanloses.php';
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
	tglsch=document.getElementById('tglsch').value;
	kodeorg=document.getElementById('kodeorg').value;
	param='method=loadData'+'&tglsch='+tglsch+'&kodeorg='+kodeorg;
	//alert(param);	
	tujuan='pabrik_slave_kelengkapanloses.php';
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

















