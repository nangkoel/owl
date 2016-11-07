/**
 * @author {alex.hutagalung at afhronaldo(at)yahoo(dot)com}
 */


function printdph()
{
	pdoc1=document.getElementById('purchdoc1').value;
	pdoc2=document.getElementById('purchdoc2').value;
	pdoc3=document.getElementById('purchdoc3').value;
	pdoc4=document.getElementById('purchdoc4').value;
	if (pdoc1.substr(0,1)!=6)
		{
		alert("No Document 1 harus berawalan angka 6");
		}
	
	else if (pdoc2!="" & (pdoc2.substr(0,1)!=6))
		{
		alert("No Document 2 harus berawalan angka 6");
		}
	else if (pdoc3!="" & (pdoc3.substr(0,1)!=6))
		{
		alert("No Document 3 harus berawalan angka 6");
		}
	else if (pdoc4!="" & (pdoc4.substr(0,1)!=6))
		{
		alert("No Document 4 harus berawalan angka 6");
		}
	else if (pdoc1.length!=10)
		{
		alert("Jumlah digit Document 1 harus 10");
		}	
	else if (pdoc2!="" & (pdoc2.length!=10))
		{
		alert("Jumlah digit Document 2 harus 10");
		}
	else if (pdoc3!="" & (pdoc3.length!=10))
		{
		alert("Jumlah digit Document 3 harus 10");
		}
	else if (pdoc4!="" & (pdoc4.length!=10))
		{
		alert("Jumlah digit Document 4 harus 10");
		}
	else if (isNaN(pdoc1))
		{
		alert("No Document 1 harus berupa angka");
		}
	else if (pdoc2!="" &  isNaN(pdoc2))
		{
		alert("No Document 2 harus berupa angka");
		}
	else if (pdoc3!="" &  isNaN(pdoc3))
		{
		alert("No Document 3 harus berupa angka");
		}	
	else if (pdoc4!="" &  isNaN(pdoc4))
		{
		alert("No Document 4 harus berupa angka");
		}	
		

	
	else 
		{
		if(confirm('Anda Yakin dengan no RFQ ini...?'))
				{
					param='pdoc1='+pdoc1+'&pdoc2='+pdoc2+'&pdoc3='+pdoc3+'&pdoc4='+pdoc4;
					window.open('pchs_dph_tampilkan.php?'+param,'newin','resizable=yes,scrollbars=yes')
			}
	}
		
			
	


function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('temp').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		  resetf();//clear from	
		}
	}	
}




function validat(ev)
{

  key=getKey(ev);
  if(key==13)
    getUserForActivation();
  else
  return tanpa_kutip_dan_sepasi(ev);	
	
}


function validat1(ev)
{

  key=getKey(ev);
  if(key==13)
    getUserForResetP();
  else
  return tanpa_kutip_dan_sepasi(ev);	
	
}



