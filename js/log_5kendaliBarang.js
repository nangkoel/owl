//JS 
function cariBarang(title,ev)
{
    // kosongkan();
     //setSloc('simpan');
     content= "<div>";
     content+="<fieldset>Kode/Nama Barang:<input type=text id=noBarang class=myinputtext onkeypress=\"return validat(event);\" maxlength=25><button class=mybutton onclick=goCariBarang()>Go</button> </fieldset>";
     content+="<div id=containercari style=\"height:250px;width:470px;overflow:scroll;\"></div></div>";
    //display window
    title=title+' Kodebarang:';
      width='500';
      height='300';
      showDialog1(title,content,width,height,ev);	
}


function goCariBarang()
{
    noBarang=trim(document.getElementById('noBarang').value);
    param='method=goCariBarang'+'&noBarang='+noBarang;
    tujuan = 'log_slave_5kendaliBarang.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    document.getElementById('containercari').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}


function goPickBarang(noBarang)
{
    document.getElementById('kdBarang').value=noBarang;
    closeDialog();
}





function cariBast(num)
{
    param='method=loadData';
    param+='&page='+num;
    tujuan = 'log_slave_5kendaliBarang.php';
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

function simpan()
{
    kdOrg=document.getElementById('kdOrg').value;
    kdBarang=document.getElementById('kdBarang').value;
    method=document.getElementById('method').value;

    if(kdOrg=='' || kdBarang=='')
    {
            alert('Field Was Empty');
            return;
    }

    param='kdOrg='+kdOrg+'&kdBarang='+kdBarang+'&method='+method;
    tujuan='log_slave_5kendaliBarang.php';
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
                                                    cancel();
                                                    loadData () ;
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
    document.getElementById('kdOrg').value='';
    document.getElementById('kdOrg').disabled=false;
    document.getElementById('kdBarang').value='';
    document.getElementById('method').value='insert';
    //document.location.reload();
}



function loadData () 
{
	param='method=loadData';
	tujuan='log_slave_5kendaliBarang.php';
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

function edit(kdOrg,kdBarang,noKartu)
{
    document.getElementById('kdOrg').value=kdOrg;
    document.getElementById('kdBarang').value=kdBarang;
    document.getElementById('kdOrg').disabled=true;
    document.getElementById('kdBarang').disabled=true;
    document.getElementById('method').value='update';
}



function del(kdOrg,kdBarang)
{
    if (confirm('Yakin ingin menghapus data?')){
	param='method=delete'+'&kdOrg='+kdOrg+'&kdBarang='+kdBarang;
        
	tujuan='log_slave_5kendaliBarang.php';
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
}

function validat(ev)
{
  key=getKey(ev);
  if(key==13){
        goCariBarang();
  } else {
  return tanpa_kutip(ev);	
  }	
}
