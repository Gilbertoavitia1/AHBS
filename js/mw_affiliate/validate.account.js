Validation.add('validate-re_account_number', 'Please make sure your account number match.', function(v) {
    var conf = $('re_account_number');
    var pass = false;
    if ($('account_number')) {
        pass = $('account_number');
    }

    return (pass.value == conf.value);
});
