<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Examples\Book\Action;

use Psr\Http\Message\ResponseInterface;
use WoohooLabs\Yin\Examples\Book\JsonApi\Document\BookDocument;
use WoohooLabs\Yin\Examples\Book\JsonApi\Hydrator\BookHydator;
use WoohooLabs\Yin\Examples\Book\JsonApi\Resource\AuthorResource;
use WoohooLabs\Yin\Examples\Book\JsonApi\Resource\BookResource;
use WoohooLabs\Yin\Examples\Book\JsonApi\Resource\PublisherResource;
use WoohooLabs\Yin\Examples\Book\JsonApi\Resource\RepresentativeResource;
use WoohooLabs\Yin\Examples\Book\Repository\BookRepository;
use WoohooLabs\Yin\JsonApi\JsonApi;

class UpdateBookRelationshipAction
{
    public function __invoke(JsonApi $jsonApi): ResponseInterface
    {
        // Checking the name of the currently requested relationship
        $relationshipName = $jsonApi->getRequest()->getAttribute('rel');

        // Retrieving a book domain object with an ID of $id
        $id = (int) $jsonApi->getRequest()->getAttribute('id');
        $book = BookRepository::getBook($id);
        if ($book === null) {
            exit("A book with an ID of '{$id}' can't be found!");
        }

        // Hydrating the retrieved book domain object from the request
        $book = $jsonApi->hydrateRelationship($relationshipName, new BookHydator(), $book);

        // Instantiating a book document
        $document = new BookDocument(
            new BookResource(
                new AuthorResource(),
                new PublisherResource(
                    new RepresentativeResource(),
                ),
            ),
        );

        // Responding with "200 Ok" status code along with the book document
        return $jsonApi->respond()->ok($document, $book);
    }
}
