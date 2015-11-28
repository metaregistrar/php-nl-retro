# php-nl-retro
A simple PHP library to connect to the .NL retro service

This service retrieves the results of a search string and returns it in an array of domainname and date fields.

Usage: 

If you are using composer (and if you're not, you should), simply require metaregistrar/php-nl-retro

try {

    $retro = new Metaregistrar\Retro\Retro('searchstring');
    
    foreach ($retro->getResults() as $result) {
    
        echo "Found domain name ".$result['domainname'].", which was deleted on ".$result['date']."\n";
        
    }
    
} catch (Exception $e) {

    echo 'ERROR: '.$e->getMessage()."\n";
    
}

See file test.php for a demo
