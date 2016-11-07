/*
 * @uthor:nangkoel@gmail.com
 * Indonesia 2009
 */
function savePeriode(val,org,obj)
{
	if(trim(val)=='')
	 {
	 	alert(org.toUpperCase()+ ' Transaction period is empty');
	 }
	 if(confirm('Are you sure..?'))
	 {
	 	param='val='+val+'&org='+org.toUpperCase();
//		alert(param);
		post_response_text('slave_savePeriode.php', param, respog);	
		obj.style.backgroundColor='#DD2222';	
	 }

   function respog()
   {
	   if(con.readyState==4)
	      {
		        if (con.status == 200) {
					obj.style.color='#000000';
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else{
                           alert('Data saved');
					}
				}
				else{
					   obj.style.color='#000000';
					   busy_off();error_catch(con.status);
					}	
	      }	
	}
	   	 
}
