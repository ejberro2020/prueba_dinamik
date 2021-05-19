<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitizes the values of an array recursively using sanitize_text_field
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_sanitize_text_field($array = array())
{

    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {

        if (is_array($value)) {
            $array[$key] = _wpbs_array_sanitize_text_field($value);
        } else {
            $array[$key] = sanitize_text_field($value);
        }

    }

    return $array;

}

function _wpbs_recursive_array_replace($find, $replace, $array)
{
    if (!is_array($array)) {
        return str_replace($find, $replace, $array);
    }
    $newArray = array();
    foreach ($array as $key => $value) {
        $newArray[$key] = _wpbs_recursive_array_replace($find, $replace, $value);
    }
    return $newArray;
}

/**
 * Sanitizes the values of an array recursively and allows HTML tags
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_wp_kses_post($array = array())
{

    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {

        if (is_array($value)) {
            $array[$key] = _wpbs_array_wp_kses_post($value);
        } else {
            $array[$key] = wp_kses_post($value);
        }

    }

    return $array;

}

/**
 * Escapes the values of an array recursively using esc_attr
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_esc_attr_text_field($array = array())
{
    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = _wpbs_array_esc_attr_text_field($value);
        } else {
            $array[$key] = esc_attr($value);
        }
    }

    return $array;
}

/**
 * Escapes the values of an array recursively using esc_textarea
 *
 * @param array $array
 *
 * @return array
 *
 */
function _wpbs_array_esc_attr_textarea_field($array = array())
{
    if (empty($array) || !is_array($array)) {
        return array();
    }

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = _wpbs_array_esc_attr_textarea_field($value);
        } else {
            $array[$key] = esc_textarea($value);
        }
    }

    return $array;
}

/**
 * Helper function to search an array
 *
 * @param string
 * @param array
 *
 * @return bool
 *
 */
function _wpbs_recursive_array_search($needle, $haystack)
{
    foreach ($haystack as $key => $value) {
        if ($key === 'user_value') {
            continue;
        }

        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value or (is_array($value) && _wpbs_recursive_array_search($needle, $value) !== false)) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Returns the current locale
 *
 * @return string
 *
 */
function wpbs_get_locale()
{

    return substr(get_locale(), 0, 2);

}

/**
 * Generates and returns a random 32 character long string
 *
 * @return string
 *
 */
function wpbs_generate_hash()
{

    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars_length = strlen($chars);
    $hash = '';

    for ($i = 0; $i < 19; $i++) {

        $hash .= $chars[rand(0, $chars_length - 1)];

    }

    return $hash . uniqid();

}

/**
 * Replace date_i18n with wp_date, but fallback to date_i18n if WP < 5.3
 *
 * @param string $format
 * @param string $timestamp
 *
 * @return string
 *
 */
function wpbs_date_i18n($format, $timestamp)
{
    if (function_exists('wp_date')) {
        $zone = new DateTimeZone('UTC');
        return wp_date($format, $timestamp, $zone);
    }
    return date_i18n($format, $timestamp);
}

/**
 * Get users IP Address
 *
 */
function wpbs_get_user_ip_address()
{
    if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') > 0) {
            $addr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}