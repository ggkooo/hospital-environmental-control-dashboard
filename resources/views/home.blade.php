<?php
use Carbon\Carbon;
$locale = app()->getLocale();
Carbon::setLocale($locale);

switch ($locale) {
    case 'en': // US
        $formato = 'm/d/y';
        break;
    case 'fr': // França
    case 'de': // Alemanha
    case 'it': // Itália
    case 'es': // Espanha
    case 'pt-br': // Brasil
    case 'pl': // Polônia
    case 'nl': // Holanda
    case 'sv': // Suécia
    case 'tr': // Turquia
    case 'ru': // Rússia
    case 'uk': // Ucrânia
        $formato = 'd/m/y';
        break;
    case 'ja': // Japão
    case 'zh': // China
    case 'ko': // Coreia
        $formato = 'y/m/d';
        break;
    case 'ar': // Árabe
        $formato = 'd/m/y'; // Padrão comum
        break;
    case 'hi': // Índia
        $formato = 'd-m-y'; // Padrão comum
        break;
    default:
        $formato = 'd/m/y'; // fallback
}

$inicio = Carbon::now()->subDays(6)->translatedFormat($formato);
$fim = Carbon::now()->translatedFormat($formato);
?>

<link rel="stylesheet" href="{{ asset('css/pages/home.css') }}">

<div class="container d-flex flex-column justify-content-center" style="min-height: calc(100vh - 20vh);">
    <h1 class="text-center mb-4">{{ __('home.title') }}</h1>
    <p class="lead text-center text-dark">{{ __('home.text') }}</p>
    <div class="text-center mt-3">
        <a href="/docs" class="btn btn-outline-primary">{{ __('home.documentation') }}</a>
    </div>
    <div class="row justify-content-center mt-5">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm card-info">
                <div class="card-body text-center d-flex flex-column justify-content-between align-items-center" style="height:100%">
                    <h5 class="card-title">{{ __('home.temperature') }}</h5>
                    <div class="w-100 d-flex flex-column align-items-center" style="margin-top:auto;">
                        <p class="display-4 text-primary mb-4">23°C</p>
                        <small class="text-muted">{{ __('home.days') }} (<?php echo $inicio; ?> - <?php echo $fim; ?>)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm card-info">
                <div class="card-body text-center d-flex flex-column justify-content-between align-items-center" style="height:100%">
                    <h5 class="card-title">{{ __('home.humidity') }}</h5>
                    <div class="w-100 d-flex flex-column align-items-center" style="margin-top:auto;">
                        <p class="display-4 text-info mb-2">60%</p>
                        <small class="text-muted">{{ __('home.days') }} (<?php echo $inicio; ?> - <?php echo $fim; ?>)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm card-info">
                <div class="card-body text-center d-flex flex-column justify-content-between align-items-center" style="height:100%">
                    <h5 class="card-title">{{ __('home.noise') }}</h5>
                    <div class="w-100 d-flex flex-column align-items-center" style="margin-top:auto;">
                        <p class="display-4 text-warning mb-4">40dB</p>
                        <small class="text-muted">{{ __('home.days') }} (<?php echo $inicio; ?> - <?php echo $fim; ?>)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
