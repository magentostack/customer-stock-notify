define([
    "jquery",
    "mage/mage",
    "mage/url"
], function ($, mage, urlBuilder) {

    return function notifier() {
        $(document).ready(function () {
            $('#form-validate-stock').mage(
                'validation', {
                    submitHandler: function (form) {
                        let customUrl = urlBuilder.build('outofstocknotifs/ajax/index');
                        $.ajax({
                            url: customUrl,
                            data: $('#form-validate-stock').serialize(),
                            type: 'POST',
                            dataType: 'json',
                            beforeSend: function () {
                                // show some loading icon
                                $('.notif_inside_container .show_msg_div').html('Please wait...');
                                $('.notif_inside_container .show_msg_div').show();
                            },
                            success: function (data, status, xhr) {
                                // data contains your controller response
                                // alert(data.msg);
                                $('.notif_inside_container .outofstocknotifier_email').val('');
                                $('.notif_inside_container .show_msg_div').html(data.msg).delay(5000).fadeOut(500);
                            },
                            error: function (xhr, status, errorThrown) {
                                console.log('An error occurred. Please try again.');
                                console.log(errorThrown);
                            }
                        });
                    }
                }
            );
        });
    }
});
