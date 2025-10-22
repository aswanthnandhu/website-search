<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Website Search Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <i class="fas fa-search text-blue-600 text-xl mr-2"></i>
                    <span class="text-xl font-bold text-gray-800">Admin Panel</span>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('admin.search.logs') }}" 
                       class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.search.logs') ? 'bg-blue-100 text-blue-600' : '' }}">
                        <i class="fas fa-list mr-1"></i>
                        Search Logs
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="flex items-center text-gray-700 hover:text-blue-600 focus:outline-none" onclick="toggleDropdown()">
                            <i class="fas fa-user-circle text-xl mr-2"></i>
                            <span class="text-sm font-medium">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>
                                Profile
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>
                                Settings
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="{{ route('admin.logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-8">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} Website Search Admin. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('button');
            
            if (!button || !button.onclick) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>

