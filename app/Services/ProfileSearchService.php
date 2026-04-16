<?php

namespace App\Services;

use App\Models\Experience;
use App\Models\FormacionAcademica;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfileSearchService
{
    public function __construct(private readonly PublicAssetUrlService $assetUrlService) {}

    public function searchUsers(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        $words = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);
        $usersQuery = $this->baseUserQuery();

        if (count($words) === 1) {
            $term = $words[0];

            $usersQuery->where(function (Builder $query) use ($term) {
                $query->where('nombre', 'LIKE', "%{$term}%")
                    ->orWhere('apellido', 'LIKE', "%{$term}%")
                    ->orWhereHas('habilidades', function (Builder $skillQuery) use ($term) {
                        $skillQuery->where('nombre', 'LIKE', "%{$term}%");
                    });
            });
        } elseif (count($words) === 2) {
            [$nombre, $apellido] = $words;

            $usersQuery->where('nombre', 'LIKE', "%{$nombre}%")
                ->where('apellido', 'LIKE', "%{$apellido}%");
        } else {
            $nombre = array_shift($words);
            $apellido = implode(' ', $words);

            $usersQuery->where('nombre', 'LIKE', "%{$nombre}%")
                ->where('apellido', 'LIKE', "%{$apellido}%");
        }

        return $this->run($usersQuery);
    }

    public function search(string $query, string $category = 'usuario', string $filter = 'nombre'): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        return match ($category) {
            'usuario' => $this->searchUsuarios($query, $filter),
            'proyecto' => $this->searchUsuariosPorProyecto($query, $filter),
            'habilidad' => $this->searchUsuariosPorHabilidad($query, $filter),
            'experiencia' => $this->searchUsuariosPorExperiencia($query, $filter),
            'profesional' => $this->searchUsuariosPorFormacion($query, $filter),
            default => [],
        };
    }

    public function searchWithFilters(array $payload): array
    {
        $normalized = $this->normalizeFilterPayload($payload);
        $page = max(1, (int) ($payload['page'] ?? 1));
        $perPage = min(50, max(1, (int) ($payload['per_page'] ?? 24)));

        $query = $this->detailedUserQuery();

        $this->applyVisibilityConstraints($query);
        $this->applyKeywordQuery($query, $normalized['q']);
        $this->applyUserFilters($query, $normalized['usuario']);
        $this->applySkillFilters($query, $normalized['habilidades']);
        $this->applyExperienceFilters($query, $normalized['experiencias']);
        $this->applyEducationFilters($query, $normalized['formaciones']);

        $paginator = $query
            ->distinct()
            ->paginate($perPage, ['usuarios.*'], 'page', $page);

        return [
            'data' => [
                'data' => $paginator->getCollection()
                    ->map(fn (Usuario $user) => $this->formatFilteredUser($user))
                    ->values()
                    ->all(),
                'total' => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
            ],
            'filters' => [
                'q' => $normalized['q'],
                'active_category' => $payload['active_category'] ?? null,
                'applied' => array_filter([
                    ...$normalized['usuario'],
                    ...$normalized['habilidades'],
                    ...$normalized['experiencias'],
                    ...$normalized['formaciones'],
                ], static fn ($value) => $value !== ''),
            ],
        ];
    }

    private function searchUsuarios(string $query, string $filter): array
    {
        $usuariosQuery = $this->baseUserQuery();

        switch ($filter) {
            case 'bio':
                $usuariosQuery->where('biografia', 'LIKE', "%{$query}%");
                break;
            case 'ubicacion':
                $usuariosQuery->where('ubicacion', 'LIKE', "%{$query}%");
                break;
            case 'nombre':
            default:
                $words = preg_split('/\s+/', trim($query), -1, PREG_SPLIT_NO_EMPTY);

                if (count($words) === 1) {
                    $term = $words[0];

                    $usuariosQuery->where(function (Builder $builder) use ($term) {
                        $builder->where('nombre', 'LIKE', "%{$term}%")
                            ->orWhere('apellido', 'LIKE', "%{$term}%");
                    });
                } else {
                    $nombre = array_shift($words);
                    $apellido = implode(' ', $words);

                    $usuariosQuery->where('nombre', 'LIKE', "%{$nombre}%")
                        ->where('apellido', 'LIKE', "%{$apellido}%");
                }
                break;
        }

        return $this->run($usuariosQuery);
    }

    private function searchUsuariosPorExperiencia(string $query, string $filter): array
    {
        $usersQuery = $this->baseUserQuery();
        $table = (new Experience())->getTable();

        $usersQuery->whereHas('experiences', function (Builder $experienceQuery) use ($query, $filter, $table) {
            switch ($filter) {
                case 'empresa':
                    $experienceQuery->where(
                        Schema::hasColumn($table, 'empresa') ? 'empresa' : 'company',
                        'LIKE',
                        "%{$query}%"
                    );
                    break;
                case 'descripcion':
                    $experienceQuery->where('descripcion', 'LIKE', "%{$query}%");
                    break;
                case 'cargo':
                case 'laboral':
                default:
                    $experienceQuery->where(
                        Schema::hasColumn($table, 'cargo') ? 'cargo' : 'title',
                        'LIKE',
                        "%{$query}%"
                    );
                    break;
            }
        });

        return $this->run($usersQuery);
    }

    private function searchUsuariosPorHabilidad(string $query, string $filter): array
    {
        $usersQuery = $this->baseUserQuery();

        $usersQuery->whereHas('habilidades', function (Builder $skillQuery) use ($query, $filter) {
            switch ($filter) {
                case 'tecnica':
                    $skillQuery->where('tipo', 'tecnica')
                        ->where('nombre', 'LIKE', "%{$query}%");
                    break;
                case 'blanda':
                    $skillQuery->where('tipo', 'blanda')
                        ->where('nombre', 'LIKE', "%{$query}%");
                    break;
                case 'nombre':
                default:
                    $skillQuery->where('nombre', 'LIKE', "%{$query}%");
                    break;
            }
        });

        return $this->run($usersQuery);
    }

    private function searchUsuariosPorFormacion(string $query, string $filter): array
    {
        $usersQuery = $this->baseUserQuery();
        $table = (new FormacionAcademica())->getTable();

        $usersQuery->whereHas('formacionAcademica', function (Builder $educationQuery) use ($query, $filter, $table) {
            switch ($filter) {
                case 'universidad':
                    $educationQuery->where('institucion', 'LIKE', "%{$query}%");
                    break;
                case 'carrera':
                    $educationQuery->where(
                        Schema::hasColumn($table, 'nombre_programa') ? 'nombre_programa' : 'nombre_carrera',
                        'LIKE',
                        "%{$query}%"
                    );
                    break;
                case 'nivel':
                default:
                    $educationQuery->where(
                        Schema::hasColumn($table, 'nivel_formacion') ? 'nivel_formacion' : 'tipo_formacion',
                        'LIKE',
                        "%{$query}%"
                    );
                    break;
            }
        });

        return $this->run($usersQuery);
    }

    private function searchUsuariosPorProyecto(string $query, string $filter): array
    {
        $usersQuery = $this->baseUserQuery();

        $usersQuery->whereHas('proyectos', function (Builder $projectQuery) use ($query, $filter) {
            switch ($filter) {
                case 'tecnologia':
                    $projectQuery->where('tecnologias', 'LIKE', "%{$query}%");
                    break;
                case 'descripcion':
                    $projectQuery->where('descripcion', 'LIKE', "%{$query}%");
                    break;
                case 'nombre':
                default:
                    $projectQuery->where('titulo', 'LIKE', "%{$query}%");
                    break;
            }
        });

        return $this->run($usersQuery);
    }

    private function baseUserQuery(): Builder
    {
        return Usuario::query()
            ->select(['id', 'nombre', 'apellido', 'foto_perfil', 'biografia', 'ubicacion'])
            ->with([
                'habilidades' => fn ($query) => $query
                    ->select(['id', 'usuario_id', 'nombre'])
                    ->orderBy('nombre'),
            ]);
    }

    private function detailedUserQuery(): Builder
    {
        $experienceTable = (new Experience())->getTable();
        $educationTable = (new FormacionAcademica())->getTable();

        return Usuario::query()
            ->select($this->userSearchColumns())
            ->with([
                'habilidades' => fn ($query) => $query
                    ->select(['id', 'usuario_id', 'nombre', 'tipo', 'nivel_texto', 'nivel_numero'])
                    ->orderBy('nombre'),
                'experiences' => fn ($query) => $query
                    ->select($this->experienceSearchColumns($experienceTable))
                    ->orderByDesc('actualmente')
                    ->orderByDesc('fecha_inicio')
                    ->orderByDesc('created_at'),
                'formacionAcademica' => fn ($query) => $query
                    ->select($this->educationSearchColumns($educationTable))
                    ->orderByDesc('actualmente')
                    ->orderByDesc('fecha_inicio')
                    ->orderByDesc('created_at'),
                'proyectos' => fn ($query) => $query
                    ->select(['id', 'usuario_id', 'titulo', 'descripcion', 'tecnologias', 'estado', 'created_at'])
                    ->orderByDesc('created_at'),
            ]);
    }

    private function run(Builder $query): array
    {
        return $query
            ->distinct()
            ->limit(20)
            ->get()
            ->map(fn (Usuario $user) => $this->formatSearchUser($user))
            ->values()
            ->all();
    }

    private function formatSearchUser(Usuario $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->nombre,
            'lastName' => $user->apellido,
            'photo' => $this->assetUrlService->fromStoragePath($user->foto_perfil),
            'bio' => $user->biografia,
            'skills' => $user->habilidades->pluck('nombre')->values(),
        ];
    }

    private function normalizeFilterPayload(array $payload): array
    {
        $flatFilters = is_array($payload['filters'] ?? null) ? $payload['filters'] : [];

        return [
            'q' => $this->cleanText($payload['q'] ?? ''),
            'usuario' => [
                'ubicacion' => $this->pickFilterValue($payload, $flatFilters, 'usuario', 'ubicacion'),
                'profesion' => $this->pickFilterValue($payload, $flatFilters, 'usuario', 'profesion'),
            ],
            'habilidades' => [
                'nombre' => $this->pickFilterValue($payload, $flatFilters, 'habilidades', 'nombre', 'habilidad'),
                'tipo' => $this->pickFilterValue($payload, $flatFilters, 'habilidades', 'tipo', 'hab_tipo'),
            ],
            'experiencias' => [
                'cargo' => $this->pickFilterValue($payload, $flatFilters, 'experiencias', 'cargo', 'exp_cargo'),
                'empresa' => $this->pickFilterValue($payload, $flatFilters, 'experiencias', 'empresa', 'exp_empresa'),
            ],
            'formaciones' => [
                'institucion' => $this->pickFilterValue($payload, $flatFilters, 'formaciones', 'institucion'),
                'nivel_formacion' => $this->pickFilterValue($payload, $flatFilters, 'formaciones', 'nivel_formacion'),
            ],
        ];
    }

    private function pickFilterValue(
        array $payload,
        array $flatFilters,
        string $group,
        string $nestedKey,
        ?string $flatKey = null
    ): string {
        $groupValues = is_array($payload[$group] ?? null) ? $payload[$group] : [];
        $value = $groupValues[$nestedKey] ?? $flatFilters[$flatKey ?? $nestedKey] ?? '';

        return $this->cleanText($value);
    }

    private function cleanText(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }

    private function applyVisibilityConstraints(Builder $query): void
    {
        if (Schema::hasColumn('usuarios', 'estado')) {
            $query->where('usuarios.estado', 'activo');
        }
    }

    private function applyKeywordQuery(Builder $query, string $term): void
    {
        if ($term === '') {
            return;
        }

        $experienceTable = (new Experience())->getTable();
        $educationTable = (new FormacionAcademica())->getTable();

        $query->where(function (Builder $builder) use ($term, $experienceTable, $educationTable) {
            $builder->where('usuarios.nombre', 'LIKE', "%{$term}%")
                ->orWhere('usuarios.apellido', 'LIKE', "%{$term}%")
                ->orWhere(DB::raw("CONCAT(usuarios.nombre, ' ', usuarios.apellido)"), 'LIKE', "%{$term}%")
                ->orWhere('usuarios.biografia', 'LIKE', "%{$term}%")
                ->orWhere('usuarios.ubicacion', 'LIKE', "%{$term}%")
                ->orWhereHas('habilidades', fn (Builder $skillQuery) => $skillQuery->where('nombre', 'LIKE', "%{$term}%"))
                ->orWhereHas('experiences', function (Builder $experienceQuery) use ($term, $experienceTable) {
                    $experienceQuery
                        ->where(Schema::hasColumn($experienceTable, 'cargo') ? 'cargo' : 'title', 'LIKE', "%{$term}%")
                        ->orWhere(Schema::hasColumn($experienceTable, 'empresa') ? 'empresa' : 'company', 'LIKE', "%{$term}%")
                        ->orWhere('descripcion', 'LIKE', "%{$term}%");
                })
                ->orWhereHas('formacionAcademica', function (Builder $educationQuery) use ($term, $educationTable) {
                    $educationQuery
                        ->where('institucion', 'LIKE', "%{$term}%")
                        ->orWhere(
                            Schema::hasColumn($educationTable, 'nombre_programa') ? 'nombre_programa' : 'nombre_carrera',
                            'LIKE',
                            "%{$term}%"
                        )
                        ->orWhere(
                            Schema::hasColumn($educationTable, 'nivel_formacion') ? 'nivel_formacion' : 'tipo_formacion',
                            'LIKE',
                            "%{$term}%"
                        );
                })
                ->orWhereHas('proyectos', function (Builder $projectQuery) use ($term) {
                    $projectQuery
                        ->where('titulo', 'LIKE', "%{$term}%")
                        ->orWhere('descripcion', 'LIKE', "%{$term}%")
                        ->orWhere('tecnologias', 'LIKE', "%{$term}%");
                });
        });
    }

    private function applyUserFilters(Builder $query, array $filters): void
    {
        if (($filters['ubicacion'] ?? '') !== '') {
            $query->where('usuarios.ubicacion', 'LIKE', '%'.$filters['ubicacion'].'%');
        }

        if (($filters['profesion'] ?? '') !== '') {
            $profession = $filters['profesion'];

            $query->where(function (Builder $builder) use ($profession) {
                $hasDirectProfession = Schema::hasColumn('usuarios', 'profesion');

                if ($hasDirectProfession) {
                    $builder->where('usuarios.profesion', 'LIKE', "%{$profession}%");
                }

                $relationCallback = function (Builder $experienceQuery) use ($profession) {
                    $table = (new Experience())->getTable();
                    $experienceQuery->where(
                        Schema::hasColumn($table, 'cargo') ? 'cargo' : 'title',
                        'LIKE',
                        "%{$profession}%"
                    );
                };

                if ($hasDirectProfession) {
                    $builder->orWhereHas('experiences', $relationCallback);
                } else {
                    $builder->whereHas('experiences', $relationCallback);
                }
            });
        }
    }

    private function applySkillFilters(Builder $query, array $filters): void
    {
        if (($filters['nombre'] ?? '') === '' && ($filters['tipo'] ?? '') === '') {
            return;
        }

        $query->whereHas('habilidades', function (Builder $skillQuery) use ($filters) {
            if (($filters['nombre'] ?? '') !== '') {
                $skillQuery->where('nombre', 'LIKE', '%'.$filters['nombre'].'%');
            }

            if (($filters['tipo'] ?? '') !== '') {
                $skillQuery->where('tipo', $filters['tipo']);
            }
        });
    }

    private function applyExperienceFilters(Builder $query, array $filters): void
    {
        if (($filters['cargo'] ?? '') === '' && ($filters['empresa'] ?? '') === '') {
            return;
        }

        $table = (new Experience())->getTable();

        $query->whereHas('experiences', function (Builder $experienceQuery) use ($filters, $table) {
            if (($filters['cargo'] ?? '') !== '') {
                $experienceQuery->where(
                    Schema::hasColumn($table, 'cargo') ? 'cargo' : 'title',
                    'LIKE',
                    '%'.$filters['cargo'].'%'
                );
            }

            if (($filters['empresa'] ?? '') !== '') {
                $experienceQuery->where(
                    Schema::hasColumn($table, 'empresa') ? 'empresa' : 'company',
                    'LIKE',
                    '%'.$filters['empresa'].'%'
                );
            }
        });
    }

    private function applyEducationFilters(Builder $query, array $filters): void
    {
        if (($filters['institucion'] ?? '') === '' && ($filters['nivel_formacion'] ?? '') === '') {
            return;
        }

        $table = (new FormacionAcademica())->getTable();

        $query->whereHas('formacionAcademica', function (Builder $educationQuery) use ($filters, $table) {
            if (($filters['institucion'] ?? '') !== '') {
                $educationQuery->where('institucion', 'LIKE', '%'.$filters['institucion'].'%');
            }

            if (($filters['nivel_formacion'] ?? '') !== '') {
                $educationQuery->where(
                    Schema::hasColumn($table, 'nivel_formacion') ? 'nivel_formacion' : 'tipo_formacion',
                    'LIKE',
                    '%'.$filters['nivel_formacion'].'%'
                );
            }
        });
    }

    private function formatFilteredUser(Usuario $user): array
    {
        $skills = $user->habilidades->map(function ($skill) {
            return [
                'id' => $skill->id,
                'nombre' => $skill->nombre,
                'tipo' => $skill->tipo,
                'nivel_texto' => $skill->nivel_texto,
                'nivel_numero' => $skill->nivel_numero,
            ];
        })->values();

        $experiences = $user->experiences->map(function ($experience) {
            return [
                'id' => $experience->id,
                'empresa' => $experience->company,
                'cargo' => $experience->title,
                'descripcion' => $experience->descripcion,
                'fecha_inicio' => optional($experience->fecha_inicio)->toDateString(),
                'fecha_fin' => optional($experience->fecha_fin)->toDateString(),
                'actualmente' => $experience->isCurrent,
            ];
        })->values();

        $education = $user->formacionAcademica->map(function ($item) {
            return [
                'id' => $item->id,
                'institucion' => $item->institucion,
                'nivel_formacion' => $item->nivel_formacion,
                'nombre_programa' => $item->nombre_programa,
                'fecha_inicio' => optional($item->fecha_inicio)->toDateString(),
                'fecha_fin' => optional($item->fecha_fin)->toDateString(),
                'actualmente' => $item->isCurrent,
            ];
        })->values();

        $projects = $user->proyectos->map(function ($project) {
            return [
                'id' => $project->id,
                'titulo' => $project->titulo,
                'descripcion' => $project->descripcion,
                'tecnologias' => $project->tecnologias,
                'estado' => $project->estado,
            ];
        })->values();

        return [
            'id' => $user->id,
            'type' => 'usuario',
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'email' => $user->email,
            'biografia' => $user->biografia,
            'ubicacion' => $user->ubicacion,
            'foto_perfil' => $this->assetUrlService->fromStoragePath($user->foto_perfil),
            'foto_portada' => $this->assetUrlService->fromStoragePath($user->foto_portada),
            'profesion' => $this->resolveProfessionLabel($user, $experiences, $education),
            'skills' => $skills->pluck('nombre')->values(),
            'habilidades' => $skills,
            'experiencias' => $experiences,
            'formaciones_academicas' => $education,
            'proyectos' => $projects,
            'profile_url' => '/perfil-profesional?usuario='.$user->id,
        ];
    }

    private function resolveProfessionLabel(Usuario $user, Collection $experiences, Collection $education): string
    {
        $directProfession = trim((string) $user->profesion);
        if ($directProfession !== '') {
            return $directProfession;
        }

        $currentExperience = $experiences->firstWhere('actualmente', true) ?? $experiences->first();
        if ($currentExperience && ! empty($currentExperience['cargo'])) {
            return (string) $currentExperience['cargo'];
        }

        $firstEducation = $education->first();
        if ($firstEducation && ! empty($firstEducation['nombre_programa'])) {
            return (string) $firstEducation['nombre_programa'];
        }

        return 'Profesional';
    }

    private function userSearchColumns(): array
    {
        return array_values(array_filter([
            'usuarios.id',
            'usuarios.nombre',
            'usuarios.apellido',
            'usuarios.email',
            'usuarios.biografia',
            'usuarios.ubicacion',
            'usuarios.foto_perfil',
            Schema::hasColumn('usuarios', 'foto_portada') ? 'usuarios.foto_portada' : null,
            Schema::hasColumn('usuarios', 'estado') ? 'usuarios.estado' : null,
            Schema::hasColumn('usuarios', 'profesion') ? 'usuarios.profesion' : null,
        ]));
    }

    private function experienceSearchColumns(string $table): array
    {
        return array_values(array_filter([
            'id',
            'usuario_id',
            Schema::hasColumn($table, 'empresa') ? 'empresa' : null,
            Schema::hasColumn($table, 'cargo') ? 'cargo' : null,
            Schema::hasColumn($table, 'company') ? 'company' : null,
            Schema::hasColumn($table, 'title') ? 'title' : null,
            'descripcion',
            'fecha_inicio',
            'fecha_fin',
            Schema::hasColumn($table, 'actualmente') ? 'actualmente' : null,
            'created_at',
        ]));
    }

    private function educationSearchColumns(string $table): array
    {
        return array_values(array_filter([
            'id',
            'usuario_id',
            Schema::hasColumn($table, 'nivel_formacion') ? 'nivel_formacion' : null,
            Schema::hasColumn($table, 'tipo_formacion') ? 'tipo_formacion' : null,
            'institucion',
            Schema::hasColumn($table, 'nombre_programa') ? 'nombre_programa' : null,
            Schema::hasColumn($table, 'nombre_carrera') ? 'nombre_carrera' : null,
            'fecha_inicio',
            'fecha_fin',
            Schema::hasColumn($table, 'actualmente') ? 'actualmente' : null,
            'created_at',
        ]));
    }
}
