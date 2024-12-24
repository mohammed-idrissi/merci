<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreateAppartement;

class CreateAppartementController extends Controller
{
    // عرض جميع الغرف
    public function index()
    {
        $rooms = CreateAppartement::all();
        return view('appartement.index', compact('rooms'));
    }

    // عرض صفحة إنشاء غرفة جديدة
    public function create()
    {
        return view('admin.create');
    }

    // تخزين الغرفة الجديدة في قاعدة البيانات
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'prix' => 'required|numeric|min:0',
            'etoiles' => 'nullable|integer|min:1|max:5',
            'extra_info' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validatedData['image'] = 'images/' . $imageName;
        }

        $validatedData['etoiles'] = $validatedData['etoiles'] ?? 3;

        CreateAppartement::create($validatedData);

        return redirect()->route('appartements.index')->with('success', 'Appartement créé avec succès !');
    }

    // عرض صفحة تعديل غرفة معينة
    public function edit($id)
    {
        $appartement = CreateAppartement::findOrFail($id);
        return view('admin.edit', compact('appartement'));
    }

    // تحديث الغرفة في قاعدة البيانات
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'prix' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'etoiles' => 'nullable|integer|min:1|max:5',
            'extra_info' => 'nullable|string|max:255',
        ]);

        $appartement = CreateAppartement::findOrFail($id);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validated['image'] = 'images/' . $imageName;
        }

        $appartement->update($validated);

        return redirect()->route('appartements.index')->with('success', 'Appartement mis à jour avec succès');
    }

    // حذف الغرفة
    public function destroy($id)
    {
        $appartement = CreateAppartement::findOrFail($id);
        $appartement->delete();

        return redirect()->route('appartements.index')->with('success', 'Appartement supprimé avec succès');
    }
}
