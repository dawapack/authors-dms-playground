<?php

declare(strict_types=1);

namespace AuthorsDMS\Services;

use AuthorsDMS\Exceptions\NotFoundException;
use AuthorsDMS\Message\ApplicationEventMessage;
use AuthorsDMS\Message\ApplicationMessage;
use AuthorsDMS\OutboundAdapters\RedisRepository;
use AuthorsDMS\OutboundAdapters\RepositoryInterface;
use AuthorsDMS\Validator\CreateAuthorRules;
use AuthorsDMS\Validator\DeleteAuthorRules;
use AuthorsDMS\Validator\GetAuthorRules;
use AuthorsDMS\Validator\UpdateAuthorRules;
use Chassis\Framework\Adapters\Message\InboundMessageInterface;
use Chassis\Framework\Logger\Logger;
use Chassis\Framework\Services\AbstractService;
use DateTime;
use Psr\Cache\InvalidArgumentException;
use Throwable;

class AuthorService extends AbstractService
{
    private const INTERNAL_SERVER_ERROR_MESSAGE = 'the server encountered an unexpected condition';

    private RedisRepository $repository;
    private array $fillable = ['firstName', 'lastName', 'email'];

    public function __construct(
        InboundMessageInterface $message,
        RepositoryInterface $repository = null
    ) {
        $this->repository = is_null($repository) ? new RedisRepository() : $repository;
        parent::__construct($message);
    }

    /**
     * @operation getAuthor
     */
    public function get()
    {
        try {
            $validation = new GetAuthorRules($this->message);
            if (!$validation->isValid()) {
                return $this->response(
                    new ApplicationMessage($validation->getErrors()), 400, 'BAD_REQUEST'
                );
            }
            $id = ($this->message->getBody())["pathParams"]["authorId"];

            return $this->response(
                new ApplicationMessage([$this->repository->getItem($id)]), 200, "DONE"
            );
        } catch (NotFoundException $reason) {
            return $this->response(
                new ApplicationMessage([]), $reason->getCode(), "NOT_FOUND"
            );
        } catch (Throwable|InvalidArgumentException $reason) {
            Logger::error(
                $reason->getMessage(),
                [
                    'component' => 'author_service_get_exception',
                    'error' => $reason,
                ]
            );
        }

        return $this->response(
            new ApplicationMessage(['code' => 500, 'message' => self::INTERNAL_SERVER_ERROR_MESSAGE]),
            500,
            "INTERNAL_SERVER_ERROR"
        );
    }

    /**
     * @operation createAuthor
     */
    public function create()
    {
        try {
            $validation = new CreateAuthorRules($this->message);
            if (!$validation->isValid()) {
                return $this->response(
                    new ApplicationMessage($validation->getErrors()), 400, 'BAD_REQUEST'
                );
            }

            $data = array_merge(
                array_intersect_key(
                    $this->message->getBody(),
                    array_flip($this->fillable)
                ),
                [
                    'createdAt' => (new DateTime())->format(self::DEFAULT_DATETIME_FORMAT),
                    'updatedAt' => null,
                ]
            );
            $id = $this->repository->saveItem($data);

            // send author created event
            $this->send('authorCreated', new ApplicationEventMessage(['authorId' => $id]));

            // return success
            return $this->response(
                new ApplicationMessage(['authorId' => $id]), 200, "DONE"
            );
        } catch (Throwable $reason) {
            // log other exceptions
            Logger::error(
                $reason->getMessage(),
                [
                    'component' => 'author_service_create_exception',
                    'error' => $reason,
                ]
            );
        }

        // finally, return internal server error
        return $this->response(
            new ApplicationMessage(['code' => 500, 'message' => self::INTERNAL_SERVER_ERROR_MESSAGE])
            , 500
            , "INTERNAL_SERVER_ERROR"
        );
    }

    /**
     * @operation updateAuthor
     */
    public function update()
    {
        try {
            $validation = new UpdateAuthorRules($this->message);
            if (!$validation->isValid()) {
                return $this->response(
                    new ApplicationMessage($validation->getErrors()), 400, 'BAD_REQUEST'
                );
            }

            $id = $this->message->getHeader('x_pathParam_authorId');
            $values = array_merge(
                $this->repository->getItem($id),
                array_intersect_key(
                    $this->message->getBody(),
                    array_flip($this->fillable)
                ),
                ['updatedAt' => (new DateTime())->format(self::DEFAULT_DATETIME_FORMAT)]
            );
            $this->repository->updateItem($id, $values);

            // send author created event
            $this->send('authorUpdated', new ApplicationEventMessage(['authorId' => $id]));

            // return success
            return $this->response(
                new ApplicationMessage(['authorId' => $id]), 200, "DONE"
            );
        } catch (NotFoundException $reason) {
            return $this->response(
                new ApplicationMessage([]), $reason->getCode(), "NOT_FOUND"
            );
        } catch (Throwable|InvalidArgumentException $reason) {
            // log other exceptions
            Logger::error(
                $reason->getMessage(),
                [
                    'component' => 'author_service_update_exception',
                    'error' => $reason,
                ]
            );
        }

        // finally, return internal server error
        return $this->response(
            new ApplicationMessage(['code' => 500, 'message' => self::INTERNAL_SERVER_ERROR_MESSAGE])
            , 500
            , "INTERNAL_SERVER_ERROR"
        );
    }

    /**
     * @operation deleteAuthor
     */
    public function delete()
    {
        try {
            $validation = new DeleteAuthorRules($this->message);
            if (!$validation->isValid()) {
                return $this->response(
                    new ApplicationMessage($validation->getErrors()), 400, 'BAD_REQUEST'
                );
            }
            $id = ($this->message->getBody())["pathParams"]["authorId"];

            $this->repository->deleteItem($id);

            // send author deleted event
            $this->send('authorDeleted', new ApplicationEventMessage(['authorId' => $id]));

            // return success
            return $this->response(
                new ApplicationMessage(['authorId' => $id]), 200, "DONE"
            );
        } catch (NotFoundException $reason) {
            return $this->response(
                new ApplicationMessage([]), $reason->getCode(), "NOT_FOUND"
            );
        } catch (Throwable|InvalidArgumentException $reason) {
            Logger::error(
                $reason->getMessage(),
                [
                    'component' => 'author_service_delete_exception',
                    'error' => $reason,
                ]
            );
        }

        return $this->response(
            new ApplicationMessage(['code' => 500, 'message' => self::INTERNAL_SERVER_ERROR_MESSAGE])
            , 500
            , "INTERNAL_SERVER_ERROR"
        );
    }
}
