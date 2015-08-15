<?php

use Cattlog\Colorize;

$destFiles = $cattlog->getDestFiles($lang);

?>Checking...
<?php foreach ($destFiles as $value): ?>
    <?php echo $value . PHP_EOL; ?>
<?php endforeach; ?>

<?php foreach ($keysFromDest as $key): ?>
<?php echo Colorize::success($key) . PHP_EOL; ?>
<?php endforeach; ?>
