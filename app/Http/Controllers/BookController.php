<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Book;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\PDF as DomPDF; // Import the DomPDF class directly

class BookController extends Controller
{
    public function __construct(){
        $this->middleware(
            'permission:list_books',
            [
                'only' => [
                    'index',
                    'getBooks',
                    'exportPDF',
                ]
            ]
        );

        $this->middleware(
            'permission:manage_books',
            [
                'only' => [
                    'create',
                    'store',
                    'edit',
                    'update',
                ]
            ]
        );

        $this->middleware(
            'permission:delete_books',
            [
                'only' => [
                    'destroy'
                ]
            ]
        );
    }


    // Show all books
    public function index()
    {
        $books = Book::all();
        return view('books.index', compact('books'));
    }
    

    public function getBooks(Request $request)
    {
        if ($request->ajax()) {
            $data = Book::select('id', 'author', 'title', 'status')
                        ->get();

            return Datatables::of($data)
                ->addColumn('actions', function ($book) {
                    return '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $book->id . '">Edit</button>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
    }

    public function exportPDF(DomPDF $pdf) // Inject the PDF service
    {
        $books = Book::select('author', 'title', 'status')->get();
        $pdf->loadView('books.pdf', compact('books'));
        return $pdf->download('books.pdf');
    }

    // Show form to create a new book
    public function create()
    {
        return view('books.create');
    }

    // Store a new book
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'status' => 'required|in:available,booked',
        ]);

        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'status' => $request->status,
        ]);

        return redirect()->route('books.index')->with('success', 'Book created successfully.');
    }

    // Show the form to edit a book
    public function edit($id)
    {
        $book = Book::findOrFail($id);
        return view('books.edit', compact('book'));
    }

    // Update a book
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'status' => 'required|in:available,booked',
        ]);

        $book = Book::findOrFail($id);
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'status' => $request->status,
        ]);

        return response()->json(['success' => 'Book updated successfully.']);
    }

    // Delete a book
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return redirect()->route('books.index')->with('success', 'Book deleted successfully.');
    }
}
