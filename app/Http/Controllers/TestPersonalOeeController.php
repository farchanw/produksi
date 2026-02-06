<?php

namespace App\Http\Controllers;

use App\Imports\PersonalOeeImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TestPersonalOeeController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $import = new PersonalOeeImport;
        Excel::import($import, $request->file('file'));

        return response()->json($import->result);
    }
}
