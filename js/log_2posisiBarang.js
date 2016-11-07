
function batal()
{
	document.location.reload();
	
}



function cariNoPo(title,ev)
{
	content= "<div>";
	content+="<fieldset>No PO:<input type=text id=noPo class=myinputtext onkeypress=\"return validat(event);\" maxlength=25><button class=mybutton onclick=goCariPo()>Go</button><br>\n\
                    <fieldset><legend>Note</legend>Hanya akan menampilkan PO yang sudah dibuat BPB</fieldset></fieldset>";
	content+="<div id=containercari style=\"height:300px;width:735px;overflow:scroll;\"></div></div>";
	//display window
	title=title+' PO:';
	width='380';
	height='350';
	showDialog1(title,content,width,height,ev);	
}



function goCariPo()
{
	noPo=trim(document.getElementById('noPo').value);
	if(noPo.length<3)
	{   
		alert('Text too short');
		return;
	}
	else
	{   
		param='method=goCariPo'+'&noPo='+noPo;
		tujuan = 'log_slave_2posisiBarang.php';
		post_response_text(tujuan, param, respog);			
	}
	//alert(param);
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



function goPickPO(nopo)
{
        document.getElementById('nopo').value=nopo;
		closeDialog();
}


/*
function cari()
{
	nopo=trim(document.getElementById('nopo').value);
	param='nopo='+nopo+'&method=loadData';
	//param='txt='+txt+'&tgl='+tgl+'&method=loadData';
	tujuan='log_slave_2posisiBarang.php';
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
                                                                
								document.getElementById('container').innerHTML=con.responseText;
							}
						}
						else {
							busy_off();
							error_catch(con.status);
						}
				  }	
		 }
		 post_response_text(tujuan, param, respog);
}







function cariBast(num)
{
		txt=trim(document.getElementById('txt').value);
		
	
		
		param='method=loadData';
		param+='&page='+num+'&txt='+txt;
		
		//param='txt='+txt+'&tgl='+tgl+'&status='+status+'&method=loadData';
		
		
		tujuan = 'log_slave_2posisiBarang.php';
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
	param='method=loadData';
	tujuan='log_slave_2posisiBarang.php';
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
*/


function validat(ev)
{
  key=getKey(ev);
  if(key==13){
        goCariPo();
  } else {
  return tanpa_kutip(ev);	
  }	
}
