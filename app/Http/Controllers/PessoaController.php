<?php

namespace App\Http\Controllers;

use App\Http\Requests\PessoaRequest;
use App\Models\Pessoa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PessoaController extends Controller
{
    public function index(Request $request): View
    {
        $pessoas = Pessoa::busca($request->query('q'))
            ->when($request->filled('status'), fn ($q) => $q->where('ativo', $request->query('status') === 'ativo'))
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();

        return view('pessoas.index', compact('pessoas'));
    }

    public function create(): View
    {
        return view('pessoas.create', ['pessoa' => new Pessoa(['ativo' => true])]);
    }

    public function store(PessoaRequest $request): RedirectResponse
    {
        $pessoa = Pessoa::create($request->validated());

        return redirect()->route('pessoas.show', $pessoa)
            ->with('success', 'Pessoa cadastrada com sucesso.');
    }

    public function show(Pessoa $pessoa): View
    {
        return view('pessoas.show', compact('pessoa'));
    }

    public function edit(Pessoa $pessoa): View
    {
        return view('pessoas.edit', compact('pessoa'));
    }

    public function update(PessoaRequest $request, Pessoa $pessoa): RedirectResponse
    {
        $pessoa->update($request->validated());

        return redirect()->route('pessoas.show', $pessoa)
            ->with('success', 'Pessoa atualizada com sucesso.');
    }

    public function destroy(Pessoa $pessoa): RedirectResponse
    {
        $pessoa->delete();

        return redirect()->route('pessoas.index')
            ->with('success', 'Pessoa excluída com sucesso.');
    }
}
