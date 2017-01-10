<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jitamin\Helper;

use DateTime;
use Jitamin\Foundation\Base;

/**
 * DateTime helpers.
 */
class DateHelper extends Base
{
    /**
     * Get formatted time.
     *
     * @param int $value
     *
     * @return string
     */
    public function time($value)
    {
        return date($this->settingModel->get('application_time_format', 'H:i'), $value);
    }

    /**
     * Get formatted date.
     *
     * @param int $value
     *
     * @return string
     */
    public function date($value)
    {
        if (empty($value)) {
            return '';
        }

        if (!ctype_digit($value)) {
            $value = strtotime($value);
        }

        return date($this->settingModel->get('application_date_format', 'm/d/Y'), $value);
    }

    /**
     * Get formatted  datetime.
     *
     * @param int $value
     *
     * @return string
     */
    public function datetime($value)
    {
        return date($this->settingModel->get('application_datetime_format', 'm/d/Y H:i'), $value);
    }

    /**
     * Get duration in seconds into human format.
     *
     * @param int $seconds
     *
     * @return string
     */
    public function duration($seconds)
    {
        if ($seconds == 0) {
            return 0;
        }

        $dtF = new DateTime('@0');
        $dtT = new DateTime("@$seconds");

        return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
    }

    /**
     * Get the age of an item in quasi human readable format.
     * It's in this format: <1h , NNh, NNd.
     *
     * @param int $timestamp Unix timestamp of the artifact for which age will be calculated
     * @param int $now       Compare with this timestamp (Default value is the current unix timestamp)
     *
     * @return string
     */
    public function age($timestamp, $now = null)
    {
        if ($now === null) {
            $now = time();
        }

        $diff = $now - $timestamp;

        if ($diff < 900) {
            return t('<15m');
        }
        if ($diff < 1200) {
            return t('<30m');
        } elseif ($diff < 3600) {
            return t('<1h');
        } elseif ($diff < 86400) {
            return '~'.t('%dh', $diff / 3600);
        }

        return t('%dd', ($now - $timestamp) / 86400);
    }

    /**
     * Get all hours for day.
     *
     * @return array
     */
    public function getDayHours()
    {
        $values = [];

        foreach (range(0, 23) as $hour) {
            foreach ([0, 30] as $minute) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $values[$time] = $time;
            }
        }

        return $values;
    }

    /**
     * Get all days of a week.
     *
     * @return array
     */
    public function getWeekDays()
    {
        $values = [];

        foreach (range(1, 7) as $day) {
            $values[$day] = $this->getWeekDay($day);
        }

        return $values;
    }

    /**
     * Get the localized day name from the day number.
     *
     * @param int $day Day number
     *
     * @return string
     */
    public function getWeekDay($day)
    {
        return date('l', strtotime('next Monday +'.($day - 1).' days'));
    }
}
