<div class="relative p-6 m-6 bg-white rounded-lg shadow-lg border border-gray-200">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg flex items-center" role="alert">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg flex items-center" role="alert">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Leaderboard Title -->
    <div class="flex justify-between items-center mb-6 pb-3 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">
            <svg class="w-7 h-7 inline-block mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Leaderboard
        </h1>
        <button wire:click="showAddUserForm" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 shadow-sm flex items-center cursor-pointer">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add User
        </button>
    </div>

    <!-- Leaderboard Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16"></th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Adjust Points</th>
                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
            @forelse ($users as $index => $user)
                <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                    <!-- Delete Button -->
                    <td class="py-3 pl-4">
                        <button
                            wire:click="deleteUser({{ $user['id'] }})"
                            wire:confirm.prompt="Are you sure you want to delete {{ $user['name'] }}?\n\nType DELETE to confirm|DELETE"
                            class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-full hover:bg-red-100 hover:text-red-600 hover:border-red-300 transition-colors duration-200 cursor-pointer" title="Remove User">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </td>

                    <!-- User Name (clickable) -->
                    <td class="py-3 pl-2 pr-6">
                        <div class="flex items-center cursor-pointer" wire:click="showUserDetails({{ $user['id'] }})">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3">
                                <span class="font-bold">{{ strtoupper(substr($user['name'], 0, 1)) }}</span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 hover:text-blue-600">{{ $user['name'] }}</div>
                                <div class="text-sm text-gray-500">Tap to view details</div>
                            </div>
                        </div>
                    </td>

                    <!-- Points Controls -->
                    <td class="py-3 px-2 text-center">
                        <div class="flex items-center justify-center space-x-3">
                            <button wire:click="subtractPoints({{ $user['id'] }})" class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-full hover:bg-gray-100 transition-colors duration-200 cursor-pointer" title="Subtract Point">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>

                            <button wire:click="addPoints({{ $user['id'] }})" class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-full hover:bg-gray-100 transition-colors duration-200 cursor-pointer" title="Add Point">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                    </td>

                    <!-- Points -->
                    <td class="py-3 pl-6 text-right pr-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user['score'] > 5 ? 'bg-green-100 text-green-800' : ($user['score'] > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $user['score'] }} points
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-8 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-500">
                            <svg class="w-12 h-12 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="text-lg font-medium mb-1">No users found</p>
                            <p class="text-sm">Add a user to get started!</p>
                            <button wire:click="showAddUserForm" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 cursor-pointer">
                                Add Your First User
                            </button>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- User Details Modal -->
    @if ($showUserModal && $selectedUser)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4 relative"
                 x-on:keydown.escape.window="$wire.closeUserModal()">
                <div class="absolute top-2 right-2">
                    <button wire:click="closeUserModal" class="h-8 w-8 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="text-center mb-5">
                    <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-blue-100 text-blue-600 text-2xl font-bold mb-3">
                        {{ strtoupper(substr($selectedUser['name'], 0, 1)) }}
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $selectedUser['name'] }}</h2>
                </div>

                <div class="space-y-4 bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Age</span>
                        <span class="text-gray-900 font-medium">{{ $selectedUser['age'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Points</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $selectedUser['score'] > 5 ? 'bg-green-100 text-green-800' : ($selectedUser['score'] > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ $selectedUser['score'] }}
                        </span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500 block mb-1">Address</span>
                        <span class="text-gray-900 break-words block">{{ $selectedUser['address'] }}</span>
                    </div>
                </div>

                <div class="mt-6">
                    <button wire:click="closeUserModal" class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Add User Modal -->
    @if ($showAddUserModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4"
                 x-on:keydown.escape.window="$wire.closeAddUserModal()">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">Add New User</h2>
                    <button wire:click="closeAddUserModal" class="h-8 w-8 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="addUser">
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="name" wire:model="name" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                            <input type="number" id="age" wire:model="age" min="1" max="100" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('age') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea id="address" wire:model="address" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                            @error('address') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button type="button" wire:click="closeAddUserModal" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer">
                            Add User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
