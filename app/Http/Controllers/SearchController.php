<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\ViewModel;
use App\DTO\Search\NavbarSearchBlockResultData;
use App\DTO\Search\NavbarSearchTransactionResultData;
use App\DTO\Search\NavbarSearchWalletResultData;
use App\Http\Livewire\Concerns\ManagesSearch;
use App\ViewModels\BlockViewModel;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\WalletViewModel;
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

    /**
     * @param WalletViewModel|BlockViewModel|TransactionViewModel $result
     */
    private function serializeResult(ViewModel $result): array
    {
        return [
            'type'       => $this->determineType($result),
            'url'        =>  $result->url(),
            'identifier' =>  $result->id() ?? $result->hash(),
            'data'       => $this->toArray($result),
        ];
    }

    private function determineType(ViewModel $result): string
    {
        return match (true) {
            $result instanceof WalletViewModel      => 'wallet',
            $result instanceof BlockViewModel       => 'block',
            $result instanceof TransactionViewModel => 'transaction',
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function toArray(ViewModel $result): array
    {
        if ($result instanceof WalletViewModel) {
            return NavbarSearchWalletResultData::fromViewModel($result)->toArray();
        }

        if ($result instanceof BlockViewModel) {
            return NavbarSearchBlockResultData::fromViewModel($result)->toArray();
        }

        if ($result instanceof TransactionViewModel) {
            return NavbarSearchTransactionResultData::fromViewModel($result)->toArray();
        }

        throw new \Exception('Invalid result type: ' . get_class($result));
    }
}
