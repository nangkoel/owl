// JavaScript Document

function getKrwyn(lksiTgs,krywnId)
{
	if((lksiTgs=='')&&(krywnId==''))
        {
        kdOrg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
	param='kodeOrg='+kdOrg+'&proses=getKrywan';
        }
        else
            {
                kdOrg=lksiTgs;
                kdKry=krywnId;
                param='kodeOrg='+kdOrg+'&proses=getKrywan'+'&kdKry='+kdKry;
            }
	tujuan='vhc_slave_save_5operator.php';
	
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
                           document.getElementById('kd_karyawan').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	
}
function simpanOpt()
{
	kd_kary=document.getElementById('kd_karyawan').options[document.getElementById('kd_karyawan').selectedIndex].value;
        kdVhc=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
	statu=document.getElementById('status').options[document.getElementById('status').selectedIndex].value;
	insert=document.getElementById('proses').value;
	param='kdKry='+kd_kary+'&status='+statu+'&proses='+insert+'&kdVhc='+kdVhc;
//	alert(param);
	tujuan='vhc_slave_save_5operator.php';
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
                            document.getElementById('proses').value='insert_karyawan';
							document.getElementById('kd_karyawan').value='';
							document.getElementById('kd_karyawan').disabled=false;
							load_data();
							batalOpt();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	
}

function load_data()
{
	param='proses=load_new_data';
	tujuan='vhc_slave_save_5operator.php';
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
function cariBast(num)
{
		param='proses=load_new_data';
		param+='&page='+num;
		tujuan = 'vhc_slave_save_5operator.php';
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
function fillField(krywn_id,stat,vhc)
{
	//document.getElementById('kd_karyawan').value=krywn_id;
        //alert(loksiTugas+','+krywn_id+','+stat+','+vhc);
        //alert(krywn_id+','+stat+','+vhc);
        //document.getElementById('kdOrg').disabled=true;
	//getKrwyn(loksiTugas,krywn_id);
        //document.getElementById('kdOrg').value=loksiTugas;
        q=document.getElementById('kd_karyawan');
        for(a=0;a<q.length;a++)
            {
                if(q.options[a].value==krywn_id)
                    {
                        q.options[a].selected=true;
                    }
            }
	document.getElementById('status').value=stat;
        x=document.getElementById('kdVhc');
        for(z=0;z<x.length;z++)
            {
                if(x.options[z].value==vhc)
                    {
                        x.options[z].selected=true;
                    }
            }
        document.getElementById('kd_karyawan').disabled=true;
	document.getElementById('proses').value='update_karyawan';
}
function batalOpt()
{
    document.getElementById('kd_karyawan').disabled=false;
    //document.getElementById('kdOrg').disabled=false;
    document.getElementById('kd_karyawan').value='';
    document.getElementById('kdVhc').value='';
    document.getElementById('status').value='';
    //document.getElementById('kdOrg').value='';
    document.getElementById('proses').value='insert_karyawan';
    
}
function delOpt(noKry)
{
	nokry=noKry;
	param='kdKry='+nokry+'&proses=deleteKry';
	tujuan='vhc_slave_save_5operator.php';
			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//document.getElementById('contain').innerHTML=con.responseText;
						load_data();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
		if(confirm("Are You Sure Delete This Data!!"))
		{
			post_response_text(tujuan, param, respog);	
		}
		else
		{
			return;
		}
}
function getKary(title,ev){
content= "<div style='width:100%;'>";
content+="<fieldset>"+title+"<input type=text id=txtnamabarang class=myinputtext size=25 maxlength=35><button class=mybutton onclick=goCariKary()>Go</button> </fieldset>";
content+="<div id=containercari style='overflow:scroll;height:300px;width:520px'></div></div>";                 
width='550';
height='350';
showDialog1(title,content,width,height,ev);		
}
function goCariKary(pil){
    txt=document.getElementById('txtnamabarang').value;
    if(pil==1){
        param='proses=getKary'+'&txtNm='+txt;
    }else{
        param='proses=getVhc'+'&txtNm='+txt;
    }
    
    tujuan = 'vhc_slave_save_5operator.php';
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
function setKary(karyid,pil){
    if(pil==1){
        kar=document.getElementById('kd_karyawan');
      for(x=0;x<kar.length;x++){
        if(kar.options[x].value==karyid){
                kar.options[x].selected=true;
        }
      }
    }else{
        kar=document.getElementById('kdVhc');
      for(x=0;x<kar.length;x++){
        if(kar.options[x].value==karyid){
                kar.options[x].selected=true;
        }
      }
    }
      
      closeDialog();
}
function loadDatavhcopr (num) {
	nik=document.getElementById('nik').value;
	nmkrywn=document.getElementById('nmkrywn').value;
	kdkndrn=document.getElementById('kdkndrn').value;
	
	param='proses=loadDatavhcopr';
        param+='&nik='+nik+'&nmkrywn='+nmkrywn+'&kdkndrn='+kdkndrn;
        param+='&page='+num;
	tujuan='vhc_slave_save_5operator.php';
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
function searchKrywnCari(title,content,ev)
{
        
        width='500';
        height='400';
        showDialog2(title,content,width,height,ev);
        //findBrg();
        //alert('asdasd');
}
function findKrywn()
{
    nmKrywn=document.getElementById('nmKrywn').value;

    param='nmKrywn='+nmKrywn+'&proses=getKrywn';
    //alert(param);
    tujuan='vhc_slave_save_5operator.php';
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
                                           document.getElementById('containerBarang').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
         }  

}
function setData(karyawanid,nik,namakaryawan,lokasitugas)
{
    //alert(karyawanid+'-'+nik+'-'+'-'+namakaryawan+'-'+lokasitugas);
    ldata=document.getElementById('kd_karyawan');
    for(adr=0;adr<ldata.length;adr++)
    {
         if(ldata.options[adr].value==karyawanid)
            {
                ldata.options[adr].selected=true;
            }
    }

    closeDialog2();
}