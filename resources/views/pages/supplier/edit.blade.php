@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Edit Data Supplier</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui informasi pemasok {{ $supplier->nama_supplier }}.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('supplier.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
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
        <form action="{{ route('supplier.update', $supplier->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Nama Supplier -->
                <div class="sm:col-span-2">
                    <label for="nama_supplier" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Supplier</label>
                    <input type="text" id="nama_supplier" name="nama_supplier" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required
                           class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800">
                </div>

                <!-- Kontak -->
                <div class="sm:col-span-2">
                    <label for="kontak" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor WhatsApp / Telepon</label>
                    <input type="text" id="kontak" name="kontak" value="{{ old('kontak', $supplier->kontak) }}" required
                           class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800">
                </div>

                <!-- Alamat -->
                <div class="sm:col-span-2">
                    <label for="alamat" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Lengkap</label>
                    <textarea id="alamat" name="alamat" rows="3" required
                              class="w-full rounded-lg border border-gray-200 bg-transparent px-4 py-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-800 dark:text-white/90 dark:focus:border-brand-800">{{ old('alamat', $supplier->alamat) }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('supplier.index') }}" class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-800/80">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">
                    Perbarui Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
