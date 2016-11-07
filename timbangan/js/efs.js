/**
 * 
 * nangkoel@gmail.com
 */
//**************************************************
//Category  and storage location
activeDoc='';
 function savecat()
 {
 	nobj=document.getElementById('catinput');
	oldobj=document.getElementById('oldcat');
	param='newcat='+nobj.value+'&oldobj='+oldobj.value;
	if(nobj.value=='')
	{
		alert('Please fill category');
		nobj.focus();
	}
	else if(confirm('Are you sure..?'))
	{
		//alert(oldobj.value);
		post_response_text('efs_slave_saveNewCat.php', param, respog);
	}
	function respog(){
	     if(con.readyState==4)
	     {
	        if(con.status==200)
	        {
	           busy_off();
			   if (con.responseText.lastIndexOf('Gagal') > -1) {
			        alert('ERROR TRANSACTION,\n'+con.responseText);
			   }
			   else
			   {
			   	window.location.reload();
			   }
			}
	        else
	        {
	          busy_off();
			  error_catch(con.status);
	        }
	     }
	}
 }
 
 function saveSloc()
 {
 	nobj=document.getElementById('catinput');
	oldobj=document.getElementById('oldcat');
	param='newcat='+nobj.value+'&oldobj='+oldobj.value;
	if(nobj.value=='')
	{
		alert('Please fill Storage location name');
		nobj.focus();
	}
	else if(confirm('Are you sure..?'))
	{
		//alert(oldobj.value);
		post_response_text('efs_slave_saveNewSloc.php', param, respog);
	}
	function respog(){
	     if(con.readyState==4)
	     {
	        if(con.status==200)
	        {
	           busy_off();
			   if (con.responseText.lastIndexOf('Gagal') > -1) {
			        alert('ERROR TRANSACTION,\n'+con.responseText);
			   }
			   else
			   {
			   	window.location.reload();
			   }
			}
	        else
	        {
	          busy_off();
			  error_catch(con.status);
	        }
	     }
	}
 }
  
 function editCt(x)
 {
    clearColor();
	cat=document.getElementById(x);
	cat.style.backgroundColor='#F88711'
 	document.getElementById('catinput').value=cat.innerHTML;
	document.getElementById('oldcat').value=cat.innerHTML;	
	document.getElementById('lab').innerHTML='Edit';	
 }
 
 function clearFormCat()
 {
 	document.getElementById('catinput').value='';
	document.getElementById('oldcat').value='';	
	document.getElementById('lab').innerHTML='New'; 
    clearColor();		
 }

 function clearColor(){
	for(k=1;k<=max_row;k++)
	 {
	 	document.getElementById('ct'+k).style.backgroundColor='#E8F2FE';
	 }
  	
 }
function delCt(x)
 {
    clearColor();
	cat=document.getElementById(x);
	cat.style.backgroundColor='#F88711'
	param='cat='+cat.innerHTML;
	if(confirm('Are you deleting \''+cat.innerHTML+'\'..?'))
	{
		//alert(param);
		post_response_text('efs_slave_deleteCat.php', param, respog);
	}
	function respog(){
	     if(con.readyState==4)
	     {
	        if(con.status==200)
	        {
	           busy_off();
			   if (con.responseText.lastIndexOf('Gagal') > -1) {
			        alert('ERROR TRANSACTION,\n'+con.responseText);
			   }
			   else
			   {
			   	window.location.reload();
			   }
			}
	        else
	        {
	          busy_off();
			  error_catch(con.status);
	        }
	     }
	}		
 }
 
