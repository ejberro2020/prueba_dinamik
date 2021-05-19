<?php

$default_strings = wpbs_form_default_strings();
$strings = array(
    'validation-strings' => array(
        'label' => __('Validation Strings', 'wp-booking-system'),
        'strings' => array(
            'validation_errors' => array(
                'label' => __('Validation Errors', 'wp-booking-system'),
            ),
            'required_field' => array(
                'label' => __('Required Field', 'wp-booking-system'),
            ),
            'invalid_email' => array(
                'label' => __('Invalid Email', 'wp-booking-system'),
            ),
            'invalid_phone' => array(
                'label' => __('Invalid Phone', 'wp-booking-system'),
            ),
            'select_date' => array(
                'label' => __('No Date Selected', 'wp-booking-system'),
            ),
            'minimum_selection' => array(
                'label' => __('Minimum Days', 'wp-booking-system'),
                'tooltip' => __('The "%s" will be replaced by the number of days set in the widget or shortcode.', 'wp-booking-system'),
            ),
            'maximum_selection' => array(
                'label' => __('Maximum Days', 'wp-booking-system'),
                'tooltip' => __('The "%s" will be replaced by the number of days set in the widget or shortcode.', 'wp-booking-system'),
            ),
            'start_day' => array(
                'label' => __('Starting Day', 'wp-booking-system'),
                'tooltip' => __('The "%s" will be replaced by the week day set in the widget or shortcode.', 'wp-booking-system'),
            ),
            'end_day' => array(
                'label' => __('Ending Day', 'wp-booking-system'),
                'tooltip' => __('The "%s" will be replaced by the week day set in the widget or shortcode.', 'wp-booking-system'),
            ),
        ),
    ),
    'other-strings' => array(
        'label' => __('Other Strings', 'wp-booking-system'),
        'strings' => array(
            'booking_id' => array(
                'label' => __('Booking ID', 'wp-booking-system'),
                'tooltip' => __('This appears in the email if the {All Fields} tag is used.', 'wp-booking-system'),
            ),
            'start_date' => array(
                'label' => __('Start Date', 'wp-booking-system'),
                'tooltip' => __('This appears in the email if the {All Fields} tag is used.', 'wp-booking-system'),
            ),
            'end_date' => array(
                'label' => __('End Date', 'wp-booking-system'),
                'tooltip' => __('This appears in the email if the {All Fields} tag is used.', 'wp-booking-system'),
            ),
            'booked_on' => array(
                'label' => __('Booked On', 'wp-booking-system'),
                'tooltip' => __('The date the booking was made on. This appears in the email if the {All Fields} tag is used.', 'wp-booking-system'),
            ),
        ),
    ),
);

$strings = apply_filters('wpbs_form_default_strings_settings_page', $strings);

foreach ($strings as $section):?>
    
    <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-heading wpbs-settings-field-large">
        <label class="wpbs-settings-field-label"><?php echo $section['label'] ?></label>
        <div class="wpbs-settings-field-inner">&nbsp;</div>
    </div>
    <?php foreach ($section['strings'] as $key => $string): ?>
    <!-- Required Field -->
    <div class="wpbs-settings-field-translation-wrapper">
        <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
            <label class="wpbs-settings-field-label" for="form_strings_<?php echo $key; ?>">
                <?php echo $string['label'] ?>
                <?php if (isset($string['tooltip'])): ?>
                    <?php echo wpbs_get_output_tooltip($string['tooltip']); ?>
                <?php endif?>
            </label>
            <div class="wpbs-settings-field-inner">
                <input name="form_strings_<?php echo $key; ?>" type="text" id="form_strings_<?php echo $key; ?>" value="<?php echo (!empty($form_meta['form_strings_' . $key][0])) ? esc_attr($form_meta['form_strings_' . $key][0]) : $default_strings[$key]; ?>" class="regular-text" >
                <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-settings-field-show-translations"><?php echo __('Translations', 'wp-booking-system'); ?> <i class="wpbs-icon-down-arrow"></i></a><?php endif?>
            </div>
        </div>
        <?php if (wpbs_translations_active()): ?>
        <!-- Required Field Translations -->
        <div class="wpbs-settings-field-translations">
            <?php foreach ($active_languages as $language): ?>
                <div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
                    <label class="wpbs-settings-field-label" for="form_strings_<?php echo $key; ?>_translation_<?php echo $language; ?>"><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo $languages[$language]; ?></label>
                    <div class="wpbs-settings-field-inner">
                        <input name="form_strings_<?php echo $key; ?>_translation_<?php echo $language; ?>" type="text" id="form_strings_<?php echo $key; ?>_translation_<?php echo $language; ?>" value="<?php echo (!empty($form_meta['form_strings_' . $key . '_translation_' . $language][0])) ? esc_attr($form_meta['form_strings_' . $key . '_translation_' . $language][0]) : ''; ?>" class="regular-text" >
                    </div>
                </div>
            <?php endforeach;?>
        </div>
        <?php endif?>
    </div>
    <?php endforeach;?>
<?php endforeach;?>