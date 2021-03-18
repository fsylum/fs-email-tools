<div id="fs-email-tools-dialog-email-log-view" class="hidden">
    Loading&hellip;
</div>

<script type="text/html" id="tmpl-fs-email-tools-dialog-email-log-view-js">
    <h2 class="nav-tab-wrapper">
        <a href="#tab-email-log-content" class="nav-tab nav-tab-active">Content</a>
        <a href="#tab-email-log-headers" class="nav-tab">Headers</a>
        <a href="#tab-email-log-attachments" class="nav-tab">Attachments</a>
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
                    <p>Attachments are only logged when the email is first sent and might not be available at the time of viewing.</p>
                </div>

                <# _(data.attachments).each(function(attachment) { #>
                    <a href="{{{ attachment.url }}}" class="button button-primary" target="_blank" rel="noopener noreferrer">{{{ attachment.name }}} ({{{ attachment.size }}})</a>
                <# }) #>
            <# } else { #>
                <div class="notice notice-error">
                    <p>There are no attachments for this email</p>
                </div>
            <# } #>
    </div>
</script>
