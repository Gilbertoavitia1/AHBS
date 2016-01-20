function show_hide_checkbox_fields_credit(objName,condition)
{
	document.getElementById(objName).disabled = condition==0?"disabled":"";
}

