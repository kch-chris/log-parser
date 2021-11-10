<?php
/**
 * Log Parser test by Christian Murillo 
*/
require __DIR__ . '/vendor/autoload.php';

use MVar\LogParser\LogIterator;
use MVar\Apache2LogParser\AccessLogParser;
use donatj\UserAgent\UserAgentParser;

/**
 * Function to request the data from the ip location 
 * 
 * @param string $ip Ip Address
 * 
 * @return array|false
 */
function IPtoLocation($ip)
{ 
    $apiURL = 'https://api.freegeoip.app/json/' . $ip . 
        '?apikey=6d2f07d0-423f-11ec-8918-416b3845c07e'; 
     
    // Make HTTP GET request using cURL 
    $ch = curl_init($apiURL); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    $apiResponse = curl_exec($ch); 
    if ($apiResponse === false) { 
        $msg = curl_error($ch); 
        curl_close($ch); 
        return false; 
    } 
    curl_close($ch); 
     
    // Retrieve IP data from API response 
    $ipData = json_decode($apiResponse, true); 
     
    // Return geolocation data 
    return !empty($ipData) ? $ipData : false; 
}

/**
 * Function to request the data from the ip location 
 * 
 * @param string $device Device System Name
 * 
 * @return string
 */
function translateDevice($device) 
{
    $response = $device;
    switch($device) {
    case "Windows" : case "Macintosh" : case  "Linux" :
                $response = "Desktop";
        break;
    case "iPad" :
                $response = "Tablet";
        break;
    case "Android" : case "iPhone" :
                $response = "Mobile";
        break;
    }
    return $response;
}

$deviceParser = new UserAgentParser;
$parser = new AccessLogParser('%h %l %u %t "%r" %>s %O "%{Referer}i" "%{User-Agent}i"');
$fp = fopen('export_log.csv', 'w');

foreach (new LogIterator('gobankingrates.com.access.log', $parser) as $line => $data) {
    print_r($data);
    $locationInfo = IPtoLocation($data['remote_host']);

    print $locationInfo['country_name'];
    print $locationInfo['region_name'];

    $resultDevice = $deviceParser->parse($data['request_headers']['User-Agent']);
    print translateDevice($resultDevice->platform())."\n";
    print $resultDevice->browser()."\n";

    $geoAndDevice = [
        $data['remote_host'],
        $data['identity'],
        $data['remote_user'],
        $data['time'],
        $data['request_line'],
        $data['response_code'],
        $data['bytes_sent'],
        $data['request']['method'],
        $data['request']['path'],
        $data['request']['protocol'],
        $data['request_headers']['Referer'],
        $data['request_headers']['User-Agent'],
        $locationInfo['country_name'],
        $locationInfo['region_name'],
        TranslateDevice($resultDevice->platform()),
        $resultDevice->browser()
    ];

    fputcsv($fp, $geoAndDevice);

}


fclose($fp);