function delSloc(x)
 {
    clearColor();
	cat=document.getElementById(x);
	cat.style.backgroundColor='#F88711'
	param='cat='+cat.innerHTML;
	if(confirm('Are you deleting \''+cat.innerHTML+'\'..?'))
	{
		//alert(param);
		post_response_text('efs_slave_deleteSloc.php', param, respog);
	}
	function respog(){
	     if(con.readyState==4)
	     {
	        if(con.status==200)
	        {
	           busy_off();
			   if (con.responseText.lastIndexOf('Gagal') > -1) {
			        alert('ERROR TRANSACTION,\n'+con.responseText);
			   }
			   else
			   {
			   	window.location.reload();
			   }
			}
	        else
	        {
	          busy_off();
			  error_catch(con.status);
	        }
	     }
	}		
 } 
 //********************************************
  //********************************************
 //Country
  function saveCtry()
 {
    clearColorCtry();
	ctry=document.getElementById('country');
	state=document.getElementById('state');
	city=document.getElementById('city');
	oldctry=document.getElementById('oldcountry');
	oldstate=document.getElementById('oldstate');
	oldcity=document.getElementById('oldcity');
		
	
	param='newcountry='+trim(ctry.value)+'&newstate='+trim(state.value);
	param+='&newcity='+trim(city.value)+'&oldcountry='+oldctry.value;
	param+='&oldstate='+oldstate.value+'&oldcity='+oldcity.value;
	if(trim(ctry.value)=='' && trim(state.value)=='' && trim(city.value)=='')
	{
		alert('Please fill at lest one field');
		ctry.focus();
	}
	else if(trim(ctry.value)=='Group...' || trim(state.value)=='Sub Group...' || trim(city.value)=='Sub Sub Group...')
	{
		alert('Field can\'t contain it\'s deafult value');
		ctry.focus();		
	}
	else if(confirm('Are you sure..?'))
	{
		//alert(param);
		post_response_text('efs_slave_saveNewCountry.php', param, respog);
	}
	function respog(){
	     if(con.readyState==4)
	     {
	        if(con.status==200)
	        {
	           busy_off();
			   if (con.responseText.lastIndexOf('Gagal') > -1) {
			        alert('ERROR TRANSACTION,\n'+con.responseText);
			   }
			   else
			   {
			   	window.location.reload();
			   }
			}
	        else
	        {
	          busy_off();
			  error_catch(con.status);
	        }
	     }
	}
 }
 
 function editCtry(x)
 {
    clearColorCtry();
	ctry=document.getElementById('country'+x);
	state=document.getElementById('state'+x);
	city=document.getElementById('city'+x);
	
	document.getElementById('row'+x).style.backgroundColor='#F88711'
	
 	document.getElementById('country').value=trim(ctry.innerHTML);
	document.getElementById('state').value=trim(state.innerHTML);
	document.getElementById('city').value=trim(city.innerHTML);	
	
 	document.getElementById('oldcountry').value=ctry.innerHTML;
	document.getElementById('oldstate').value=state.innerHTML;
	document.getElementById('oldcity').value=city.innerHTML;	
	
	document.getElementById('lab').innerHTML='Edit Group';	
 }
 
 function clearFormCtry()
 {
 	document.getElementById('country').value='Group...';
	document.getElementById('state').value='Sub Group...';
    document.getElementById('city').value='Sub Sub Group...';	
 	document.getElementById('oldcountry').value='';
	document.getElementById('oldstate').value='';
    document.getElementById('oldcity').value='';	
	document.getElementById('lab').innerHTML='New Group'; 
    clearColorCtry();		
 }

 function clearColorCtry(){
	for(k=1;k<=max_row;k++)
	 {
	 	document.getElementById('row'+k).style.backgroundColor='#E8F2FE';
	 }
  	
 }
function delCtry(x)
 {
    clearColorCtry();
	ctry=document.getElementById('country'+x);
	state=document.getElementById('state'+x);
	city=document.getElementById('city'+x);
	document.getElementById('row'+x).style.backgroundColor='#F88711'
    param='newcountry='+ctry.innerHTML+'&newstate='+state.innerHTML;
	param+='&newcity='+city.innerHTML;
	if(confirm('Are you deleting Group..?'))
	{
		//alert(param);
		post_response_text('efs_slave_deleteCountry.php', param, respog);
	}
	function respog(){
	     if(con.readyState==4)
	     {
	        if(con.status==200)
	        {
	           busy_off();
			   if (con.responseText.lastIndexOf('Gagal') > -1) {
			        alert('ERROR TRANSACTION,\n'+con.responseText);
			   }
			   else
			   {
			   	window.location.reload();
			   }
			}
	        else
	        {
	          busy_off();
			  error_catch(con.status);
	        }
	     }
	}		
 }

