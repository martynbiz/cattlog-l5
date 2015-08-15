<?php

use Cattlog\Colorize;

$destFiles = $cattlog->getDestFiles($lang);

?>Checking...
<?php foreach ($destFiles as $file): ?>
<?php if (file_exists($file)): ?>
    <?php echo $file . PHP_EOL; ?>
<?php else: ?>
    <?php echo Colorize::warning($file) . PHP_EOL; ?>
<?php endif; ?>
<?php endforeach; ?>

<?php foreach ($keysFromDest as $key): ?>
<?php echo Colorize::success($key) . PHP_EOL; ?>
<?php endforeach; ?>
