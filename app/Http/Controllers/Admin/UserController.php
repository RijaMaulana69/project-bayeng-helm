<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('nama_role', '!=', 'owner');
            })
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        $roles = Role::where('nama_role', '!=', 'owner')->get();

        return view('admin.user.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::where('nama_role', '!=', 'owner')->get();

        return view('admin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255',
            'password'     => 'required|string|min:8',
            'id_role'      => 'required|exists:role,id_role',
        ]);

        $exists = User::whereRaw('LOWER(username) = ?', [strtolower($request->username)])->exists();

        if ($exists) {
            return redirect()->route('admin.user.create')
                ->withInput()
                ->with('error', 'Username sudah digunakan.');
        }

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'id_role'      => $request->id_role,
        ]);

        $authUser = Auth::guard('web')->user();
        ActivityLog::log('create_user', 'Menambahkan user: ' . $user->nama_lengkap . ' (' . $user->username . ')', 'user', $authUser->id_user, $authUser->username);

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user  = User::findOrFail($id);
        $roles = Role::where('nama_role', '!=', 'owner')->get();

        return view('admin.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'id_role'      => 'required|exists:role,id_role',
            'password'     => 'nullable|string|min:8',
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'id_role'      => $request->id_role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $authUser = Auth::guard('web')->user();
        ActivityLog::log('update_user', 'Mengubah user: ' . $user->nama_lengkap . ' (' . $user->username . ')', 'user', $authUser->id_user, $authUser->username);

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $authUser = Auth::guard('web')->user();

        if ($authUser->id_user == $id) {
            return redirect()->route('admin.user.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user = User::findOrFail($id);

        try {
            $nama = $user->nama_lengkap;
            $username = $user->username;
            $user->delete();

            ActivityLog::log('delete_user', 'Menghapus user: ' . $nama . ' (' . $username . ')', 'user', $authUser->id_user, $authUser->username);

            return redirect()->route('admin.user.index')
                ->with('success', 'User berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('admin.user.index')
                ->with('error', 'User tidak dapat dihapus karena masih memiliki data terkait.');
        }
    }
}