function let(val,id)
{
	if(id=='country' && val=='Group...')
		document.getElementById(id).value='';
	else if(id=='country' && val=='')
	    document.getElementById(id).value='Group...';
		
	if(id=='state' && val=='Sub Group...')
		document.getElementById(id).value='';
	else if(id=='state' && val=='')
	    document.getElementById(id).value='Sub Group...';
		
	if(id=='city' && val=='Sub Sub Group...')
		document.getElementById(id).value='';
	else if	(id=='city' && val=='')
	    document.getElementById(id).value='Sub Sub Group...';
		
} 

function getState(country)
{
  if (country == '') {
  	document.getElementById('state').innerHTML = '<option></option>';
	document.getElementById('city').innerHTML = '<option></option>';
  }
  else {
  	param = 'group=' + country;
  	post_response_text('efs_slave_getState.php', param, respog);
  }		
  	function respog(){
  		if (con.readyState == 4) {
  			if (con.status == 200) {
  				busy_off();
  				if (con.responseText.lastIndexOf('Gagal') > -1) {
  					alert('ERROR TRANSACTION,\n' + con.responseText);
  				}
  				else {
  					document.getElementById('state').innerHTML = con.responseText;
			        document.getElementById('city').innerHTML = '<option></option>';			
  				}
  			}
  			else {
  				busy_off();
  				error_catch(con.status);
  			}
  		}
  	}  
}

