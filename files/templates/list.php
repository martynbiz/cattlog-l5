<?php

use Cattlog\Colorize;

$destFiles = $fileSystem->getDestFiles($lang);

?>Checking...
<?php foreach ($destFiles as $file): ?>
<?php if (file_exists($file)): ?>
    <?php echo $file . PHP_EOL; ?>
<?php else: ?>
    <?php echo Colorize::warning($file) . PHP_EOL; ?>
<?php endif; ?>
<?php endforeach; ?>

<?php foreach ($emptyKeys as $key => $value): ?>
<?php echo Colorize::warning($key) . PHP_EOL; ?>
<?php endforeach; ?>
<?php foreach ($nonEmptyKeys as $key => $value): ?>
<?php echo $key . PHP_EOL; ?>
<?php endforeach; ?>
