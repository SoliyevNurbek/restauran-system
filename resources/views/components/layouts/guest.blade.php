<!doctype html>
<html lang="uz" class="h-full" x-data="{}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Restoran boshqaruvi' }}</title>
    <link rel="icon" type="image/png" href="{{ !empty($appSetting?->favicon_path) ? asset('storage/'.$appSetting->favicon_path) : (!empty($appSetting?->logo_path) ? asset('storage/'.$appSetting->logo_path) : asset('Javohirlogo.png')) }}">
    <link rel="shortcut icon" href="{{ !empty($appSetting?->favicon_path) ? asset('storage/'.$appSetting->favicon_path) : (!empty($appSetting?->logo_path) ? asset('storage/'.$appSetting->logo_path) : asset('Javohirlogo.png')) }}">
    <script>
        (() => {
            const theme = localStorage.getItem('theme') || '{{ $appSetting->theme_preference ?? 'light' }}';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f4faf6',
                            100: '#e6f4eb',
                            200: '#cce9d6',
                            300: '#a8d7b8',
                            400: '#7fbe95',
                            500: '#4f9f6c',
                            600: '#3f8458',
                            700: '#336948',
                            800: '#2c543c',
                            900: '#254533'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-primary-50 text-slate-800 dark:bg-slate-950 dark:text-slate-100">
    {{ $slot }}
</body>
</html>

