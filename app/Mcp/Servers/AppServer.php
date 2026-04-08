<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\StoreOverview;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('App Server')]
#[Version('0.0.1')]
#[Instructions('Instructions describing how to use the server and its features.')]
class AppServer extends Server
{
    protected array $tools = [
        StoreOverview::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
