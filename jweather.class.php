<?php

class jweather {

    private $debugCount = 1;
    private $debug = true;
    private $location;
    private $unit;
    private $imgfolder;
    private $tmp;

    private function debug($str, $title = 'Debug') {
        if ($this->debug) {
            echo '<br/><fieldset><legend>'.$title.' - '.$this->debugCount.'</legend><pre>';
            print_r($str);
            echo '<pre></fieldset><br/>';
            $this->debugCount++;
        }
    }

    private function show($ur, $content) {
        if ($ur) {
            $jsonStr = file_get_contents($ur);
        }
        else {
            $jsonStr = $content;
        }
        $phpobj = json_decode($jsonStr);

        $feed = $phpobj->query->results->channel;

		$wd = $feed->wind->direction;
		if ($wd>=348.75&&$wd<=360){$wd="N";}
        if ($wd>=0&&$wd<11.25){$wd="N";}
        if ($wd>=11.25&&$wd<33.75){$wd="NNE";}
        if ($wd>=33.75&&$wd<56.25){$wd="NE";}
        if ($wd>=56.25&&$wd<78.75){$wd="ENE";}
        if ($wd>=78.75&&$wd<101.25){$wd="L";}
        if ($wd>=101.25&&$wd<123.75){$wd="ESE";}
        if ($wd>=123.75&&$wd<146.25){$wd="SE";}
        if ($wd>=146.25&&$wd<168.75){$wd="SSE";}
        if ($wd>=168.75&&$wd<191.25){$wd="S";}
        if ($wd>=191.25&&$wd<213.75){$wd="SSO";}
        if ($wd>=213.75&&$wd<236.25){$wd="SO";}
        if ($wd>=236.25&&$wd<258.75){$wd="OSO";}
        if ($wd>=258.75&&$wd<281.25){$wd="O";}
        if ($wd>=281.25&&$wd<303.75){$wd="ONO";}
        if ($wd>=303.75&&$wd<326.25){$wd="NO";}
        if ($wd>=326.25&&$wd<348.75){$wd="NNO";}

		$wf = $feed->item->forecast[0];
		
		// Determine Day or Night
		$pubdate = $feed->item->pubDate;
        $n = strpos($pubdate, ":");
        $pubdate = substr($pubdate,$n-2,8);

        $tpb = strtotime($pubdate);
        $tsr = strtotime($feed->astronomy->sunrise);
        $tss = strtotime($feed->astronomy->sunset);

		if ($tpb>$tsr && $tpb<$tss) {
            $daynight = 'd';
        } else {
            $daynight = 'n';
        }


        $html = "<div class='clearfix'></div>";

        foreach ($feed->item->forecast as $forecast) {
            $html .= '<div class=" col-sm-2 text-center"><div class="weatherMain">' . $forecast->day.','.$forecast->date .'<br>';

            $html .= '<div class="weatherItem" style="background-image: url(' . $this->imgfolder . $forecast->code . $daynight . '.png); background-repeat: no-repeat;">';
            $html .= '<div class="weatherCity">' . $feed->location->city . '</div>';
            $html .= '<div class="weatherTemp">' . $forecast->low . '&deg;' . $feed->units->temperature . ', ' . $forecast->high . '&deg;' . $feed->units->temperature . '</div>';
            $html .= '<div class="weatherDesc">' . $feed->item->condition->text . '</div>';

            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        echo $html;
        return $jsonStr;
    }



    private function urlToPath($url)
    {
        $dir = $this->tmp . DIRECTORY_SEPARATOR . "YahooWeatherPHPAPI";
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
        if (!file_exists($path) || filectime($path) + 100 < time()) {
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

    public function __construct($location ,$unit = 'c', $imgfolder = 'img/', $debug = false) {
        $this->tmp = sys_get_temp_dir();

        try {
            if (is_string($location)) {
                $this->location = $location;
            } else {
                throw new Exception("Invalid argument - LOCATION should be a STRING, but it is an ".gettype($location).".");
            }

            if (is_string($unit)) {
                $this->unit = $unit;
            } else {
                throw new Exception("Invalid argument - UNIT should be a STRING, but it is an ".gettype($unit).".");
            }

            if (is_string($imgfolder)) {
                $this->imgfolder = $imgfolder;
            } else {
                throw new Exception("Invalid argument - IMG FOLDER should be a STRING, but it is an ".gettype($imgfolder).".");
            }

            if (is_bool($debug)) {
                $this->debug = $debug;
            } else {
                throw new Exception("Invalid argument - DEBUG should be a BOOLEAN, but it is an ".gettype($debug).".");
            }

            $this->debug("Location: $location\nUnit: $unit\nDebug: $debug");

            $query = "select * from weather.forecast where woeid in (select woeid from geo.places(1) where text='".$this->location."') and u='c'";
            $url = 'http://query.yahooapis.com/v1/public/yql?q='.urlencode($query).'&rnd='.date('Y') . (date('n')-1) . date('w') . date('G') .'&format=json';

            $this->setTempPath(__DIR__.'/temps');
            if ($this->isCached($url)){
                $jsonStr = $this->getCached($url);
                $this->show(false,$jsonStr);
            } else {
                $jsonStr = $this->show($url,false);
                $this->setCached($url,$jsonStr);
            }


        } catch(Exception $e) {
            $this->debug($e->getMessage(),'Exception');
        }
    }
}
