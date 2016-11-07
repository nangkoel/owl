<?php
//@uthor nangkoel
//Jakarta Indonesia
//2008
//free software is a free mind
//you are free about to copy/use of this script with your own risk
//==================================================================

class RFCNANGKOEL{
//configurasi koneksi
//client sap harus disesuaikan dengan installasi sap
//untuk client sap mohon dikoordinasikan dengan basis
//konfigurasi ini sebagai default, dan dapat diubah melalui method
//dengan value yang sesuai dengan konfigurasi sap
	var $rfc_uname 	        ='';			//string
	var $rfc_pwd		    ='';		//string
	var $dest_sap_ip	    ='192.168.1.10';	//string (server SAP development/training)
	//var $dest_sap_ip	    ='192.168.1.11';	//string (server SAP production)
	var $client_on_sap	='230';				//string (training)
	//var $client_on_sap	    ='100';				//string (production)
	var $sap_sys_num	    ='01';				//string
//==================================================================
	var $array_header	    = Array();			//table header output
	var $array_content		= Array();			//isi table

function login($uname,$pwd)
{
			$this->rfc_uname=$uname;
			$this->rfc_pwd  =$pwd;
			
			$LOGIN = array ("ASHOST"=>$this->dest_sap_ip,
			                "SYSNR"=>$this->sap_sys_num,
			                "CLIENT"=>$this->client_on_sap,
			                "USER"=>$this->rfc_uname,
			                "PASSWD"=>$this->rfc_pwd,
			                "CODEPAGE"=>"1404"
			                );
			
			//koneksi ke sap/Try to connect to SAP using our Login array
			   $rfc = saprfc_open ($LOGIN);
			   saprfc_close($rfc);
			   if(! $rfc )
			   {
			      return false;
			   }
			   else
			   {
			   	return true;
			   }
			   	
}	
	
function do_read_table($table_on_sap,$criteria='',$fields=Array())
{	
 
//variable dibawah merupakan hasil query yang akan di proses
	$result_data_row = null;					//data yang akan dikembalikan
	$result_field_row= null;					//data yang akan dikembalikan
//=================================================================
			$err=false;
			if(isset($table_on_sap))
			{
			//membuat array login/Build the login array
			$LOGIN = array ("ASHOST"=>$this->dest_sap_ip,
			                "SYSNR"=>$this->sap_sys_num,
			                "CLIENT"=>$this->client_on_sap,
							"USER"=>'ITSAP',
							"PASSWD"=>'bumitama',
			                //"USER"=>$_SESSION['userSAP'],
			                //"PASSWD"=>$_SESSION['pwdSAP'],
			                "CODEPAGE"=>"1404"
			                );
			//koneksi ke sap/Try to connect to SAP using our Login array
			   $rfc = saprfc_open ($LOGIN);
			   if(! $rfc )
			   {
			       $err="Koneksi gaga/The RFC connection has failed with the following error:<br>".saprfc_error();
			   }
			  else
			  {
			   $fce = saprfc_function_discover($rfc, "RFC_READ_TABLE");
			   if(! $fce )
			   {
			       $err="Fungsi gagal/The function module has failed.";
			   }
			 if(!$err)
				{ 			
					//Convert to uppercase the name of the table to show
					   $Table = STRTOUPPER($table_on_sap);
					
					//Pass import parameters
					   saprfc_import ($fce,"QUERY_TABLE",$Table);
					   saprfc_import ($fce,"DELIMITER","#"); 
					//Pass table parameters
					   if($criteria!='')
					   {
						   $option=Array("TEXT"=>$criteria);
						   saprfc_table_append ($fce,"OPTIONS",$option);
					   }
					   else
					   {
					   	 saprfc_table_init ($fce,"OPTIONS");
					   }  
					       ///saprfc_table_append ($fce,"OPTIONS",Array("TEXT"=>"BNAME EQ 'ITD_DEV'"));
					   if(count($fields>0))
					   {					   
						   for($x=0;$x<count($fields);$x++)
						   saprfc_table_append ($fce,"FIELDS",Array("FIELDNAME"=>$fields[$x]));
					   }
					   else
					   {
					   	 saprfc_table_init ($fce,"FIELDS");
					   }
					   saprfc_table_init ($fce,"DATA");
					
					//Call and execute the function
					   $rc = saprfc_call_and_receive ($fce);
					   if ($rfc_rc != SAPRFC_OK)
					   {
					       if ($rfc == SAPRFC_EXCEPTION )
					           $err= ("Exception raised: ".saprfc_exception($fce));
					       else
					           $err= ("Call error: ".saprfc_error($fce));
					   }			 
					 if(!$err)
					 { 
					  //Fetch the data from the internal tables
					   $data_row = saprfc_table_rows ($fce,"DATA");//jumlah baris
					   $field_row = saprfc_table_rows ($fce,"FIELDS");//jumlah kolom

				
					//masukkan ke dalam array
					   $arrhead=Array();
					   for($i=1; $i<=$field_row ; $i++)
					   {
					//Read the internal table, row by row
						   $FIELDS = saprfc_table_read ($fce,"FIELDS",$i);
						   array_push($arrhead,$FIELDS['FIELDNAME']);
					   }
					
					   for ($i=1; $i<=$data_row; $i++)
					   {
					     $DATA = saprfc_table_read ($fce,"DATA",$i);
					     $TEST = SPLIT("#",$DATA['WA']);
						     for($j=0; $j<$field_row; $j++)
						     {
						         $arrContent[$arrhead[$j]][$i]=$TEST[$j];
						     }
					   }  
					   return $arrContent;
					//realease the function and close the connection
					   saprfc_function_free($fce);
					   saprfc_close($rfc);		
					  }
					  else
					  {
					  	echo $err;
						return '';
						exit;
					  }	 
				}
			}
		}
}//close function
}
?>