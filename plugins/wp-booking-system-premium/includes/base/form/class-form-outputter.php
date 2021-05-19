<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Form_Outputter
{

    /**
     * The arguments for the form outputter
     *
     * @access protected
     * @var    array
     *
     */
    protected $args;

    /**
     * The WPBS_Form
     *
     * @access protected
     * @var    WPBS_Form
     *
     */
    protected $form = null;

    /**
     * The Calendar ID attached to the form
     *
     * @access protected
     * @var    int
     *
     */
    protected $calendar_id = null;

    /**
     * The form fields
     *
     * @access protected
     * @var    array
     *
     */
    protected $form_fields = null;

    /**
     * The available form fields
     *
     * @access protected
     * @var    array
     *
     */
    protected $available_form_fields = array();

    /**
     * The plugin general settings
     *
     * @access protected
     * @var    array
     *
     */
    protected $plugin_settings = array();

    /**
     * A unique string
     *
     * @access protected
     * @var    array
     *
     */
    protected $unique;

    /**
     * Constructor
     *
     * @param WPBS_Form $form
     * @param array     $args
     *
     */
    public function __construct($form, $args = array(), $form_fields = array(), $calendar_id = null)
    {

        /**
         * Set arguments
         *
         */
        $this->args = wp_parse_args($args, wpbs_get_form_output_default_args());

        /**
         * Set the form
         *
         */
        $this->form = $form;

        /**
         * Set the form
         *
         */
        $this->calendar_id = $calendar_id;

        /**
         * Set available Form Fields
         *
         */
        $this->available_form_fields = wpbs_form_available_field_types();

        /**
         * Set plugin settings
         *
         */
        $this->plugin_settings = get_option('wpbs_settings', array());

        /**
         * Set the form fields
         *
         */
        if (empty($form_fields)) {
            $this->form_fields = $form->get('fields');
        } else {
            $this->form_fields = $form_fields;
        }

        /**
         * Set the unique string to prevent conflicts if the same form is embedded twice on the same page
         *
         */
        $this->unique = hash('crc32', microtime(), false);
    }

    /**
     * Constructs and returns the HTML for the entire form
     *
     * @return string
     *
     */
    public function get_display()
    {

        /**
         * Return nothing if form is in Trash
         *
         */
        if ($this->form->get('status') == 'trash') {
            return '';
        }

        /**
         * Prepare needed data
         *
         */

        $form_html_data = 'data-id="' . $this->form->get('id') . '" ';

        foreach ($this->args as $arg => $val) {
            $form_html_data .= 'data-' . $arg . '="' . esc_attr($val) . '" ';
        }

        /**
         * Handle output for existing form
         *
         */

        $output = '';

        $output .= apply_filters('wpbs_form_outputter_form_before', '', $this->form, $this->args);

        $output .= '<form method="post" action="#" id="wpbs-form-' . (int) $this->form->get('id') . '" class="wpbs-form-container wpbs-form-' . (int) $this->form->get('id') . '" ' . $form_html_data . '>';

        $output .= $this->get_form_errors();

        $output .= apply_filters('wpbs_form_outputter_form_fields_before', '', $this->form, $this->args);

        $output .= $this->get_form_fields();

        $output .= $this->get_form_return_url();

        $output .= apply_filters('wpbs_form_outputter_form_fields_after', '', $this->form, $this->args);

        $output .= $this->get_form_button();

        $output .= $this->get_form_styles();

        $output .= '</form>';

        $output .= apply_filters('wpbs_form_outputter_form_after', '', $this->form, $this->args);

        return apply_filters('wpbs_form_outputter_form', $output, $this->form, $this->args);
    }

    /**
     * Check if we have form errors, not field errors
     *
     * @return mixed
     *
     */
    protected function get_form_errors()
    {

        if (!isset($this->form_fields['form_error'])) {
            return false;
        }

        $output = '<div class="wpbs-form-general-error">';
        $output .= $this->form_fields['form_error'];
        $output .= '</div>';

        return $output;

    }

    /**
     * Constructs and returns the HTML for displaying the selected dates
     *
     * @return string
     *
     */

    protected function get_selected_dates()
    {

        if (empty($this->args['show_date_selection']) || $this->args['show_date_selection'] == 0) {
            return '';
        }

        $output = '
        <div class="wpbs-form-selected-dates">
            <div class="wpbs-form-selected-date">
                <div class="wpbs-form-field wpbs-form-field-start-date">
                    <div class="wpbs-form-field-label"><label>' . wpbs_get_form_default_string($this->form->get('id'), 'start_date', $this->args['language']) . ': </label></div>
                    <div class="wpbs-form-field-input">-</div>
                </div>
            </div>
            <div class="wpbs-form-selected-date">
                <div class="wpbs-form-field wpbs-form-field-end-date">
                    <div class="wpbs-form-field-label"><label>' . wpbs_get_form_default_string($this->form->get('id'), 'end_date', $this->args['language']) . ': </label></div>
                    <div class="wpbs-form-field-input">-</div>
                </div>
            </div>
        </div>
        ';

        return $output;
    }

    /**
     * Constructs and returns the HTML for form fields
     *
     * @return string
     *
     */
    protected function get_form_fields()
    {

        $output = '<div class="wpbs-form-fields">';

        $output .= apply_filters('wpbs_form_outputter_form_fields_inner_before', '', $this->form);

        $output .= $this->get_selected_dates();

        foreach ($this->form_fields as $field) {

            if (!isset($field['type'])) {
                continue;
            }

            // Check if form field is available (eg. if form field was added through an add-on, and the add-on no longer exists)
            if (!array_key_exists($field['type'], $this->available_form_fields)) {
                continue;
            }

            $output .= apply_filters('wpbs_form_outputter_form_field_' . $this->form->get('id') . '_' . $field['id'] . '_before', '', $this->form);

            $field_output_method = 'get_form_field_type__' . $field['type'];

            if (method_exists($this, $field_output_method)) {
                $output .= $this->{$field_output_method}($field);
            }

            $output .= apply_filters('wpbs_form_outputter_form_field_' . $this->form->get('id') . '_' . $field['id'] . '_after', '', $this->form);

        }

        $output .= apply_filters('wpbs_form_outputter_form_fields_inner_after', '', $this->form);

        $output .= '</div>';

        return $output;

    }

    /**
     * Constructs and returns the HTML the TEXT field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__text($field)
    {

        $output = $this->get_form_field_header($field);
        $output .= '<input
            type="text"
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            value="' . $this->get_form_field_value($field) . '"
            placeholder="' . esc_attr($this->get_form_field_translation($field['values'], 'placeholder')) . '"
        />';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the DATEPICKER field
     * 
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__datepicker($field)
    {

        $output = $this->get_form_field_header($field);
        $output .= '<input
            type="text"
            class="wpbs-form-datepicker"
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            value="' . $this->get_form_field_value($field) . '"
            placeholder="' . esc_attr($this->get_form_field_translation($field['values'], 'placeholder')) . '"
            autocomplete="off"
            data-format="'.$this->get_form_field_translation($field['values'], 'date_format').'"
        />';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the PHONE field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__phone($field)
    {
        return $this->get_form_field_type__text($field);
    }

    /**
     * Constructs and returns the HTML the EMAIL field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__email($field)
    {

        $output = $this->get_form_field_header($field);
        $output .= '<input
            type="email"
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            value="' . $this->get_form_field_value($field) . '"
            placeholder="' . esc_attr($this->get_form_field_translation($field['values'], 'placeholder')) . '"
        />';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the TEXTAREA field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__textarea($field)
    {

        $output = $this->get_form_field_header($field);
        $output .= '<textarea
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            placeholder="' . esc_attr($this->get_form_field_translation($field['values'], 'placeholder')) . '"
        >' . $this->get_form_field_value($field) . '</textarea>';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the DROPDOWN field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__dropdown($field)
    {

        $output = $this->get_form_field_header($field);
        $output .= '<select
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
        >';

        $value = $this->get_form_field_value($field);

        if ($this->get_form_field_translation($field['values'], 'placeholder')) {
            $default_selected = (empty($value)) ? 'selected' : '';
            $output .= '<option value="" ' . $default_selected . ' disabled>' . esc_attr($this->get_form_field_translation($field['values'], 'placeholder')) . '</option>';
        }

        $options = $this->get_form_field_translation($field['values'], 'options');

        $options = apply_filters('wpbs_form_field_options_' . $this->form->get('id') . '_' . $field['id'], $options);

        if ($options) {
            foreach ($options as $i => $option) {
                if (is_null($option)) {
                    continue;
                }
                $selected = ($value == $option) ? 'selected' : '';
                $output .= '<option ' . $selected . ' value="' . esc_attr($option) . '">' . esc_attr($option) . '</option>';
            }
        }

        $output .= '</select>';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the CHECKBOX field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__checkbox($field)
    {

        $output = $this->get_form_field_header($field);

        $values = $this->get_form_field_value($field);

        $options = $this->get_form_field_translation($field['values'], 'options');

        $options = apply_filters('wpbs_form_field_options_' . $this->form->get('id') . '_' . $field['id'], $options);

        if ($options) {
            foreach ($options as $i => $option) {
                if (is_null($option)) {
                    continue;
                }

                $selected = (!empty($values) && in_array($option, $values)) ? ' checked' : '';

                $output .= '<label for="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '">';
                $output .= '<input
                    type="checkbox"
                    name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '[]"
                    id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '"
                    value="' . esc_attr($option) . '"
                    ' . $selected . '
                >';
                $output .= ' ' . esc_attr($option) . '<span></span></label>';
            }
        }

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the RADIO field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__radio($field)
    {

        $output = $this->get_form_field_header($field);

        $value = $this->get_form_field_value($field);

        $options = $this->get_form_field_translation($field['values'], 'options');

        $options = apply_filters('wpbs_form_field_options_' . $this->form->get('id') . '_' . $field['id'], $options);

        if ($options) {
            foreach ($options as $i => $option) {
                if (is_null($option)) {
                    continue;
                }

                $selected = ($value == $option) ? ' checked' : '';

                $output .= '<label for="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '">';
                $output .= '<input
                    type="radio"
                    name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
                    id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '"
                    value="' . esc_attr($option) . '"
                    ' . $selected . '
                >';
                $output .= ' ' . esc_attr($option) . '<span></span></label>';
            }
        }

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the HTML field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__html($field)
    {

        $output = $this->get_form_field_header($field);

        $output .= html_entity_decode(esc_html($this->get_form_field_translation($field['values'], 'value')));

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the HIDDEN field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__hidden($field)
    {

        $output = $this->get_form_field_header($field);

        $output .= '<input
            type="hidden"
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            value="' . $this->get_form_field_value($field) . '"
        />';

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the CAPTCHA field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__captcha($field)
    {
        
        $recaptcha_type = wpbs_get_recaptcha_type();
        $recaptcha_keys = wpbs_get_recaptcha_keys();

        if($recaptcha_type == 'v2'){
            wp_enqueue_script('google-recaptcha');
        }

        $recaptcha_site_key = ($recaptcha_keys) ? $recaptcha_keys['site_key'] : '';

        $output = '';

        if ($recaptcha_type == 'v2') {
            $output .= $this->get_form_field_header($field);
            $output .= '<div class="wpbs-google-recaptcha-v2" id="wpbs-google-recaptcha-' . $this->form->get('id') . '-' . $this->unique . '" data-sitekey="' . $recaptcha_site_key . '"></div>';
            $output .= $this->get_form_field_footer($field);
        }

        if ($recaptcha_type == 'v3') {
            $output .= '<input class="wpbs-google-recaptcha-v3" type="hidden" name="g-recaptcha-response" id="wpbs-google-recaptcha-v3-' . $this->form->get('id') . '-' . $this->unique . '" data-sitekey="' . $recaptcha_site_key . '">';
        }

        return $output;
    }

    /**
     * Constructs and returns the HTML the Total field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__total($field)
    {

        // Check for default price.
        $default_price = wpbs_get_calendar_meta($this->calendar_id, 'default_price', true);

        if (!is_numeric($default_price) || $default_price < 0) {
            return '<p class="wpbs-form-general-error">' . __('No default price is defined for this calendar. Please define a default price in order to use payments.', 'wp-booking-system') . '</p>';
        }

        $output = $this->get_form_field_header($field);

        $output .= '<div class="wpbs-total-price wpbs-total-price-' . $this->form->get('id') . '-' . $field['id'] . '" data-string-item="' . wpbs_get_payment_default_string('item', $this->args['language']) . '" data-string-total="' . wpbs_get_payment_default_string('total', $this->args['language']) . '" data-string-select-dates="' . wpbs_get_payment_default_string('select_dates', $this->args['language']) . '">' . ($this->get_form_field_value($field) ? $this->get_form_field_value($field) : wpbs_get_payment_default_string('select_dates', $this->args['language'])) . '</div>';

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the Payment Chooser Field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__payment_method($field)
    {

        // Check for default price.
        $default_price = wpbs_get_calendar_meta($this->calendar_id, 'default_price', true);

        if (!is_numeric($default_price) || $default_price < 0) {
            return '<p class="wpbs-form-general-error">' . __('No default price is defined for this calendar. Please define a default price in order to use payments.', 'wp-booking-system') . '</p>';
        }

        $output = $this->get_form_field_header($field);

        $payment_methods = wpbs_get_payment_methods();

        $active_payment_methods = wpbs_get_active_payment_methods();

        $payment_methods_exist = false;

        $single_payment_method = (count($active_payment_methods) == 1) ? true : false;

        if ($active_payment_methods) {
            foreach ($payment_methods as $payment_method_slug => $payment_method_name) {
                if (!in_array($payment_method_slug, $active_payment_methods)) {
                    continue;
                }

                $checked = ($this->get_form_field_value($field) == $payment_method_slug || $single_payment_method == true) ? true : false;

                $payment_methods_exist = true;
                $output .= '<label class="wpbs-payment-method-label wpbs-payment-method-label-' . $payment_method_slug . '"><input type="radio" ' . ($checked ? 'checked' : '') . ' name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '" value="' . $payment_method_slug . '" /> ' . apply_filters('wpbs_form_outputter_payment_method_name_' . $payment_method_slug, '', $this->args['language']) . '<span></span></label>';
                $output .= '<p class="wpbs-payment-method-description wpbs-payment-method-description-' . $payment_method_slug . ' ' . ($checked ? 'wpbs-payment-method-description-open' : '') . '">' . apply_filters('wpbs_form_outputter_payment_method_description_' . $payment_method_slug, '', $this->args['language']) . '</p>';
            }
        } else {
            $output .= '<p>' . __('No payment methods are available.', 'wp-booking-system') . '</p>';
        }

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the Product Field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__product_field($field)
    {

        $price = floatval($this->get_form_field_translation($field['values'], 'pricing'));

        $addition = $this->get_form_field_translation($field['values'], 'pricing_type');

        $output = $this->get_form_field_header($field);
        $output .= '<input
            type="hidden"
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            value="' . $price . '|' . $this->get_form_field_translation($field['values'], 'label') . '"
            data-price="' . $price . '"
            data-addition="' . $addition . '"
            data-display-value="' . $this->get_form_field_translation($field['values'], 'label') . '"
        />';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the Product Checkbox Field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__product_checkbox($field)
    {

        $output = $this->get_form_field_header($field);

        $values = $this->get_form_field_value($field);

        $options = $this->get_form_field_translation($field['values'], 'options_pricing');

        $options = apply_filters('wpbs_form_field_options_' . $this->form->get('id') . '_' . $field['id'], $options);

        $addition = $this->get_form_field_translation($field['values'], 'pricing_type');

        if ($options) {
            foreach ($options as $i => $option) {
                if (empty($option)) {
                    continue;
                }

                if (strpos($option, '|') === false) {
                    continue;
                }

                list($price, $option_value) = explode('|', $option);

                $selected = (!empty($values) && in_array($option, $values)) ? ' checked' : '';

                $output .= '<label for="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '">';
                $output .= '<input
                    type="checkbox"
                    name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '[]"
                    id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '"
                    value="' . esc_attr($option) . '"
                    data-display-value="' . esc_attr($option_value) . '"
                    data-price="' . $price . '"
                    data-addition="' . $addition . '"
                    ' . $selected . '
                >';
                $output .= ' ' . esc_attr($option_value) . '<span></span></label>';
            }
        }

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the Product Checkbox Field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__product_radio($field)
    {

        $output = $this->get_form_field_header($field);

        $values = $this->get_form_field_value($field);

        $options = $this->get_form_field_translation($field['values'], 'options_pricing');

        $options = apply_filters('wpbs_form_field_options_' . $this->form->get('id') . '_' . $field['id'], $options);

        $addition = $this->get_form_field_translation($field['values'], 'pricing_type');

        if ($options) {
            foreach ($options as $i => $option) {
                if (empty($option)) {
                    continue;
                }

                if (strpos($option, '|') === false) {
                    continue;
                }

                list($price, $option_value) = explode('|', $option);

                $selected = ($values == $option) ? 'checked' : '';

                $output .= '<label for="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '">';
                $output .= '<input
                    type="radio"
                    name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
                    id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $i . '-' . $this->unique . '"
                    value="' . esc_attr($option) . '"
                    data-display-value="' . esc_attr($option_value) . '"
                    data-price="' . $price . '"
                    data-addition="' . $addition . '"
                    ' . $selected . '
                >';
                $output .= ' ' . esc_attr($option_value) . '<span></span></label>';
            }
        }

        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the Product DROPDOWN field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__product_dropdown($field)
    {

        $output = $this->get_form_field_header($field);
        $output .= '<select
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
        >';

        $value = $this->get_form_field_value($field);

        if ($this->get_form_field_translation($field['values'], 'placeholder')) {
            $default_selected = (empty($value)) ? 'selected' : '';
            $output .= '<option value="" ' . $default_selected . ' disabled>' . esc_attr($this->get_form_field_translation($field['values'], 'placeholder')) . '</option>';
        }

        $options = $this->get_form_field_translation($field['values'], 'options_pricing');

        $options = apply_filters('wpbs_form_field_options_' . $this->form->get('id') . '_' . $field['id'], $options);

        $addition = $this->get_form_field_translation($field['values'], 'pricing_type');

        if ($options) {
            foreach ($options as $i => $option) {
                if (empty($option)) {
                    continue;
                }

                if (strpos($option, '|') === false) {
                    continue;
                }

                list($price, $option_value) = explode('|', $option);

                $selected = ($value == $option) ? 'selected' : '';
                $output .= '<option ' . $selected . ' value="' . esc_attr($option) . '" data-display-value="' . esc_attr($option_value) . '" data-price="' . $price . '" data-addition="' . $addition . '">' . esc_attr($option_value) . '</option>';
            }
        }

        $output .= '</select>';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the Coupon Field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__coupon($field)
    {

        $value = $this->get_form_field_value($field);

        $output = $this->get_form_field_header($field);
        $output .= '<div class="wpbs-coupon-code">';
        $output .= '<input
            type="text"
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            value="' . $value . '"
            placeholder="' . esc_attr($this->get_form_field_translation($field['values'], 'placeholder')) . '"

        />';

        $output .= '<button class="wpbs-coupon-code-button wpbs-coupon-code-add" data-label="' . wpbs_get_form_default_string($this->form->get('id'), 'apply_coupon_code', $this->args['language']) . '">' . wpbs_get_form_default_string($this->form->get('id'), 'apply_coupon_code', $this->args['language']) . '</button>';
        $output .= '</div>';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Constructs and returns the HTML the Consent Field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__consent($field)
    {

        $output = $this->get_form_field_header($field);

        $checkbox_label = $this->get_form_field_translation($field['values'], 'checkbox_label');

        $link = $this->get_form_field_translation($field['values'], 'link');

        $values = $this->get_form_field_value($field);

        $selected = (!empty($values) && $values[0]) ? ' checked' : '';

        $output .= '<label>';
        $output .= '<input
            type="checkbox"
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '[]"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            value="' . ($checkbox_label ? esc_attr($checkbox_label) : __('Agreed', 'wp-booking-system')) . '"
            ' . $selected . '
        >';
        $output .= ' ';

        $label = '';

        $label .= (!empty($link)) ? '<a href="' . $link . '" target="_blank">' : '';

        $label .= esc_attr($checkbox_label);

        $label .= (!empty($link)) ? '</a>' : '';

        $output .= apply_filters('wpbs_form_outputter_consent_label', $label);

        $output .= '<span></span></label>';

        $output .= $this->get_form_field_footer($field);

        return $output;

    }

    /**
     * Constructs and returns the HTML the INVENTORY field
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_type__inventory($field)
    {

        $output = $this->get_form_field_header($field);
        
        if(isset($_POST['form_data'])){
            parse_str($_POST['form_data'], $form_data);
        }
        
        $maximum_inventory = isset($form_data['wpbs-form-field-inventory-maximum']) && absint($form_data['wpbs-form-field-inventory-maximum']) > 0 ? $form_data['wpbs-form-field-inventory-maximum'] : 0;

        $output .= '<select
            name="wpbs-input-' . $this->form->get('id') . '-' . $field['id'] . '"
            id="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"
            class="wpbs-form-field-inventory-dropdown"
            data-placeholder-disabled="' . esc_attr(wpbs_get_payment_default_string('select_dates', $this->args['language'])) . '"
            data-placeholder-enabled="' . $this->get_form_field_translation($field['values'], 'placeholder') . '"
        >';

        if ($maximum_inventory) {

            $value = $this->get_form_field_value($field);

            for ($i = 1; $i <= $maximum_inventory; $i++) {
                $selected = ($value == $i) ? 'selected' : '';
                $output .= '<option ' . $selected . ' value="' . $i . '">' . $i . '</option>';
            }
        } else {
            $output .= '<option value="" selected disabled>' . esc_attr(wpbs_get_payment_default_string('select_dates', $this->args['language'])) . '</option>';
        }

        $output .= '</select>';
        $output .= '<input type="hidden" value="' . $maximum_inventory . '" class="wpbs-form-field-inventory-maximum" name="wpbs-form-field-inventory-maximum" />';
        $output .= $this->get_form_field_footer($field);

        return $output;
    }

    /**
     * Helper function to output field header
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_header($field)
    {
        $custom_class = isset($field['values']['default']['class']) ? $field['values']['default']['class'] : '';
        $layout_class = isset($field['values']['default']['layout']) ? $field['values']['default']['layout'] : 'wpbs-field-layout-default';
        $error_class = isset($field['error']) ? 'wpbs-form-field-has-error' : '';
        $required_class = $this->form_field_is_required($field) ? 'wpbs-field-required' : '';

        $output = '<div class="wpbs-form-field wpbs-form-field-' . $field['type'] . ' wpbs-form-field-' . $this->form->get('id') . '-' . $field['id'] . ' ' . $custom_class . ' ' . $layout_class . ' ' . $required_class . ' ' . $error_class . '">';
        $output .= $this->get_form_field_label($field);

        $output .= '<div class="wpbs-form-field-input">';

        return $output;
    }

    /**
     * Helper function to output field footer
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_footer($field)
    {

        $output = $this->get_form_field_description($field);
        $output .= $this->get_form_field_error($field);

        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    /**
     * Helper function to get field labels
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_label($field)
    {
        if (isset($field['values']['default']['hide_label']) && $field['values']['default']['hide_label'] == 'on') {
            return false;
        }

        if (in_array($field['type'], array('html', 'hidden'))) {
            return false;
        }

        $for = !in_array($field['type'], array('dropdown', 'radio', 'checkbox', 'product_dropdown', 'product_radio', 'product_checkbox')) ? 'for="wpbs-form-field-input-' . $this->form->get('id') . '-' . $field['id'] . '-' . $this->unique . '"' : '';

        $output = '<div class="wpbs-form-field-label">';
        $output .= '<label ' . $for . '>';
        $output .= $this->get_form_field_translation($field['values'], 'label');
        $output .= ($this->form_field_is_required($field)) ? '<sup class="wpbs-field-required-asterisk">*</sup>' : '';
        $output .= '</label>';
        $output .= '</div>';
        return $output;
    }

    /**
     * Helper function to get field value
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_value($field)
    {
        if (isset($field['user_value'])) {
            if (is_array($field['user_value'])) {
                return $field['user_value'];
            }

            return esc_attr($field['user_value']);
        }

        if (isset($field['values']['default']['dynamic_population']) && $field['values']['default']['dynamic_population'] == 'on') {
            if (isset($_GET['wpbs-field-' . $field['id']])) {
                return esc_attr($_GET['wpbs-field-' . $field['id']]);
            }

            $dynamic_value = apply_filters('wpbs_form_field_value_' . $this->form->get('id') . '_' . $field['id'], false);

            if ($dynamic_value !== false) {
                return esc_attr($dynamic_value);
            }
        }

        return esc_attr($this->get_form_field_translation($field['values'], 'value'));
    }

    /**
     * Helper function to get field descriptions
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_description($field)
    {
        if (empty($this->get_form_field_translation($field['values'], 'description'))) {
            return false;
        }

        $output = '<div class="wpbs-form-field-description">';
        $output .= '<small>' . $this->get_form_field_translation($field['values'], 'description') . '</small>';
        $output .= '</div>';
        return $output;
    }

    /**
     * Helper function to get field errors
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_field_error($field)
    {
        $error = isset($field['error']) ? $field['error'] : '';

        $output = '';

        if ($error) {
            $output .= '<div class="wpbs-form-field-error"><small>' . $error . '</small></div>';
        }
        return $output;
    }

    /**
     * Helper function to get translations
     *
     * @param array $values
     * @param string $key
     *
     * @return string
     *
     */
    protected function get_form_field_translation($values, $key)
    {
        $language = $this->args['language'];

        if (array_key_exists($language, $values) && !empty($values[$language][$key])) {
            return $values[$language][$key];
        }

        if (isset($values['default'][$key]) && !empty($values['default'][$key])) {

            return $values['default'][$key];
        }

        return null;
    }

    /**
     * Checks if a field is required
     *
     * @param array $field
     *
     * @return bool
     *
     */
    protected function form_field_is_required($field)
    {
        return (isset($field['values']['default']['required']) && $field['values']['default']['required'] == 'on') || in_array($field['type'], array('consent', 'payment_method')) ? true : false;
    }

    /**
     * Constructs and returns the HTML the submit button
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_form_button()
    {

        $language = $this->args['language'];
        $label = wpbs_get_translated_form_meta($this->form->get('id'), 'submit_button_label', $language);

        if (empty($label)) {
            $label = __('Submit', 'wp-booking-system');
        }

        $output = '<div class="wpbs-form-field wpbs-form-submit-button">';

        $output .= '<button type="submit" formnovalidate id="wpbs-form-submit-' . $this->form->get('id') . '">' . esc_attr($label) . '</button>';

        $output .= '</div>';

        return $output;
    }

    protected function get_form_return_url()
    {

        if (isset($_POST['wpbs_permalink'])) {
            $permalink = sanitize_text_field($_POST['wpbs_permalink']);
        } else {
            $permalink = get_permalink();
        }

        return '<input type="hidden" name="wpbs-return-url" value="' . $permalink . '" />';

    }

    /**
     * Generates the styles for the form colors
     *
     * @return string
     *
     */
    protected function get_form_styles()
    {

        if (isset($this->plugin_settings['form_styling']) && $this->plugin_settings['form_styling'] == 'theme') {
            return '';
        }

        $colors = array(
            'button_background_color' => '#aaaaaa',
            'button_background_hover_color' => '#7f7f7f',
            'button_text_color' => '#ffffff',
            'button_text_hover_color' => '#ffffff',
        );

        foreach ($colors as $color_key => $color) {
            if (isset($this->plugin_settings[$color_key]) && !empty($this->plugin_settings[$color_key])) {
                $colors[$color_key] = $this->plugin_settings[$color_key];
            }
        }

        $output = '<style>';

        // Button
        $output .= '.wpbs-main-wrapper .wpbs-form-container .wpbs-form-field button.wpbs-coupon-code-button, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field button[type="submit"], .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field input[type="submit"], .wpbs-payment-confirmation-stripe-form #wpbs-stripe-card-button, #wpbs-authorize-net-button-container #wpbs-authorize-net-submit {background: ' . $colors['button_background_color'] . ' !important; color: ' . $colors['button_text_color'] . ' !important; }';

        // Button Hover
        $output .= '.wpbs-main-wrapper .wpbs-form-container .wpbs-form-field button.wpbs-coupon-code-button:hover, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field button[type="submit"]:hover, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field input[type="submit"]:hover, .wpbs-payment-confirmation-stripe-form #wpbs-stripe-card-button:hover, #wpbs-authorize-net-button-container #wpbs-authorize-net-submit:hover {background: ' . $colors['button_background_hover_color'] . ' !important;  color: ' . $colors['button_text_hover_color'] . ' !important; }';

        // Radio & Checkbox
        $output .= '.wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-radio .wpbs-form-field-input label input:checked ~ span, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-payment_method .wpbs-form-field-input label input:checked ~ span, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-checkbox .wpbs-form-field-input label input:checked ~ span, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-consent .wpbs-form-field-input label input:checked ~ span, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-product_radio .wpbs-form-field-input label input:checked ~ span, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-product_checkbox .wpbs-form-field-input label input:checked ~ span {background: ' . $colors['button_background_color'] . ' !important;  }';

        // Checkbox Active State
        $output .= '.wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-checkbox .wpbs-form-field-input label span:after, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-consent .wpbs-form-field-input label span:after, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-product_checkbox .wpbs-form-field-input label span:after {border-color: ' . $colors['button_text_color'] . ' !important; }';

        // Radio Active State
        $output .= '.wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-payment_method .wpbs-form-field-input label input[type="radio"]:checked~span:after, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-product_radio .wpbs-form-field-input label input[type="radio"]:checked~span:after, .wpbs-main-wrapper .wpbs-form-container .wpbs-form-field.wpbs-form-field-radio .wpbs-form-field-input label input[type="radio"]:checked~span:after { background: ' . $colors['button_text_color'] . ' !important; }';

        $output .= apply_filters('wpbs_form_outputter_form_styles', '', $colors);

        $output .= '</style>';

        return $output;
    }

    /**
     * Get the unique code to diferentiate forms
     *
     */
    public function get_unique()
    {
        return $this->unique;
    }

    /**
     * Get the language
     *
     */
    public function get_language()
    {
        return $this->args['language'];
    }

}
