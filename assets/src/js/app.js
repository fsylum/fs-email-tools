(function ($) {
    $(document).ready(function () {
        $('#fs-email-tools-reroute-status').on('change', function () {
            let isChecked = $(this).is(':checked');

            $('#fs-email-tools-reroute-recipients, #fs-email-tools-reroute-append-status').prop('disabled', !isChecked);
        }).change();

        $('#fs-email-tools-reroute-append-status').on('change', function () {
            let isChecked = $(this).is(':checked');

            $('#fs-email-tools-reroute-append-location').prop('disabled', !isChecked);
        }).change();

        $('#fs-email-tools-log-status').on('change', function () {
            let isChecked = $(this).is(':checked');

            $('.fs-email-tools-log-keep-indefinitely').prop('disabled', !isChecked);
        }).change();

        $('.fs-email-tools-log-keep-indefinitely').on('change', function () {
            let checkedValue = $(this).filter(':checked').val();

            if (typeof checkedValue === 'undefined') {
                return;
            }

            $('#fs-email-tools-log-keep-in-days').prop('disabled', checkedValue == 1);
        }).change();
    });
})(jQuery);
