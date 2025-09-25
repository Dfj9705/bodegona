<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json([
            'status' => true,
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except(['_token', 'password_confirmation']);
        $creado = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json(['data' => $creado], 200);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        return response()->json($user);
    }
    /**
     * Update the rol in storage.
     */
    public function rol(Request $request, User $user)
    {
        $validator =  Validator::make($request->all(), [
            'user_id' => ['required','exists:users,id'],
            'rol' => ['required','exists:roles,id']
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $rol = Role::findById($request->rol, 'web');    
        $asignacion = $user->syncRoles($rol);

        return response()->json(['data' => $asignacion], 200);
        
    }
    /**
     * Update the password resource in storage.
     */
    public function password(Request $request, User $user)
    {
        $validator =  Validator::make($request->all(), [
            'user_password_id' => ['required','exists:users,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->password = Hash::make($request->password);
        $actualizacion = $user->update();

        return response()->json(['data' => $actualizacion], 200);
        
    }
    /**
     * Update the status in storage.
     */
    public function status(Request $request, User $user)
    {

        $user->status = !$user->status;
        $actualizacion = $user->update();

        return response()->json(['data' => $actualizacion], 200);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
