<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\AppServer;

Mcp::web('/mcp', AppServer::class);
