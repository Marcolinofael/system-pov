{{-- Foto do beneficiário ou, sem foto, a inicial do nome: @include('partials.avatar', ['b' => $b, 'tamanho' => 40]) --}}
@php($tamanho = $tamanho ?? 40)
@if ($b->foto)
    <img src="{{ $b->foto_url }}" alt="Foto de {{ $b->nome_exibicao }}" loading="lazy"
         class="pov-avatar {{ $classe ?? '' }}" style="width: {{ $tamanho }}px; height: {{ $tamanho }}px">
@else
    <span class="pov-avatar pov-avatar-inicial {{ $classe ?? '' }}"
          style="width: {{ $tamanho }}px; height: {{ $tamanho }}px; font-size: {{ round($tamanho * 0.42) }}px">
        {{ mb_strtoupper(mb_substr($b->nome_exibicao ?: '?', 0, 1)) }}
    </span>
@endif
