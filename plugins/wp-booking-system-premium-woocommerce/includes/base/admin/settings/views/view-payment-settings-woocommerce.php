<?php 
$active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());
$languages = wpbs_get_languages();


?>

<h2><?php echo __('WooCommerce', 'wp-booking-system-woocommerce') ?><?php echo wpbs_get_output_tooltip(__("Give the customer the option to pay with a credit card using WooCommerce.", 'wp-booking-system-woocommerce'));?></h2>


<!-- Enable WooCommerce -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
	<label class="wpbs-settings-field-label" for="payment_wc_enable">
        <?php echo __( 'Active', 'wp-booking-system-woocommerce'); ?>
    </label>

	<div class="wpbs-settings-field-inner">
        <label for="payment_wc_enable" class="wpbs-checkbox-switch">
            <input data-target="#wpbs-payment-woocommerce" name="wpbs_settings[payment_wc_enable]" type="checkbox" id="payment_wc_enable"  class="regular-text wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo ( !empty( $settings['payment_wc_enable'] ) ) ? 'checked' : '';?> >
            <div class="wpbs-checkbox-slider"></div>
        </label>
	</div>
</div>

<div id="wpbs-payment-woocommerce" class="wpbs-payment-on-arrival-wrapper wpbs-settings-wrapper <?php echo ( !empty($settings['payment_wc_enable']) ) ? 'wpbs-settings-wrapper-show' : '';?>">

    <!-- Payment Method Name -->
    <div class="wpbs-settings-field-translation-wrapper">
        <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
            <label class="wpbs-settings-field-label" for="payment_wc_name">
                <?php echo __( 'Display name', 'wp-booking-system-woocommerce'); ?>
                <?php echo wpbs_get_output_tooltip(__("The payment method name that appears on the booking form.", 'wp-booking-system-woocommerce'));?>
            </label>

            <div class="wpbs-settings-field-inner">
                <input name="wpbs_settings[payment_wc_name]" type="text" id="payment_wc_name"  class="regular-text" value="<?php echo ( !empty( $settings['payment_wc_name'] ) ) ? $settings['payment_wc_name'] : $defaults['display_name'];?>" >
                <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-settings-field-show-translations"><?php echo __('Translations', 'wp-booking-system-woocommerce'); ?> <i class="wpbs-icon-down-arrow"></i></a><?php endif?>
            </div>
        </div>
        <?php if (wpbs_translations_active()): ?>
        <!-- Required Field Translations -->
        <div class="wpbs-settings-field-translations">
            <?php foreach ($active_languages as $language): ?>
                <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
                    <label class="wpbs-settings-field-label" for="payment_wc_name_translation_<?php echo $language; ?>"><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo $languages[$language]; ?></label>
                    <div class="wpbs-settings-field-inner">
                        <input name="wpbs_settings[payment_wc_name_translation_<?php echo $language; ?>]" type="text" id="payment_wc_name_translation_<?php echo $language; ?>" value="<?php echo (!empty($settings['payment_wc_name_translation_'. $language])) ? esc_attr($settings['payment_wc_name_translation_'. $language]) : ''; ?>" class="regular-text" >
                    </div>
                </div>
            <?php endforeach;?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Payment Method Description -->
    <div class="wpbs-settings-field-translation-wrapper">
        <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
            <label class="wpbs-settings-field-label" for="payment_wc_description">
                <?php echo __( 'Description', 'wp-booking-system-woocommerce'); ?>
                <?php echo wpbs_get_output_tooltip(__("The payment method description that appears on the booking form.", 'wp-booking-system-woocommerce'));?>
            </label>

            <div class="wpbs-settings-field-inner">
                <input name="wpbs_settings[payment_wc_description]" type="text" id="payment_wc_description"  class="regular-text" value="<?php echo ( !empty( $settings['payment_wc_description'] ) ) ? $settings['payment_wc_description'] : $defaults['description'];?>" >
                <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-settings-field-show-translations"><?php echo __('Translations', 'wp-booking-system-woocommerce'); ?> <i class="wpbs-icon-down-arrow"></i></a><?php endif?>
            </div>
        </div>
        <?php if (wpbs_translations_active()): ?>
        <!-- Required Field Translations -->
        <div class="wpbs-settings-field-translations">
            <?php foreach ($active_languages as $language): ?>
                <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
                    <label class="wpbs-settings-field-label" for="payment_wc_description_translation_<?php echo $language; ?>"><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo $languages[$language]; ?></label>
                    <div class="wpbs-settings-field-inner">
                        <input name="wpbs_settings[payment_wc_description_translation_<?php echo $language; ?>]" type="text" id="payment_wc_description_translation_<?php echo $language; ?>" value="<?php echo (!empty($settings['payment_wc_description_translation_'. $language])) ? esc_attr($settings['payment_wc_description_translation_'. $language]) : ''; ?>" class="regular-text" >
                    </div>
                </div>
            <?php endforeach;?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Payment Button Label -->
    <div class="wpbs-settings-field-translation-wrapper">
        <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
            <label class="wpbs-settings-field-label" for="payment_wc_button_label">
                <?php echo __( 'Pay Button Label', 'wp-booking-system-woocommerce'); ?>
            </label>

            <div class="wpbs-settings-field-inner">
                <input name="wpbs_settings[payment_wc_button_label]" type="text" id="payment_wc_button_label"  class="regular-text" value="<?php echo ( !empty( $settings['payment_wc_button_label'] ) ) ? $settings['payment_wc_button_label'] : $defaults['button_label'];?>" >
                <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-settings-field-show-translations"><?php echo __('Translations', 'wp-booking-system-woocommerce'); ?> <i class="wpbs-icon-down-arrow"></i></a><?php endif?>
            </div>
        </div>
        <?php if (wpbs_translations_active()): ?>
        <!-- Required Field Translations -->
        <div class="wpbs-settings-field-translations">
            <?php foreach ($active_languages as $language): ?>
                <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
                    <label class="wpbs-settings-field-label" for="payment_wc_button_label_translation_<?php echo $language; ?>"><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo $languages[$language]; ?></label>
                    <div class="wpbs-settings-field-inner">
                        <input name="wpbs_settings[payment_wc_button_label_translation_<?php echo $language; ?>]" type="text" id="payment_wc_button_label_translation_<?php echo $language; ?>" value="<?php echo (!empty($settings['payment_wc_button_label_translation_'. $language])) ? esc_attr($settings['payment_wc_button_label_translation_'. $language]) : ''; ?>" class="regular-text" >
                    </div>
                </div>
            <?php endforeach;?>
        </div>
        <?php endif; ?>
    </div>

    <!-- WooCommerce Product -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
        <label class="wpbs-settings-field-label" for="payment_wc_product_id">
            <?php echo __('WooCommerce Product', 'wp-booking-system'); ?>
            <?php echo wpbs_get_output_tooltip(__("Select a product that will be associated with all booking payments.", 'wp-booking-system-woocommerce'));?>
        </label>
        <div class="wpbs-settings-field-inner">
            <select name="wpbs_settings[payment_wc_product_id]" id="payment_wc_product_id">
                <?php $items = get_posts( array('post_type' => 'product', 'numberposts' => apply_filters('wpsbc_wc_product_numberposts', 100)) ); foreach($items as $item):?>
                    <option value="<?php echo $item->ID;?>" <?php selected(isset($settings['payment_wc_product_id']) ? $settings['payment_wc_product_id'] : 0, $item->ID);?>><?php echo $item->post_title ?></option>
                <?php endforeach;?>
            </select>
        </div>
    </div>

    <!-- Disable WooCommerce Emails -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
        <label class="wpbs-settings-field-label" for="payment_wc_disable_emails">
            <?php echo __( 'Disable Emails', 'wp-booking-system-woocommerce'); ?>
            <?php echo wpbs_get_output_tooltip(__('Disable the "Order Completed" emails sent by WooCommerce to the customer to avoid duplicate emails if you enabled User Notifications in WP Booking System.', 'wp-booking-system-woocommerce'));?>
        </label>

        <div class="wpbs-settings-field-inner">
            <label for="payment_wc_disable_emails" class="wpbs-checkbox-switch">
                <input name="wpbs_settings[payment_wc_disable_emails]" type="checkbox" id="payment_wc_disable_emails"  class="regular-text wpbs-settings-toggle" <?php echo ( !empty( $settings['payment_wc_disable_emails'] ) ) ? 'checked' : '';?> >
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>
    </div>

    <!-- Confirmation Type -->
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
        <label class="wpbs-settings-field-label" for="payment_wc_skip_confirmation_page">
            <?php echo __( "Disable 'Thank You' Page", 'wp-booking-system-woocommerce'); ?>
            <?php echo wpbs_get_output_tooltip(__("Disable the default WooCommerce 'Thank You' page. After the payment is completed, redirect the customer back to the calendar page and show WP Booking System's confirmation message instead of WooCommerce's confirmation page.", 'wp-booking-system-woocommerce'));?>
        </label>

        <div class="wpbs-settings-field-inner">
            <label for="payment_wc_skip_confirmation_page" class="wpbs-checkbox-switch">
                <input  name="wpbs_settings[payment_wc_skip_confirmation_page]" type="checkbox" id="payment_wc_skip_confirmation_page"  class="regular-text wpbs-settings-toggle" <?php echo ( !empty( $settings['payment_wc_skip_confirmation_page'] ) ) ? 'checked' : '';?> >
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>
    </div>

    <!-- Documentation -->
    <div class="wpbs-page-notice notice-info wpbs-form-changed-notice"> 
        <p><?php echo __( 'If you need help setting up setting up WooCommerce, <a target="_blank" href="https://www.wpbookingsystem.com/documentation/woocommerce-checkout-integration/">check out our guide</a> which offers step by step instructions.', 'wp-booking-system-woocommerce'); ?></p>
    </div>


</div>