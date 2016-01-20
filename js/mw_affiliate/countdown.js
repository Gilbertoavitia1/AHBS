function countdown(dateDiff,text)
{   
	fullDays = parseInt(dateDiff/(60*60*24));
	
    fullHours = parseInt((dateDiff-(fullDays*60*60*24))/(60*60));
    
    fullMinutes = parseInt((dateDiff-(fullDays*60*60*24)-(fullHours*60*60))/60);
    fullSecond = parseInt((dateDiff - (fullDays*60*60*24) - (fullHours*60*60) - fullMinutes * 60));
    var result = "";
    
    if(fullDays) result += fullDays +' days, ';
    
    if(fullHours) result += fullHours+' hours, ';
    else if(fullDays) result += fullHours+' hours, ';
    
    if(fullMinutes) result += fullMinutes+' minutes, ';
    else if(fullHours || fullDays) result +=fullMinutes+' minutes, ';
    if(fullSecond) result += fullSecond +' seconds.';
    else if(fullHours || fullDays ||fullMinutes) result += fullSecond +' seconds.';
    result = text +" "+result;
    document.getElementById('clock').innerHTML = result;
    dateDiff--;
    if(dateDiff!=0)
    {
    	setTimeout('countdown('+ dateDiff + ',"' +text+ '");',1000);
    }
    if(dateDiff==0)  document.getElementById('clock').innerHTML = "";
    
}
