<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\PDF as DomPDF; // Import the DomPDF class directly
class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('id', 'name', 'email');
            return Datatables::of($data)->make(true);
        }
    }
    public function exportPDF(DomPDF $pdf) // Inject the PDF service
    {
        $users = User::select('name', 'email')->get();
        $pdf->loadView('users.pdf', compact('users'));
        return $pdf->download('users.pdf');
    }
}
