function getKey(e)
{
        var key;
        if(window.event) {
               // for IE, e.keyCode or window.event.keyCode can be used
               key = e.keyCode;
        }
        else if(e.which) {
               key = e.which;
        }
        else {
               // no event, so pass through
               return true;
        }
      return key;
}
function tanpa_kutip(e)
{
  key=getKey(e);
  if(key==39 || key==34 || key==38 || key==44 )
  return false;
  else
  return true;
}
function tanpa_kutip_koma(e)
{
  key=getKey(e);
  if(key==39||key==34||key==38||key==44)
  return false;
  else
  return true;
}
function tanpa_kutip_koma_titik(e)
{
  key=getKey(e);
  if(key==39 || key==34 || key==38 || key==44 || key==46)
  return false;
  else
  return true;
}
function angka_doang(e)
{
 key=getKey(e);
 if((key<48 || key>57) && (key!=8 && key!=46))
  return false;
 else
 {
     return true;
 }
}
function query(sUrl)
      {
        var oScript = document.createElement("script");
        oScript.src = sUrl;
        document.body.appendChild(oScript);
      }

function tanpa_kutip_dan_sepasi(e)
{
 key=getKey(e);
 if(key==39 || key==34 || key==38 || key==32)
    return false;
 else
    return true;
}
function disable_paste(e) //disable ctrl+v
{
        var forbiddenKeys = new Array('v');
        var key;
        var isCtrl;
        if(window.event)
        {
                key = window.event.keyCode;     //IE
                if(window.event.ctrlKey)
                        isCtrl = true;
                else
                        isCtrl = false;
        }
        else
        {
                key = e.which;     //firefox
                if(e.ctrlKey)
                        isCtrl = true;
                else
                        isCtrl = false;
        }
        if(isCtrl)
        {
                for(i=0; i<forbiddenKeys.length; i++)
                {
                        if(forbiddenKeys[i].toLowerCase() == String.fromCharCode(key).toLowerCase())
                        {
                                return false;
                        }
                }
        }
        return true;
}
function tanpa_koma(e)
{
  key=getKey(e);
  if(key==39 || key==34 || key==38)
  return false;
  else
  return true;
}
function angka_doang_titik(e)
{
 key=getKey(e);
 if((key<48 || key>57) && (key!=8 && key!=46))
  return false;
 else
 {
     return true;
 }
}