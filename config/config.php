<?php

return [
    'github' => [
        'client_id' => env('HORIZON_ACCESS_GITHUB_CLIENT_ID'),
        'client_secret' => env('HORIZON_ACCESS_GITHUB_CLIENT_SECRET'),
        'redirect' => env('HORIZON_ACCESS_CALLBACK', '/auth/callback'),
    ],
    'middleware' => 'horizon',
    'home' => '/horizon',
    'redirect' => env('HORIZON_ACCESS_REDIRECT', '/auth/redirect'),
    'callback' => env('HORIZON_ACCESS_CALLBACK', '/auth/callback'),
    'session-key' => 'jauntin-github-user',
    'team-members-uri' => '/orgs/jauntin/teams/administrators/members',
];
