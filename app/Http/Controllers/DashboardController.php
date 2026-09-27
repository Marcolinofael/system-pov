<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'totalPessoas' => Pessoa::count(),
            'pessoasAtivas' => Pessoa::where('ativo', true)->count(),
            'novasNoMes' => Pessoa::where('created_at', '>=', now()->startOfMonth())->count(),
            'totalUsuarios' => User::count(),
            'ultimasPessoas' => Pessoa::latest()->limit(5)->get(),
        ]);
    }
}
