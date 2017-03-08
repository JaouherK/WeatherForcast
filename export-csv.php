<?php

$f= $_POST['contenu'];
$a = explode("*",$f);
require 'php-export-data.class.php';
$exporter = new ExportDataExcel('browser', $_GET['place'].'-'.date("Y-m-d H:i:s").'.xls');
$exporter->initialize();
$exporter->addRow(array('date','min temp','max temp','description'));

foreach ($a as $v) {
    $n = explode("/",$v);
    $exporter->addRow(array($n[0],$n[1],$n[2],$n[3]));
}
$exporter->finalize();
exit();