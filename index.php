<?php

require 'vendor/autoload.php';

use rssAtomFeed\db;
use rssAtomFeed\rssAtomFeed;

date_default_timezone_set('Europe/Warsaw');

$db = new db();
$rssAtomFeed = new rssAtomFeed( $db );

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
    </head>
    <body>
        <?php echo $rssAtomFeed->user_interface; ?>

    </body>
</html>