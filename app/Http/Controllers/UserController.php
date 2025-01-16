<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
  public function index()
  {
    return view('users.index', [
      'roles' => Role::all()
    ]);
  }

  public function fetchUsers()
  {
    // $users = User::with('role')->get();
    // $users = User::all()->map(function ($user) {
    //   $user->photo_url = $user->photo ? asset('storage/' . $user->photo) : asset('user.png');
    //   return $user;
    // });
    $users = User::with('role')->get()->map(function ($user) {
      $user->photo_url = $user->photo ? asset('storage/' . $user->photo) : asset('user.png');
      return $user;
    });
    return response()->json(['data' => $users]);
  }

  public function store(CreateUserRequest $request)
  {
    // dd($request->all());
    try {
      DB::beginTransaction();

      $data = $request->all();

      if ($request->hasFile('photo')) {
        $data['photo'] = $request->file('photo')->store('photos', 'public');
      }

      User::create($data);

      DB::commit();

      return response()->json(['message' => 'User created successfully.']);
    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }
  }

  public function edit($id)
  {
    $user = User::with('role')->get()->findOrFail($id);
    $user->photo_url = $user->photo ? asset('storage/' . $user->photo) : asset('user.png');

    return response()->json($user);
  }

  public function update(UpdateUserRequest $request, $id)
  {
    // dd($request->all());
    try {
      DB::beginTransaction();

      $data = $request->all();

      $user = User::findOrFail($id);

      if ($request->hasFile('photo')) {
        // $file = $request->file('image');
        // $filename = time() . '.' . $file->getClientOriginalExtension();
        // $file->storeAs('public/images', $filename);
        if ($user->photo) {
          Storage::disk('public')->delete($user->photo);
        }
        $data['photo'] = $request->file('photo')->store('photos', 'public');
      }

      $user->update($data);

      DB::commit();

      return response()->json(['message' => 'User updated successfully.']);
    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }
  }

  public function destroy($id)
  {
    $user = User::findOrFail($id);
    $user->delete();
    return response()->json(['message' => 'User deleted successfully.']);
  }

  public function toggleActive($id)
  {
    $user = User::findOrFail($id);
    $user->is_active = !$user->is_active;
    $user->save();

    $status = $user->is_active ? 'activated' : 'deactivated';
    return response()->json(['message' => "User successfully {$status}."]);
  }
}
