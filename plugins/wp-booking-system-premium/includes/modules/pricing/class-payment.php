<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The main class for the Payment
 *
 */
class WPBS_Payment extends WPBS_Base_Object
{

    /**
     * The Id of the payment
     *
     * @access protected
     * @var    int
     *
     */
    protected $id;

    /**
     * The booking id of the payment
     *
     * @access protected
     * @var    int
     *
     */
    protected $booking_id;

    /**
     * The payment gateway that was used
     *
     * @access protected
     * @var    string
     *
     */
    protected $gateway;

    /**
     * The ID of the order
     *
     * @access protected
     * @var    string
     *
     */
    protected $order_id;

    /**
     * The status of the order
     *
     * @access protected
     * @var    string
     *
     */
    protected $order_status;

    /**
     * The payment details
     *
     * @access protected
     * @var    string
     *
     */
    protected $details;

    /**
     * The prices, stored in $details['prices'];
     *
     * @access protected
     * @var array
     */
    protected $prices;

    /**
     * The date when the payment was created
     *
     * @access protected
     * @var    string
     *
     */
    protected $date_created;

    /**
     * The constructor
     *
     */
    public function __construct($object = false)
    {

        if ($object === false) {
            $object = new stdClass();
        }

        parent::__construct($object);

        // Set the prices if available
        if (isset($this->details['price']) && $this->details['price']) {
            $this->prices = $this->details['price'];
        }

        $this->set_currency();

        $this->backwards_compatibility();

    }

    /**
     * Make sure nothing breaks
     * 
     */
    private function backwards_compatibility(){
        
        // Set the default multiplication for old bookings.
        if(!isset($this->prices['events']['multiplication'])){
            $this->prices['events']['multiplication'] = 1;
        }
    }

    /**
     * Set the currency
     *
     */
    public function set_currency()
    {

        if (!isset($this->prices['currency']) || empty($this->prices['currency'])) {
            $this->prices['currency'] = wpbs_get_currency();
        }

    }

    /**
     * Get the currency
     *
     * @return string
     */
    public function get_currency()
    {
        return $this->prices['currency'];
    }

    /**
     * Get the currency
     *
     * @return string
     */
    public function get_display_currency()
    {
        return apply_filters('wpbs_display_currency', $this->get_currency());
    }

    /**
     * Get the total value of the order
     *
     * @return float
     */
    public function get_total()
    {
        return $this->prices['total'];
    }

    /**
     * Get the subtotal value of the order
     *
     * @return float
     */
    public function get_subtotal()
    {
        return $this->prices['subtotal'];
    }

    /**
     * Check wether the order is paid or not
     *
     * @return book
     */
    public function is_paid()
    {
        return isset($this->prices['paid']) && $this->prices['paid'] == true;
    }

    /**
     * Get the payment method
     *
     * @return string
     */
    public function get_payment_method()
    {
        return $this->prices['payment_method'];
    }

    /**
     * Check if it's a part payment
     *
     * @return bool
     */
    public function is_part_payment()
    {
        return isset($this->prices['part_payments']);
    }

    /**
     * Get the value of the first payment (deposit)
     *
     * @return float
     *
     */
    public function get_total_first_payment()
    {
        return $this->prices['part_payments']['first_payment'];
    }

    /**
     * Get the value of the second payment (final payment)
     *
     * @return float
     *
     */
    public function get_total_second_payment()
    {
        return $this->prices['part_payments']['second_payment'];
    }

    /**
     * Check if the deposit was paid
     *
     * @return bool
     */
    public function is_deposit_paid()
    {
        return isset($this->details['part_payments']['deposit']) && $this->details['part_payments']['deposit'] === true;
    }

    /**
     * Check if the final payment was paid
     *
     * @return bool
     */
    public function is_final_payment_paid()
    {
        return isset($this->details['part_payments']['final_payment']) && $this->details['part_payments']['final_payment'] === true;
    }

