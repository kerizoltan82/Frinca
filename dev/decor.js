
var globalStateQm = 0;
var globalStateActionCard3 = 0;

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

function popupPlayerInfo(player)
{
    var infoobj = returnObjById('playerinfo_'+player );
    alert(infoobj.innerHTML);
}

function setActioncard(player, cardindex)
{
    // first check all actioncards
    var cardobj;
    for(i=0; i<4; i++) {
        cardobj = returnObjById('player_'+player + '_actioncard_'+i);
        if( cardobj) { // not all actioncards are present, some have already been used up.
            if( i != cardindex && cardobj.style.backgroundColor == 'red') {
                alert('Egyszerre csak egy akciókártyát szabad felhasználni!');
                return;
            }
        }
    }

    var cardobj = returnObjById('player_'+player + '_actioncard_'+cardindex);
    var currentstate = !(cardobj.style.backgroundColor == "red" );
    var actpartext = returnObjById('actionparam1');
    var actpar2text = returnObjById('actionparam2');
    
    // set state for actioncard
    if( currentstate ) {
        cardobj.style.backgroundColor = 'red';
    } else {
        cardobj.style.backgroundColor = 'transparent';
    }
    
    //action card 0: double move
    if( cardindex == 0) {
        if( currentstate ) {
            actpartext.value = '0';
        } else {
            actpartext.value = '';
        }
    }
    
    //action card 1: 'move where i want'
    if( cardindex == 1) {
        var movetypetext = returnObjById('movetypeparam');
        if( currentstate ) {
            movetypetext.value = 'ActionCard1';
        } else {
            movetypetext.value = '';
        }
    }
        
    //action card 2: 'max 10' deliver
    if( cardindex == 2) {
        if( currentstate ) {
            actpartext.value = '2';
            setDeliver();
        } // no else because fire once card
    }

    //action card 3: '-1' deliver
    if( cardindex == 3) {
        if( currentstate ) {
            actpartext.value = '3';
            globalStateActionCard3 = 1;
            showFruitPopup() ;
        } else {
            actpartext.value = '';
            actpar2text.value = '';
        }
    }
}

function submitForm()
{
	var submitform = returnObjById('gamestepform');    
    //alert('now step');
    //submitform['weird'].value = '';
    submitform.submit();
    
}

function setDeliver()
{
	var movetypetext = returnObjById('movetypeparam');
	movetypetext.value = 'Deliver';
    submitForm();
}

function setPlaceBlade(blade)
{
	// search for html input fields
	var movetypetext = returnObjById('movetypeparam');
	movetypetext.value = 'Place_Farmer';
	var movepar1text = returnObjById('moveparam1');
	movepar1text.value = blade;
    //alert('the blade: '+blade);
    submitForm();
}

// move farmer
//function setMoveBlade(blade)
function bladeClick(blade)
{
	//get action card
	var movetypetext = returnObjById('movetypeparam');
    if(movetypetext.value == 'ActionCard1') {
        //now get from or to pos
        var actpartext = returnObjById('actionparam1');
        var actpar2text = returnObjById('actionparam2');
        if(actpartext.value != '') {
            // TODO: check if valid
            actpar2text.value = blade;
            // execute move
            submitForm();
        } else {
            // TODO: check if valid
            actpartext.value = blade;
        }
    } else {
        //move
        // TODO: check if valid
        movetypetext.value = 'Move_Farmer';
        var movepar1text = returnObjById('moveparam1');
        movepar1text.value = blade;
        submitForm();
    }
}

// set region for delivering
function setMoveRegion(region, qm)
{
    // search for the region fruitcard
    var fruitcarddiv = returnObjById('region_fruitcard_'+region);
    // if this has already been pressed (pressed state)
    var currentstate = !(fruitcarddiv.style.backgroundColor == "red" );
	// search for html input fields
	var movetypetext = returnObjById('movetypeparam');
	var movepar1text = returnObjById('moveparam1');
    var movepar2text = returnObjById('moveparam2'); // for questionmarks
    //state handling
    if(currentstate) {
        fruitcarddiv.style.backgroundColor = "red";
        if(movepar1text.value=='') {
            movepar1text.value = region;
            movepar2text.value = '0';
        } else {
            movepar1text.value = movepar1text.value + ',' + region;
            movepar2text.value = movepar2text.value + ',0';
        }
        //handle questionmark fruit selection
        if(qm == '1') {
            globalStateQm = 1;
            showFruitPopup() ;
        }
        //handle '-1' actioncard (index 3)
        /*
        var actpartext = returnObjById('actionparam1');
        if( actpartext.value == '3') {
            var actpar2text = returnObjById('actionparam2');
            actpar2text.value = region;
            fruitcarddiv.style.border = "3px solid white";
        }*/
        
    } else {
        fruitcarddiv.style.backgroundColor = "transparent";
        //fruitcarddiv.style.border = "0";
        var war = movepar1text.value.split(",");		
        var warqm = movepar2text.value.split(",");	
        var rst = '';
        var rstqm = '';
        for(var i=0; i<war.length; i++)
		{
            if( ! (parseInt(war[i]) == region) )
            {
                if(rst == '')  {
                    rst = war[i];
                    rstqm = warqm[i];
                }
                else {
                    rst = rst + ',' + war[i];
                    rstqm = rstqm + ',' + warqm[i];
                }
            }
        }
        movepar1text.value = rst; 
        movepar2text.value = rstqm; 

    }
}

