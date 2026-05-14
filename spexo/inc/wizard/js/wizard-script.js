(function($){

    function showNotice(message, messageType) {
        $('.theme-wizard-main').before('<div class="theme-wizard-notice notice notice-'+ messageType +'">'+ message +'</div>');
    }

    function removeNotice() {
        $(".theme-wizard-notice").slideUp(300, function() { $(this).remove(); });
    }

    $('[data-tab="theme-welcome"]').addClass('nav-tab-active');
    $('#theme-welcome').addClass('active');
    
    $(document).ready(function(){

        $('[data-tab="theme-welcome"]').addClass('nav-tab-active');
        $('#theme-welcome').addClass('active');
    });
    
    /* ----------- skip wizard with confirmation - [START] -----------*/

    jQuery(document).on('click', '.skip-theme-wizard', function(e) {
        e.preventDefault();    
        tmpcoder_skip_theme_wizard_confirm_popup_open();
    });
    
    jQuery(document).on('click', '.tmpcoder-skip-theme-wizard-confirm-button', function(e) {
        e.preventDefault();
        jQuery('.tmpcoder-skip-theme-wizard-popup-wrap').fadeOut();

        tmpcoder_skip_theme_setup_wizard();
    });

    jQuery(document).on('click','.popup-close', function(){
        jQuery('#tmpcoder-skip-theme-wizard-confirm-popup').fadeOut();
        jQuery('.tmpcoder-admin-popup').fadeOut();
        jQuery('.tmpcoder-skip-theme-wizard-popup-wrap').fadeOut();
    })
    
    function tmpcoder_skip_theme_wizard_confirm_popup_open() {
        jQuery('#tmpcoder-skip-theme-wizard-confirm-popup').fadeIn();
        jQuery('.tmpcoder-skip-theme-wizard-popup-wrap').fadeIn();
        jQuery('.tmpcoder-admin-popup').fadeIn();
    }
    
    function tmpcoder_skip_theme_setup_wizard() {
        window.location.href = tmpcoderMessages.tmpcoder_admin_url;
    }
    /* ----------- skip wizard with confirmation - [END] -----------*/

    $( document ).on('click', '#theme-welcome .next-step-btn', function(){
        const $btn = $(this);
        const originalLabel = $btn.find('.spexo-wizard-btn__label').text();
        const loadingLabel = tmpcoderMessages.wizard_button_loading_text || 'Activating...';
        $btn.prop('disabled', true);
        $btn.css('pointer-events', 'none');
        $btn.addClass('is-loading');
        $btn.find('.spexo-wizard-btn__label').text(loadingLabel);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: tmpcoderMessages.wizard_one_step_action,
                nonce: tmpcoderMessages.wizard_one_step_nonce,
            },
            success: function(resp){
                if ( resp && resp.success && resp.data && resp.data.redirect_url ) {
                    window.location.href = resp.data.redirect_url;
                    return;
                }

                const errorMessage = (resp && resp.data && resp.data.message) ? resp.data.message : tmpcoderMessages.wizard_setup_failed;
                showNotice(errorMessage, 'error');
                setTimeout(removeNotice, 6000);
                $btn.prop('disabled', false);
                $btn.css('pointer-events', 'auto');
                $btn.removeClass('is-loading');
                $btn.find('.spexo-wizard-btn__label').text(originalLabel);
            },
            error: function(xhr){
                let errorMessage = tmpcoderMessages.wizard_setup_failed;
                if ( xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message ) {
                    errorMessage = xhr.responseJSON.data.message;
                }
                showNotice(errorMessage, 'error');
                setTimeout(removeNotice, 6000);
                $btn.prop('disabled', false);
                $btn.css('pointer-events', 'auto');
                $btn.removeClass('is-loading');
                $btn.find('.spexo-wizard-btn__label').text(originalLabel);
            },
        });
    });

    $(document).on('click', '.theme-wizard-nav .nav-tab', function(e){
        e.preventDefault();
        $('[data-tab="theme-welcome"]').addClass('nav-tab-active');
        $('.tab-content').removeClass('active');
        $('#theme-welcome').addClass('active');
    });

})(jQuery)