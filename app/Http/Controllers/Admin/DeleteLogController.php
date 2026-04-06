<?php

namespace App\Http\Controllers\Admin;
use App\Models\DeleteLog;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteLogController extends Controller
{
      public function index()
    {
        $logs = DeleteLog::with('user')->latest()->paginate(10);
        return view('admin.delete_logs.index', compact('logs'));
    }
}