function getSelectedFruitFromPopup() 
{
    var selectedfruit = 0;
    for(i=0;i<6; i++) {
        fruitobj = document.getElementById('fruitpopup_fruit_'+i);
        if( fruitobj.style.backgroundColor == "red" ) {
            selectedfruit = i;
        }
    }
    return selectedfruit;
}

function hideFruitPopup() 
{
    // save selected fruit
    var fruitobj;
    var selectedfruit = getSelectedFruitFromPopup();
    var actpartext = returnObjById('actionparam1');
    if (globalStateActionCard3 != 0 ) {
        // action card 3 was pressed.
        var actpar2text = returnObjById('actionparam2');
        actpar2text.value = selectedfruit;
        globalStateActionCard3 = 0;
    }
    if(globalStateQm != 0) {
        // questionmark region delivering
        var movepar2text = returnObjById('moveparam2'); 
        //if( movepar2text.value == 
        movepar2text.value = movepar2text.value.substr(0,movepar2text.value.length-1) + selectedfruit;
        globalStateQm = 0;
    }
    // deselect all fruits before hiding,
    // when shown again, no fruit will be selected.
    fruitpopup_deselectAllFruits();
    //hide blanket and popup
	var el = document.getElementById('blanket');
    var popupwin = document.getElementById('fruitpopup_window');
    el.style.display = 'none';
    popupwin.style.display = 'none';
}

function fruitpopup_deselectAllFruits() 
{
    var fruitobj;
    for(i=0;i<6; i++) {
        fruitobj = document.getElementById('fruitpopup_fruit_'+i);
        fruitobj.style.backgroundColor = "transparent";
    }
}

function fruitpopup_selectFruit(fruitindex)
{
    // disable all other fruits
    fruitpopup_deselectAllFruits();
    var fruitobj;
    
    fruitobj = document.getElementById('fruitpopup_fruit_'+fruitindex);
    fruitobj.style.backgroundColor = "red";
}

function showFruitPopup() 
{

	var el = document.getElementById('blanket');
    var popupwin = document.getElementById('fruitpopup_window');
	if ( el.style.display == 'none' )  {	
        el.style.display = 'block';
        popupwin.style.display = 'block';
        //fruitpopup_selectFruit(-1);
    } else {
        el.style.display = 'none';
        popupwin.style.display = 'none';
    }
    
/*
    var el;
    el = document.createElement('div');
    el.style="position: absolute; left: 1px; top: 1px; width 1280px; height: 1024px; background-color: rgba(0,0,0,64); padding: 0; margin: 0;";
    el.innerHTML = "";
    var el2;
    el2 = document.createElement('div');
    el2.innerHTML = "FRUITS";
    el.appendChild(el2);
    
    document.appendChild(el);
    */
}

function new_game_load() {
	// set a different player name for each player at startup
	for(var i=0; i<4; i++)
	{
		var el2 = document.getElementById('player_name_select_' + i);	
		el2.selectedIndex = i;
	}
}

function player_select_color(player_ind, color_ind) {
	var el = document.getElementById('inp_color' + player_ind);
	el.value = color_ind;

	for(var i=0; i<4; i++)
	{
		var el2 = document.getElementById('player_color_selector_box' + player_ind + '_' + i);	
		//alert(el2);
		if(i == color_ind ) {
			el2.className = 'player_color_selector_selected';
		}
		else {
			el2.className = 'player_color_selector';
		}
	}
}


// sets the hidden text field for player name to be submitted by form
// not used, because select can submit a field to POSTs
function new_game_player_numberofplayers_changed(combo_index) {
	if(combo_index >=0) {
		for(var i=0; i<4; i++)
		{
			var el = document.getElementById('new_player_box_' + i);
			if(i<combo_index+2) { //0 and 1 players are not an option in the select
				el.style="display: block;";
			}
			else {
				el.style="display: none;";
			}
		}

	}
	
}
