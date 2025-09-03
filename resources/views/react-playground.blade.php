<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>React Test</title>
    @viteReactRefresh  {{-- ✅ this enables HMR and fixes the preamble error --}}
    @vite('resources/js/app.jsx')
</head>
<body class="flex justify-center items-center min-h-screen bg-gray-100">
    <!-- 👇 React will mount into this div -->
    <div id="react-root"></div>
</body>
</html>
