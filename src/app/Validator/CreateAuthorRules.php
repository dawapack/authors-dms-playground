<?php

declare(strict_types=1);

namespace AuthorsDMS\Validator;

use AuthorsDMS\Message\AbstractMessageValidator;

class CreateAuthorRules extends AbstractMessageValidator
{
    // https://github.com/rakit/validation/tree/v1.4.0#available-rules
    protected array $rules = [
        'payload.firstName' => 'required|between:2,64',
        'payload.lastName' => 'required|between:2,64',
        'payload.email' => 'required|email',
    ];
    protected array $messages = [
        'payload.firstName:required' => 'first name is required',
        'payload.firstName:between' => 'first name length is not between 2 and 64 characters',
        'payload.firstName' => 'first name is not valid',
        'payload.lastName:required' => 'last name is required',
        'payload.lastName:between' => 'last name length is not between 2 and 64 characters',
        'payload.lastName' => 'last name is not valid',
        'payload.email' => 'email is not valid',
    ];
}