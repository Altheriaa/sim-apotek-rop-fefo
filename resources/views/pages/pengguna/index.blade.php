@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Data Pengguna</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Manajemen akses aplikasi (Admin & Apoteker).</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('pengguna.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Tambah Pengguna
            </a>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="rounded-lg bg-success-50 p-4 text-success-800 border border-success-200 dark:bg-success-900/20 dark:text-success-400 dark:border-success-800/30">
        {{ session('success') }}
    </div>
    @endif

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[800px] text-left">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Nama Pengguna</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Username</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Role</th>
                        <th class="px-5 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penggunas as $user)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/20">
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $user->nama_user }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->username }}
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-brand-100 text-brand-700 dark:bg-brand-900/30 dark:text-brand-400' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('pengguna.edit', $user->id) }}" class="text-brand-500 hover:text-brand-600 transition">Edit</a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('pengguna.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-error-500 hover:text-error-600 transition">Hapus</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($penggunas->hasPages())
        <div class="border-t border-gray-100 p-4 dark:border-gray-800">
            {{ $penggunas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
