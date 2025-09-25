<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\IrigasiImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function showForm()
    {
        return view('admin.import'); // form upload
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new IrigasiImport, $request->file('file'));

        return back()->with('success', 'Data berhasil diimport!');
    }
}
