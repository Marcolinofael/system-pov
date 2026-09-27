@csrf

@php($proprioUsuario = $user->exists && $user->is(auth()->user()))

<div class="card card-primary card-outline">
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="name">Nome *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                       class="form-control @error('name') is-invalid @enderror" required maxlength="150">
                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                       class="form-control @error('email') is-invalid @enderror" required>
                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="role">Perfil *</label>
                <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" @disabled($proprioUsuario)>
                    @foreach (\App\Models\User::ROLES as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected(old('role', $user->role) === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
                @if ($proprioUsuario)
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <small class="form-text text-muted">Você não pode alterar o próprio perfil.</small>
                @endif
                @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="password">Senha {{ $user->exists ? '' : '*' }}</label>
                <input type="password" id="password" name="password" autocomplete="new-password"
                       class="form-control @error('password') is-invalid @enderror" @required(! $user->exists)>
                @if ($user->exists)
                    <small class="form-text text-muted">Deixe em branco para manter a senha atual.</small>
                @endif
                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="password_confirmation">Confirmar senha</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       autocomplete="new-password" class="form-control">
            </div>
        </div>

        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" value="1"
                   @checked(old('ativo', $user->ativo)) @disabled($proprioUsuario)>
            <label class="custom-control-label" for="ativo">Usuário ativo (pode acessar o sistema)</label>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        <a href="{{ route('users.index') }}" class="btn btn-default">Cancelar</a>
    </div>
</div>