function savedph()
{
	//dapetin keseluruhannya
	purch_subject=document.getElementById('purch_subject').value;
	purch_kurs=document.getElementById('purch_kurs').value;
	purch_total=document.getElementById('purch_total_vendor').value;
	total_material=document.getElementById('total_material').value;
	pdoc1=document.getElementById('pdoc1').value;
	pdoc2=document.getElementById('pdoc2').value;
	pdoc3=document.getElementById('pdoc3').value;
	pdoc4=document.getElementById('pdoc4').value;
	signer1=document.getElementById('signer1').value;
	signer2=document.getElementById('signer2').value;
	signer3=document.getElementById('signer3').value;
	signer4=document.getElementById('signer4').value;
	signer5=document.getElementById('signer5').value;
	signer6=document.getElementById('signer6').value;
	signer7=document.getElementById('signer7').value;
	pdept=document.getElementById('pdept').value;
	note=document.getElementById('note').value;
	jab1=document.getElementById('jab1').value;
	jab2=document.getElementById('jab2').value;
	jab3=document.getElementById('jab3').value;
	unit=document.getElementById('purch_unit').value;
	kebut=document.getElementById('kebut').value;
	pp=document.getElementById('pp').value;
	pay1=document.getElementById('add_spe1_pay').value;
	pay2=document.getElementById('add_spe2_pay').value;
	pay3=document.getElementById('add_spe3_pay').value;
	pay4=document.getElementById('add_spe4_pay').value;
	disc1=document.getElementById('add_spe1_disc').value;
	disc2=document.getElementById('add_spe2_disc').value;
	disc3=document.getElementById('add_spe3_disc').value;
	disc4=document.getElementById('add_spe4_disc').value;
	del1=document.getElementById('add_spe1_del').value;
	del2=document.getElementById('add_spe2_del').value;
	del3=document.getElementById('add_spe3_del').value;
	del4=document.getElementById('add_spe4_del').value;
	fra1=document.getElementById('add_spe1_franco').value;
	fra2=document.getElementById('add_spe2_franco').value;
	fra3=document.getElementById('add_spe3_franco').value;
	fra4=document.getElementById('add_spe4_franco').value;
	ppn1=document.getElementById('add_spe1_ppn').value;
	ppn2=document.getElementById('add_spe2_ppn').value;
	ppn3=document.getElementById('add_spe3_ppn').value;
	ppn4=document.getElementById('add_spe4_ppn').value;
	pnwrn1=document.getElementById('add_spe1_pnwrn').value;
	pnwrn2=document.getElementById('add_spe2_pnwrn').value;
	pnwrn3=document.getElementById('add_spe3_pnwrn').value;
	pnwrn4=document.getElementById('add_spe4_pnwrn').value;
	
	
	param='subject='+purch_subject+'&total_vendor='+purch_total+'&total_material='+total_material+'='+total_material;
	param+='&kurs='+purch_kurs;
	param+='&pdoc1='+pdoc1+'&pdoc2='+pdoc2+'&pdoc3='+pdoc3+'&pdoc4='+pdoc4;
	param+='&signer1='+signer1+'&signer2='+signer2+'&signer3='+signer3+'&signer4='+signer4+'&signer5='+signer5+'&signer6='+signer6+'&signer7='+signer7;
	param+='&pdept='+pdept+'&note='+note;pp
	param+='&jab1='+jab1+'&jab2='+jab2+'&jab3='+jab3+'&unit='+unit+'&kebut='+kebut+'&pp='+pp;
	param+='&pay1='+pay1+'&pay2='+pay2+'&pay3='+pay3+'&pay4='+pay4;
	param+='&disc1='+disc1+'&disc2='+disc2+'&disc3='+disc3+'&disc4='+disc4;
	param+='&del1='+del1+'&del2='+del2+'&del3='+del3+'&del4='+del4;
	param+='&fra1='+fra1+'&fra2='+fra2+'&fra3='+fra3+'&fra4='+fra4;
	param+='&ppn1='+ppn1+'&ppn2='+ppn2+'&ppn3='+ppn3+'&ppn4='+ppn4;
	param+='&pnwrn1='+pnwrn1+'&pnwrn2='+pnwrn2+'&pnwrn3='+pnwrn3+'&pnwrn4='+pnwrn4;
	for (x=1;x<=purch_total;x++)
	{
	eval("purch_vendor_"+x+"=document.getElementById('purch_vendor_'+x).value");
	param+="&purch_vendor_"+x+"="+eval("purch_vendor_"+x);
	}
	
	for (y=1;y<total_material;y++)
	{
	//dapetin no material & purch
	eval("purch_no_material_"+y+"=document.getElementById('purch_no_material_'+y).innerHTML");
	eval("purch_pr_"+y+"=document.getElementById('purch_pr_'+y).innerHTML");
	//dapetin text masing2 didalamnya
	eval("purch_spek_1_"+y+"=document.getElementById('add_spe1_'+y).value");
	eval("purch_spek_2_"+y+"=document.getElementById('add_spe2_'+y).value");
	eval("purch_spek_3_"+y+"=document.getElementById('add_spe3_'+y).value");
	eval("purch_spek_4_"+y+"=document.getElementById('add_spe4_'+y).value");

	
	param+="&purch_no_material_"+y+"="+eval("purch_no_material_"+y);
	param+="&purch_pr_"+y+"="+eval("purch_pr_"+y);
	param+="&purch_spek_1_"+y+"="+eval("purch_spek_1_"+y);
	param+="&purch_spek_2_"+y+"="+eval("purch_spek_2_"+y);
	param+="&purch_spek_3_"+y+"="+eval("purch_spek_3_"+y);
	param+="&purch_spek_4_"+y+"="+eval("purch_spek_4_"+y);

	}
	if(confirm('Anda Yakin Ingin Menyimpan DPH...?'))
			{
		post_response_text('pchs_dph_simpan.php', param, respog);					
				
			}
		
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('temp').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		  //resetf();//clear from	
		}
	}	
}

function konci(e)
{
         if(window.event)
	 {
         if(event.keyCode==13)
            load_harga();
         else if((event.keyCode<48 || event.keyCode>57) && (event.keyCode!=8 && event.keyCode!=46))
           return false;
          }
        else if(e.which)
 	{
           if(e.which==13)
             load_harga();
           else if((e.which<48 || e.which>57) && (e.which!=8 && e.which!=46))
           return false;
        }
        else
		{
               return true;
        }
}
function get_event(e)
{
         if(window.event)
	 {
           if(event.keyCode==39 || event.keyCode==34|| event.keyCode==38)
           return false;
          }
        else if(e.which)
 	{
          if(e.which==39 || e.which==34 || e.which==38)
           return false;
        }
        else
	{
            return true;
        }
}
function kosong(obj)
{
  if(parseInt(obj.value)==0)
     obj.value='';
}
function fill_usdonly(g)
{
 if(g.value=='')
     g.value='0';
}
