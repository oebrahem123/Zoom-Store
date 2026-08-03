<?php

namespace App\Http\Controllers;

use App\Services\DesignService;

class DesignsController extends Controller
{
    public function __construct(private DesignService $designService) {}

    public function index()
    {
        return response()->json($this->designService->getAvailableDesigns());
    }
}
