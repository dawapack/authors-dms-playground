<?php

declare(strict_types=1);

namespace AuthorsDMS\Validator;

use AuthorsDMS\Message\AbstractMessageValidator;

class DeleteAuthorRules extends AbstractMessageValidator
{
    // https://github.com/rakit/validation/tree/v1.4.0#available-rules
    protected array $rules = [
        'payload.pathParams.authorId' => self::UUID_REGEX_RULE,
    ];
    protected array $messages = [
        'payload.pathParams.authorId:required' => 'author id is required',
        'payload.pathParams.authorId' => 'author id format is not valid',
    ];
}