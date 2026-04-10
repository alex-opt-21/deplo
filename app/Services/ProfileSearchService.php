<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Builder;

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

        $usersQuery->whereHas('experiences', function (Builder $experienceQuery) use ($query, $filter) {
            switch ($filter) {
                case 'empresa':
                    $experienceQuery->where('company', 'LIKE', "%{$query}%");
                    break;
                case 'academica':
                    $experienceQuery->where('tipo', 'academica')
                        ->where(function (Builder $innerQuery) use ($query) {
                            $innerQuery->where('title', 'LIKE', "%{$query}%")
                                ->orWhere('descripcion', 'LIKE', "%{$query}%");
                        });
                    break;
                case 'laboral':
                default:
                    $experienceQuery->where('tipo', 'laboral')
                        ->where(function (Builder $innerQuery) use ($query) {
                            $innerQuery->where('title', 'LIKE', "%{$query}%")
                                ->orWhere('descripcion', 'LIKE', "%{$query}%");
                        });
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

        $usersQuery->whereHas('formacionAcademica', function (Builder $educationQuery) use ($query, $filter) {
            switch ($filter) {
                case 'universidad':
                    $educationQuery->where('institucion', 'LIKE', "%{$query}%");
                    break;
                case 'carrera':
                    $educationQuery->where('nombre_carrera', 'LIKE', "%{$query}%");
                    break;
                case 'nivel':
                default:
                    $educationQuery->where('tipo_formacion', 'LIKE', "%{$query}%");
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
                'habilidades' => fn (Builder $query) => $query
                    ->select(['id', 'usuario_id', 'nombre'])
                    ->orderBy('nombre'),
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
}
