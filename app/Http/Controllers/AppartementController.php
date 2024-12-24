<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreateAppartement;

class AppartementController extends Controller
{
    // عرض الشقق
    public function index()
    {
        $rooms = CreateAppartement::all();
        return view('appartement.index', compact('rooms'));
    }

    // صفحة إنشاء شقة جديدة
    public function create()
    {
        return view('appartement.create');
    }

    // تخزين شقة جديدة في قاعدة البيانات
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

    // صفحة تعديل الشقة
    public function edit($id)
    {
        $room = CreateAppartement::findOrFail($id);
        return view('appartement.edit', compact('room'));
    }

    // تحديث الشقة في قاعدة البيانات
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

    // حذف الشقة
    public function destroy($id)
    {
        $room = CreateAppartement::findOrFail($id);

        if ($room->image && file_exists(public_path($room->image))) {
            unlink(public_path($room->image));
        }

        $room->delete();

        return redirect()->route('appartement.index')->with('success', 'Appartement supprimé avec succès!');
    }

    // عرض تفاصيل شقة معينة
    public function show($id)
    {
        $room = CreateAppartement::findOrFail($id);
        return view('appartement.show', compact('room'));
    }

    // عرض الشقق في قسم الإدارة
    public function appartementAdmin()
    {
        $rooms = CreateAppartement::all();
        return view('appartement.admin', compact('rooms'));
    }

    // التحقق من صحة الشقة
    public function validation($id)
    {
        $room = CreateAppartement::findOrFail($id);
        $price = $room->prix;

        return view('appartement.appartementValid', ['price' => $price]);
    }

    // التحقق من صحة الشقة (دالة أخرى مشابهة)
    public function Validation2($id)
    {
        $room = CreateAppartement::findOrFail($id);
        $price = $room->prix;

        return view('appartement.appartementValid', ['price' => $price]);
    }

    // إضافة شقة جديدة (تحقق إضافي)
    public function validateAppartement($id)
    {
        $room = CreateAppartement::findOrFail($id);
        $price = $room->prix;

        return view('appartement.validate', ['price' => $price]);
    }

    // رفع الصورة
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