    /**
     * Get the payment final payment method
     *
     * @return string
     */
    public function get_final_payment_method()
    {
        return $this->prices['part_payments']['method'];
    }

    /**
     * Calculate the prices based on selected dates and form input
     *
     * @param array $post_data
     * @param WPBS_Form @form
     * @param array $form_args
     * @param array $form_fields
     *
     * @return array
     *
     */
    public function calculate_prices($post_data, $form, $form_args, $form_fields)
    {

        $this->vat = new WPBS_VAT;

        $this->prices['has_vat'] = $this->vat->is_enabled();

        if ($this->vat->is_enabled()) {
            $this->prices['vat_percentage'] = $this->vat->get_percentage();
        }

        $this->prices['total'] = 0;

        $language = $form_args['language'];

        // Get payment method
        $this->prices['payment_method'] = '';

        foreach ($form_fields as $field) {
            if ($field['type'] != 'payment_method') {
                continue;
            }

            if (!isset($field['user_value'])) {
                continue;
            }

            $this->prices['payment_method'] = $field['user_value'];
        }

        $this->prices = apply_filters('wpbs_payment_prices', $this->prices, $post_data);

        $settings = get_option('wpbs_settings');

        // Get default price
        $calendar_id = absint(!empty($post_data['calendar']['id']) ? $post_data['calendar']['id'] : 0);
        $default_price = wpbs_get_calendar_meta($calendar_id, 'default_price', true);

        $default_price = apply_filters('wpbs_pricing_item_modifier', $default_price, $this->prices, 'default_price');

        // Product name

        $product_name = wpbs_get_translated_form_meta($form->get('id'), 'product_name', $language);

        if (empty($product_name)) {
            $product_name = (isset($settings['payment_product_name_translation_' . $language]) && !empty($settings['payment_product_name_translation_' . $language])) ? $settings['payment_product_name_translation_' . $language] : ((isset($settings['payment_product_name']) && !empty($settings['payment_product_name'])) ? $settings['payment_product_name'] : __('Item', 'wp-booking-system'));
        }

        $product_name = apply_filters('wpbs_pricing_product_name', $product_name, $calendar_id, $form->get('id'), $language, $this);

        // Get selection style
        $selection_style = $post_data['form']['selection_style'];

        $this->prices['selection_style'] = $selection_style;

        /**
         * Calculate Events Price
         */

        // Set defaults
        $events_price = 0;
        $quantity = 0;

        // Set dates
        $start_date = wpbs_convert_js_to_php_timestamp($post_data['calendar']['start_date']);
        $end_date = wpbs_convert_js_to_php_timestamp($post_data['calendar']['end_date']);

        // Start Date
        $events_begin = new DateTime();
        $events_begin->setTimestamp($start_date);

        // End date
        $events_end = new DateTime();
        $events_end->setTimestamp($end_date);
        if ($selection_style == 'normal') {
            $events_end->modify('+1 day'); // Add +1 day to correct the interval
        }

        if ($selection_style == 'split' && $start_date == $end_date) {
            $events_end->modify('+1 day');
        }

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($events_begin, $interval, $events_end);

        foreach ($period as $event_date) {
            // Check for custom price
            $events = wpbs_get_events(array('calendar_id' => $calendar_id, 'date_day' => $event_date->format('d'), 'date_month' => $event_date->format('m'), 'date_year' => $event_date->format('Y')));

            if (!empty($events) && (!empty($events[0]->get('price')) || $events[0]->get('price') === 0)) {
                $event_price = apply_filters('wpbs_pricing_item_modifier', $events[0]->get('price'), $this->prices, 'event_price');
            } else {
                $event_price = $default_price;
            }

            if (is_numeric($event_price)) {
                $events_price += $event_price;
            }

            $this->prices['events']['individual_days'][$event_date->format('Ymd')] = round((float) $event_price, 2);

            //Increase quantity
            $quantity++;
        }

        // Check for multiplication
        $this->prices['events']['multiplication'] = 1;

        if (wpbs_get_form_meta($form->get('id'), 'multiplication_field', true)) {
            $multiplication_field_id = wpbs_get_form_meta($form->get('id'), 'multiplication_field', true);

            foreach ($form_fields as $field) {
                if ($field['id'] != $multiplication_field_id) {
                    continue;
                }

                if (!isset($field['user_value'])) {
                    continue;
                }

                $this->prices['events']['multiplication'] = absint($field['user_value']);
            }

            $events_price = $events_price * $this->prices['events']['multiplication'];

        }

        $events_price = apply_filters('wpbs_pricing_events_price', $events_price, $this->prices, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date);

        $events_price = $this->vat->deduct_vat($events_price);

        $events_price = round($events_price, 2);

        $this->prices['total'] += $events_price;

        $this->prices['quantity'] = $quantity;
        $this->prices['default_price'] = $default_price;

        $this->prices['events']['name'] = $product_name;
        $this->prices['events']['price'] = $events_price;

        $this->prices = apply_filters('wpbs_get_checkout_price_after_events', $this->prices, $this, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date);

        /**
         * Calculate Extras Prices
         */

        foreach ($form_fields as $form_field) {
            if (!wpbs_form_field_is_product($form_field['type'])) {
                continue;
            }

            if (empty($form_field['user_value'])) {
                continue;
            }

            if (!is_array($form_field['user_value']) && trim($form_field['user_value']) == '|') {
                continue;
            }

            // Check if is an array or single value
            $options = (!is_array($form_field['user_value'])) ? array($form_field['user_value']) : $form_field['user_value'];

            // Check if we have a date range
            if (isset($form_field['values']['default']['date_range']) && !empty($form_field['values']['default']['date_range'])) {
                $date_range = $form_field['values']['default']['date_range'];
                list($date_range_start, $date_range_end) = explode("|", $date_range);

                // Make the dates a DateTime object
                $date_range_start = DateTime::createFromFormat('Y-m-d H:i:s', $date_range_start . ' 00:00:00');
                $date_range_end = DateTime::createFromFormat('Y-m-d H:i:s', $date_range_end . ' 00:00:00');

                // Check if the dates are valid
                if ($date_range_start && $date_range_end) {

                    // Clone the event starting date in case we need to modify it
                    $date_range_events_begin = clone $events_begin;

                    $in_range = false;

                    // Check recurrence
                    if ($form_field['values']['default']['date_range_type'] == 'yearly') {
                        $date_range_events_begin->setDate(date('Y'), $date_range_events_begin->format('m'), $date_range_events_begin->format('d'));
                        $date_range_start->setDate(date('Y'), $date_range_start->format('m'), $date_range_start->format('d'));
                        $date_range_end->setDate(date('Y'), $date_range_end->format('m'), $date_range_end->format('d'));
                    }

                    // Add offsets
                    $date_range_start->modify('-1 day');
                    $date_range_end->modify('+1 day');

                    // Check if date is in range
                    if ($date_range_start < $date_range_end) {
                        $in_range = $date_range_start < $date_range_events_begin && $date_range_events_begin < $date_range_end;
                    } else {
                        $in_range = $date_range_events_begin > $date_range_start || $date_range_events_begin < $date_range_end;
                    }

                    if ($in_range == false) {
                        continue;
                    }

                }
            }

            // Loop
            foreach ($options as $option) {

                if (empty($option)) {
                    continue;
                }

                // Explode and get key value pairs
                list($price, $value) = explode('|', $option);

                if (empty($price)) {
                    $price = 0;
                }

                $price = apply_filters('wpbs_pricing_item_modifier', $price, $this->prices, 'extras_price');

                // Check addition
                $addition = $form_field['values']['default']['pricing_type'];
                $extra_price = ($addition == 'per_day') ? $price * $quantity : $price;

                // Multiply by another field
                $multiplication_id = isset($form_field['values']['default']['multiplication']) ? absint($form_field['values']['default']['multiplication']) : false;
                $multiplication = false;

                if ($multiplication_id !== false) {
                    foreach ($form_fields as $multiplication_form_field) {
                        if ($multiplication_form_field['id'] != $multiplication_id) {
                            continue;
                        }
                        if (empty($multiplication_form_field['user_value']) && strlen($multiplication_form_field['user_value']) === 0) {
                            continue;
                        }

                        $multiplication_value = (int) $multiplication_form_field['user_value'];

                        if (in_array($multiplication_form_field['type'], array('product_dropdown', 'product_checkbox', 'product_radio'))) {
                            $multiplication_value = (int) preg_replace("/[^0-9]/", "", explode('|', $multiplication_form_field['user_value'])[1]);
                        }

                        $multiplication = max(1, absint($multiplication_value));

                        $extra_price = $extra_price * $multiplication;
                    }
                }

                $original_line_label = $value;

                if (isset($form_field['values'][$language]['line_label']) && !empty($form_field['values'][$language]['line_label'])) {
                    $line_label = $form_field['values'][$language]['line_label'];
                } elseif (isset($form_field['values']['default']['line_label']) && !empty($form_field['values']['default']['line_label'])) {
                    $line_label = $form_field['values']['default']['line_label'];
                } else {
                    $line_label = '%%';
                }

                $line_label = str_replace('%%', $original_line_label, $line_label);

                // Filter extra fields
                $extra = apply_filters('wpbs_get_checkout_price_extra', array(
                    'field_id' => $form_field['id'],
                    'label' => $line_label,
                    'original_label' => $original_line_label,
                    'price' => $this->vat->deduct_vat($price, false),
                    'addition' => $addition,
                    'multiplication' => $multiplication,
                    'total' => $extra_price,
                ), $this->prices);

                $extra['total'] = $this->vat->deduct_vat($extra['total']);

                $extra['total'] = round($extra['total'], 2);

                // Add to final output
                $this->prices['extras'][] = $extra;

                // Increment total price
                $this->prices['total'] += $extra['total'];
            }
        }

        $this->prices['subtotal'] = $this->prices['total'];

        $this->prices = apply_filters('wpbs_get_checkout_price_before_subtotal', $this->prices, $this, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date);

        if ($this->prices['total'] < 0) {
            $this->prices['total'] = 0;
        }

        $this->prices['subtotal'] = $this->prices['total'];

        /**
         * Calculate Taxes
         *
         */
        $taxes = array();

        /**
         * VAT
         *
         */
        if ($this->vat->is_enabled()) {
            $taxes[] = array(
                'name' => $this->vat->get_name($language),
                'percentage' => $this->vat,
                'fixed_amount' => 0,
                'calculation' => 'per_booking',
                'value' => $this->vat->get_vat_amount(),
                'type' => 'vat',
            );

            $this->prices['total'] += $this->vat->get_vat_amount();
        }

        /**
         * Taxes
         *
         */
        if (isset($settings['payment_tax_enable']) && $settings['payment_tax_enable'] == 'on' && isset($settings['payment_tax_name'])) {

            for ($i = 0; $i < count($settings['payment_tax_name']); $i++) {

                if (isset($settings['payment_tax_calendars'][$i]) && !empty($settings['payment_tax_calendars'][$i]) && !in_array($calendar_id, (array) $settings['payment_tax_calendars'][$i])) {
                    continue;
                }

                // Check if there's a value set
                if (empty($settings['payment_tax_percentage'][$i]) && (!isset($settings['payment_tax_fixed_amount'][$i]) || empty($settings['payment_tax_fixed_amount'][$i]))) {
                    continue;
                }

                // Check if there's a date range set
                if (isset($settings['payment_tax_start_period'][$i]) && !empty($settings['payment_tax_start_period'][$i]) && isset($settings['payment_tax_end_period'][$i]) && !empty($settings['payment_tax_end_period'][$i])) {

                    $in_range = false;

                    $start = DateTime::createFromFormat('d/m/Y H:i:s', $settings['payment_tax_start_period'][$i] . ' 00:00:00');
                    $start->modify('-1 day');
                    $end = DateTime::createFromFormat('d/m/Y H:i:s', $settings['payment_tax_end_period'][$i] . ' 00:00:00');
                    $end->modify('+1 day');

                    if ($start < $end) {
                        $in_range = $start < $events_begin && $events_begin < $end;
                    } else {
                        $in_range = $events_begin > $start || $events_begin < $end;
                    }

                    if ($in_range == false) {
                        continue;
                    }
                }

                // Set the base name
                $tax_name = (isset($settings['payment_tax_name_translation_' . $language][$i]) && !empty($settings['payment_tax_name_translation_' . $language][$i])) ? $settings['payment_tax_name_translation_' . $language][$i] : $settings['payment_tax_name'][$i];

                // Calculate tax
                if (isset($settings['payment_tax_type'][$i]) && $settings['payment_tax_type'][$i] == 'fixed_amount') {
                    $tax_value = $settings['payment_tax_fixed_amount'][$i];
                    $tax_value = apply_filters('wpbs_pricing_item_modifier', $tax_value, $this->prices, 'tax_value');

                    if ($settings['payment_tax_calculation'][$i] == 'per_day') {
                        $tax_value = $tax_value * $quantity;
                    }

                    // Check multiplication field.
                    $tax_multiplication_field = (isset($settings['payment_tax_multiplication'][$i]) && !empty($settings['payment_tax_multiplication'][$i])) ? $settings['payment_tax_multiplication'][$i] : false;

                    if ($tax_multiplication_field !== false) {
                        list($tax_form_id, $tax_form_field_id) = explode('_', $tax_multiplication_field);

                        $tax_multiplication_value = 0;

                        if ($form->get('id') == $tax_form_id) {
                            foreach ($form_fields as $form_field) {
                                if ($form_field['id'] != $tax_form_field_id) {
                                    continue;
                                }

                                $tax_multiplication_value = absint($form_field['user_value']);

                            }

                        }

                        if ($tax_multiplication_value !== 0) {
                            $tax_value = $tax_value * $tax_multiplication_value;
                        }

                    }

                } else {
                    $tax_source = $this->prices['subtotal'];
                    if (isset($settings['payment_tax_application'][$i]) && $settings['payment_tax_application'][$i] == 'calendar') {
                        $tax_source = $this->prices['events']['price'];
                    } elseif (isset($settings['payment_tax_application'][$i]) && $settings['payment_tax_application'][$i] == 'form') {
                        $tax_source = 0;
                        if (isset($this->prices['extras'])) {
                            foreach ($this->prices['extras'] as $form_tax_product) {
                                $tax_source += $form_tax_product['total'];
                            }
                        }

                    }

                    $tax_source = apply_filters('wpbs_get_checkout_tax_source', $tax_source, $tax_name, $this->prices, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date);

                    $tax_value = $tax_source * $settings['payment_tax_percentage'][$i] / 100;
                    $tax_name .= ' - ' . $settings['payment_tax_percentage'][$i] . '%';
                }

                $tax_value = apply_filters('wpbs_get_checkout_tax', $tax_value, $tax_name, $calendar_id, $form_args, $form, $form_fields, $start_date, $end_date);

                $tax_value = round($tax_value, 2);

                $taxes[] = array(
                    'name' => $tax_name,
                    'percentage' => $settings['payment_tax_percentage'][$i],
                    'fixed_amount' => isset($settings['payment_tax_fixed_amount'][$i]) ? $settings['payment_tax_fixed_amount'][$i] : 0,
                    'calculation' => isset($settings['payment_tax_calculation'][$i]) ? $settings['payment_tax_calculation'][$i] : 'per_booking',
                    'value' => $tax_value,
                    'type' => 'custom',
                );

                $this->prices['total'] += $tax_value;
            }

        }

        if (!empty($taxes)) {
            $this->prices['taxes'] = $taxes;
        }

        $this->prices = apply_filters('wpbs_get_checkout_price_after_total', $this->prices, $post_data, $calendar_id, $form_args, $form, $form_fields);

        $this->prices['total'] = round($this->prices['total'], 2);

        $this->prices = apply_filters('wpbs_get_checkout_price_after', $this->prices, $post_data, $calendar_id, $form_args, $form, $form_fields);

        return $this->prices;
    }

