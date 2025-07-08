<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;

class PersonController extends Controller
{
    public function index()
    {
        $people = Person::all();
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

        Person::create($request->all());

        return redirect()->route('people.index')->with('success', 'Person created successfully!');
    }

    public function edit(Person $person)
    {
        return view('people.edit', compact('person'));
    }

    public function update(Request $request, Person $person)
    {
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
        $person->delete();
        return redirect()->route('people.index')->with('success', 'Person deleted successfully!');
    }
}
