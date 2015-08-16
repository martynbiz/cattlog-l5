<?php

use Cattlog\Output;

// don't filter files that don't exist, we want to show those too
$destFiles = $fileSystem->getDestFiles($lang);

?>Checking...
<?php foreach ($destFiles as $file): ?>
<?php if ($fileSystem->fileExists($file)): ?>
    <?php echo $file . PHP_EOL; ?>
<?php else: ?>
    <?php echo Output::highlight($file) . PHP_EOL; ?>
<?php endif; ?>
<?php endforeach; ?>

<?php

$keysFromDest = $cattlog->getKeysFromDestFiles($lang);
$keysFromSrc = $cattlog->getKeysFromSrcFiles();

$keysToAdd = $cattlog->getAddedKeys($keysFromDest, $keysFromSrc);
$keysToRemove = $cattlog->getRemovedKeys($keysFromDest, $keysFromSrc);

if (count($keysToAdd)): ?>
The following new keys were found:
<?php foreach ($keysToAdd as $value): ?>
    <?php echo Output::_($value, Output::BG_GREEN) . PHP_EOL; ?>
<?php endforeach; ?>

<?php endif; if (count($keysToRemove)): ?>
The following keys are obsolete:
<?php foreach ($keysToRemove as $value): ?>
    <?php echo Output::_($value, Output::BG_RED) . PHP_EOL; ?>
<?php endforeach; ?>

<?php endif; ?>
