
document.observe('dom:loaded', function () {
	
	if($('affiliate_money_affiliate_withdrawn_period').value == '1'){
		$('affiliate_money_affiliate_withdrawn_day').up(1).show();
		$('affiliate_money_affiliate_withdrawn_month').up(1).hide();
		
	}else if($('affiliate_money_affiliate_withdrawn_period').value == '2'){
		$('affiliate_money_affiliate_withdrawn_day').up(1).hide();
		$('affiliate_money_affiliate_withdrawn_month').up(1).show();
	}
	$('affiliate_money_affiliate_withdrawn_period').observe('change', function () {
		
		if($('affiliate_money_affiliate_withdrawn_period').value == '1'){
			$('affiliate_money_affiliate_withdrawn_day').up(1).show();
			$('affiliate_money_affiliate_withdrawn_month').up(1).hide();
			
		}else if($('affiliate_money_affiliate_withdrawn_period').value == '2'){
			$('affiliate_money_affiliate_withdrawn_day').up(1).hide();
			$('affiliate_money_affiliate_withdrawn_month').up(1).show();
		}
	}); 






});
//var $j_mw = jQuery.noConflict();
/*
$j_mw(function(){
	
	$j_mw("#withdraw_massaction-select").removeClass('required-entry');
	
	if($j_mw("#affiliate_money_affiliate_withdrawn_period").val()=='1')
	{   
		$j_mw("#affiliate_money_affiliate_withdrawn_day").parent().parent().show();
		$j_mw("#affiliate_money_affiliate_withdrawn_month").parent().parent().hide();
		
	}
	else if($j_mw("#affiliate_money_affiliate_withdrawn_period").val()=='2')
	{   
		$j_mw("#affiliate_money_affiliate_withdrawn_month").parent().parent().show();
		$j_mw("#affiliate_money_affiliate_withdrawn_day").parent().parent().hide();
		
	}
	$j_mw("#affiliate_money_affiliate_withdrawn_period").change(function(){
		if($j_mw("#affiliate_money_affiliate_withdrawn_period").val()=='1')
		{   
			$j_mw("#affiliate_money_affiliate_withdrawn_day").parent().parent().show();
			$j_mw("#affiliate_money_affiliate_withdrawn_month").parent().parent().hide();
			
		}
		else if($j_mw("#affiliate_money_affiliate_withdrawn_period").val()=='2')
		{   
			$j_mw("#affiliate_money_affiliate_withdrawn_month").parent().parent().show();
			$j_mw("#affiliate_money_affiliate_withdrawn_day").parent().parent().hide();
			
		}
	});
	
	
	if($j_mw("#auto_withdrawn").val()=='1')
	{   
		$j_mw("#withdrawn_level").parent().parent().show();
		
	}
	else if($j_mw("#auto_withdrawn").val()=='2')
	{   
		$j_mw("#withdrawn_level").parent().parent().hide();
		
	}
	$j_mw("#auto_withdrawn").change(function(){
		if($j_mw("#auto_withdrawn").val()=='1')
		{   
			$j_mw("#withdrawn_level").parent().parent().show();
			
		}
		else if($j_mw("#auto_withdrawn").val()=='2')
		{   
			$j_mw("#withdrawn_level").parent().parent().hide();
			
		}
	});
	if($j_mw("#payment_gateway").val()=='banktransfer')
	{   
		$j_mw("#bank_name").parent().parent().show();
		if(!$j_mw("#bank_name").hasClass('required-entry')) $j_mw("#bank_name").addClass('required-entry');
		$j_mw("#name_account").parent().parent().show();
		if(!$j_mw("#name_account").hasClass('required-entry')) $j_mw("#name_account").addClass('required-entry');
		$j_mw("#bank_country").parent().parent().show();
		if(!$j_mw("#bank_country").hasClass('required-entry')) $j_mw("#bank_country").addClass('required-entry');
		$j_mw("#swift_bic").parent().parent().show();
		if(!$j_mw("#swift_bic").hasClass('required-entry')) $j_mw("#swift_bic").addClass('required-entry');
		$j_mw("#account_number").parent().parent().show();
		if(!$j_mw("#account_number").hasClass('required-entry')) $j_mw("#account_number").addClass('required-entry');
		$j_mw("#re_account_number").parent().parent().show();
		if(!$j_mw("#re_account_number").hasClass('required-entry')) $j_mw("#re_account_number").addClass('required-entry');
		$j_mw("#payment_email").parent().parent().hide();
		if($j_mw("#payment_email").hasClass('required-entry')) $j_mw("#payment_email").removeClass('required-entry');
	}
	else
	{   
		$j_mw("#bank_name").parent().parent().hide();
		if($j_mw("#bank_name").hasClass('required-entry')) $j_mw("#bank_name").removeClass('required-entry');
		$j_mw("#name_account").parent().parent().hide();
		if($j_mw("#name_account").hasClass('required-entry')) $j_mw("#name_account").removeClass('required-entry');
		$j_mw("#bank_country").parent().parent().hide();
		if($j_mw("#bank_country").hasClass('required-entry')) $j_mw("#bank_country").removeClass('required-entry');
		$j_mw("#swift_bic").parent().parent().hide();
		if($j_mw("#swift_bic").hasClass('required-entry')) $j_mw("#swift_bic").removeClass('required-entry');
		$j_mw("#account_number").parent().parent().hide();
		if($j_mw("#account_number").hasClass('required-entry')) $j_mw("#account_number").removeClass('required-entry');
		$j_mw("#re_account_number").parent().parent().hide();
		if($j_mw("#re_account_number").hasClass('required-entry')) $j_mw("#re_account_number").removeClass('required-entry');
		$j_mw("#payment_email").parent().parent().show();
		if(!$j_mw("#payment_email").hasClass('required-entry')) $j_mw("#payment_email").addClass('required-entry');

	};
	$j_mw("#payment_gateway").change(function(){
		if($j_mw("#payment_gateway").val()=='banktransfer')
		{   
			$j_mw("#bank_name").parent().parent().show();
			if(!$j_mw("#bank_name").hasClass('required-entry')) $j_mw("#bank_name").addClass('required-entry');
			$j_mw("#name_account").parent().parent().show();
			if(!$j_mw("#name_account").hasClass('required-entry')) $j_mw("#name_account").addClass('required-entry');
			$j_mw("#bank_country").parent().parent().show();
			if(!$j_mw("#bank_country").hasClass('required-entry')) $j_mw("#bank_country").addClass('required-entry');
			$j_mw("#swift_bic").parent().parent().show();
			if(!$j_mw("#swift_bic").hasClass('required-entry')) $j_mw("#swift_bic").addClass('required-entry');
			$j_mw("#account_number").parent().parent().show();
			if(!$j_mw("#account_number").hasClass('required-entry')) $j_mw("#account_number").addClass('required-entry');
			$j_mw("#re_account_number").parent().parent().show();
			if(!$j_mw("#re_account_number").hasClass('required-entry')) $j_mw("#re_account_number").addClass('required-entry');
			$j_mw("#payment_email").parent().parent().hide();
			if($j_mw("#payment_email").hasClass('required-entry')) $j_mw("#payment_email").removeClass('required-entry');
			
		}
		else 
		{   
			$j_mw("#bank_name").parent().parent().hide();
			if($j_mw("#bank_name").hasClass('required-entry')) $j_mw("#bank_name").removeClass('required-entry');
			$j_mw("#name_account").parent().parent().hide();
			if($j_mw("#name_account").hasClass('required-entry')) $j_mw("#name_account").removeClass('required-entry');
			$j_mw("#bank_country").parent().parent().hide();
			if($j_mw("#bank_country").hasClass('required-entry')) $j_mw("#bank_country").removeClass('required-entry');
			$j_mw("#swift_bic").parent().parent().hide();
			if($j_mw("#swift_bic").hasClass('required-entry')) $j_mw("#swift_bic").removeClass('required-entry');
			$j_mw("#account_number").parent().parent().hide();
			if($j_mw("#account_number").hasClass('required-entry')) $j_mw("#account_number").removeClass('required-entry');
			$j_mw("#re_account_number").parent().parent().hide();
			if($j_mw("#re_account_number").hasClass('required-entry')) $j_mw("#re_account_number").removeClass('required-entry');
			$j_mw("#payment_email").parent().parent().show();
			if(!$j_mw("#payment_email").hasClass('required-entry')) $j_mw("#payment_email").addClass('required-entry');
			
		}
	});
	
	
	
});
*/