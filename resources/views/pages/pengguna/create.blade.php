@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Tambah Pengguna</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Buat akun baru untuk akses aplikasi.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pengguna.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
                Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="rounded-lg bg-error-50 p-4 text-error-800 border border-error-200 dark:bg-error-900/20 dark:text-error-400 dark:border-error-800/30">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white shadow-theme-sm dark:border-gray-800 dark:bg-gray-dark">
        <form action="{{ route('pengguna.store') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Nama Lengkap -->
                <div>
                    <label for="nama_user" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                    <input type="text" id="nama_user" name="nama_user" value="{{ old('nama_user') }}" required
                           class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800"
                           placeholder="Contoh: Budi Santoso">
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                           class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800"
                           placeholder="Untuk login, contoh: budi123">
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role Akses</label>
                    <select id="role" name="role" required
                            class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800 dark:bg-gray-900">
                        <option value="" disabled selected>Pilih Role...</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="apoteker" {{ old('role') == 'apoteker' ? 'selected' : '' }}>Apoteker</option>
                    </select>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input type="password" id="password" name="password" required minlength="8"
                           class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800"
                           placeholder="Minimal 8 karakter">
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="reset" class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
                    Reset
                </button>
                <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
