function getCust(nokontrak)
{
	nokontrak=document.getElementById('nokontrak').value;	
	param='method=getCust'+'&nokontrak='+nokontrak;
	tujuan='pabrik_slave_pengapalanModo.php';
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
						//alert(con.responseText);
						ar=con.responseText.split("###");
						document.getElementById('kdCust').innerHTML=ar[0];
						document.getElementById('kdbarang').innerHTML=ar[1];
						document.getElementById('kdTangki').innerHTML=ar[2];
						
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


function del(notran)
{
	
	param='method=delete'+'&notran='+notran;
	tujuan='pabrik_slave_pengapalanModo.php';
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




function edit(notran,tgl,nodo,kdKapal,transp,berat,nokontrak,kdCust,kdbarang)
{
	document.getElementById('notran').value=notran;
	document.getElementById('tgl').value=tgl;
	document.getElementById('nodo').value=nodo;
	document.getElementById('kdKapal').value=kdKapal;
	document.getElementById('transp').value=transp;
	document.getElementById('berat').value=berat;
	document.getElementById('nokontrak').value=nokontrak;
	document.getElementById('method').value='update';
	getCust(nokontrak);
	
	//document.getElementById('kdCust').value=kdCust;
	//document.getElementById('kdbarang').value=kdbarang;
}

function simpan()
{
	notran=document.getElementById('notran').value;
	tgl=document.getElementById('tgl').value;
	kodeorg=document.getElementById('kodeorg').value;
	nokontrak=document.getElementById('nokontrak').value;
	nodo=document.getElementById('nodo').value;
	kdCust=document.getElementById('kdCust').value;
	kdbarang=document.getElementById('kdbarang').value;
	kdKapal=document.getElementById('kdKapal').value;
	transp=document.getElementById('transp').value;
	berat=document.getElementById('berat').value;
	method=document.getElementById('method').value;
	kdtng=document.getElementById('kdTangki');
	kdtng=kdtng.options[kdtng.selectedIndex].value;
	
	
	if(nodo=='' || kdKapal=='' || transp=='' || nokontrak=='')
	{
		alert('Field empty');return;
	}
	
	
	param='method='+method+'&notran='+notran+'&tgl='+tgl+'&kodeorg='+kodeorg+'&nokontrak='+nokontrak+'&nodo='+nodo;
	param+='&kdCust='+kdCust+'&kdbarang='+kdbarang+'&kdKapal='+kdKapal+'&transp='+transp+'&berat='+berat+'&kdTangki='+kdtng;
	
	
	//param='regional='+regional+'&kdkegiatan='+kdkegiatan+'&rp='+rp+'&insen='+insen+'&konversi='+konversi+'&method='+method;
	
	
	tujuan='pabrik_slave_pengapalanModo.php';
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
		perSch=document.getElementById('perSch').value;
		notranSch=document.getElementById('notranSch').value;
		nokontrakSch=document.getElementById('nokontrakSch').value;
		
		param='method=loadData'+'&perSch='+perSch+'&page='+num+'&notranSch='+notranSch+'&nokontrakSch='+nokontrakSch;
		
		tujuan = 'pabrik_slave_pengapalanModo.php';
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
	notranSch=document.getElementById('notranSch').value;
	nokontrakSch=document.getElementById('nokontrakSch').value;
	param='method=loadData'+'&perSch='+perSch+'&notranSch='+notranSch+'&nokontrakSch='+nokontrakSch;
	
	tujuan='pabrik_slave_pengapalanModo.php';
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

















