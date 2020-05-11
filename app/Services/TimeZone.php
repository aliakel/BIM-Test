<?php

namespace BeInMedia\Services;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * Trait TimeZone
 * @package BeInMedia\Services
 */
trait TimeZone
{

    /**
     * @param $value
     * @param $tz
     * @param string $format
     * @return string
     */
    public function convertToUtc($value, $tz, $format = "g:i A")
    {
        try {
            date_default_timezone_set($tz);
            $date = new DateTime($value);
            $date->setTimezone(new DateTimeZone('UTC'));

            return $date->format($format);
        } catch (Exception $e) {
            return $value;
        }
    }

    /**
     * @param $value
     * @param $tz
     * @param string $format
     * @return string
     */
    public function convertFromUtc($value, $tz, $format = "g:i A")
    {
        try {
            $date = new DateTime($value);
            $date->setTimezone(new DateTimeZone($tz));
            return $date->format($format);
        } catch (Exception $e) {
            return $value;
        }

    }

    /**
     * @param string $ip
     * @return mixed|string
     */
    public function getTimeZone(string $ip)
    {
        $str = str_replace('.', '_', $ip);
        try {
            return cache()->remember($str, 300, function () use ($ip) {
                $geo = $this->getVisitorGeoInfo($ip);
                if (is_array($geo) && isset($geo['time_zone'])) {
                    return $geo['time_zone'];
                }
                return 'UTC';
            });

        } catch (Exception $e) {
            logger("Geo info failed for ip {$ip}");
            return 'UTC';
        }

    }

    /**
     * @param $ip
     * @return bool|mixed
     */
    function getVisitorGeoInfo($ip)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://freegeoip.app/json/" . $ip,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return false;
        } else {
            return json_decode($response, true);
        }
    }


}
