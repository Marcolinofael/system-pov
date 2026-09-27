<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeneficiarioRequest;
use App\Models\Beneficiario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BeneficiarioController extends Controller
{
    public function index(Request $request): View
    {
        $beneficiarios = Beneficiario::busca($request->query('q'))
            ->when($request->filled('status'), fn ($q) => $q->where('ativo', $request->query('status') === 'ativo'))
            ->when($request->filled('necessidade'), fn ($q) => $q->whereJsonContains('necessidades', $request->query('necessidade')))
            ->withCount('familiares')
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();

        return view('beneficiarios.index', compact('beneficiarios'));
    }

    public function create(): View
    {
        return view('beneficiarios.create', [
            'beneficiario' => new Beneficiario(['ativo' => true, 'data_cadastro' => today()]),
        ]);
    }

    public function store(BeneficiarioRequest $request): RedirectResponse
    {
        $beneficiario = DB::transaction(function () use ($request) {
            $beneficiario = new Beneficiario($this->dados($request));
            $beneficiario->user_id = $request->user()->id;
            $beneficiario->save();
            $beneficiario->familiares()->createMany($request->validated('familiares', []));

            return $beneficiario;
        });

        return redirect()->route('beneficiarios.show', $beneficiario)
            ->with('success', 'Beneficiário cadastrado com sucesso.');
    }

    public function show(Beneficiario $beneficiario): View
    {
        $beneficiario->load('familiares', 'cadastradoPor');

        return view('beneficiarios.show', compact('beneficiario'));
    }

    public function edit(Beneficiario $beneficiario): View
    {
        $beneficiario->load('familiares');

        return view('beneficiarios.edit', compact('beneficiario'));
    }

    public function update(BeneficiarioRequest $request, Beneficiario $beneficiario): RedirectResponse
    {
        DB::transaction(function () use ($request, $beneficiario) {
            $beneficiario->update($this->dados($request));

            // A composição familiar é regravada inteira a cada edição
            $beneficiario->familiares()->delete();
            $beneficiario->familiares()->createMany($request->validated('familiares', []));
        });

        return redirect()->route('beneficiarios.show', $beneficiario)
            ->with('success', 'Cadastro atualizado com sucesso.');
    }

    public function destroy(Beneficiario $beneficiario): RedirectResponse
    {
        $beneficiario->delete();

        return redirect()->route('beneficiarios.index')
            ->with('success', 'Cadastro excluído com sucesso.');
    }

    private function dados(BeneficiarioRequest $request): array
    {
        $dados = Arr::except($request->validated(), 'familiares');
        $dados['data_cadastro'] ??= today();

        return $dados;
    }
}
