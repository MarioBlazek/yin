<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Examples\User\Action;

use Psr\Http\Message\ResponseInterface;
use WoohooLabs\Yin\Examples\User\JsonApi\Document\UserDocument;
use WoohooLabs\Yin\Examples\User\JsonApi\Resource\ContactResource;
use WoohooLabs\Yin\Examples\User\JsonApi\Resource\UserResource;
use WoohooLabs\Yin\Examples\User\Repository\UserRepository;
use WoohooLabs\Yin\JsonApi\JsonApi;

class GetUserRelationshipsAction
{
    public function __invoke(JsonApi $jsonApi): ResponseInterface
    {
        // Checking the "id" of the currently requested user
        $id = (int) $jsonApi->getRequest()->getAttribute('id');

        // Checking the name of the currently requested relationship
        $relationshipName = $jsonApi->getRequest()->getAttribute('rel');

        // Retrieving a user domain object with an ID of $id
        $user = UserRepository::getUser($id);

        // Instantiating a book document
        $document = new UserDocument(new UserResource(new ContactResource()));

        // Responding with "200 Ok" status code along with the requested relationship document
        return $jsonApi->respond()->okWithRelationship($relationshipName, $document, $user);
    }
}