    /**
     * Generate an array containing all the line items
     *
     * @param bool $unfiltered - false = remove empty array items
     *
     * @return array(label,value,tooltip,class,description,quantity,individual_price,type)
     *
     */
    public function get_line_items($unfiltered = false)
    {


        $line_items = array();

        $line_items['events'] = array(
            'label' => $this->prices['events']['name'] . ' <span class="wpbs-line-item-quantity">&times; ' . $this->prices['quantity'] * $this->prices['events']['multiplication'] . '</span>',
            'label_raw' => $this->prices['events']['name'],
            'value' => wpbs_get_formatted_price($this->prices['events']['price'], $this->get_display_currency(), true),
            'tooltip' => wpbs_get_formatted_price(round($this->prices['events']['price'] / ($this->prices['quantity'] * $this->prices['events']['multiplication']), 2), $this->get_display_currency()),
            'quantity' => $this->prices['quantity'] * $this->prices['events']['multiplication'],
            'price' => $this->prices['events']['price'],
            'multiplication' => $this->prices['events']['multiplication'],
            'individual_price' => $this->prices['events']['price'] / ($this->prices['quantity'] * $this->prices['events']['multiplication']),
            'type' => 'event',
            'prices_per_day' => isset($this->prices['events']['individual_days']) ? $this->prices['events']['individual_days'] : array(),
            'class' => 'wpbs-pricing-table-events wpbs-line-item-price-' . $this->prices['events']['price'],
            'editable' => true,
        );

        // Extras
        if (isset($this->prices['extras'])) {
            foreach ($this->prices['extras'] as $i => $item) {
                $line_items[] = array(
                    'label' => $item['label'] . ' <span class="wpbs-line-item-quantity">' . ($item['addition'] == 'per_day' ? '&times; ' . ($item['multiplication'] !== false ? $this->prices['quantity'] * $item['multiplication'] : $this->prices['quantity']) : ($item['multiplication'] !== false ? '&times; ' . $item['multiplication'] : '')) . '</span>',
                    'label_raw' => $item['label'],
                    'value' => wpbs_get_formatted_price($item['total'], $this->get_display_currency(), true),
                    'quantity' => ($item['addition'] == 'per_day' ? ($item['multiplication'] !== false ? $this->prices['quantity'] * $item['multiplication'] : $this->prices['quantity']) : ($item['multiplication'] !== false ? $item['multiplication'] : 1)),
                    'price' => $item['total'],
                    'individual_price' => $item['price'],
                    'type' => 'extra',
                    'class' => 'wpbs-pricing-table-extra wpbs-pricing-table-extra-' . sanitize_title($item['label']) . ' wpbs-line-item-price-' . $item['total'],
                    'editable' => true,
                );
            }
        }

        $line_items = apply_filters('wpbs_line_items_before_subtotal', $line_items, $this);

        $line_items['subtotal'] = array();

        // Taxes
        if (isset($this->prices['taxes'])) {
            foreach ($this->prices['taxes'] as $i => $item) {
                $line_items[] = array(
                    'label' => $item['name'],
                    'value' => wpbs_get_formatted_price($item['value'], $this->get_display_currency(), true),
                    'price' => $item['value'],
                    'type' => (isset($item['type']) && $item['type'] == 'vat' ? 'vat' : 'tax'),
                    'class' => 'wpbs-pricing-table-tax wpbs-line-item-price-' . $item['value'],
                    'editable' => true,
                );
            }
        }

        // Total
        $line_items['total'] = array(
            'label' => __('Total', 'wp-booking-system'),
            'value' => wpbs_get_formatted_price($this->prices['total'], $this->get_display_currency(), true),
            'price' => $this->prices['total'],
            'class' => 'wpbs-line-item-total wpbs-pricing-table-total wpbs-line-item-price-' . $this->prices['total'],
            'type' => 'total',
            'editable' => true,
        );

        $line_items = apply_filters('wpbs_line_items_after_total', $line_items, $this);

        if ($unfiltered == true) {
            return $line_items;
        }

        return array_filter($line_items);
    }

