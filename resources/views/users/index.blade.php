@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola data pengguna, hak akses, dan kredensial akun.</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" 
     x-data="{ showModal: false, isEdit: false, editData: {}, searchQuery: '', roleFilter: '' }">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div class="flex gap-4 items-center w-full max-w-xl">
            <div class="relative flex-1">
                <input type="text" x-model="searchQuery" class="block w-full pl-3 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" placeholder="Cari Nama atau Email...">
            </div>
            <select x-model="roleFilter" class="block w-48 py-2 px-3 border border-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm bg-white">
                <option value="">Semua Role</option>
                <option value="super_admin">Admin</option>
                <option value="hakim">Hakim</option>
                <option value="masyarakat">Masyarakat</option>
            </select>
        </div>
        @can('manage-users')
        <button @click="showModal = true; isEdit = false; editData = { role: 'masyarakat' }" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm shadow-emerald-200 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Tambah User
        </button>
        @endcan
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-500">
            <thead class="bg-white border-b border-gray-100 text-gray-700">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-600">Nama Lengkap</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Email</th>
                    <th class="px-6 py-4 font-semibold text-gray-600">Role</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($users as $user)
                <tr class="hover:bg-emerald-50/20 transition-colors duration-150"
                    x-show="(searchQuery === '' || '{{ strtolower($user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($user->email) }}'.includes(searchQuery.toLowerCase())) && 
                            (roleFilter === '' || '{{ $user->role }}' === roleFilter)">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $user->role === 'super_admin' ? 'bg-purple-50 text-purple-700 border-purple-200' : ($user->role === 'hakim' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200') }}">
                            {{ $user->role === 'super_admin' ? 'Admin' : ($user->role === 'hakim' ? 'Hakim' : 'Masyarakat') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @can('manage-users')
                        <button type="button" @click="showModal = true; isEdit = true; editData = {{ json_encode($user) }}" class="text-blue-600 hover:text-blue-900 text-sm font-semibold transition-colors">Edit</button>
                        <form action="/users/{{ $user->id }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-semibold transition-colors" {{ $user->id === auth()->id() ? 'disabled' : '' }} class="{{ $user->id === auth()->id() ? 'opacity-50 cursor-not-allowed' : '' }}">Hapus</button>
                        </form>
                        @else
                        <span class="text-gray-400 text-sm">-</span>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Tidak ada data pengguna ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Form Tambah/Edit User -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/50" 
         style="display: none;">
        
        <div class="relative w-full max-w-xl bg-white rounded-xl shadow-2xl p-6 overflow-hidden my-auto border border-gray-100 max-h-[90vh] overflow-y-auto text-left" @click.away="showModal = false">
            <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Edit Pengguna' : 'Tambah Pengguna'"></h3>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            
            <!-- Body Form CRUD -->
            <div>
                <form :action="isEdit ? '/users/' + editData.id : '{{ route('users.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        @method('PUT')
                    </template>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" :value="isEdit ? editData.name : ''" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Email</label>
                            <input type="email" name="email" :value="isEdit ? editData.email : ''" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Role</label>
                            <select name="role" x-model="editData.role" required class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm bg-white">
                                <option value="masyarakat">Masyarakat</option>
                                <option value="hakim">Hakim</option>
                                <option value="super_admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">
                                Password <span x-show="isEdit" class="text-gray-400 font-normal">(Kosongkan jika tidak ingin mengubah)</span>
                            </label>
                            <input type="password" name="password" :required="!isEdit" class="mt-1 block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 flex flex-row-reverse border-t border-gray-100 pt-4 gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:w-auto">
                            Simpan Data
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
