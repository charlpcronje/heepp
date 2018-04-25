<?php

/**
 * Returns a DateTimeValue from a date representation from pick_date_widget2
 *
 * @param string $value
 * @param DateTimeValue $default
 * @return DateTimeValue
 */
function getDateValue($value = '',$default = EMPTY_DATETIME) {
    if ($value instanceof DateTimeValue) {
        return $value;
    }

    if ($value != '' && $value != date_format_tip(user_config_option('date_format'))) {
        $date_format = user_config_option('date_format');
        return DateTimeValueLib::dateFromFormatAndString($date_format, $value);
    }
    return $default;
}

/**
 * Returns an array separating hours and minutes
 *
 * @param string $value
 * @return array
 */
function getTimeValue($value = '') {
    if ($value == '' || $value == 'hh:mm') {
        return null;
    }

    $values = explode(':', $value);
    $h = array_var($values,0);

    $is_pm = str_ends_with(strtoupper(trim(array_var($values,1))),'PM');
    if ($is_pm && $h < 12) {
        $h = ($h + 12) % 24;
    }

    if ($h == 12 && str_ends_with(strtoupper(trim(array_var($values,1))),'AM')) {
        $h = 0;
    }

    $m = str_replace(array(' AM',' PM',' am','pm'),'', array_var($values,1));
    return array('hours' => $h, 'mins' => $m);
}

function year($date) {
    return date('Y',strtotime($date));
}

function month($date) {
    return date('m',strtotime($date));
}

function day($date) {
    return date('d',strtotime($date));
}