    /**
     * Returns the HTML for the pricing table displayed on the front-end
     *
     * @param string $language
     *
     * @return string
     *
     */
    public function get_pricing_table($language)
    {

        $line_items = $this->get_pricing_table_line_items($language);

        $output = '';

        $output .= apply_filters('wpbs_pricing_table_before', '', $line_items, $this->prices);

        $output .= '<table class="wpbs-pricing-table">';

        // Table Head
        $output .= '<thead><tr><th>' . wpbs_get_payment_default_string('item', $language) . '</th><th>' . wpbs_get_payment_default_string('total', $language) . '</th></thead>';

        // Table Body
        $output .= '<tbody>';

        foreach ($line_items as $line_item) {
            $output .= '
			<tr class="' . (isset($line_item['class']) ? $line_item['class'] : '') . '">
				<td>' .
                (isset($line_item['tooltip']) && !empty($line_item['tooltip']) ? '<span class="wpbs-abbr" title="' . $line_item['tooltip'] . '">' . (isset($line_item['label']) && !empty($line_item['label']) ? $line_item['label'] : '') . '<span>' : (isset($line_item['label']) && !empty($line_item['label']) ? $line_item['label'] : '')) .
                (isset($line_item['description']) && !empty($line_item['description']) ? '<small class="wpbs-line-item-description">' . $line_item['description'] . '<small>' : '') .
                '</td>
				<td>' .
                (isset($line_item['value']) && !empty($line_item['value']) ? $line_item['value'] : '') .
                '</td>
			</tr>';
        }

        $output .= '</tbody>';

        $output .= '</table>';

        $output .= apply_filters('wpbs_pricing_table_after', '', $line_items, $this->prices);

        return $output;
    }

