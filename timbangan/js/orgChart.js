/*
 * @uthor:nangkoel@gmail.com
 * Indonesia 2009
 */

 activeOrg='';
 orgVal   ='';
 clos 	  =1;//this will STOP on the #9th child
 function saveOrg()
 {
 	_orgcode    = trim(document.getElementById('orgcode').value);
	_orgname    = trim(document.getElementById('orgname').value);
	_orgtype    = trim(document.getElementById('orgtype').value);
	_orgadd     = trim(document.getElementById('orgadd').value);
	_orgcity    = trim(document.getElementById('orgcity').value);
	_orgcountry = document.getElementById('orgcountry').options[document.getElementById('orgcountry').selectedIndex].value;
	_orgzip     = trim(document.getElementById('orgzip').value);
	_orgtelp    = trim(document.getElementById('orgtelp').value);
	_detail     = trim(document.getElementById('orgdetail').value);
//response++++++++++++++++++++++++++++++++++++++++
	   function respog(){
	   	//save active org on memory incase slow server response
			id         = activeOrg;
			newCaption = _orgcode;
	      if(con.readyState==4)
	      {
		        if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						if (id == 'HQ') {
						//just reload when org is HQ
						window.location.reload();
						}
						else if(id.lastIndexOf('_new')>-1)
						{
						  if (clos<9) {
						  	nex=clos+1;
						  	ne = "<li class=mmgr>";
						  	ne += "<img title=expand class=arrow src='images/foldc_.png' height=17px  onclick=show_sub('gr" + _orgcode + "',this);>";
						  	ne += "<a class=elink id='el" + _orgcode + "'  onclick=\"javascript:activeOrg=this.id;orgVal='" + orgVal + "';getCurrent('" + _orgcode + "');setpos('inputorg',event);\">" + _orgcode + "</a>";
						  	ne += "<ul id=gr" + _orgcode + " style='display:none;'>";
						  	ne += "<div id=main" + _orgcode + ">";
						  	ne += "</div>";
						  	ne += "<li class=mmgr>";
						  	ne += "<a id='" + _orgcode + "_new' class=elink title='Create Child'  onclick=\"javascript:orgVal='" + _orgcode + "';clos="+nex+";activeOrg='" + _orgcode + "_new';setpos('inputorg',event);\">New Org<a>";
						  	ne += "</li>";
						  	ne += "</ul>";
							ne += "</li>";
						  }
						  else
						  {
						  	ne = "<li class=mmgr>";
						  	ne += "<img title=expand class=arrow src='images/menu/arrow_8.gif'>";
						  	ne += "<a class=elink id='el" + _orgcode + "'  onclick=\"javascript:activeOrg=this.id;orgVal='" + orgVal + "';getCurrent('" + _orgcode + "');setpos('inputorg',event);\">" + _orgcode + "</a>";
                            ne += "</li>";					  	
						  }						
                          //alert('main'+orgVal);
						   document.getElementById('main'+orgVal).innerHTML+=ne;							
						}
						else {
							document.getElementById(id).innerHTML = newCaption;
							clearForm();
						}
					  hideById('inputorg');
					  clearForm();	
					}
				}
				else {busy_off();error_catch(con.status);}	
	      }	
	   }
//++++++++++++++++++++++++++++++++++++++++++++++++

	if(_orgcode.length==0 || _orgname.length==0)
	{
		alert('Org. Code and Org.Name is NULL');
	}		
	else
	{
		if(confirm('Save new Organization, Are you sure..?'))
		{
			param ='parent='	+orgVal;
			param+='&orgcode='	+_orgcode;
			param+='&orgname='	+_orgname;
			param+='&orgtype='	+_orgtype;
			param+='&orgadd='	+_orgadd;
			param+='&orgcity='	+_orgcity;
			param+='&orgcountry='+_orgcountry;												
			param+='&orgzip='	+_orgzip;	
			param+='&orgtelp='	+_orgtelp;
			param+='&orgdetail='+_detail;						
		  post_response_text('slave_saveNewOrg.php', param, respog);
	      //alert(param);
	   }	
	}
 }
 
 function clearForm()
 {
  	document.getElementById('orgcode').value ='';
	document.getElementById('orgname').value ='';
	document.getElementById('orgtype').value ='';
	document.getElementById('orgadd').value  ='';
	document.getElementById('orgcity').value ='';
    document.getElementById('orgzip').value  ='';
	document.getElementById('orgtelp').value ='';
 }


function getCurrent(code)
{
	param='code='+code;
	post_response_text('slave_getCurrentOrg.php', param, respon);
   function respon(){
      if(con.readyState==4)
      {
	        if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					if (con.responseText != '-1') {
						//alert(con.responseText);
						fillForm(con.responseText);
					}
					else 
						clearForm();	  
				}
			}
			else {busy_off();error_catch(con.status);}	
      }	
   }	
  function  fillForm(arrtex)
  {
  	arr=arrtex.split('|');
  	document.getElementById('orgcode').value =arr[0];
	document.getElementById('orgname').value =arr[1];
	//document.getElementById('orgtype').value =arr[2];
	obj=document.getElementById('orgtype');
	for(xY=0;xY<obj.length;xY++)
	{
		if(obj.options[xY].value==arr[2])
		{
			obj.options[xY].selected=true;
		}
	}
	document.getElementById('orgadd').value  =arr[3];
	document.getElementById('orgcity').value =arr[5];
    document.getElementById('orgzip').value  =arr[6];
	document.getElementById('orgtelp').value =arr[4];
	curr=0;
	ctobj=document.getElementById('orgcountry');
	ct=ctobj.length;
	for (x = 0; x < ct; x++) {
		if (ctobj.options[x].value == arr[7]) //check if country code is match with option value, then select it
             ctobj.options[x].selected=true;
	}
  } 	
}

function setpos(id,e)
{
	pos=getMouseP(e);
	document.getElementById(id).style.top=pos[1]+'px';
	document.getElementById(id).style.left=pos[0]+'px';
	document.getElementById(id).style.display='';	
}
