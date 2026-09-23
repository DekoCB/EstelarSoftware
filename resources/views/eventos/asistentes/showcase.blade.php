<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $evento->nombre }} — Estelar Eventos</title>

    @php $config = \App\Models\EmpresaConfig::config(); @endphp
    @if($config->logo_sidebar)
        <link rel="icon" href="{{ $config->logoSidebarUrl() }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;800;900&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/go-left-showcase-mount.jsx'])
</head>
<body class="bg-black">

    <div id="glsRoot"
        data-nombre="{{ $evento->nombre }}"
        data-fecha="{{ optional($evento->fecha_inicio)->format('d/m/Y') ?? '' }}"
        data-descripcion="{{ $evento->descripcion ?? '' }}"
        data-banner="{{ asset('img/Banner28.jpeg') }}"
        data-ticket-image="{{ asset('img/Go Left Estelar.jpeg') }}"
        data-video="{{ asset('video/VideoLeft.mp4') }}"
        data-bases="{{ asset('docs/Bases-GoLeft.pdf') }}"
        data-action="{{ route('eventos.inscripcion.store', $evento) }}"
        data-csrf="{{ csrf_token() }}"
    ></div>

</body>
</html>