    /**
     * Get the line items for the pricing table
     * Adds the Subtotal field as a line item
     *
     * @param string $language
     *
     * @return array
     *
     */
    public function get_pricing_table_line_items($language)
    {

        // Get line items
        $line_items = $this->get_localized_line_items($language, true);
        // Add subtotal
        if ($this->get_subtotal() != $this->get_total() || $this->get_subtotal() == 0) {
            $line_items['subtotal'] = array(
                'label' => wpbs_get_payment_default_string('subtotal', $language),
                'value' => wpbs_get_formatted_price($this->get_subtotal(), $this->get_display_currency(), true),
                'class' => 'wpbs-line-item-subtotal wpbs-pricing-table-subtotal',
            );
        } else {
            unset($line_items['subtotal']);
        }

        return $line_items;
    }

    /**
     * Adds translations to the line items
     *
     * @param string $language
     * @param bool $unfiltered
     *
     * @return array
     *
     */
    public function get_localized_line_items($language, $unfiltered = false)
    {

        // Get line items
        $line_items = $this->get_line_items($unfiltered);

        // Add translation for "Total" label.
        $line_items['total']['label'] = wpbs_get_payment_default_string('total', $language);

        // Add translation for "Deposit" label.
        if (isset($line_items['first_payment'])) {
            $line_items['first_payment']['label'] = '&#8211; ' . wpbs_get_payment_default_string('part_payments_deposit', $language);
        }

        // Add translation for "Final Payment" label.
        if (isset($line_items['second_payment'])) {
            $line_items['second_payment']['label'] = '&#8211; ' . wpbs_get_payment_default_string('part_payments_final_payment_' . $this->get_final_payment_method(), $language);
        }

        return apply_filters('wpbs_get_localized_line_items', $line_items, $this);
    }

}
