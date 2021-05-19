<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>


<?php

    /**
     * Hook to add extra fields at the top of the Form Tab
     *
     * @param array $settings
     *
     */
    do_action( 'wpbs_submenu_page_settings_tab_form_top', $settings );

?>

<!-- Email Templates -->
<h2>
    <?php echo __( 'General Settings', 'wp-booking-system' ); ?>
</h2>


<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="email_logs">
        <?php echo __( 'Enable Email Logging', 'wp-booking-system' ); ?>
        <?php echo wpbs_get_output_tooltip(__("Save a log of all emails sent to a customer. Logs will be available in the Booking modal popup.", 'wp-booking-system'));?>
    </label>

    <div class="wpbs-settings-field-inner">
        <label for="email_logs" class="wpbs-checkbox-switch">
            <input name="wpbs_settings[email_logs]" type="checkbox" id="email_logs" class="regular-text wpbs-settings-toggle" <?php echo (!empty($settings['email_logs'])) ? 'checked' : ''; ?>>
            <div class="wpbs-checkbox-slider"></div>
        </label>
    </div>
</div>

<!-- Form Styling -->
<h2><?php echo __( 'Form Styling', 'wp-booking-system' ); ?></h2>

<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-medium">
    <label class="wpbs-settings-field-label" for="form_styling">
        <?php echo __( 'Default Styling', 'wp-booking-system' ); ?>
        <?php echo wpbs_get_output_tooltip(__("By default we use our own styling to make sure everything looks fine. You can select 'Theme Styling' to disable the output of our custom CSS.", 'wp-booking-system'));?>
    </label>

    <div class="wpbs-settings-field-inner">
        <select name="wpbs_settings[form_styling]" id="form_styling">
            <option value="default" <?php echo isset($settings['form_styling']) ? selected($settings['form_styling'], 'default', false) : '';?> ><?php echo __('Plugin Styling','wp-booking-system') ?></option>
            <option value="theme" <?php echo isset($settings['form_styling']) ? selected($settings['form_styling'], 'theme', false) : '';?>><?php echo __('Theme Styling','wp-booking-system') ?></option>
        </select>
        
    </div>
</div>

<div class="wpbs-settings-fields-form-styling">

    <!-- Button Style -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline">
        <label class="wpbs-settings-field-label"><?php echo __( 'Button Style', 'wp-booking-system' ); ?></label>
        <div class="wpbs-settings-field-inner wpbs-settings-field-inner-inline">
            <input name="wpbs_settings[button_background_color]" type="text" class="wpbs-colorpicker" value="<?php echo ( ! empty( $settings['button_background_color'] ) ? esc_attr( $settings['button_background_color'] ) : '' ); ?>" />
            <small><?php echo __('Background Color', 'wp-booking-system') ?></small>
        </div>
        <div class="wpbs-settings-field-inner wpbs-settings-field-inner-inline">
            <input name="wpbs_settings[button_text_color]" type="text" class="wpbs-colorpicker" value="<?php echo ( ! empty( $settings['button_text_color'] ) ? esc_attr( $settings['button_text_color'] ) : '' ); ?>" />
            
            <small><?php echo __('Text Color','wp-booking-system') ?></small>
        </div>
    </div>

    <!-- Button Hover Style  -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline">
        <label class="wpbs-settings-field-label"><?php echo __( 'Button Hover Style', 'wp-booking-system' ); ?></label>
        <div class="wpbs-settings-field-inner wpbs-settings-field-inner-inline">
            <input name="wpbs_settings[button_background_hover_color]" type="text" class="wpbs-colorpicker" value="<?php echo ( ! empty( $settings['button_background_hover_color'] ) ? esc_attr( $settings['button_background_hover_color'] ) : '' ); ?>" />
            <small><?php echo __('Background Color', 'wp-booking-system') ?></small>
        </div>
        <div class="wpbs-settings-field-inner wpbs-settings-field-inner-inline">
            <input name="wpbs_settings[button_text_hover_color]" type="text" class="wpbs-colorpicker" value="<?php echo ( ! empty( $settings['button_text_hover_color'] ) ? esc_attr( $settings['button_text_hover_color'] ) : '' ); ?>" />
            <small><?php echo __('Text Color','wp-booking-system') ?></small>
        </div>
    </div>

</div>

<!-- reCAPTCHA Keys -->
<h2><?php echo __( 'Google reCAPTCHA', 'wp-booking-system' ); ?></h2>


