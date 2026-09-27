<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renomeia (em vez de recriar) para preservar cadastros já existentes
        Schema::rename('pessoas', 'beneficiarios');

        Schema::table('beneficiarios', function (Blueprint $table) {
            // Identificação
            $table->string('nome_social', 150)->nullable()->after('nome');
            $table->string('rg', 20)->nullable()->after('cpf');
            $table->char('nis', 11)->nullable()->unique()->after('rg');
            $table->string('sexo', 20)->nullable()->after('data_nascimento');
            $table->string('estado_civil', 20)->nullable()->after('sexo');
            $table->string('cor_raca', 20)->nullable()->after('estado_civil');
            $table->string('escolaridade', 40)->nullable()->after('cor_raca');

            // Contato e endereço
            $table->string('telefone_recado', 20)->nullable()->after('telefone');
            $table->string('complemento', 60)->nullable()->after('numero');
            $table->string('ponto_referencia', 150)->nullable()->after('uf');

            // Situação socioeconômica
            $table->string('situacao_moradia', 30)->nullable();
            $table->string('situacao_trabalho', 30)->nullable();
            $table->decimal('renda_familiar', 10, 2)->nullable();
            $table->json('beneficios')->nullable();
            $table->boolean('possui_deficiencia')->default(false);
            $table->text('saude')->nullable();

            // Atendimento
            $table->json('necessidades')->nullable();
            $table->date('data_cadastro')->nullable();
            $table->boolean('consentimento_lgpd')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::create('familiares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiario_id')->constrained()->cascadeOnDelete();
            $table->string('nome', 150);
            $table->string('parentesco', 30);
            $table->date('data_nascimento')->nullable();
            $table->decimal('renda', 10, 2)->nullable();
            $table->string('observacao', 150)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('familiares');

        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropUnique(['nis']);
            $table->dropColumn([
                'nome_social', 'rg', 'nis', 'sexo', 'estado_civil', 'cor_raca', 'escolaridade',
                'telefone_recado', 'complemento', 'ponto_referencia',
                'situacao_moradia', 'situacao_trabalho', 'renda_familiar', 'beneficios',
                'possui_deficiencia', 'saude', 'necessidades', 'data_cadastro', 'consentimento_lgpd',
            ]);
        });

        Schema::rename('beneficiarios', 'pessoas');
    }
};
