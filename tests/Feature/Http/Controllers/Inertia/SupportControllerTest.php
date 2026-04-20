<?php

declare(strict_types=1);

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Huddle\Zendesk\Facades\Zendesk;
use Inertia\Testing\AssertableInertia as Assert;
use Zendesk\API\Exceptions\ApiResponseException;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this->get(route('contact'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Support/Index')
            ->where('subjects', config('web.contact.subjects'))
            ->where('socialNetworkUrls.twitter', config('social.networks.twitter.url'))
            ->where('socialNetworkUrls.github', config('social.networks.github.url')));
});

it('should be possible to successfully send the form', function () {
    Zendesk::shouldReceive('tickets->create')->andReturn([]);

    $this->post(route('contact'), [
        'name'    => 'test',
        'email'   => 'test@ardenthq.com',
        'subject' => 'general',
        'message' => 'test',
    ])->assertOk();
});

it('should show validation error if validation fails', function () {
    $this->post(route('contact'), [
        'name'    => 'test',
        'email'   => 'test',
        'subject' => 'general',
        'message' => 'test',
    ])->assertSessionHasErrors(['email']);
});

it('should show an error when something goes wrong', function () {
    Zendesk::shouldReceive('tickets->create')
        ->andThrow(new ApiResponseException(new RequestException('test', new Request('get', '/test'))));

    $this->post(route('contact'), [
        'name'    => 'test',
        'email'   => 'test@ardenthq.com',
        'subject' => 'general',
        'message' => 'test',
    ])->assertInternalServerError()
    ->assertJson(['message' => trans('messages.contact_error')]);
});
