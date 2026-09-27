<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use App\Models\Familiar;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $ativos = Beneficiario::where('ativo', true);

        // Quantos cadastros ativos precisam de cada tipo de ajuda
        $necessidades = collect(Beneficiario::NECESSIDADES)
            ->map(fn ($rotulo, $chave) => [
                'chave' => $chave,
                'rotulo' => $rotulo,
                'total' => (clone $ativos)->whereJsonContains('necessidades', $chave)->count(),
            ])
            ->filter(fn ($n) => $n['total'] > 0)
            ->sortByDesc('total')
            ->values();

        return view('dashboard', [
            'totalAtivos' => (clone $ativos)->count(),
            'pessoasAtendidas' => (clone $ativos)->count()
                + Familiar::whereHas('beneficiario', fn ($q) => $q->where('ativo', true))->count(),
            'novosNoMes' => Beneficiario::where('created_at', '>=', now()->startOfMonth())->count(),
            'semConsentimento' => (clone $ativos)->where('consentimento_lgpd', false)->count(),
            'necessidades' => $necessidades,
            'ultimos' => Beneficiario::withCount('familiares')->latest()->limit(6)->get(),
        ]);
    }
}
