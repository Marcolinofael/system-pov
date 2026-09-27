<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $termo = $request->query('q');

        $users = User::when($termo, fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$termo}%")
                ->orWhere('email', 'like', "%{$termo}%")))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create', ['user' => new User(['role' => 'operador', 'ativo' => true])]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Usuário cadastrado com sucesso.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $dados = $request->validated();

        if (blank($dados['password'] ?? null)) {
            unset($dados['password']);
        }

        // Impede que o administrador tire o próprio acesso
        if ($user->is($request->user())) {
            $dados['role'] = $user->role;
            $dados['ativo'] = true;
        }

        $user->update($dados);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Você não pode excluir o seu próprio usuário.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}
