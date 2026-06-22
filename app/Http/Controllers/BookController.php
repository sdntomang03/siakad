<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:superadmin|operator']);
    }

    public function index()
    {
        $user = auth()->user();
        $books = Book::where('school_id', $user->school_id)
            ->orderBy('type')
            ->orderBy('tingkat')
            ->orderBy('title')
            ->paginate(20);

        return view('books.index', compact('books'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'type' => 'required|in:paket,perpustakaan',
            'tingkat' => 'nullable|integer|min:1|max:6',
            'stock' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['school_id'] = auth()->user()->school_id;
        if ($data['type'] === 'perpustakaan') {
            $data['tingkat'] = null;
        }

        Book::create($data);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {

        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'type' => 'required|in:paket,perpustakaan',
            'tingkat' => 'nullable|integer|min:1|max:6',
            'stock' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($data['type'] === 'perpustakaan') {
            $data['tingkat'] = null;
        }

        $book->update($data);

        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus.');
    }
}
