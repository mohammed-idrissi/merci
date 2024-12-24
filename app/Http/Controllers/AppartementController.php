<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreateAppartement;

class AppartementController extends Controller
{
    public function index()
    {
        $rooms = CreateAppartement::all();
        return view('appartement.index', compact('rooms'));
    }

    public function create()
    {
        return view('appartement.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'prix' => 'required|numeric',
            'etoiles' => 'required|integer|min:1|max:5',
            'extra_info' => 'nullable|string',
        ]);

        $validatedData['image'] = $this->uploadImage($request, 'images/appartements');

        CreateAppartement::create($validatedData);

        return redirect()->route('appartement.index')->with('success', 'Appartement ajouté avec succès!');
    }

    public function edit($id)
    {
        $room = CreateAppartement::findOrFail($id);
        return view('appartement.edit', compact('room'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|numeric',
            'etoiles' => 'required|integer|min:1|max:5',
            'extra_info' => 'nullable|string',
        ]);

        $room = CreateAppartement::findOrFail($id);

        $validatedData['image'] = $this->uploadImage($request, 'images/appartements', $room->image);

        $room->update($validatedData);

        return redirect()->route('appartement.index')->with('success', 'Appartement mis à jour avec succès!');
    }

    public function destroy($id)
    {
        $room = CreateAppartement::findOrFail($id);

        if ($room->image && file_exists(public_path($room->image))) {
            unlink(public_path($room->image));
        }

        $room->delete();

        return redirect()->route('appartement.index')->with('success', 'Appartement supprimé avec succès!');
    }

    public function show($id)
    {
        $room = CreateAppartement::findOrFail($id);
        return view('appartement.show', compact('room'));
    }

    public function adminIndex()
    {
        $rooms = CreateAppartement::all();
        return view('admin.rooms.index', compact('rooms'));
    }
    public function appartementAdmin()
    {
        $rooms = CreateAppartement::all();
        return view('appartement.admin', compact('rooms'));
    }


    private function uploadImage(Request $request, $directory, $existingImage = null)
    {
        if ($request->hasFile('image')) {
            if ($existingImage && file_exists(public_path($existingImage))) {
                unlink(public_path($existingImage));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path($directory), $imageName);
            return $directory . '/' . $imageName;
        }

        return $existingImage;
    }
}
