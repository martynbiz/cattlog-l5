<?php

use Cattlog\Colorize;

$data = $cattlog->getDataFromFile($lang);

$keysFromData = count($data) ? array_keys($data) : array();
$keysFromCode = $cattlog->getKeysFromSrcFiles();
$keysToAdd = $cattlog->getAddedKeys($keysFromData, $keysFromCode);
$keysToRemove = $cattlog->getRemovedKeys($keysFromData, $keysFromCode);

?>The following keys have been added:
<?php foreach ($keysToAdd as $value): ?>
    <?php echo Colorize::_($value, Colorize::BG_GREEN) . PHP_EOL; ?>
<?php endforeach; ?>

The following keys have been removed:
<?php foreach ($keysToRemove as $value): ?>
    <?php echo Colorize::_($value, Colorize::BG_RED) . PHP_EOL; ?>
<?php endforeach; ?>
