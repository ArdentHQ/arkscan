<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use ARKEcosystem\Foundation\UserInterface\Http\Controllers\Controller;
use Huddle\Zendesk\Facades\Zendesk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Zendesk\API\Exceptions\ApiResponseException;

final class ContactController extends Controller
{
    public function index(Request $request): View
    {
        return view('app.contact');
    }

    public function handle(Request $request): RedirectResponse
    {
        /** @phpstan-ignore-next-line */
        $data = $request->validate([
            'name'    => ['required', 'max:64'],
            'email'   => ['required', 'email'],
            'subject' => ['required', 'string', Rule::in($this->getSubjects())],
            'message' => ['required', 'max:2048'],
        ]);

        try {
            Zendesk::tickets()->create([
                'requester' => [
                    'name'  => $data['name'],
                    'email' => $data['email'],
                ],
                'subject' => config('web.contact.source').' - '.$this->getSubjectLabel($data['subject']),
                'comment' => [
                    'body' => $data['message'],
                ],
                'priority' => 'normal',
                'tags'     => [strtolower(config('web.contact.source'))],
            ]);
        } catch (ApiResponseException $exception) {
            /* @phpstan-ignore-next-line */
            flash()->error(trans('messages.contact_error'));

            return redirect()->route('contact');
        }

        /* @phpstan-ignore-next-line */
        flash()->success(trans('messages.contact'));

        return redirect()->route('contact');
    }

    private function getSubjects(): Collection
    {
        /** @var array<int, array<string, string>> */
        $subjects = config('web.contact.subjects');

        return collect($subjects)->pluck('value');
    }

    private function getSubjectLabel(string $value): string
    {
        /** @var array<int, array<string, string>> */
        $subjects = config('web.contact.subjects');

        /** @var array{value:string, label:string} */
        $subject = collect($subjects)->filter(function ($element) use ($value) {
            return $element['value'] === $value;
        })->first();

        return $subject['label'];
    }
}
