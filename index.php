<?php
/**
 * Created by PhpStorm.
 * User: kharr
 * Date: 07/03/2017
 * Time: 21:52
 */


/**
 * OpenWeatherMap-PHP-API — A php api to parse weather data from http://www.OpenWeatherMap.org .
 *
 * @license MIT
 *
 * Please see the LICENSE file distributed with this source code for further
 * information regarding copyright and licensing.
 *
 * Please visit the following links to read about the usage policies and the license of
 * OpenWeatherMap before using this class:
 *
 * @see http://www.OpenWeatherMap.org
 * @see http://www.OpenWeatherMap.org/terms
 * @see http://openweathermap.org/appid
 */

use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\AbstractCache;


require_once '/Controller/bootstrap.php';

/**
 * Example cache implementation.
 *
 * @ignore
 */
class ExampleCache extends AbstractCache
{
    protected $tmp;

    public function __construct()
    {
        $this->tmp = sys_get_temp_dir();
    }

    private function urlToPath($url)
    {
        $dir = $this->tmp . DIRECTORY_SEPARATOR . "OpenWeatherMapPHPAPI";
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $path = $dir . DIRECTORY_SEPARATOR . md5($url);

        return $path;
    }

    /**
     * @inheritdoc
     */
    public function isCached($url)
    {
        $path = $this->urlToPath($url);
        if (!file_exists($path) || filectime($path) + $this->seconds < time()) {
            echo "Weather data is NOT cached!\n";

            return false;
        }

        echo "Weather data is cached!\n";

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getCached($url)
    {
        return file_get_contents($this->urlToPath($url));
    }

    /**
     * @inheritdoc
     */
    public function setCached($url, $content)
    {
        file_put_contents($this->urlToPath($url), $content);
    }

    /**
     * @inheritdoc
     */
    public function setTempPath($path)
    {
        if (!is_dir($path)) {
            mkdir($path);
        }

        $this->tmp = $path;
    }
}

// Language of data (try your own language here!):
$lang = 'en';

// Units (can be 'metric' or 'imperial' [default]):
$units = 'metric';

// Example 1: Use your own cache implementation. Cache for 10 seconds only in this example.
$cache = new ExampleCache();
$cache->setTempPath(__DIR__.'/temps');
$owm = new OpenWeatherMap($myApiKey, null, $cache, 10);

$forecast = $owm->getWeatherForecast('Berlin', $units, $lang, '', 10);
echo "EXAMPLE 1<hr />\n\n\n";

echo "City: " . $forecast->city->name;
echo "<br />\n";
echo "LastUpdate: " . $forecast->lastUpdate->format('d.m.Y H:i');
echo "<br />\n";
echo "Sunrise : " . $forecast->sun->rise->format("H:i:s (e)") . " Sunset : " . $forecast->sun->set->format("H:i:s (e)");
echo "<br />\n";
echo "<br />\n";

foreach ($forecast as $weather) {
    // Each $weather contains a Cmfcmf\ForecastWeather object which is almost the same as the Cmfcmf\Weather object.
    // Take a look into 'Examples_Current.php' to see the available options.
    echo "Weather forecast at " . $weather->time->day->format('d.m.Y') . " from " . $weather->time->from->format('H:i') . " to " . $weather->time->to->format('H:i');
    echo "<br />\n";
    echo $weather->temperature;
    echo "<br />\n";
    echo "Sun rise: " . $weather->sun->rise->format('d.m.Y H:i (e)');
    echo "<br />\n";
    echo "---";
    echo "<br />\n";
}

// Example 2: Get forecast for the next 3 days for Berlin.
$forecast = $owm->getWeatherForecast('Berlin', $units, $lang, '', 3);
echo "EXAMPLE 2<hr />\n\n\n";

foreach ($forecast as $weather) {
    echo "Weather forecast at " . $weather->time->day->format('d.m.Y') . " from " . $weather->time->from->format('H:i') . " to " . $weather->time->to->format('H:i') . "<br />";
    echo $weather->temperature . "<br />\n";
    echo "<br />\n";
    echo "Sun rise: " . $weather->sun->rise->format('d.m.Y H:i (e)');
    echo "<br />\n";
    echo "---<br />\n";
}
