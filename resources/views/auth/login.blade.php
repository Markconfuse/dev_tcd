<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCD - Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="min-h-screen bg-gray-950 flex"> 
<!-- LEFT SIDE (Brand Section) -->
<div class="hidden lg:flex w-1/2 relative overflow-hidden p-16 flex-col justify-between"
     style="background: linear-gradient(to left, rgba(10, 50, 30, 0.95), #0f1118); color: #a7f3d0;">

        <!-- Abstract blurred shapes -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-green-500/20 rounded-full blur-3xl"></div>

        <div class="relative z-10">
            <h1 class="text-5xl font-extrabold tracking-tight" style="color: #95A5A6;">
                TCD Portal
            </h1>
            <p class="mt-6 text-lg max-w-md leading-relaxed">
               A smarter way to manage your Solution Consulting tickets.
            </p>
            <img src="img/ics48white.png" style="height: 135px; width: 560px;" />
        </div>

        <div class="relative z-10 text-sm">
            © {{ date('Y') }} ICS - TCD Portal. All rights reserved.
        </div>
    </div>

    <!-- RIGHT SIDE (Login Section) -->
    <div class="flex flex-1 items-center justify-center bg-gray-100 relative">

        <div class="w-full max-w-md p-10 bg-white rounded-2xl shadow-2xl animate-fadeIn">

            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-gray-800">
                    Welcome Back
                </h2>
                <p class="text-gray-500 mt-2">
                    Sign in with your ICS Gmail account
                </p>
            </div>

            <!-- Google Button -->
            <button id="gauth"
                class="w-full flex items-center justify-center gap-3 bg-gray-900 hover:bg-black text-white font-semibold py-4 rounded-xl transition duration-300 shadow-lg hover:shadow-xl">

                <svg width="22" height="22" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.6 0 6.8 1.2 9.3 3.5l6.9-6.9C36.5 2.1 30.6 0 24 0 14.6 0 6.5 5.4 2.6 13.3l8 6.2C12.5 13 17.7 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.1 24.5c0-1.6-.1-3.1-.4-4.5H24v8.6h12.4c-.5 2.7-2 5-4.2 6.6l6.5 5.1c3.8-3.5 7.4-9 7.4-15.8z"/>
                    <path fill="#FBBC05" d="M10.6 28.9c-1-3 0-6.2 0-6.2l-8-6.2C.9 19.5 0 21.7 0 24c0 2.3.9 4.5 2.6 7.5l8-6.2z"/>
                    <path fill="#34A853" d="M24 48c6.6 0 12.1-2.2 16.1-6l-6.5-5.1c-2 1.3-4.6 2.1-9.6 2.1-6.3 0-11.5-3.5-13.4-8.4l-8 6.2C6.5 42.6 14.6 48 24 48z"/>
                </svg>

                Continue with Google
            </button>

            <!-- Loader -->
            <div id="loader" class="hidden mt-6 text-center">
                <div class="flex justify-center items-center gap-2">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-bounce"></div>
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-bounce delay-150"></div>
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-bounce delay-300"></div>
                </div>
                <p class="text-sm text-gray-500 mt-3">Redirecting securely...</p>
            </div>

        </div>
    </div>

<script>
    @if(Session::has('message'))
        Swal.fire({
            icon: '{{ Session::get('status') }}',
            title: '{{ Session::get('message') }}',
            showConfirmButton: false,
            timer: 2000
        });
    @endif

    document.getElementById('gauth').addEventListener('click', function () {
        document.getElementById('loader').classList.remove('hidden');
        window.location.href = "{{ route('googleRedirect') }}";
    });
</script>

</body>
</html>