function getCity(state)
{
  if (state == '') {
  	document.getElementById('city').innerHTML = '<option></option>';
  }
  else {
  	param = 'state=' + state;
  	post_response_text('efs_slave_getCity.php', param, respog);
  }			
  	function respog(){
  		if (con.readyState == 4) {
  			if (con.status == 200) {
  				busy_off();
  				if (con.responseText.lastIndexOf('Gagal') > -1) {
  					alert('ERROR TRANSACTION,\n' + con.responseText);
  				}
  				else {
  					document.getElementById('city').innerHTML = con.responseText;
  				}
  			}
  			else {
  				busy_off();
  				error_catch(con.status);
  			}
  		}
  	}  
}
 //****************************************************
 //New Document
 
 function nextUpload(){
 	subject = document.getElementById('subject').value;
 	docnum = document.getElementById('docnum').value;
 	docdate = document.getElementById('docdate').value;
 	objcategory = document.getElementById('category');
 	objcountry = document.getElementById('country');
 	objstate = document.getElementById('state');
 	objcity = document.getElementById('city');
 	objsloc = document.getElementById('sloc');
 	category = objcategory.options[objcategory.selectedIndex].text;
 	country = objcountry.options[objcountry.selectedIndex].text;
 	state = objstate.options[objstate.selectedIndex].text;
 	city = objcity.options[objcity.selectedIndex].text;
 	sloc = objsloc.options[objsloc.selectedIndex].text;
 	remark = document.getElementById('remark').value;
	trxid=frmupl.document.getElementById('trxid').value;
	
	
 	if (trim(sloc) !== '' && trim(subject) != '' && trim(docdate).length == 10) {
 		if (confirm('Are you sure..?')) {
 			param = 'subject=' + trim(subject) + '&docnum=' + trim(docnum);
 			param += '&docdate=' + trim(docdate) + '&category=' + trim(category);
 			param += '&country=' + trim(country) + '&state=' + trim(state);
 			param += '&city=' + trim(city) + '&sloc=' + trim(sloc)+'&trxid='+trxid;
 			param += '&remark=' + trim(remark);
 			post_response_text('efs_slave_saveNewDoc.php', param, respog);
 		}
 	}
 	else {
 		alert('Subject, Document.Date and Storage.Location can not blank');
 	}
 	
 	function respog(){
 		if (con.readyState == 4) {
 			if (con.status == 200) {
 				busy_off();
 				if (con.responseText.lastIndexOf('Gagal') > -1 || con.responseText.lastIndexOf('Error') > -1) {
 					alert('ERROR TRANSACTION,\n' + con.responseText);
 				}
 				else {
 					//alert(con.responseText);
						activeDoc=parseInt(con.responseText);
						showUploadForm(activeDoc);
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
		
		function showUploadForm(x){
			document.getElementById('lab').innerHTML='TrxNum:<font color=#AA3333> '+x+'</font><br>Subject:<font color=#AA3333> '+subject+'</font>';
			document.getElementById('temp').style.display = 'none';
			document.getElementById('uploadform').style.display = '';
		}
}

function upload()
{
	jlh=frmupl.document.getElementsByName('file[]').length;
	for(x=(jlh-1);x>=0;x--)
	{
		try {
			
			d = frmupl.document.getElementsByName('file[]')[x];
			if (d.value == '') {
				frmupl.document.getElementById('cont').removeChild(d);
                document.getElementById('frmupl').style.height=(document.getElementById('frmupl').offsetHeight-22)+'px';
			}
		}
		catch(e){}	
	}
    frmupl.document.getElementById('trxid').value = activeDoc;	
	frmupl.document.getElementById('frm').submit();
	//******************************************
	//hide form upload
	document.getElementById('delform').style.display='none';
	document.getElementById('finish').style.display='';
}

function uploadMore()
{
	frmupl.location.reload();
	document.getElementById('delform').style.display = '';
	document.getElementById('finish').style.display='none';
}

function newFile()
{

     newElement = frmupl.document.createElement("input");
	 newElement.setAttribute("type","file");
	 newElement.setAttribute("name","file[]");
	 newElement.setAttribute("class","mybutton");
	 newElement.setAttribute("size","35");		
	 document.getElementById('frmupl').style.height=(document.getElementById('frmupl').offsetHeight+23)+'px';
     frmupl.document.getElementById('cont').appendChild(newElement); 
}

function cancelUpload()
{
	param='trxid='+activeDoc;
	if(confirm('Are you deleting this Document..?'))
	{
		//alert(param);
		post_response_text('efs_slave_deleteDoc.php', param, respog);
	}
	function respog(){
	     if(con.readyState==4)
	     {
	        if(con.status==200)
	        {
	           busy_off();
			   if (con.responseText.lastIndexOf('Gagal') > -1) {
			        alert('ERROR TRANSACTION,\n'+con.responseText);
			   }
			   else
			   {
			   	alert('Delete success');
				window.location='';
			   }
			}
	        else
	        {
	          busy_off();
			  error_catch(con.status);
	        }
	     }
	}		
}

function delF(fl,no,img)
{
	param='filename='+fl;
	frame.document.getElementById('row'+no).style.backgroundColor='#D47024';
	if(confirm('Are you deleting this file..?'))
	{
		//alert(param);
		post_response_text('efs_slave_deleteFile.php', param, respog);
	}
	else
	{
	frame.document.getElementById('row'+no).style.backgroundColor='#FFFFFF';	
	}
	function respog(){
	     if(con.readyState==4)
	     {
	        if(con.status==200)
	        {
	           busy_off();
			   if (con.responseText.lastIndexOf('Gagal') > -1) {
			        alert('ERROR TRANSACTION,\n'+con.responseText);
			   }
			   else
			   {
			   	alert('Delete success');
                frame.document.getElementById('row'+no).innerHTML='';//style.backgroundColor='#FFFFFF';
				clearView();
			   }
			}
	        else
	        {
	          busy_off();
			  error_catch(con.status);
	        }
	     }
	}	
    function clearView()
	{
	  //frame.document.getElementById('td'+no).innerHTML='';
	  //img.style.display='none';
	}
}


function formReset()
{
	frmupl.document.getElementById('frm').reset();	
}
//=============================================================
//Update/edit
function getDE(e)
{
	win1=document.getElementById('editContainer');
    cord= new Array();
	activeDoc='';
	cord=getMouseP(e);
	win1.style.display='';
	win1.style.top=(cord[1])+'px';
	win1.style.left=(cord[0]-200)+'px';	
    frmupl.document.getElementById('trxid').value ='';
    document.getElementById('tion').innerHTML='Edit Mode';	
	param='';
		post_response_text('efs_slave_getDocEdit.php', param, respog);

	function respog(){
	     if(con.readyState==4)
	     {
	        if(con.status==200)
	        {
	           busy_off();
			   if (con.responseText.lastIndexOf('Gagal') > -1) {
			        alert('ERROR TRANSACTION,\n'+con.responseText);
			   }
			   else
			   {
			   	document.getElementById('editContent').innerHTML=con.responseText;	
				win1.style.display='';
			   }
			}
	        else
	        {
	          busy_off();
			  error_catch(con.status);
	        }
	     }
	}		
}

function getDocForEdit(offset)
{
	param='offset='+offset;
		post_response_text('efs_slave_getDocEdit.php', param, respog);

	function respog(){
	     if(con.readyState==4)
	     {
	        if(con.status==200)
	        {
	           busy_off();
			   if (con.responseText.lastIndexOf('Gagal') > -1) {
			        alert('ERROR TRANSACTION,\n'+con.responseText);
			   }
			   else
			   {
			   	document.getElementById('editContent').innerHTML=con.responseText;	
			   }
			}
	        else
	        {
	          busy_off();
			  error_catch(con.status);
	        }
	     }
	}	
}
function cancelEdit()
{
	 activeDoc='';
     frmupl.document.getElementById('trxid').value ='';	
     document.getElementById('editContent').innerHTML='';
     document.getElementById('editContainer').style.display='none';	
	 document.getElementById('new').checked=true;
	 document.getElementById('tion').innerHTML='New Entry';
}

function loadDat(trxid,subject,docnum,category,docdate,country,state,city,sloc)
{
	 activeDoc=trxid;
     frmupl.document.getElementById('trxid').value =trxid;
	 
	 subj=document.getElementById('subject');
	 docn=document.getElementById('docnum');
	 cat=document.getElementById('category');
	 docdate=document.getElementById('docdate');
	 coun=document.getElementById('country');
	 ste=document.getElementById('state');
	 cit=document.getElementById('city');
	 slo=document.getElementById('sloc');
	 coun.options[0].text=country;
	 coun.options[0].selected=true; 
	 ste.options[0].text=state;
	 ste.options[0].selected=true;
	 cit.options[0].text=city;
	 cit.options[0].selected=true;
	 slo.options[0].text=sloc;
	 slo.options[0].selected=true;
	 	 	 
	 subj.value=subject;
	 docn.value=docnum;
	 cat.value=category;
     document.getElementById('editContent').innerHTML='';
     document.getElementById('editContainer').style.display='none';		 
}
//=============================================================
//search

function search()
{
	toSearch=trim(document.getElementById('ser').value);
	if(toSearch=='')
	{
		alert('Input some data');
		document.getElementById('ser').focus();
	}
	else
	{
		param='texttosearch='+toSearch;
	}
		post_response_text('efs_slave_searchDoc.php', param, respog);

	function respog(){
	     if(con.readyState==4)
	     {
	        if(con.status==200)
	        {
	           busy_off();
			   if (con.responseText.lastIndexOf('Gagal') > -1) {
			        alert('ERROR TRANSACTION,\n'+con.responseText);
			   }
			   else
			   {
			   	document.getElementById('result').innerHTML=con.responseText;
			   }
			}
	        else
	        {
	          busy_off();
			  error_catch(con.status);
	        }
	     }
	}		
}

function periksa(e)
{
  key=getKey(e);
  if(key==13)
    search();
}

function showFile(trid,e)
{
   document.getElementById('ordereditorcontent').innerHTML='';
	pos= new Array();
	pos=getMouseP(e);
    param='trxid='+trid;
	post_response_text('efs_slave_getFile.php', param, respog);
    function respog(){
	      if(con.readyState==4)
	      {
		        if (con.status == 200) {
					busy_off();
					if (con.responseText.lastIndexOf('Gagal') > -1) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//*********************
						//Displays order editor
						document.getElementById('ordereditor').style.top=pos[1]+'px';
						document.getElementById('ordereditor').style.left=pos[0]+'px';
						document.getElementById('ordereditor').style.display='';
						document.getElementById('ordereditorcontent').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
	      }	
	  }	
	
}

function closeFileBrowser()
{
	document.getElementById('ordereditor').style.display='none';
}

function showStatistic(x)
{
  chil.location='efs_slave_showStatistic.php?tahun='+x;	
}

function showOprPerformance(x)
{
  chil.location='efs_slave_showOprPerformance.php?bulan='+x;	
}
