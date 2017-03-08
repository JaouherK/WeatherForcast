<?php
/**
 * Created by PhpStorm.
 * User: kharr
 * Date: 08/03/2017
 * Time: 02:32
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
    public $uri;
    public $path;

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

        $this->path = $dir . DIRECTORY_SEPARATOR . md5($url);

        return $this->path;
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
$this->uri = $url;
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

$lang = 'en';
$units = 'metric';
$cache = new ExampleCache();
$cache->setTempPath(__DIR__.'/temps');
$cache->setSeconds(100);
$owm = new OpenWeatherMap($myApiKey, null, $cache, 100);

$forecast = $owm->getWeatherForecast($_GET['place'], $units, $lang, '', 11);
$html = '<div class="clearfix"></div>';
$ar = array();
foreach ($forecast as $weather) {
    $html .= '<div class=" col-sm-2 text-center"><div class="weatherMain">' . $weather->time->day->format('d.m.Y') .'<br>';
    $html .= '<div class="weatherItem" style="background-image: url('  . $weather->weather->getIconUrl() . '); background-repeat: no-repeat; background-position: center; ">';
    $html .= '<div class="weatherCity">' . $forecast->city->name . '</div>';
    $html .= '<div class="weatherTemp">' . $weather->temperature->min . ',' . $weather->temperature->max .'</div>';
    $html .= '<div class="weatherDesc">' . $weather->wind->speed->getDescription() . ' '.$weather->clouds->getDescription().'</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $aux = $weather->time->day->format('d.m.Y')."/".$weather->temperature->min->getValue()."/".$weather->temperature->max->getValue()."/".$weather->clouds->getDescription()."/";
    array_push($ar, $aux);
}
echo $html;