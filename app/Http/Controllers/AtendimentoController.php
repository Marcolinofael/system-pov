<?php

namespace App\Http\Controllers;

use App\Http\Requests\AtendimentoRequest;
use App\Models\Atendimento;
use App\Models\AtendimentoFoto;
use App\Models\Beneficiario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AtendimentoController extends Controller
{
    public function index(Request $request): View
    {
        $filtro = Atendimento::query()
            ->when($request->filled('q'), fn (Builder $q) => $q->whereHas(
                'beneficiario', fn (Builder $b) => $b->busca($request->query('q'))
            ))
            ->when($request->filled('tipo'), fn (Builder $q) => $q->where('tipo', $request->query('tipo')))
            ->when($request->filled('de'), fn (Builder $q) => $q->whereDate('data', '>=', $request->query('de')))
            ->when($request->filled('ate'), fn (Builder $q) => $q->whereDate('data', '<=', $request->query('ate')));

        // Resumo do período filtrado, por tipo
        $resumo = (clone $filtro)
            ->selectRaw('tipo, count(*) as total, count(distinct beneficiario_id) as familias, sum(quantidade) as itens')
            ->groupBy('tipo')
            ->orderByDesc('total')
            ->get();

        $atendimentos = $filtro->with('beneficiario', 'responsavel', 'fotos')
            ->orderByDesc('data')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('atendimentos.index', compact('atendimentos', 'resumo'));
    }

    public function create(Request $request): View
    {
        return view('atendimentos.create', [
            'atendimento' => new Atendimento([
                'data' => today(),
                'beneficiario_id' => $request->query('beneficiario'),
            ]),
            'beneficiarios' => $this->opcoesBeneficiarios(),
        ]);
    }

    public function store(AtendimentoRequest $request): RedirectResponse
    {
        $atendimento = new Atendimento($this->dados($request));
        $atendimento->user_id = $request->user()->id;
        $atendimento->save();
        $this->adicionarFotos($request, $atendimento);

        return $this->voltar($request, $atendimento)->with('success', 'Atendimento registrado.');
    }

    public function edit(Request $request, Atendimento $atendimento): View
    {
        abort_unless($atendimento->podeSerAlteradoPor($request->user()), 403);

        return view('atendimentos.edit', [
            'atendimento' => $atendimento,
            'beneficiarios' => $this->opcoesBeneficiarios($atendimento->beneficiario_id),
        ]);
    }

    public function update(AtendimentoRequest $request, Atendimento $atendimento): RedirectResponse
    {
        abort_unless($atendimento->podeSerAlteradoPor($request->user()), 403);

        $atendimento->update($this->dados($request));

        $remover = $atendimento->fotos()->whereIn('id', $request->validated('remover_fotos', []))->get();
        foreach ($remover as $foto) {
            Storage::disk('local')->delete($foto->caminho);
            $foto->delete();
        }

        $this->adicionarFotos($request, $atendimento);

        return $this->voltar($request, $atendimento)->with('success', 'Atendimento atualizado.');
    }

    public function destroy(Request $request, Atendimento $atendimento): RedirectResponse
    {
        abort_unless($atendimento->podeSerAlteradoPor($request->user()), 403);

        $caminhos = $atendimento->fotos()->pluck('caminho')->all();
        $atendimento->delete();
        Storage::disk('local')->delete($caminhos);

        return $this->voltar($request, $atendimento)->with('success', 'Atendimento excluído.');
    }

    /** Exibe uma foto do atendimento (disco privado: só usuários logados). */
    public function foto(AtendimentoFoto $foto): StreamedResponse
    {
        $disco = Storage::disk('local');

        abort_unless($disco->exists($foto->caminho), 404);

        return $disco->response($foto->caminho, null, ['Cache-Control' => 'private, max-age=604800']);
    }

    private function dados(AtendimentoRequest $request): array
    {
        return Arr::except($request->validated(), ['fotos', 'remover_fotos']);
    }

    private function adicionarFotos(AtendimentoRequest $request, Atendimento $atendimento): void
    {
        foreach ($request->file('fotos', []) as $arquivo) {
            $atendimento->fotos()->create([
                'caminho' => $arquivo->store('atendimentos/'.$atendimento->id, 'local'),
            ]);
        }
    }

    /** Beneficiários ativos (mais o já vinculado, caso tenha sido inativado). */
    private function opcoesBeneficiarios(?int $incluir = null)
    {
        return Beneficiario::where('ativo', true)
            ->when($incluir, fn ($q) => $q->orWhere('id', $incluir))
            ->orderBy('nome')
            ->get(['id', 'nome', 'nome_social', 'cpf', 'bairro']);
    }

    /** Volta para a ficha da família quando o registro foi feito por lá. */
    private function voltar(Request $request, Atendimento $atendimento): RedirectResponse
    {
        if ($request->input('origem') === 'beneficiario') {
            return redirect()->to(route('beneficiarios.show', $atendimento->beneficiario_id).'#atendimentos');
        }

        return redirect()->route('atendimentos.index');
    }
}
