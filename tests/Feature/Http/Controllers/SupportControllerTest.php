<?php

declare(strict_types=1);

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Huddle\Zendesk\Facades\Zendesk;
use Spatie\Honeypot\ProtectAgainstSpam;
use Zendesk\API\Exceptions\ApiResponseException;

beforeEach(function () {
    $this->withoutMiddleware(ProtectAgainstSpam::class);
});

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this->get(route('contact'))
        ->assertOk();
});

it('should be possible to successfully send the form', function () {
    Zendesk::shouldReceive('tickets->create')->andReturn([]);

    $this->postJson(route('contact'), [
        'name'    => 'test',
        'email'   => 'test@ardenthq.com',
        'subject' => 'general',
        'message' => 'test',
    ])->assertOk();
});

it('should show validation error if validation fails', function () {
    $this->postJson(route('contact'), [
        'name'    => 'test',
        'email'   => 'test',
        'subject' => 'general',
        'message' => 'test',
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('should show an error when something goes wrong', function () {
    Zendesk::shouldReceive('tickets->create')
        ->andThrow(new ApiResponseException(new RequestException('test', new Request('get', '/test'))));

    $this->postJson(route('contact'), [
        'name'    => 'test',
        'email'   => 'test@ardenthq.com',
        'subject' => 'general',
        'message' => 'test',
    ])->assertStatus(500)
        ->assertJson([
            'message' => trans('messages.contact_error'),
        ]);
});
