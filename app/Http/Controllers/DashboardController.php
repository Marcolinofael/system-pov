<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
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

        // Famílias ativas sem nenhum atendimento nos últimos 60 dias
        $semAtendimento = (clone $ativos)
            ->whereDoesntHave('atendimentos', fn ($q) => $q->where('data', '>=', today()->subDays(60)))
            ->withMax('atendimentos', 'data')
            ->orderByRaw('atendimentos_max_data is not null, atendimentos_max_data')
            ->limit(8)
            ->get();

        return view('dashboard', [
            'totalAtivos' => (clone $ativos)->count(),
            'pessoasAtendidas' => (clone $ativos)->count()
                + Familiar::whereHas('beneficiario', fn ($q) => $q->where('ativo', true))->count(),
            'novosNoMes' => Beneficiario::where('created_at', '>=', now()->startOfMonth())->count(),
            'atendimentosNoMes' => Atendimento::where('data', '>=', today()->startOfMonth())->count(),
            'semConsentimento' => (clone $ativos)->where('consentimento_lgpd', false)->count(),
            'necessidades' => $necessidades,
            'semAtendimento' => $semAtendimento,
            'ultimosAtendimentos' => Atendimento::with('beneficiario')->orderByDesc('data')->orderByDesc('id')->limit(6)->get(),
        ]);
    }
}
