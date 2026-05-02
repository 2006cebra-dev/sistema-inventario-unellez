<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Crear un nuevo perfil desde el modal
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $emailFicticio = strtolower(str_replace(' ', '', $request->name)) . rand(1000, 9999) . '@oswainv.local';

        User::create([
            'name' => $request->name,
            'email' => $emailFicticio,
            'password' => bcrypt('12345678')
        ]);

        return response()->json(['success' => true]);
    }

    // Actualizar perfil y subir/reemplazar foto
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->hasFile('profile_photo')) {
            $request->validate([
                'profile_photo' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
            ]);

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            $path = $request->file('profile_photo')->store('perfiles', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        return response()->json(['success' => true]);
    }

    // Eliminar perfil y su foto asociada
    public function destroy($id)
    {
        if ($id == 1) {
            return response()->json(['success' => false, 'message' => 'El administrador principal no puede ser eliminado.']);
        }

        $user = User::findOrFail($id);
        
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}
