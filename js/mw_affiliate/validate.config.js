Validation.addAllThese([
    [
     	'validate-commission-discount', 
     	'Invalid format', 
     	function(value) {
     		return /^[0-9]+\.?[0-9]*([%])?$/.test(value);
     	}
    ],
]);