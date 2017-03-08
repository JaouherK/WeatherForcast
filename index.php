<!DOCTYPE html>
<html>
<head>
    <title>Weather Forecast</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <style type="text/css">
        body {
            text-align: center;
        }

        #city {
            width: 200px;
            height: 30px;
            font-size: 16px;
        }

        #btn {
            width: 80px;
            height: 30px;
            font-size: 16px;
        }

        #weather {
            font-size: 20px;
        }
    </style>
</head>
<body>
<div>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">


<input class="form-controller" id="city" name="city">
        <input id="btn" type="submit" value="Go!"/>
    </form>
</div>


<?php
/**
 * Created by PhpStorm.
 * User: kharr
 * Date: 07/03/2017
 * Time: 23:04
 */
if (isset($_POST['city'])) {
    $check = checkCityOrCountry($_POST['city']);

    if (!$check) {
        ?>
        <div class="alert alert-warning">
            <strong>Warning!</strong> This is not a valid address.
        </div>
<?php
    }
    else {
$i =0;
        foreach ($check as $alternative) {
            echo $i++."/ ";
            foreach ($alternative['address_components'] as $alt) {
                if (in_array('political',$alt['types'] ))
                echo "<a href='getForecast.php?place=".$alt['long_name']."'>".$alt['long_name']."</a> (".$alt['types'][0].") > ";
            }
            echo "<br>";
        }
    }
}

function checkCityOrCountry($name)
{
    $geo_data = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$name.'&sensor=true&language=en');
    $arr = json_decode($geo_data, true);

    if ($arr['status'] == 'ZERO_RESULTS')
    return false;
    else
        return $arr['results'];
}
?>
</body>
</html>