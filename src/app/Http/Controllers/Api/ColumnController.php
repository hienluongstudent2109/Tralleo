<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Column;

class ColumnController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string'
        ]);

        $max = Column::where('project_id', $data['project_id'])->max('position');

        $column = Column::create([
            ...$data,
            'position' => $max + 1
        ]);

        return response()->json($column);
    }
}
