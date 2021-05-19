<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Form_Mailer extends WPBS_Mailer
{

    /**
     * The WPBS_Form
     *
     * @access protected
     * @var    WPBS_Form
     *
     */
    protected $form = null;

    /**
     * The WPBS_Calendar
     *
     * @access protected
     * @var    WPBS_Calendar
     *
     */
    protected $calendar = null;

    /**
     * The booking
     *
     * @access protected
     * @var    WPBS_Booking
     *
     */
    protected $booking = null;

    /**
     * The booking id
     *
     * @access protected
     * @var    int
     *
     */
    protected $booking_id = null;

    /**
     * The form fields
     *
     * @access protected
     * @var    array
     *
     */
    protected $form_fields = null;

    /**
     * The language of the email
     *
     * @access protected
     * @var    string
     *
     */
    protected $language;

    /**
     * Booking Start Date
     *
     * @access protected
     * @var    string
     *
     */
    protected $booking_start_date;

    /**
     * Booking End Date
     *
     * @access protected
     * @var    string
     *
     */
    protected $booking_end_date;

    /**
     * Constructor
     *
     * @param WPBS_Form $form
     * @param array     $args
     *
     */
    public function __construct($form, $calendar, $booking_id, $form_fields, $language, $booking_start_date, $booking_end_date)
    {

        /**
         * Set the form
         *
         */
        $this->form = $form;

        /**
         * Set the calendar
         *
         */
        $this->calendar = $calendar;

        /**
         * Set the booking id
         *
         */
        $this->booking_id = $booking_id;

        /**
         * Set the booking object
         *
         */
        $this->booking = wpbs_get_booking($booking_id);

        /**
         * Set the form fields
         *
         */
        $this->form_fields = $form_fields;

        /**
         * Set the language
         *
         */
        $this->language = $language;

        /**
         * Set the booking dates
         *
         */
        $this->booking_start_date = $booking_start_date;
        $this->booking_end_date = $booking_end_date;

    }

    public function prepare($type)
    {   

        switch_to_locale( wpbs_language_to_locale($this->language) );

        $this->type = $type;

        // Check if $type is a valid notification type
        if (!in_array($type, array('user', 'admin', 'payment', 'payment_success', 'reminder', 'followup'))) {
            return false;
        }

        // Check if notification is enabled
        $notification = $this->get_field('enable', $type);
        if ($notification != 'on') {
            return false;
        }

        $email_tags = new WPBS_Email_Tags($this->form, $this->calendar, $this->booking_id, $this->form_fields, $this->language, $this->booking_start_date, $this->booking_end_date);

        // Set Fields
        $this->send_to = $email_tags->parse($this->get_field('send_to', $type));
        $this->send_to_cc = $email_tags->parse($this->get_field('send_to_cc', $type));
        $this->send_to_bcc = $email_tags->parse($this->get_field('send_to_bcc', $type));
        $this->from_name = $email_tags->parse($this->get_field('from_name', $type));
        $this->from_email = $email_tags->parse($this->get_field('from_email', $type));
        $this->reply_to = $email_tags->parse($this->get_field('reply_to', $type));
        $this->subject = $email_tags->parse($this->get_field('subject', $type));
        $this->message = $email_tags->parse(nl2br($this->get_field('message', $type)));
        $this->attachments = apply_filters('wpbs_form_mailer_attachments', array(), $type, $this->form, $this->calendar, $this->booking_id);

    }

    /**
     * Helper function to get the translated value of a field
     *
     * @param string $field
     * @param string $type
     *
     * @return string
     *
     */
    protected function get_field($field, $type)
    {
        return wpbs_get_translated_form_meta($this->form->get('id'), $type . '_notification_' . $field, $this->language);
    }

    
}
