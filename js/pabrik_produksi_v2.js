/**
 * @author {nangkoel@gmail.com}
 * jakarta indonesia
 */
function getPeriode(){
	pabrik=document.getElementById('pabrik').options[document.getElementById('pabrik').selectedIndex].text;
	param='proses=getPeriode'+'&kdPabrik='+pabrik;
	tujuan='pabrik_slave_3produksiHarian_v2.php';
	post_response_text(tujuan, param, respog);
	function respog(){
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {;
							document.getElementById('periode').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 		
}

 function getLaporanPrdPabrik()
{
    periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    tampil=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].text;
    pabrik=document.getElementById('pabrik').options[document.getElementById('pabrik').selectedIndex].text;
    param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik+'&proses=preview';
    tujuan='pabrik_slave_3produksiHarian_v2.php';
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
						else {;
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


function laporanPDF(periode,tampil,pabrik,ev)
{
    param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik;
   tujuan = 'pabrik_slave_printProduksi_pdf_v1.php?'+param;	
 //display window
   title=periode;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
}

function grafikProduksi(periode,tampil,pabrik,ev)
{
   param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik;
   //document.getElementById('container').innerHTML="<img src='pabrik_slave_grafikProduksi.php?"+param+"'>";		
   tujuan='pabrik_slave_grafikProduksi.php?'+param;
   title=periode;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
}

function laporanEXCEL(periode,tampil,pabrik,ev)
{
    param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik;
    tujuan = 'pabrik_slave_3produksiHarian_v2.php?method=excel&'+param;	
    //display window
    title=periode;
    width='700';
    height='400';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1(title,content,width,height,ev);
}

