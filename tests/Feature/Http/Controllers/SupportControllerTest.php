<?php

declare(strict_types=1);

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Huddle\Zendesk\Facades\Zendesk;
use Zendesk\API\Exceptions\ApiResponseException;

it('should render the page without any errors', function () {
    $this->withoutExceptionHandling();

    $this->get(route('contact'))
        ->assertOk();
});

it('should be possible to successfully send the form', function () {
    Zendesk::shouldReceive('tickets->create')->andReturn([]);

    $this->post(route('contact'), [
        'name'    => 'test',
        'email'   => 'test@ardenthq.com',
        'subject' => 'general',
        'message' => 'test',
    ])->assertRedirect(route('contact'));
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
    ])->assertRedirect(route('contact'))
    ->assertSessionHas('laravel_flash_message');
});
