<?php

namespace App\Http\Controllers;

use App\Http\Requests\SocialRequest;
use App\Models\Social;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $socials = Social::forUser($user->id)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'cv_url' => $this->resolveCvUrl($user, $socials),
            'links' => $socials->values(),
        ]);
    }

    public function store(SocialRequest $request): JsonResponse
    {
        $data = $this->payload($request);
        $user = $request->user();

        if ($this->shouldPersistCvUrl($data)) {
            $this->persistCvUrl($user, $data['cv_url']);
        }

        if (! isset($data['plataforma'], $data['url'])) {
            return response()->json([
                'cv_url' => $this->resolveCvUrl($user->fresh(), Social::forUser($user->id)->get()),
                'message' => 'CV actualizado correctamente',
            ], 201);
        }

        $social = Social::updateOrCreate(
            Social::platformIdentity($user->id, $data['plataforma']),
            Social::persistenceData($data)
        );

        return response()->json($social, 201);
    }

    public function update(SocialRequest $request, int $id): JsonResponse
    {
        $social = Social::forUser($request->user()->id)->findOrFail($id);
        $data = $this->payload($request);

        if ($this->shouldPersistCvUrl($data)) {
            $this->persistCvUrl($request->user(), $data['cv_url']);
        }

        $social->update(Social::persistenceData($data));

        return response()->json($social->fresh());
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $social = Social::forUser($request->user()->id)->findOrFail($id);
        $social->delete();

        return response()->json(['message' => 'Enlace social eliminado correctamente']);
    }

    private function payload(SocialRequest $request): array
    {
        $data = $request->persistenceData();
        $cvFile = $request->file('cvFile') ?? $request->file('cv_file');

        if ($cvFile) {
            $data['cv_url'] = $cvFile->store('cv', 'public');
        }

        if (array_key_exists('cv_url', $data) && blank($data['cv_url'])) {
            unset($data['cv_url']);
        }

        return $data;
    }

    private function shouldPersistCvUrl(array $data): bool
    {
        return array_key_exists('cv_url', $data) && filled($data['cv_url']);
    }

    private function persistCvUrl(Usuario $user, string $path): void
    {
        $userData = Usuario::persistenceData(['url_cv' => $path]);

        if ($userData !== []) {
            $user->update($userData);
            return;
        }

        $social = Social::forUser($user->id)->orderByDesc('id')->first();

        if ($social) {
            $social->update(Social::persistenceData(['cv_url' => $path]));
            return;
        }

        Social::create([
            'usuario_id' => $user->id,
            ...Social::persistenceData(['cv_url' => $path]),
        ]);
    }

    private function resolveCvUrl(Usuario $user, $socials): string
    {
        if (! empty($user->url_cv)) {
            return (string) $user->url_cv;
        }

        return (string) ($socials->firstWhere('url_cv', '!=', null)?->url_cv ?? '');
    }
}
