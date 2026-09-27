<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeneficiarioRequest;
use App\Models\Atendimento;
use App\Models\AtendimentoFoto;
use App\Models\Beneficiario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BeneficiarioController extends Controller
{
    public function index(Request $request): View
    {
        $beneficiarios = Beneficiario::busca($request->query('q'))
            ->when($request->filled('status'), fn ($q) => $q->where('ativo', $request->query('status') === 'ativo'))
            ->when($request->filled('necessidade'), fn ($q) => $q->whereJsonContains('necessidades', $request->query('necessidade')))
            ->withCount('familiares')
            ->withMax('atendimentos', 'data')
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

        $this->salvarFoto($request, $beneficiario);

        return redirect()->route('beneficiarios.show', $beneficiario)
            ->with('success', 'Beneficiário cadastrado com sucesso.');
    }

    public function show(Beneficiario $beneficiario): View
    {
        $beneficiario->load('familiares', 'cadastradoPor', 'atendimentos.responsavel', 'atendimentos.fotos');

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

        $this->salvarFoto($request, $beneficiario);

        return redirect()->route('beneficiarios.show', $beneficiario)
            ->with('success', 'Cadastro atualizado com sucesso.');
    }

    public function destroy(Beneficiario $beneficiario): RedirectResponse
    {
        // Arquivos não são apagados pelo cascade do banco: junta tudo antes de excluir
        $arquivos = AtendimentoFoto::whereIn('atendimento_id', Atendimento::where('beneficiario_id', $beneficiario->id)->select('id'))
            ->pluck('caminho')
            ->push($beneficiario->foto)
            ->filter()
            ->all();

        $beneficiario->delete();
        Storage::disk('local')->delete($arquivos);

        return redirect()->route('beneficiarios.index')
            ->with('success', 'Cadastro excluído com sucesso.');
    }

    /** Exibe a foto (disco privado: só usuários logados têm acesso). */
    public function foto(Beneficiario $beneficiario): StreamedResponse
    {
        $disco = Storage::disk('local');

        abort_unless($beneficiario->foto && $disco->exists($beneficiario->foto), 404);

        return $disco->response($beneficiario->foto, null, ['Cache-Control' => 'private, max-age=604800']);
    }

    private function salvarFoto(BeneficiarioRequest $request, Beneficiario $beneficiario): void
    {
        $antiga = $beneficiario->foto;

        if ($request->hasFile('foto')) {
            $beneficiario->foto = $request->file('foto')->store('beneficiarios', 'local');
        } elseif ($request->boolean('remover_foto')) {
            $beneficiario->foto = null;
        } else {
            return;
        }

        $beneficiario->save();

        if ($antiga && $antiga !== $beneficiario->foto) {
            Storage::disk('local')->delete($antiga);
        }
    }

    private function dados(BeneficiarioRequest $request): array
    {
        $dados = Arr::except($request->validated(), ['familiares', 'foto', 'remover_foto']);
        $dados['data_cadastro'] ??= today();

        return $dados;
    }
}
