var jQueryOWElementor = jQuery.noConflict();

(function (jQuery) {
    window.onload = (event) => {
        //If post is in workflow and any user like editor edit the post
        if (elementor_is_in_workflow === 'true') {
            jQuery('#elementor-panel-saver-button-publish').addClass('owf-elementor-hidden');
        }

        // Display submit to workflow button
        if (owf_process === 'submit') {
            jQuery('#elementor-panel-saver-button-publish').addClass('owf-elementor-hidden');
            jQuery('#elementor-panel-footer-sub-menu-item-save-template').after('<div id="elementor-panel-footer-sub-menu-item-submit-workflow" class="elementor-panel-footer-sub-menu-item"><input type="hidden" id="hi_is_team" name="hi_is_team" /><i class="elementor-icon eicon-folder" aria-hidden="true"></i><span class="elementor-title">' + owf_submit_workflow_vars.submitToWorkflowButton + '</span></div>');
        }

        // Display sign-off button
        if (owf_process === 'sign-off') {
            jQuery('#elementor-panel-saver-button-publish').addClass('owf-elementor-hidden');
            jQuery('#elementor-panel-footer-sub-menu-item-save-template').after('<div id="elementor-panel-footer-sub-menu-item-signoff-workflow" class="elementor-panel-footer-sub-menu-item"><i class="elementor-icon eicon-folder" aria-hidden="true"></i><span class="elementor-title">' + owf_submit_step_vars.signOffButton + '</span></div>');
        }
    };
}(jQueryOWElementor));
