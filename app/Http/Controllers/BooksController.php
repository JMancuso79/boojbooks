<?php

namespace App\Http\Controllers;

use Auth;
use App\Book;
use App\Traits\Books;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BooksController extends Controller
{
    use Books;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserBookList()
    {
        // Check if user is authenticated
        $auth_user = Auth::user();
        // If authenticated
        if($auth_user) {
            // Get the user's reading list
            $books = Book::where('user_id', $auth_user->id)->orderBy('order', 'asc')->get();

            return $books;
        }
        // Not authenticated
        else {
            return response()->json([
                'message' => 'fail-auth',
                'book' => null
            ]); 
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if user is authenticated
        $auth_user = Auth::user();
        // If authenticated
        if($auth_user) {
            // Validate data
            $validator = Validator::make($request->all(), [
                'google_id' => ['required', 'string'],
                'title' => ['required', 'string'],
                'author' => ['required', 'string'],
            ]);
            // If validation Fails
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'fail-validation',
                    'errors' => $validator->errors()->first()
                ]);
            } 
            // If validation is successful
            else {
                // This is used to determine order
                $user_book_count = Book::where('user_id', $auth_user->id)->count();

                $book = new Book;

                $book->google_id = $request->google_id;
                $book->user_id = $auth_user->id;
                $book->title = $request->title;
                $book->author = $request->author;
                $book->description = $request->description;
                $book->image = $request->image;
                $book->order = $user_book_count + 1;

                $book->save();
                // Book record created successfully
                if($book) {
                    return response()->json([
                        'message' => 'success',
                        'book' => $book
                    ]);                
                } 
                // Record was not created
                else {
                    return response()->json([
                        'message' => 'fail-db',
                        'book' => null
                    ]); 
                }
            }           
        }
        // Not authenticated
        else {
            return response()->json([
                'message' => 'fail-auth',
                'book' => null
            ]); 
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Show the details view
        return view('book-details')->with('id', $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Update book order
        $book = Book::where('id', $id)->first();
        if($book) {
            $book->order = $request->order;

            $book->save();

            return response()->json([
                'message' => 'success',
            ]); 
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Get book to remove by the record id
        $book = Book::where('id', $id)->first();
        // Book to remove is found
        if($book) {
            $book->delete();
            // Success
            return response()->json([
                'message' => 'success',
            ]); 
        } 
        // Something went wrong
        else {
            return response()->json([
                'message' => 'fail',
            ]);             
        }
    }

    // Is this book in user's reading list?
    public function bookStatus($id) {
        $auth_user = Auth::user();

        if($auth_user) {
            $book = Book::where('google_id', $id)->where('user_id', $auth_user->id)->first();

            if($book) {
                return $book->id;
            } else {
                return 0;
            }            
        } else {
            return 0;
        }

    }

    // Get books from Book Trait
    public function getBooks() {
        return $this->bookVolumes();
    }

    // Get book from Book Trait
    public function getBookByVolumeId($id) {
        return $this->bookByVolumeId($id);
    }

    // Get the ids for books in the user's reading list
    public function getUserBookIds() {
        $auth_user = Auth::user();

        if($auth_user) {
            $books = Book::where('user_id', $auth_user->id)->pluck('google_id');

            return $books;
        } else {
            return null;
        }      
    }

    // Remove a book by it's Google volume id
    public function destroyByVolumeId($id) {
        // Get book 
        $book = Book::where('google_id', $id)->first();
        // Book has been found
        if($book) {
            $book->delete();
            // Success
            return response()->json([
                'message' => 'success',
            ]); 

        } 
        // Something went wrong
        else {
            return response()->json([
                'message' => 'fail',
            ]);             
        }
    }
}
