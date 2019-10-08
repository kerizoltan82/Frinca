
var error_reported = false;
   
function setupTimer(game, expectedplayerid)
{
    setInterval(function(){checkIfUpdate(game, expectedplayerid)}, 5000);
}

function checkIfUpdate(game, expectedplayerid) {
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    var postString = "game="+game+"&expected="+expectedplayerid;
    xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
        if( xmlhttp.responseText == 'update' ) {
            //reload page
            //alert('reload');
            //window.location.reload();
            window.location = window.location.href;
        } else {
            //alert('Kod: ' + xmlhttp.responseText);
            // error handling
            if( xmlhttp.responseText.substring(0,5) == 'error' && error_reported ==false) {
                error_reported = true;
                alert('Hiba van a szerverhez való kapcsolatban. A játék állásáról nem érhetők el folyamatos frissítések. Kód: ' + xmlhttp.responseText);
            }
        }
    }
  }
  
    xmlhttp.open("POST","checkIfReady.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(postString);
    //xmlhttp.send();
}