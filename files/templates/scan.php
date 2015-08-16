<?php

use Cattlog\Colorize;

$destFiles = $fileSystem->getDestFiles($lang);

$keysFromDest = $cattlog->getKeysFromDestFiles($lang);
$keysFromSrc = $cattlog->getKeysFromSrcFiles();

$keysToAdd = $cattlog->getAddedKeys($keysFromDest, $keysFromSrc);
$keysToRemove = $cattlog->getRemovedKeys($keysFromDest, $keysFromSrc);

?>Checking...
<?php foreach ($destFiles as $file): ?>
<?php if (file_exists($file)): ?>
    <?php echo $file . PHP_EOL; ?>
<?php else: ?>
    <?php echo Colorize::highlight($file) . PHP_EOL; ?>
<?php endif; ?>
<?php endforeach; ?>

<?php if (count($keysToAdd)): ?>
The following new keys were found:
<?php foreach ($keysToAdd as $value): ?>
    <?php echo Colorize::_($value, Colorize::BG_GREEN) . PHP_EOL; ?>
<?php endforeach; ?>

<?php endif; if (count($keysToRemove)): ?>
The following keys are obsolete:
<?php foreach ($keysToRemove as $value): ?>
    <?php echo Colorize::_($value, Colorize::BG_RED) . PHP_EOL; ?>
<?php endforeach; ?>

<?php endif; ?>
