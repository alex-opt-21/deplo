<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexes('usuarios', [
            ['columns' => ['provider', 'provider_id'], 'name' => 'usuarios_provider_provider_id_index'],
            ['columns' => ['nombre'], 'name' => 'usuarios_nombre_index'],
            ['columns' => ['apellido'], 'name' => 'usuarios_apellido_index'],
            ['columns' => ['ubicacion'], 'name' => 'usuarios_ubicacion_index'],
        ]);

        $this->addIndexes('habilidades', [
            ['columns' => ['usuario_id'], 'name' => 'habilidades_usuario_id_index'],
            ['columns' => ['usuario_id', 'tipo'], 'name' => 'habilidades_usuario_tipo_index'],
            ['columns' => ['nombre'], 'name' => 'habilidades_nombre_index'],
        ]);

        $this->addIndexes('experience', [
            ['columns' => ['usuario_id'], 'name' => 'experience_usuario_id_index'],
            ['columns' => ['usuario_id', 'tipo'], 'name' => 'experience_usuario_tipo_index'],
            ['columns' => ['company'], 'name' => 'experience_company_index'],
        ]);

        $this->addIndexes('proyectos', [
            ['columns' => ['usuario_id'], 'name' => 'proyectos_usuario_id_index'],
            ['columns' => ['estado'], 'name' => 'proyectos_estado_index'],
            ['columns' => ['titulo'], 'name' => 'proyectos_titulo_index'],
        ]);

        $this->addIndexes('social', [
            ['columns' => ['usuario_id'], 'name' => 'social_usuario_id_index'],
            ['columns' => ['usuario_id', 'nombre_plataforma'], 'name' => 'social_usuario_plataforma_index'],
        ]);

        $this->addIndexes('formacion_academica', [
            ['columns' => ['usuario_id'], 'name' => 'formacion_academica_usuario_id_index'],
            ['columns' => ['institucion'], 'name' => 'formacion_academica_institucion_index'],
            ['columns' => ['nombre_carrera'], 'name' => 'formacion_academica_nombre_carrera_index'],
            ['columns' => ['tipo_formacion'], 'name' => 'formacion_academica_tipo_formacion_index'],
        ]);
    }

    public function down(): void
    {
        $this->dropIndexes('usuarios', [
            'usuarios_provider_provider_id_index',
            'usuarios_nombre_index',
            'usuarios_apellido_index',
            'usuarios_ubicacion_index',
        ]);

        $this->dropIndexes('habilidades', [
            'habilidades_usuario_id_index',
            'habilidades_usuario_tipo_index',
            'habilidades_nombre_index',
        ]);

        $this->dropIndexes('experience', [
            'experience_usuario_id_index',
            'experience_usuario_tipo_index',
            'experience_company_index',
        ]);

        $this->dropIndexes('proyectos', [
            'proyectos_usuario_id_index',
            'proyectos_estado_index',
            'proyectos_titulo_index',
        ]);

        $this->dropIndexes('social', [
            'social_usuario_id_index',
            'social_usuario_plataforma_index',
        ]);

        $this->dropIndexes('formacion_academica', [
            'formacion_academica_usuario_id_index',
            'formacion_academica_institucion_index',
            'formacion_academica_nombre_carrera_index',
            'formacion_academica_tipo_formacion_index',
        ]);
    }

    private function addIndexes(string $table, array $indexes): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        foreach ($indexes as $index) {
            if (! $this->tableHasColumns($table, $index['columns']) || Schema::hasIndex($table, $index['name'])) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($index) {
                $blueprint->index($index['columns'], $index['name']);
            });
        }
    }

    private function dropIndexes(string $table, array $indexes): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        foreach ($indexes as $indexName) {
            if (! Schema::hasIndex($table, $indexName)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($indexName) {
                $blueprint->dropIndex($indexName);
            });
        }
    }

    private function tableHasColumns(string $table, array $columns): bool
    {
        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }
};
