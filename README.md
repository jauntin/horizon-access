# Jauntin Horizon Access Package

This is a simple package that gates Laravel Horizon to GitHub team members.

Access will be restricted to users belonging to the team defined in config: `horizon-access.team-members-uri`, which by default is a Jauntin team.

## Integration

### Installation

Include this package adding the following to `composer.json`

```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/jauntin/horizon-access"
        }
    ],
```

Then run `composer require jauntin/horizon-access`.

### GitHub OAuth setup

To use this, you must [set up an OAuth app](https://docs.github.com/en/developers/apps/building-oauth-apps/creating-an-oauth-app) in your **organization**. You must set the callback URL.

By default, you should set it to `http{s}://{mySite.com}/horizon/auth/callback`; this value is configurable as noted below.

### Configuration

You must set `horizon-access.github.client_id / HORIZON_ACCESS_GITHUB_CLIENT_ID` and `horizon-access.github.client_secret / HORIZON_ACCESS_GITHUB_CLIENT_SECRET`.
Other default values should work without further configuration unless needed.

By default, this package is "enabled" when you set those required values.

If you need to customize the config, you can run `php artisan vendor:publish --tag=horizon-access.config` to publish this package's default config to your project.
For example, if you are using a credentials manager, you can customize the config with:

```php
    'github' => [
        // ...
        'client_secret' => env('HORIZON_ACCESS_GITHUB_CLIENT_SECRET', credentials('HORIZON_ACCESS_GITHUB_CLIENT_SECRET')),
        // ...
    ]
```

### Modifications

Update your `horizon` configuration by making the `middleware` key match `horizon-access.middleware` config.

```php
    // e.g.
    'middleware' => ['horizon'],
```

Update your `HorizonServiceProvider` to ask this package to handle gating. For example, the following snippet uses this package's gate when this package is enabled and falls back to a default gate in other situations:

```php
// file app/Providers/HorizonServiceProvider
// ...
use Jauntin\HorizonAccess\Facades\HorizonAccess;
// ...
    protected function gate()
    {
        if (HorizonAccess::enabled()) {
            HorizonAccess::defineGate();
        } else {
            Gate::define('viewHorizon', function ($user = null) {
                return in_array(Config::get('app.env'), ['local', 'dev', 'uat']);
            });
        }
    }
```
