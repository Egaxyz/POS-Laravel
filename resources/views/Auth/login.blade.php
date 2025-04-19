<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body
    class="h-screen flex items-center justify-center bg-gradient-to-r from-blue-200 via-indigo-300 to-green-200 relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 bg-gray-50 bg-opacity-30 backdrop-blur-sm"
        style="background-image: url('https://www.transparenttextures.com/patterns/diagonal-stripes-light.png');"></div>

    <div class="relative z-10 bg-white bg-opacity-90 p-8 rounded-lg shadow-xl w-96">
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-4">Login</h2>

        @if (session('error'))
            <p class="text-red-500 text-sm text-center">{{ session('error') }}</p>
        @endif

        @if ($errors->any())
            <div class="text-sm text-red-600 bg-red-100 p-2 rounded mb-3">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700" for="nama">Nama:</label>
                <input type="text" name="nama" id="nama" required autocomplete="off"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="password">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-gray-50">
                    <button type="button" onclick="togglePassword()"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-600">
                        <svg id="eyeIcon" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-white hover:text-blue-600 border hover:border-blue-600 transition-all shadow-md">
                Login
            </button>
        </form>
    </div>

    <script>
        function togglePassword() {
            let password = document.getElementById("password");
            let eyeIcon = document.getElementById("eyeIcon");

            if (password.type === "password") {
                password.type = "text";
                eyeIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3-5 10-5 10 5 10 5M2 12s3 5 10 5 10-5 10-5"/>`;
            } else {
                password.type = "password";
                eyeIcon.innerHTML =
                    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }
    </script>
</body>

</html>
