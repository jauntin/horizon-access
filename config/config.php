<?php

return [
    'github' => [
        'client_id' => env('HORIZON_ACCESS_GITHUB_CLIENT_ID'),
        'client_secret' => env('HORIZON_ACCESS_GITHUB_CLIENT_SECRET'),
        'redirect' => '/horizon/auth/callback',
    ],
    'enabled' => env('HORIZON_ACCESS_GITHUB_CLIENT_ID') && env('HORIZON_ACCESS_GITHUB_CLIENT_SECRET') ? true : false,
    'middleware' => 'horizon',
    'home' => '/horizon',
    'redirect' => '/horizon/auth/redirect',
    'callback' => '/horizon/auth/callback',
    'session-key' => 'jauntin-github-user',
    'team-members-uri' => '/orgs/jauntin/teams/administrators/members',
];
