<?php
namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(){
        $this->middleware(
            'permission:reserve',
            [
                'only' => [
                    'reserve'
                ]
            ]
        );
    }

    public function reserve($bookId, Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $book = Book::findOrFail($bookId);

        // Create the booking
        Booking::create([
            'user_id' => auth()->user()->id,
            'bookable_id' => $bookId,
            'bookable_type' => Book::class,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // Update the status of the booked item (you can adjust this if needed)
        $book->update(['status' => 'booked']);

        return redirect()->back()->with('success', 'Item successfully booked.');
    }

}
