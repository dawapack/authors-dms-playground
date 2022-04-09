<?php

declare(strict_types=1);

namespace AuthorsDMS\Validator;

use AuthorsDMS\Message\AbstractMessageValidator;

class UpdateAuthorRules extends AbstractMessageValidator
{
    // https://github.com/rakit/validation/tree/v1.4.0#available-rules
    protected array $rules = [
        'payload.firstName' => 'required|between:2,64',
        'payload.lastName' => 'required|between:2,64',
        'payload.email' => 'required|email',
        'headers.x_pathParam_authorId' => self::UUID_REGEX_RULE,
    ];
    protected array $messages = [
        'payload.firstName:required' => 'first name is required',
        'payload.firstName:between' => 'first name length is not between 2 and 64 characters',
        'payload.firstName' => 'first name is not valid',
        'payload.lastName:required' => 'last name is required',
        'payload.lastName:between' => 'last name length is not between 2 and 64 characters',
        'payload.lastName' => 'last name is not valid',
        'payload.email' => 'email is not valid',
        'headers.x_pathParam_authorId:required' => 'author id is required',
        'headers.x_pathParam_authorId' => 'author id format is not valid',
    ];
}