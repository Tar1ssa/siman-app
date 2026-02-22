<?php

return [
    // Relative paths from project base to include in the backup.
    // Adjust this array to include any project folders you need backed up.
    'paths' => [
        'storage/app',
        'public/storage'
    ],

    // How many backup archives to keep. Older archives will be deleted.
    'retention' => env('BACKUP_RETENTION', 6),
];
