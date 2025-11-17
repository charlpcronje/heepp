<?php
return [
    'ignore.when' => [
        '*' => [
            'files' => ['codealike.json','robots.txt','*.git','*.gitignore'],
            'folders' => ['sessions','schema','logs']
        ],
        'indexing' => [
            'files'   => ['*.png','*.jpg','*.gif','*.'],
            'folders' => ['.vscode','_notes','molds','env']
        ]
    ]
];
