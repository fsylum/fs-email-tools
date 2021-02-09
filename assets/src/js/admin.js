(function ($) {
    $(document).ready(function () {
        $('#fs-email-tools-reroute-status').on('change', function () {
            let isChecked = $(this).is(':checked');

            $('#fs-email-tools-reroute-recipients, #fs-email-tools-reroute-append-status').prop('disabled', !isChecked);
            $('#fs-email-tools-reroute-append-status').trigger('change');
        }).change();

        $('#fs-email-tools-reroute-append-status').on('change', function () {
            let isChecked         = $(this).is(':checked');
            let isRerouteEnabled = $('#fs-email-tools-reroute-status').is(':checked');

            $('#fs-email-tools-reroute-append-location').prop('disabled', (!isChecked || !isRerouteEnabled));
        }).change();

        $('#fs-email-tools-bcc-status').on('change', function () {
            $('#fs-email-tools-bcc-recipients').prop('disabled', !$(this).is(':checked'));
        }).change();

        $('#fs-email-tools-log-status').on('change', function () {
            let isChecked = $(this).is(':checked');

            $('.fs-email-tools-log-keep-indefinitely').prop('disabled', !isChecked);
            $('.fs-email-tools-log-keep-indefinitely').trigger('change')
        }).change();

        $('.fs-email-tools-log-keep-indefinitely').on('change', function () {
            let checkedValue = $(this).filter(':checked').val();
            let isLogEnabled = $('#fs-email-tools-log-status').is(':checked');

            if (typeof checkedValue === 'undefined') {
                return;
            }

            $('#fs-email-tools-log-keep-in-days').prop('disabled', (checkedValue == 1 || !isLogEnabled));
        }).change();

        $('.js-delete-email-log').on('click', function (e) {
            e.preventDefault();

            $('#fs-email-tools-dialog-delete').dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    Cancel: function() {
                        $(this).dialog('close');
                    },
                    Confirm: function() {
                        $(this).dialog('close');
                    },
                }
            });
        });
    });
})(jQuery);
