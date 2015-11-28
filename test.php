<?php
include('Retro.php');

if ($argc<2) {
    echo "Usage: test.php <searchstring>";
    die();
}

try {
    $retro = new Metaregistrar\Retro\Retro($argv[1]);
    foreach ($retro->getResults() as $result) {
        echo "Found domain name ".$result['domainname'].", which was deleted on ".$result['date']."\n";
    }
} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage();
    echo "\n";
}