<?php

namespace bkfCustomElements;

use function Breakdance\Util\getDirectoryPathRelativeToPluginFolder;
	
add_action('breakdance_loaded', function () {
	require __BKF_PATH_BD__ . "/cat.php";
    \Breakdance\ElementStudio\registerSaveLocation(
        getDirectoryPathRelativeToPluginFolder(__BKF_PATH_BD__),
        'bkfCustomElements',
        'element',
        __('FloristPress Elements', 'bakkbone-florist-companion'),
        true,
    );
},
    9
);