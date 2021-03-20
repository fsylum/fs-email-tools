<div id="fs-email-tools-dialog-email-log-view" class="hidden">
    <?php esc_html_e('Loading', 'fs-email-tools'); ?>&hellip;
</div>

<script type="text/html" id="tmpl-fs-email-tools-dialog-email-log-view-js">
    <h2 class="nav-tab-wrapper">
        <a href="#tab-email-log-content" class="nav-tab nav-tab-active"><?php esc_html_e('Content', 'fs-email-tools'); ?></a>
        <a href="#tab-email-log-headers" class="nav-tab"><?php esc_html_e('Headers', 'fs-email-tools'); ?></a>
        <a href="#tab-email-log-attachments" class="nav-tab"><?php esc_html_e('Attachments', 'fs-email-tools'); ?></a>
    </h2>

    <div id="tab-email-log-content" class="tab-pane">
        {{{ data.message }}}
    </div>
    <div id="tab-email-log-headers" class="tab-pane hidden">
        <pre>{{{ data.headers }}}</pre>
    </div>
    <div id="tab-email-log-attachments" class="tab-pane hidden">
        <# if (data.attachments.length) { #>
            <div class="notice notice-info">
                <p><?php esc_html_e('Attachments are only logged when the email is first sent and might not be available at the time of viewing.', 'fs-email-tools'); ?></p>
            </div>

            <# _(data.attachments).each(function(attachment) { #>
                <a href="{{{ attachment.url }}}" class="button button-primary" target="_blank" rel="noopener noreferrer">{{{ attachment.name }}} ({{{ attachment.size }}})</a>
            <# }) #>
        <# } else { #>
            <div class="notice notice-error">
                <p><?php esc_html_e('There are no attachments for this email.', 'fs-email-tools'); ?></p>
            </div>
        <# } #>
    </div>
</script>