<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-medium">
    <label class="wpbs-settings-field-label" for="recaptcha_type">
        <?php echo __( 'reCAPTCHA Type', 'wp-booking-system' ); ?>
    </label>

    <div class="wpbs-settings-field-inner">
        <select name="wpbs_settings[recaptcha_type]" id="recaptcha_type">
            <option value="v2_tickbox" <?php echo isset($settings['recaptcha_type']) ? selected($settings['recaptcha_type'], 'v2_tickbox', false) : '';?> ><?php echo __('reCAPTCHA v2 Tickbox','wp-booking-system') ?></option>
            <option value="v3" <?php echo isset($settings['recaptcha_type']) ? selected($settings['recaptcha_type'], 'v3', false) : '';?>><?php echo __('reCAPTCHA v3','wp-booking-system') ?></option>
        </select>
        
    </div>
</div>

<!-- reCAPTCHA v2 -->
<div class="wpbs-settings-field-wrapper-recapthca wpbs-settings-field-wrapper-recapthca-v2_tickbox wpbs-hide">

    <!-- reCAPTCHA Keys -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline  wpbs-settings-field-large">

        <label class="wpbs-settings-field-label" for="recaptcha_v2_site_key"><?php echo __( 'v2 Site Key', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <input name="wpbs_settings[recaptcha_v2_site_key]" id="recaptcha_v2_site_key" type="text" value="<?php echo ( ! empty( $settings['recaptcha_v2_site_key'] ) ? esc_attr( $settings['recaptcha_v2_site_key'] ) : '' ); ?>" />
        </div>
        
    </div>

    <!-- reCAPTCHA Keys -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">

        <label class="wpbs-settings-field-label" for="recaptcha_v2_secret_key"><?php echo __( 'v2 Secret Key', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <input name="wpbs_settings[recaptcha_v2_secret_key]" id="recaptcha_v2_secret_key" type="text" value="<?php echo ( ! empty( $settings['recaptcha_v2_secret_key'] ) ? esc_attr( $settings['recaptcha_v2_secret_key'] ) : '' ); ?>" />
        </div>
        
    </div>

</div>

<!-- reCAPTCHA v3 -->
<div class="wpbs-settings-field-wrapper-recapthca wpbs-settings-field-wrapper-recapthca-v3  wpbs-hide">

    <!-- reCAPTCHA Keys -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline  wpbs-settings-field-large">

        <label class="wpbs-settings-field-label" for="recaptcha_v3_site_key"><?php echo __( 'v3 Site Key', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <input name="wpbs_settings[recaptcha_v3_site_key]" id="recaptcha_v3_site_key" type="text" value="<?php echo ( ! empty( $settings['recaptcha_v3_site_key'] ) ? esc_attr( $settings['recaptcha_v3_site_key'] ) : '' ); ?>" />
        </div>
        
    </div>

    <!-- reCAPTCHA Keys -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">

        <label class="wpbs-settings-field-label" for="recaptcha_v3_secret_key"><?php echo __( 'v3 Secret Key', 'wp-booking-system' ); ?></label>

        <div class="wpbs-settings-field-inner">
            <input name="wpbs_settings[recaptcha_v3_secret_key]" id="recaptcha_v3_secret_key" type="text" value="<?php echo ( ! empty( $settings['recaptcha_v3_secret_key'] ) ? esc_attr( $settings['recaptcha_v3_secret_key'] ) : '' ); ?>" />
        </div>
        
    </div>

</div>

<!-- Form Defaults -->
<h2>
    <?php echo __( 'Form Defaults', 'wp-booking-system' ); ?>
    <?php echo wpbs_get_output_tooltip(__("Set some default values so you won't have to fill in your name and email address everytime you send an email from the Booking Manager.", 'wp-booking-system'));?>
</h2>

<!-- From Name -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="default_from_name"><?php echo __( 'Default "From Name"', 'wp-booking-system' ); ?></label>

    <div class="wpbs-settings-field-inner">
        <input name="wpbs_settings[default_from_name]" id="default_from_name" type="text" value="<?php echo ( ! empty( $settings['default_from_name'] ) ? esc_attr( $settings['default_from_name'] ) : '' ); ?>" />
    </div>
</div>

<!-- From Email -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="default_from_email"><?php echo __( 'Default "From Email"', 'wp-booking-system' ); ?></label>

    <div class="wpbs-settings-field-inner">
        <input name="wpbs_settings[default_from_email]" id="default_from_email" type="text" value="<?php echo ( ! empty( $settings['default_from_email'] ) ? esc_attr( $settings['default_from_email'] ) : '' ); ?>" />
    </div>
</div>

<!-- Reply To -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="default_reply_to"><?php echo __( 'Default "Reply To"', 'wp-booking-system' ); ?></label>

    <div class="wpbs-settings-field-inner">
        <input name="wpbs_settings[default_reply_to]" id="default_reply_to" type="text" value="<?php echo ( ! empty( $settings['default_reply_to'] ) ? esc_attr( $settings['default_reply_to'] ) : '' ); ?>" />
    </div>
</div>

<!-- CC -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="default_cc"><?php echo __( 'Default "CC"', 'wp-booking-system' ); ?></label>

    <div class="wpbs-settings-field-inner">
        <input name="wpbs_settings[default_cc]" id="default_cc" type="text" value="<?php echo ( ! empty( $settings['default_cc'] ) ? esc_attr( $settings['default_cc'] ) : '' ); ?>" />
    </div>
</div>

<!-- BCC -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="default_bcc"><?php echo __( 'Default "BCC"', 'wp-booking-system' ); ?></label>

    <div class="wpbs-settings-field-inner">
        <input name="wpbs_settings[default_bcc]" id="default_bcc" type="text" value="<?php echo ( ! empty( $settings['default_bcc'] ) ? esc_attr( $settings['default_bcc'] ) : '' ); ?>" />
    </div>
</div>

<!-- Email Templates -->
<h2>
    <?php echo __( 'Email Customization', 'wp-booking-system' ); ?>
    <?php echo wpbs_get_output_tooltip(__("Add some style to all the outgoing emails.", 'wp-booking-system'));?>
</h2>


<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="fancy_emails_disable">
        <?php echo __( 'Disable Fancy Emails', 'wp-booking-system' ); ?>
    </label>

    <div class="wpbs-settings-field-inner">
        <label for="fancy_emails_disable" class="wpbs-checkbox-switch">
            <input name="wpbs_settings[fancy_emails_disable]" type="checkbox" id="fancy_emails_disable" class="regular-text wpbs-settings-toggle" <?php echo (!empty($settings['fancy_emails_disable'])) ? 'checked' : ''; ?>>
            <div class="wpbs-checkbox-slider"></div>
        </label>
    </div>
</div>

<!-- Button Style -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline">
    <label class="wpbs-settings-field-label" for="fancy_emails_accent"><?php echo __( 'Accent Colour', 'wp-booking-system' ); ?></label>
    <div class="wpbs-settings-field-inner">
        <input name="wpbs_settings[fancy_emails_accent]" type="text" class="wpbs-colorpicker" value="<?php echo ( ! empty( $settings['fancy_emails_accent'] ) ? esc_attr( $settings['fancy_emails_accent'] ) : '' ); ?>" />
    </div>
</div>

<!-- Logo -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="fancy_emails_logo">
        <?php echo __( 'Logo', 'wp-booking-system'); ?>
    </label>

    <div class="wpbs-settings-field-inner">
        
        <img class="wpbs-media-upload-preview" src="<?php echo ( !empty( $settings['fancy_emails_logo'] ) ) ? $settings['fancy_emails_logo'] : '';?>" />
        <input type="text" name="wpbs_settings[fancy_emails_logo]" id="fancy_emails_logo" class="wpbs-media-upload-url regular-text" value="<?php echo ( !empty( $settings['fancy_emails_logo'] ) ) ? $settings['fancy_emails_logo'] : '';?>" >
        <button name="upload-btn" class="wpbs-media-upload-button button-secondary"><?php echo __( 'Upload Image', 'wp-booking-system'); ?></button>
        <a href="#" class="wpbs-media-upload-remove <?php echo ( empty( $settings['fancy_emails_logo'] ) ) ? 'wpbs-hide' : '';?>"><?php echo __( 'remove image', 'wp-booking-system'); ?></a>
    </div>
</div>

<!-- Logo Height -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="fancy_emails_logo_height"><?php echo __( 'Logo Height', 'wp-booking-system' ); ?></label>

    <div class="wpbs-settings-field-inner">
        <input name="wpbs_settings[fancy_emails_logo_height]" id="fancy_emails_logo_height" type="number" value="<?php echo ( ! empty( $settings['fancy_emails_logo_height'] ) ? esc_attr( $settings['fancy_emails_logo_height'] ) : '' ); ?>" placeholder="60" /> px
    </div>
</div>

<!-- Message Templates -->
<h2>
    <?php echo __( 'Message Templates', 'wp-booking-system' ); ?>
    <?php echo wpbs_get_output_tooltip(__("Create default email templates you can re-use everytime you send an email from the Booking Manager. You can also include general email tags which are not related to forms, like {Start Date} or {Booking ID}.", 'wp-booking-system'));?>
</h2>

<div class="wpbs-hide">
    <?php wp_editor('', 'form_email_template_body', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false, 'textarea_name' => '')); ?>
</div>

<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="automatically_load_template">
        <?php echo __( 'Automatically Load', 'wp-booking-system' ); ?>
        <?php echo wpbs_get_output_tooltip(__("Automatically load the contents of an email template in the email form, wihtout having to select it from the dropdown.", 'wp-booking-system'));?>
    </label>

    <div class="wpbs-settings-field-inner">
        <select name="wpbs_settings[automatically_load_template]" id="automatically_load_template">
            <option value="0">-</option>
            <?php  if (isset($settings['form_email_template_name'])) for ($i = 0; $i < count($settings['form_email_template_name']); $i++): ?>
            <option value="<?php echo ($i+1);?>" <?php echo isset($settings['automatically_load_template']) ? selected($settings['automatically_load_template'], ($i+1), false) : '';?> ><?php echo __('Template #','wp-booking-system') ?><?php echo ($i + 1);?></option>
            <?php endfor; ?>
        </select>
        
    </div>
</div>

<div class="wpbs-email-template-wrapper">
        
	<div class="wpbs-email-template-fields">
		
		<?php if (isset($settings['form_email_template_name'])) for ($i = 0; $i < count($settings['form_email_template_name']); $i++): ?>
		
		<div class="postbox wpbs-email-template-field">

			<h3 class="hndle">
				<span><?php echo __('Email Template #', 'wp-booking-system') ?><?php echo ($i + 1);?></span>
				<a href="#" class="wpbs-settings-email-template-remove" title="<?php echo __('Remove', 'wp-booking-system') ?>"><i class="wpbs-icon-close"></i> <?php echo __('Remove', 'wp-booking-system') ?></a>
			</h3>
				
			<div class="inside">
				<!-- Name -->
				<div class="wpbs-email-template-name">
					<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-xlarge">
						<label class="wpbs-settings-field-label" for="form_email_template_name_<?php echo $i;?>"><?php echo __('Subject', 'wp-booking-system') ?></label>
						
						<div class="wpbs-settings-field-inner">
							<input name="wpbs_settings[form_email_template_name][]" id="form_email_template_name_<?php echo $i;?>" placeholder="Subject" type="text" value="<?php echo (!empty($settings['form_email_template_name'][$i]) ? esc_attr($settings['form_email_template_name'][$i]) : ''); ?>" />
						</div>
					</div>
				</div>


				<div class="wpbs-email-template-body">
					<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-xlarge">
						<label class="wpbs-settings-field-label" for="form_email_template_body_<?php echo $i;?>"><?php echo __('Message', 'wp-booking-system') ?></label>

						<div class="wpbs-settings-field-inner">
							<div class="wpbs-email-template-wp-editor" data-id="form_email_template_body_<?php echo $i;?>" data-order-id="<?php echo $i;?>">
								<?php wp_editor((!empty($settings['form_email_template_body'][$i]) ? wp_kses_post($settings['form_email_template_body'][$i]) : ''), 'form_email_template_body_' . $i, array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false, 'textarea_name' => 'wpbs_settings[form_email_template_body][]')); ?>
							</div>
						</div>
					</div>
					
				</div>
			</div>
			
		</div>
		<?php endfor;?>
	</div>

	<a href="#" class="button-secondary wpbs-settings-email-template-add"><?php echo __('Add New Template', 'wp-booking-system') ?></a>
        
</div>





<?php

    /**
     * Hook to add extra fields at the bottom of the Form Tab
     *
     * @param array $settings
     *
     */
    do_action( 'wpbs_submenu_page_settings_tab_form_bottom', $settings );

?>

<!-- Submit button -->
<input type="submit" class="button-primary" value="<?php echo __( 'Save Settings', 'wp-booking-system' ); ?>" />