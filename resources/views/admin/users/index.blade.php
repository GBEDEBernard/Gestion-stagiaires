<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-10 font-serif">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-extrabold text-gray-800 dark:text-white">Gestion des utilisateurs</h1>
        <a href="{{ route('admin.users.create') }}" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 transition">
            + Créer un utilisateur
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 mb-6 rounded shadow">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto shadow-lg rounded-lg">
        <table class="min-w-full bg-white divide-y divide-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle(s)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $user->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">{{ $user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $user->email }}</td>

                    <!-- Status -->
                    <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                        @if($user->status === 'actif')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Actif</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Inactif</span>
                        @endif
                    </td>

                    <!-- Rôles -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @forelse($user->roles as $role)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1">{{ $role->name }}</span>
                        @empty
                            —
                        @endforelse
                    </td>

                    <!-- Permissions -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-wrap gap-1">
                            @forelse($user->getAllPermissions() as $permission)
                                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded cursor-pointer hover:bg-gray-200" title="{{ $permission->name }}">
                                    {{ Str::limit($permission->name, 15) }}
                                </span>
                            @empty
                                —
                            @endforelse
                        </div>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 whitespace-nowrap text-center flex justify-center gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" 
                           class="bg-blue-600 text-white px-3 py-1 rounded shadow hover:bg-blue-700 transition" data-confirm-edit>Modifier</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" data-confirm-delete>
                            @csrf @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded shadow hover:bg-red-700 transition">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-400 italic">Aucun utilisateur trouvé</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
</x-app-layout>
