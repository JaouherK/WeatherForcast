<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="css/weather.css" rel="stylesheet" type="text/css"/>
    <title>Weather</title>
</head>
<body>

<?php
/**
 * Created by PhpStorm.
 * User: kharr
 * Date: 07/03/2017
 * Time: 21:52
 */

echo '<h1>Using Open weather map</h1>';
require_once('OpenWeatherMap.class.php');

echo "<div class=\"clearfix\"></div>";
?>
<div class="clearfix"></div>
<form action="export-csv.php?place=<?= $_GET['place'] ?>" method="post" target="_blank">
    <input name="contenu" value="<?= implode("*",$ar)?>" class="form-control hidden">
    <button type="submit" class="btn btn-danger pull-right  margin-right-sm">Export<br>CSV</button>
</form>
<?php
echo '<h1>Using Yahoo weather</h1>';

require_once('jweather.class.php');

new jweather($_GET['place'], 'c', 'img/', false);

?>

