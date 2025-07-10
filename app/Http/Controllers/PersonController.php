<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Person;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::where('user_id', Auth::id())->get();
        return view('people.index', compact('people'));
    }

    public function create()
    {
        return view('people.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:people,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        $person = new Person($request->all());
        $person->user_id = Auth::id();
        $person->save();

        return redirect()->route('people.index')->with('success', 'Person created successfully!');
    }

    public function edit(Person $person)
    {
        // Ensure user can only edit their own people
        if ($person->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('people.edit', compact('person'));
    }

    public function update(Request $request, Person $person)
    {
        // Ensure user can only update their own people
        if ($person->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:people,email,' . $person->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        $person->update($request->all());

        return redirect()->route('people.index')->with('success', 'Person updated successfully!');
    }

    public function destroy(Person $person)
    {
        // Ensure user can only delete their own people
        if ($person->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $person->delete();
        return redirect()->route('people.index')->with('success', 'Person deleted successfully!');
    }
}
