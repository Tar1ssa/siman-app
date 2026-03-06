<?php

if (!function_exists('submission_token')) {
    /**
     * Generate a unique submission token for preventing duplicate form submissions
     */
    function submission_token()
    {
        return bin2hex(random_bytes(16));
    }
}

if (!function_exists('submission_token_field')) {
    /**
     * Generate a hidden input field with submission token
     */
    function submission_token_field()
    {
        $token = submission_token();
        return '<input type="hidden" name="_submission_token" value="' . $token . '">';
    }
}
