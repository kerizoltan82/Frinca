
function returnObjById( id )
{
    if (document.getElementById)
        var returnVar = document.getElementById(id);
    else if (document.all)
        var returnVar = document.all[id];
    else if (document.layers)
        var returnVar = document.layers[id];
    return returnVar;
}

function guestlogin()
{
    var loginform = returnObjById('loginform' );
    var nametext = returnObjById('loginname' );
    nametext.value = 'guest';
    loginform.submit();
}

function registerplayer()
{
    var loginform = returnObjById('loginform' );
    var nametext = returnObjById('loginname' );
    nametext.value;
    loginform.submit();
}

function loadArchivedGames() 
{
    
}