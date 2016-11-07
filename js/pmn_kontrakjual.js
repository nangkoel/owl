// JavaScript Document


//update ind. penambahan js posting 22-01-2014



function cekDate()
{
	tglKrm=document.getElementById('tglKrm').value;
	tglSd=document.getElementById('tglSd').value;
	param='method=cekDate'+'&tglKrm='+tglKrm+'&tglSd='+tglSd;
	//alert(param);
	tujuan='pmn_kontrakjual_slave.php';
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
						if(con.responseText=='a')
						{
							alert('Date not valid');
							document.getElementById('tglSd').value='';
						}
						
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}



function posting(noKntrk)
{
	param='method=posting'+'&noKntrk='+noKntrk;
	tujuan='pmn_kontrakjual_slave.php';
	if(confirm('Posting??'))
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
						loadNewData()
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}

function loadNewData()
{
        param='method=LoadNew';
        tujuan='pmn_kontrakjual_slave.php';
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
                                                        document.getElementById('containerlist').innerHTML=con.responseText;
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
                param='method=LoadNew';
                param+='&page='+num;
                tujuan = 'pmn_kontrakjual_slave.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('containerlist').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function saveKP()
{
        noKntrk=document.getElementById('noKtrk').value;
        custid=document.getElementById('custId').value;
        tglkntr=document.getElementById('tlgKntrk').value;
        kdbrg=document.getElementById('kdBrg').value;
        satuan=document.getElementById('stn').value;
        HrgStn=document.getElementById('HrgStn').value;
        tBlg=document.getElementById('tBlg').innerHTML;
        qty=document.getElementById('jmlh').value;
        tglKrm=document.getElementById('tglKrm').value;
        tglSd=document.getElementById('tglSd').value;
        tlransi=document.getElementById('tlransi').value;
        noDo=document.getElementById('noDo').value;
        kualitas=document.getElementById('kualitas').value;
        syrtByr=document.getElementById('syrtByr').value;
        tndtng=document.getElementById('tndtng').value;
        tmbngn=document.getElementById('tmbngn').value;
        cttn1=document.getElementById('cttn1').value;
        cttn2=document.getElementById('cttn2').value;
        cttn3=document.getElementById('cttn3').value;
        cttn4=document.getElementById('cttn4').value;
        cttn5=document.getElementById('cttn5').value;
        othCttn=document.getElementById('othCttn').value;
        kdPt=document.getElementById('kdPt').value;
        kurs=document.getElementById('kurs').value;
		
		ppn=document.getElementById('ppn').value;
		lamamuat=document.getElementById('lamamuat').value;
		pelabuhan=document.getElementById('pelabuhan').value;
		demurage=document.getElementById('demurage').value;
		
		met=document.getElementById('method').value;
        
		
		//if(($noKntrk=='')||($custId=='')||($kdBrg=='')||($HrgStn=='')||($tBlg=='')||($qty=='')||($tlgKntrk='')||($satuan='')||($kualitas='')||($tglKrm='')||($tglSd=''))
		
		
		
		param='noKntrk='+noKntrk+'&custId='+custid+'&tlgKntrk='+tglkntr+'&kdBrg='+kdbrg;
        param+='&satuan='+satuan+'&tBlg='+tBlg+'&qty='+qty+'&tglKrm='+tglKrm+'&tglSd='+tglSd;
        param+='&kualitas='+kualitas+'&syrtByr='+syrtByr+'&tmbngn='+tmbngn;
        param+='&cttn1='+cttn1+'&cttn2='+cttn2+'&cttn3='+cttn3+'&cttn4='+cttn4+'&cttn5='+cttn5+'&HrgStn='+HrgStn;
        param+='&method='+met+'&tndtng='+tndtng+'&noDo='+noDo+'&tlransi='+tlransi+'&othCttn='+othCttn+'&kdPt='+kdPt+'&kurs='+kurs;
		param+='&ppn='+ppn+'&lamamuat='+lamamuat+'&pelabuhan='+pelabuhan+'&demurage='+demurage;
        tujuan='pmn_kontrakjual_slave.php';
       // alert(param);
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
                                                        //document.getElementById('stn').innerHTML=con.responseText;
                                                        loadNewData();
                                                        clearFrom();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 
         if(confirm("Are you sure?"))	
         {
                post_response_text(tujuan, param, respog);
         }

}

function clearFrom()
{
        document.getElementById('noKtrk').value='';
        document.getElementById('custId').value='';
        document.getElementById('tlgKntrk').value='';
        document.getElementById('kdBrg').value='';
        document.getElementById('HrgStn').value='';

        document.getElementById('jmlh').value='';
        document.getElementById('tglKrm').value='';
        document.getElementById('tglSd').value='';
        document.getElementById('tlransi').value='';
        document.getElementById('noDo').value='';
        document.getElementById('kualitas').value='';
        document.getElementById('syrtByr').value='';
        document.getElementById('tndtng').value='';
        document.getElementById('tmbngn').value='';
        document.getElementById('cttn1').value='';
        document.getElementById('cttn2').value='';
        document.getElementById('cttn3').value='';
        document.getElementById('cttn4').value='';
        document.getElementById('cttn5').value='';
        document.getElementById('othCttn').value='';
        document.getElementById('kdPt').value='';
        document.getElementById('method').value='insert';
        document.getElementById('noKtrk').disabled=false;
        document.getElementById('nmPerson').innerHTML='';
        document.getElementById('fax').innerHTML='';
        document.getElementById('stn').innerHTML='';
        document.getElementById('tBlg').innerHTML='';
		
		document.getElementById('ppn').value='0';
		document.getElementById('lamamuat').value='';
		document.getElementById('pelabuhan').value='';
		document.getElementById('demurage').value='';
}
function getSatuan(kdbrg,cust,sat)
{
        if((kdbrg==0)||(cust==0)||(sat==0))
        {
                kdBrg=document.getElementById('kdBrg').value;
                param='kdBrg='+kdBrg+'&method=getSatuan';
        }
        else
        {
                kdBrg=kdbrg;
                satuan=sat;
                param='kdBrg='+kdBrg+'&method=getSatuan'+'&satuan='+satuan;
        }

        //alert(param);
        tujuan='pmn_kontrakjual_slave.php';

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
                                                        document.getElementById('stn').innerHTML=con.responseText;
                                                        getDataCust(cust);
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
function copyFromLast()
{
        param='method=getLastData';
        tujuan='pmn_kontrakjual_slave.php';
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
                                                        document.getElementById('noKtrk').disabled=false;
                                                        ar=con.responseText.split("###");
                                                        document.getElementById('noKtrk').value=ar[0];
                                                        document.getElementById('custId').value=ar[1];
                                                        document.getElementById('tlgKntrk').value=ar[2];
                                                        document.getElementById('kdBrg').value=ar[3];
                                                        document.getElementById('HrgStn').value=ar[4];
                                                        document.getElementById('tBlg').value=ar[5];
                                                        document.getElementById('jmlh').value=ar[6];
                                                        document.getElementById('tglKrm').value=ar[7];
                                                        document.getElementById('tglSd').value=ar[8];
                                                        document.getElementById('tlransi').value=ar[9];
                                                        document.getElementById('noDo').value=ar[10];
                                                        document.getElementById('kualitas').value=ar[11];
                                                        document.getElementById('syrtByr').value=ar[12];
                                                        document.getElementById('tndtng').value=ar[13];
                                                        document.getElementById('tmbngn').value=ar[14];
                                                        document.getElementById('cttn1').value=ar[15];
                                                        document.getElementById('cttn2').value=ar[16];
                                                        document.getElementById('cttn3').value=ar[17];
                                                        document.getElementById('cttn4').value=ar[18];
                                                        document.getElementById('cttn5').value=ar[19];
                                                        document.getElementById('othCttn').value=ar[20];
                                                        getSatuan(ar[3],ar[1],ar[21]);
                                                        document.getElementById('kdPt').value=ar[22];

                                                        //document.getElementById('stn').value;
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
function getDataCust(dt)
{
        if(dt==0)
        {
                custId=document.getElementById('custId').value;
        }
        else
        {
                custId=dt;
        }
        param='method=getCust'+'&custId='+custId;
        tujuan='pmn_kontrakjual_slave.php';
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
                                                        ar=con.responseText.split("###");
                                                        document.getElementById('nmPerson').innerHTML="Contact Person : &nbsp;"+ar[0];
                                                        document.getElementById('fax').innerHTML=", Fax No.: &nbsp;"+ar[1];
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
function fillField(nokntrk)
{
        noKntrk=nokntrk;
        param='method=getEditData'+'&noKntrk='+noKntrk;
        tujuan='pmn_kontrakjual_slave.php';
        tabAction(document.getElementById('tabFRM0'),0,'FRM',1);
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
                                                        ar=con.responseText.split("###");
                                                        document.getElementById('noKtrk').value=ar[0];
                                                        document.getElementById('custId').value=ar[1];
                                                        document.getElementById('tlgKntrk').value=ar[2];
                                                        document.getElementById('kdBrg').value=ar[3];
                                                        document.getElementById('HrgStn').value=ar[4];
                                                        document.getElementById('tBlg').innerHTML=ar[5];
                                                        document.getElementById('jmlh').value=ar[6];
                                                        document.getElementById('tglKrm').value=ar[7];
                                                        document.getElementById('tglSd').value=ar[8];
                                                        document.getElementById('tlransi').value=ar[9];
                                                        document.getElementById('noDo').value=ar[10];
                                                        document.getElementById('kualitas').value=ar[11];
                                                        document.getElementById('syrtByr').value=ar[12];
                                                        document.getElementById('tndtng').value=ar[13];
                                                        document.getElementById('tmbngn').value=ar[14];
                                                        document.getElementById('cttn1').value=ar[15];
                                                        document.getElementById('cttn2').value=ar[16];
                                                        document.getElementById('cttn3').value=ar[17];
                                                        document.getElementById('cttn4').value=ar[18];
                                                        document.getElementById('cttn5').value=ar[19];
                                                        document.getElementById('othCttn').value=ar[20];
                                                        document.getElementById('kdPt').value=ar[22];
                                                        document.getElementById('kurs').value=ar[23];
														
														document.getElementById('ppn').value=ar[24];
                                                        document.getElementById('lamamuat').value=ar[25];
                                                        document.getElementById('pelabuhan').value=ar[26];
                                                        document.getElementById('demurage').value=ar[27];
														
														
                                                        getSatuan(ar[3],ar[1],ar[21]);
                                                        
                                                        //document.getElementById('stn').value;
                                                        document.getElementById('noKtrk').disabled=true;
														document.getElementById('method').value='update';
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

function delData(nokontrk)
{
        noKntrk=nokontrk;
        param='method=dataDel'+'&noKntrk='+noKntrk;
        // alert(param);
        tujuan='pmn_kontrakjual_slave.php';
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
                                                        //document.getElementById('stn').innerHTML=con.responseText;
                                                        //clearFrom();
                                                        //tabAction(document.getElementById('tabFRM0'),0,'FRM',1);
														
                                                        document.getElementById('method').value='insert';
														loadNewData();

                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 
         if(confirm("Are you sure?"))	
         {
                post_response_text(tujuan, param, respog);
         }

}
function cariNoKntrk()
{
        txtSearch=document.getElementById('txtnokntrk').value;
        param='txtSearch='+txtSearch+'&method=cariNokntrk';
        tujuan='pmn_kontrakjual_slave.php';
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
                                                        //document.getElementById('stn').innerHTML=con.responseText;
                                                        //clearFrom();
                                                        //tabAction(document.getElementById('tabFRM0'),0,'FRM',1);
                                                        //tabAction(document.getElementById('tabFRM1'),0,'FRM',1);	
                                                        document.getElementById('containerlist').innerHTML=con.responseText;

                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 
}