<?php

use App\Mcp\Servers\AppServer;
use App\Mcp\Tools\StoreOverview;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('store overview tool returns store status info', function () {
    $response = AppServer::tool(StoreOverview::class);

    $response->assertOk()
        ->assertSee('Store Status Overview:')
        ->assertSee('Total Orders:')
        ->assertSee('pickup');
});
