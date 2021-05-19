<?php
$form_id = absint(!empty($_GET['form_id']) ? $_GET['form_id'] : 0);
$form = wpbs_get_form($form_id);

if (is_null($form)) {
    return;
}

$form_meta = wpbs_get_form_meta($form_id);
$form_data = $form->get('fields');

$wc_billing_fields = wpbs_wc_get_billing_fields();

$available_form_fields = array();

foreach ($form_data as $field):

    if (in_array($field['type'], array('payment_method', 'coupon', 'captcha', 'total', 'consent'))) {
        continue;
    }

    $available_form_fields[$field['id']] = (isset($field['values']['default']['label']) ? $field['values']['default']['label'] : __('no label', 'wp-booking-system-woocommerce')) . ' (ID:' . $field['id'] . ')';

endforeach;
?>

<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-heading wpbs-settings-field-large">
    <label class="wpbs-settings-field-label">
        <?php echo __('WooCommerce Field Mapping', 'wp-booking-system-woocommerce'); ?>
        <?php echo wpbs_get_output_tooltip(__("Automatically fill in the Checkout Form fields in WooCommerce with values from the Booking Form.", 'wp-booking-system-woocommerce'));?>
    </label>
    <div class="wpbs-settings-field-inner">&nbsp;</div>
</div>



<!-- Billing Fields -->
<?php foreach($wc_billing_fields as $field_id => $field_name): ?>
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">
    <label class="wpbs-settings-field-label" for="wc_field_mapping_<?php echo $field_id;?>">
        <?php echo $field_name; ?>
    </label>

    <div class="wpbs-settings-field-inner">
        <select name="wc_field_mapping_<?php echo $field_id;?>"id="wc_field_mapping_<?php echo $field_id;?>" class="regular-text">
            <option value="">-</option>
            <?php foreach($available_form_fields as $form_field_id => $form_field_label): ?>
                <option <?php echo (!empty($form_meta['wc_field_mapping_' . $field_id][0]) && $form_meta['wc_field_mapping_' . $field_id][0] == $form_field_id) ? 'selected="selected"' : ''; ?> value="<?php echo $form_field_id;?>"><?php echo $form_field_label ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
<?php endforeach; ?>