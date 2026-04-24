<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BoardService;

class BoardController extends Controller
{
    public function show($projectId, BoardService $service)
    {
        return $service->getBoard($projectId);
    }
}
