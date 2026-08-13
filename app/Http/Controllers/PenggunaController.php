<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_user', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $penggunas = $query->latest()->paginate(15);

        return view('pages.pengguna.index', [
            'title'     => 'Data Pengguna',
            'penggunas' => $penggunas,
        ]);
    }

    public function create()
    {
        return view('pages.pengguna.create', [
            'title' => 'Tambah Pengguna',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_user' => 'required|string|max:255',
            'username'  => 'required|string|max:100|unique:users,username',
            'password'  => 'required|string|min:6|confirmed',
            'role'      => 'required|in:admin,karyawan',
        ]);

        $validated['password'] = $validated['password']; // Will be hashed by cast

        User::create($validated);

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $pengguna)
    {
        return view('pages.pengguna.edit', [
            'title'    => 'Edit Pengguna',
            'pengguna' => $pengguna,
        ]);
    }

    public function update(Request $request, User $pengguna)
    {
        $validated = $request->validate([
            'nama_user' => 'required|string|max:255',
            'username'  => ['required', 'string', 'max:100', Rule::unique('users')->ignore($pengguna->id)],
            'password'  => 'nullable|string|min:6|confirmed',
            'role'      => 'required|in:admin,karyawan',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $pengguna->update($validated);

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $pengguna->delete();

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
