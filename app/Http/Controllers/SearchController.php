<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\ViewModel;
use App\Http\Livewire\Concerns\ManagesSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

final class SearchController
{
    use ManagesSearch;

    public function index(Request $request): JsonResponse
    {
        $this->query = $request->input('query');

        $results = $this->results();

        return response()->json([
            'results'    => $results->map(fn (ViewModel $result) => $this->serializeResult($result)),
            'hasResults' => $results->isNotEmpty(),
        ]);
    }

    public function redirect(Request $request): RedirectResponse|Redirector|JsonResponse|Response
    {
        $this->query = $request->input('query');

        $redirectResponse = $this->goToFirstResult();

        if ($redirectResponse === null) {
            return response()->noContent();
        }

        return $redirectResponse;
    }

    private function serializeResult(ViewModel $result): array
    {
        $model = $result->model();

        return [
            'type'       => class_basename($model),
            'url'        => method_exists($result, 'url') ? $result->url() : null,
            'identifier' => method_exists($result, 'id') ? $result->id() : (method_exists($result, 'hash') ? $result->hash() : null),
            'attributes' => method_exists($model, 'toArray') ? $model->toArray() : [],
        ];
    }
}
