(function ($) {
    // Set dialog width to 700px max, but make it responsive for lower screen resolution, modified from https://gist.github.com/jameswilson/869c0b7d66881e8f9c0e5f3dc8b33156
    let resizeDialog = function () {
        let $visibleDialogs = $('.ui-dialog:visible');

        $visibleDialogs.each(function () {
            let $this  = $(this);
            let dialog = $this.find('.ui-dialog-content').data('ui-dialog');
            let wWidth = $(window).width();

            if (wWidth < (parseInt(dialog.options.maxWidth, 10) + 50)) {
                $this.css('max-width', 'none');
                $this.css('width', '95%');
            } else {
                $this.css('max-width', 'none');
                $this.css('width', dialog.options.maxWidth + 'px');
            }

            dialog.option('position', dialog.options.position);
        });
    };

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

            if (window.confirm('This email log will be permanently deleted and cannot be recovered. Are you sure?')) {
                window.location = this.href;
            };
        });

        $('#doaction, #doaction2').on('click', function (e) {
            e.preventDefault();

            if (window.confirm('The selected email logs will be permanently deleted and cannot be recovered. Are you sure?')) {
                $(this).closest('form').trigger('submit');
            };
        });

        let startDateDatepicker = $('#filter-start-date').datepicker({
            nextText: '&rsaquo;',
            prevText: '&lsaquo;'
        });

        let endDateDatepicker = $('#filter-end-date').datepicker({
            nextText: '&rsaquo;',
            prevText: '&lsaquo;'
        });

        startDateDatepicker.on('change', function () {
            endDateDatepicker.datepicker('option', 'minDate', startDateDatepicker.datepicker('getDate'));
        });

        endDateDatepicker.on('change', function () {
            startDateDatepicker.datepicker('option', 'maxDate', endDateDatepicker.datepicker('getDate'));
        });

        $(window).on('resize', resizeDialog);

        var nl2br = function (str, isXhtml) {
            if (typeof str === 'undefined' || str === null) {
                return '';
            }

            const breakTag = (isXhtml || typeof isXhtml === 'undefined') ? '<br ' + '/>' : '<br>';

            return (str + '').replace(/(\r\n|\n\r|\r|\n)/g, breakTag + '$1');
        }

        $('#fs-email-tools-dialog-email-log-view').dialog({
            autoOpen: false,
            closeOnEscape: true,
            dialogClass: 'wp-dialog',
            draggable: false,
            maxWidth: 700,
            minWidth: 100,
            modal: true,
            resizable: false,
            title: 'Email Preview',
            open: function (event, ui) {
                var $dialog = $(this);

                wp.ajax.post('fs_email_tools_get_email_log', {
                    id: $dialog.data('emailLogId')
                })
                .done(function (res) {
                    var html = [];

                    html.push('<a href="#">Show headers</a>');
                    html.push('<pre>');
                    html.push($.trim(res.headers));
                    html.push('</pre>');
                    html.push(res.message);
                    html.push('<p><strong>Attachments</strong></p>');
                    html.push('<ul>');
                    html.push('<li><a href="#">basename1.pdf</a> (404 kB)</li>');
                    html.push('<li><a href="#">basename2.pdf</a> (12 kB)</li>');
                    html.push('<li><a href="#">basename3.pdf</a> (Not Found)</li>');
                    html.push('</ul>');

                    $dialog.html(html.join(''));
                    resizeDialog();
                })
                .fail(function (res) {
                    $dialog.dialog('close');
                    alert('The email log cannot be retrieved. Please try again later.');
                });
            },
            close: function (event, ui) {
                $(this).html('Loading&hellip;');
            },
        });

        $('.js-view-email-log').on('click', function (e) {
            e.preventDefault();
            $('#fs-email-tools-dialog-email-log-view').data('emailLogId', $(this).data('id')).dialog('open');
        });
    });
})(jQuery);
