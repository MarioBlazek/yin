<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\Tests\JsonApi\Request;

use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yin\JsonApi\Exception\DefaultExceptionFactory;
use WoohooLabs\Yin\JsonApi\Exception\MediaTypeUnacceptable;
use WoohooLabs\Yin\JsonApi\Exception\MediaTypeUnsupported;
use WoohooLabs\Yin\JsonApi\Exception\QueryParamMalformed;
use WoohooLabs\Yin\JsonApi\Exception\QueryParamUnrecognized;
use WoohooLabs\Yin\JsonApi\Exception\RelationshipNotExists;
use WoohooLabs\Yin\JsonApi\Exception\TopLevelMemberNotAllowed;
use WoohooLabs\Yin\JsonApi\Exception\TopLevelMembersIncompatible;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequest;
use WoohooLabs\Yin\JsonApi\Serializer\JsonDeserializer;

use function implode;

class JsonApiRequestTest extends TestCase
{
    #[Test]
    public function validateJsonApiContentTypeHeader(): void
    {
        $request = $this->createRequestWithHeader('content-type', 'application/vnd.api+json');

        $request->validateContentTypeHeader();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateJsonApiContentTypeHeaderWithSemicolon(): void
    {
        $request = $this->createRequestWithHeader('content-type', 'application/vnd.api+json;');

        $request->validateContentTypeHeader();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateEmptyContentTypeHeader(): void
    {
        $request = $this->createRequestWithHeader('content-type', '');

        $request->validateContentTypeHeader();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateHtmlContentTypeHeader(): void
    {
        $request = $this->createRequestWithHeader('content-type', 'text/html; charset=utf-8');

        $request->validateContentTypeHeader();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateMultipleMediaTypeContentTypeHeader(): void
    {
        $request = $this->createRequestWithHeader('content-type', 'application/vnd.api+json, text/*;q=0.3, text/html;q=0.7');

        $request->validateContentTypeHeader();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateCaseInsensitiveContentTypeHeader(): void
    {
        $request = $this->createRequestWithHeader('content-type', 'Application/vnd.Api+JSON, text/*;q=0.3, text/html;Q=0.7');

        $request->validateContentTypeHeader();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateInvalidContentTypeHeaderWithExtMediaType(): void
    {
        $request = $this->createRequestWithHeader('content-type', 'application/vnd.api+json; ext="ext1,ext2"');

        $this->expectException(MediaTypeUnsupported::class);

        $request->validateContentTypeHeader();
    }

    #[Test]
    public function validateInvalidContentTypeHeaderWithWhitespaceBeforeParameter(): void
    {
        $request = $this->createRequestWithHeader('content-type', 'application/vnd.api+json ; ext="ext1,ext2"');

        $this->expectException(MediaTypeUnsupported::class);

        $request->validateContentTypeHeader();
    }

    #[Test]
    public function validateContentTypeHeaderWithJsonApiProfileMediaTypeParameter(): void
    {
        $request = $this->createRequestWithHeader(
            'content-type',
            'application/vnd.api+json;profile=https://example.com/profiles/last-modified',
        );

        $request->validateContentTypeHeader();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateContentTypeHeaderWithInvalidMediaTypeParameter(): void
    {
        $request = $this->createRequestWithHeader('content-type', 'application/vnd.api+json; Charset=utf-8');

        $this->expectException(MediaTypeUnsupported::class);

        $request->validateContentTypeHeader();
    }

    #[Test]
    public function validateAcceptHeaderWithJsonApiMediaType(): void
    {
        $request = $this->createRequestWithHeader('accept', 'application/vnd.api+json');

        $request->validateAcceptHeader();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateAcceptHeaderWithJsonApiProfileMediaTypeParameter(): void
    {
        $request = $this->createRequestWithHeader(
            'content-type',
            'application/vnd.api+json; Profile = https://example.com/profiles/last-modified',
        );

        $request->validateContentTypeHeader();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateAcceptHeaderWithInvalidMediaTypeParameters(): void
    {
        $request = $this->createRequestWithHeader('accept', 'application/vnd.api+json; ext="ext1,ext2"; charset=utf-8; lang=en');

        $this->expectException(MediaTypeUnacceptable::class);

        $request->validateAcceptHeader();
    }

    #[Test]
    public function validateEmptyQueryParams(): void
    {
        $request = $this->createRequestWithQueryParams([]);

        $request->validateQueryParams();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateBasicQueryParams(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'fields' => ['user' => 'name, address'],
                'include' => ['contacts'],
                'sort' => ['-name'],
                'page' => ['number' => '1'],
                'filter' => ['age' => '21'],
                'profile' => '',
            ],
        );

        $request->validateQueryParams();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateInvalidQueryParams(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'fields' => ['user' => 'name, address'],
                'paginate' => ['-name'],
            ],
        );

        $this->expectException(QueryParamUnrecognized::class);

        $request->validateQueryParams();
    }

    #[Test]
    public function validateTopLevelMembersWithoutBody(): void
    {
        $request = $this->createRequest();

        $request->validateTopLevelMembers();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateTopLevelMembersWhenEmpty(): void
    {
        $request = $this->createRequestWithJsonBody(
            [],
        );

        // FIXME https://github.com/woohoolabs/yin/issues/101
        // $this->expectException(RequiredTopLevelMembersMissing::class);

        $request->validateTopLevelMembers();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateTopLevelMembersWhenDataAndErrors(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [],
                'errors' => [],
            ],
        );

        $this->expectException(TopLevelMembersIncompatible::class);

        $request->validateTopLevelMembers();
    }

    #[Test]
    public function validateTopLevelMembersWhenIncludedWithoutData(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'errors' => [],
                'included' => [],
            ],
        );

        $this->expectException(TopLevelMemberNotAllowed::class);

        $request->validateTopLevelMembers();
    }

    #[Test]
    public function validateTopLevelMembersWhenData(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [],
            ],
        );

        $request->validateTopLevelMembers();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateTopLevelMembersWhenDataAndIncluded(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [],
                'included' => [],
            ],
        );

        $request->validateTopLevelMembers();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function validateTopLevelMembersWhenErrors(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'errors' => [],
            ],
        );

        $request->validateTopLevelMembers();

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function getIncludedFieldsWhenEmpty(): void
    {
        $request = $this->createRequestWithQueryParams([]);

        $includedFields = $request->getIncludedFields('');

        self::assertSame([], $includedFields);
    }

    #[Test]
    public function getIncludedFieldsForResource(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'fields' => [
                    'book' => 'title,pages',
                ],
            ],
        );

        $includedFields = $request->getIncludedFields('book');

        self::assertSame(['title', 'pages'], $includedFields);
    }

    #[Test]
    public function getIncludedFieldsForUnspecifiedResource(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'fields' => [
                    'book' => 'title,pages',
                ],
            ],
        );

        $includedFields = $request->getIncludedFields('newspaper');

        self::assertSame([], $includedFields);
    }

    #[Test]
    public function getIncludedFieldWhenMalformed(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'fields' => '',
            ],
        );

        $this->expectException(QueryParamMalformed::class);

        $request->getIncludedFields('');
    }

    #[Test]
    public function getIncludedFieldWhenFieldMalformed(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'fields' => [
                    'book' => [],
                ],
            ],
        );

        $this->expectException(QueryParamMalformed::class);

        $request->getIncludedFields('');
    }

    #[Test]
    public function isIncludedFieldWhenAllFieldsRequested(): void
    {
        $request = $this->createRequestWithQueryParams(['fields' => []]);
        self::assertTrue($request->isIncludedField('book', 'title'));

        $request = $this->createRequestWithQueryParams([]);
        self::assertTrue($request->isIncludedField('book', 'title'));
    }

    #[Test]
    public function isIncludedFieldWhenNoFieldRequested(): void
    {
        $request = $this->createRequestWithQueryParams(['fields' => ['book1' => '']]);

        $isIncludedField = $request->isIncludedField('book1', 'title');

        self::assertFalse($isIncludedField);
    }

    #[Test]
    public function isIncludedFieldWhenGivenFieldIsSpecified(): void
    {
        $request = $this->createRequestWithQueryParams(['fields' => ['book' => 'title,pages']]);

        $isIncludedField = $request->isIncludedField('book', 'title');

        self::assertTrue($isIncludedField);
    }

    #[Test]
    public function hasIncludedRelationshipsWhenTrue(): void
    {
        $request = $this->createRequestWithQueryParams(['include' => 'authors']);

        $hasIncludedRelationships = $request->hasIncludedRelationships();

        self::assertTrue($hasIncludedRelationships);
    }

    #[Test]
    public function hasIncludedRelationshipsWhenFalse(): void
    {
        $queryParams = ['include' => ''];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertFalse($request->hasIncludedRelationships());

        $queryParams = [];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertFalse($request->hasIncludedRelationships());
    }

    #[Test]
    public function getIncludedEmptyRelationshipsWhenEmpty(): void
    {
        $baseRelationshipPath = 'book';
        $includedRelationships = [];
        $queryParams = ['include' => ''];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertSame($includedRelationships, $request->getIncludedRelationships($baseRelationshipPath));

        $baseRelationshipPath = 'book';
        $includedRelationships = [];
        $queryParams = [];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertSame($includedRelationships, $request->getIncludedRelationships($baseRelationshipPath));
    }

    #[Test]
    public function getIncludedRelationshipsForPrimaryResource(): void
    {
        $baseRelationshipPath = '';
        $includedRelationships = ['authors'];
        $queryParams = ['include' => implode(',', $includedRelationships)];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertSame($includedRelationships, $request->getIncludedRelationships($baseRelationshipPath));
    }

    #[Test]
    public function getIncludedRelationshipsForEmbeddedResource(): void
    {
        $baseRelationshipPath = 'book';
        $includedRelationships = ['authors'];
        $queryParams = ['include' => 'book,book.authors'];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertSame($includedRelationships, $request->getIncludedRelationships($baseRelationshipPath));
    }

    #[Test]
    public function getIncludedRelationshipsForMultipleEmbeddedResource(): void
    {
        $baseRelationshipPath = 'book.authors';
        $includedRelationships = ['contacts', 'address'];
        $queryParams = ['include' => 'book,book.authors,book.authors.contacts,book.authors.address'];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertSame($includedRelationships, $request->getIncludedRelationships($baseRelationshipPath));
    }

    #[Test]
    public function getIncludedRelationshipsWhenMalformed(): void
    {
        $this->expectException(QueryParamMalformed::class);

        $queryParams = ['include' => []];

        $request = $this->createRequestWithQueryParams($queryParams);
        $request->getIncludedRelationships('');
    }

    #[Test]
    public function isIncludedRelationshipForPrimaryResourceWhenEmpty(): void
    {
        $baseRelationshipPath = '';
        $requiredRelationship = 'authors';
        $defaultRelationships = [];
        $queryParams = ['include' => ''];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertFalse(
            $request->isIncludedRelationship($baseRelationshipPath, $requiredRelationship, $defaultRelationships),
        );
    }

    #[Test]
    public function isIncludedRelationshipForPrimaryResourceWhenEmptyWithDefault(): void
    {
        $baseRelationshipPath = '';
        $requiredRelationship = 'authors';
        $defaultRelationships = ['publisher' => true];
        $queryParams = [];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertFalse(
            $request->isIncludedRelationship($baseRelationshipPath, $requiredRelationship, $defaultRelationships),
        );
    }

    #[Test]
    public function isIncludedRelationshipForPrimaryResourceWithDefault(): void
    {
        $baseRelationshipPath = '';
        $requiredRelationship = 'authors';
        $defaultRelationships = ['publisher' => true];
        $queryParams = ['include' => 'editors'];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertFalse(
            $request->isIncludedRelationship($baseRelationshipPath, $requiredRelationship, $defaultRelationships),
        );
    }

    #[Test]
    public function isIncludedRelationshipForEmbeddedResource(): void
    {
        $baseRelationshipPath = 'authors';
        $requiredRelationship = 'contacts';
        $defaultRelationships = [];
        $queryParams = ['include' => 'authors,authors.contacts'];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertTrue(
            $request->isIncludedRelationship($baseRelationshipPath, $requiredRelationship, $defaultRelationships),
        );
    }

    #[Test]
    public function isIncludedRelationshipForEmbeddedResourceWhenDefaulted(): void
    {
        $baseRelationshipPath = 'authors';
        $requiredRelationship = 'contacts';
        $defaultRelationships = ['contacts' => true];
        $queryParams = ['include' => ''];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertFalse(
            $request->isIncludedRelationship($baseRelationshipPath, $requiredRelationship, $defaultRelationships),
        );
    }

    #[Test]
    public function getSortingWhenEmpty(): void
    {
        $sorting = [];
        $queryParams = ['sort' => ''];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertSame($sorting, $request->getSorting());
    }

    #[Test]
    public function getSortingWhenNotEmpty(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'sort' => 'name,age,sex',
            ],
        );

        $sorting = $request->getSorting();

        self::assertSame(['name', 'age', 'sex'], $sorting);
    }

    #[Test]
    public function getSortingWhenMalformed(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'sort' => ['name' => 'asc'],
            ],
        );

        $this->expectException(QueryParamMalformed::class);

        $request->getSorting();
    }

    #[Test]
    public function getPaginationWhenEmpty(): void
    {
        $pagination = [];
        $queryParams = ['page' => []];

        $request = $this->createRequestWithQueryParams($queryParams);
        self::assertSame($pagination, $request->getPagination());
    }

    #[Test]
    public function getPaginationWhenNotEmpty(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'page' => ['number' => '1', 'size' => '10'],
            ],
        );

        $pagination = $request->getPagination();

        self::assertSame(['number' => '1', 'size' => '10'], $pagination);
    }

    #[Test]
    public function getPaginationWhenMalformed(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'page' => '',
            ],
        );

        $this->expectException(QueryParamMalformed::class);

        $request->getPagination();
    }

    #[Test]
    public function getFilteringWhenEmpty(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'filter' => [],
            ],
        );

        $filtering = $request->getFiltering();

        self::assertEmpty($filtering);
    }

    #[Test]
    public function getFilteringWhenNotEmpty(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'filter' => ['name' => 'John'],
            ],
        );

        $filtering = $request->getFiltering();

        self::assertSame(['name' => 'John'], $filtering);
    }

    #[Test]
    public function getFilteringWhenMalformed(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'filter' => '',
            ],
        );

        $this->expectException(QueryParamMalformed::class);

        $request->getFiltering();
    }

    #[Test]
    public function getFilteringParam(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'filter' => ['name' => 'John'],
            ],
        );

        $filteringParam = $request->getFilteringParam('name');

        self::assertSame('John', $filteringParam);
    }

    #[Test]
    public function getDefaultFilteringParamWhenNotFound(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'filter' => ['name' => 'John'],
            ],
        );

        $filteringParam = $request->getFilteringParam('age', false);

        self::assertFalse($filteringParam);
    }

    #[Test]
    public function getAppliedProfilesWhenEmpty(): void
    {
        $request = $this->createRequestWithHeader('content-type', 'application/vnd.api+json');

        $profiles = $request->getAppliedProfiles();

        self::assertEmpty($profiles);
    }

    #[Test]
    public function getAppliedProfilesWhenOneProfile(): void
    {
        $request = $this->createRequestWithHeader(
            'content-type',
            'application/vnd.api+json;profile=https://example.com/profiles/last-modified',
        );

        $profiles = $request->getAppliedProfiles();

        self::assertSame(
            [
                'https://example.com/profiles/last-modified',
            ],
            $profiles,
        );
    }

    #[Test]
    public function getAppliedProfilesWhenTwoProfiles(): void
    {
        $request = $this->createRequestWithHeader(
            'content-type',
            'application/vnd.api+json;profile="https://example.com/profiles/last-modified https://example.com/profiles/created"',
        );

        $profiles = $request->getAppliedProfiles();

        self::assertSame(
            [
                'https://example.com/profiles/last-modified',
                'https://example.com/profiles/created',
            ],
            $profiles,
        );
    }

    #[Test]
    public function getAppliedProfilesWhenMultipleJsonApiContentTypes(): void
    {
        $request = $this->createRequestWithHeader(
            'content-type',
            'application/vnd.api+json;profile = https://example.com/profiles/last-modified, ' .
            'application/vnd.api+json;profile="https://example.com/profiles/last-modified https://example.com/profiles/created"',
        );

        $profiles = $request->getAppliedProfiles();

        self::assertSame(
            [
                'https://example.com/profiles/last-modified',
                'https://example.com/profiles/created',
            ],
            $profiles,
        );
    }

    #[Test]
    public function isProfileAppliedWhenTrue(): void
    {
        $request = $this->createRequestWithHeader(
            'content-type',
            'application/vnd.api+json;profile="https://example.com/profiles/last-modified https://example.com/profiles/created"',
        );

        $isProfileApplied = $request->isProfileApplied('https://example.com/profiles/created');

        self::assertTrue($isProfileApplied);
    }

    #[Test]
    public function isProfileAppliedWhenFalse(): void
    {
        $request = $this->createRequestWithHeader(
            'content-type',
            'application/vnd.api+json;profile="https://example.com/profiles/last-modified https://example.com/profiles/created"',
        );

        $isProfileApplied = $request->isProfileApplied('https://example.com/profiles/inexistent-profile');

        self::assertFalse($isProfileApplied);
    }

    #[Test]
    public function getRequestedProfilesWhenEmpty(): void
    {
        $request = $this->createRequestWithHeader('accept', 'application/vnd.api+json');

        $profiles = $request->getRequestedProfiles();

        self::assertEmpty($profiles);
    }

    #[Test]
    public function getRequestedProfilesWhenTwoProfiles(): void
    {
        $request = $this->createRequestWithHeader(
            'accept',
            'application/vnd.api+json;profile="https://example.com/profiles/last-modified https://example.com/profiles/created"',
        );

        $profiles = $request->getRequestedProfiles();

        self::assertSame(
            [
                'https://example.com/profiles/last-modified',
                'https://example.com/profiles/created',
            ],
            $profiles,
        );
    }

    #[Test]
    public function isProfileRequestedWhenTrue(): void
    {
        $request = $this->createRequestWithHeader(
            'accept',
            'application/vnd.api+json;profile="https://example.com/profiles/last-modified https://example.com/profiles/created"',
        );

        $isProfileRequested = $request->isProfileRequested('https://example.com/profiles/created');

        self::assertTrue($isProfileRequested);
    }

    #[Test]
    public function isProfileRequestedWhenFalse(): void
    {
        $request = $this->createRequestWithHeader(
            'accept',
            'application/vnd.api+json;profile="https://example.com/profiles/last-modified https://example.com/profiles/created"',
        );

        $isProfileRequested = $request->isProfileRequested('https://example.com/profiles/inexistent-profile');

        self::assertFalse($isProfileRequested);
    }

    #[Test]
    public function getRequiredProfilesWhenMalformed(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'profile' => [],
            ],
        );

        $this->expectException(QueryParamMalformed::class);

        $request->getRequiredProfiles();
    }

    #[Test]
    public function getRequiredProfilesWhenEmpty(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'profile' => '',
            ],
        );

        $profiles = $request->getRequiredProfiles();

        self::assertEmpty($profiles);
    }

    #[Test]
    public function getRequiredProfilesWhenTwoProfiles(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'profile' => 'https://example.com/profiles/last-modified https://example.com/profiles/created',
            ],
        );

        $profiles = $request->getRequiredProfiles();

        self::assertSame(
            [
                'https://example.com/profiles/last-modified',
                'https://example.com/profiles/created',
            ],
            $profiles,
        );
    }

    #[Test]
    public function isProfileRequiredWhenTrue(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'profile' => 'https://example.com/profiles/last-modified https://example.com/profiles/created',
            ],
        );

        $isProfileRequired = $request->isProfileRequired('https://example.com/profiles/created');

        self::assertTrue($isProfileRequired);
    }

    #[Test]
    public function isProfileRequiredWhenFalse(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'profile' => 'https://example.com/profiles/last-modified https://example.com/profiles/created',
            ],
        );

        $isProfileRequired = $request->isProfileRequired('https://example.com/profiles/inexistent-profile');

        self::assertFalse($isProfileRequired);
    }

    #[Test]
    public function withHeaderInvalidatesParsedJsonApiHeaders(): void
    {
        $request = $this->createRequest()
            ->withHeader(
                'content-type',
                'application/vnd.api+json;profile=https://example.com/profiles/last-modified',
            )
            ->withHeader(
                'accept',
                'application/vnd.api+json;profile=https://example.com/profiles/last-modified',
            );

        $request->getAppliedProfiles();
        $request->getRequestedProfiles();

        $request = $request
            ->withHeader(
                'content-type',
                'application/vnd.api+json;profile=https://example.com/profiles/created',
            )
            ->withHeader(
                'accept',
                'application/vnd.api+json;profile=https://example.com/profiles/created',
            );

        self::assertSame(['https://example.com/profiles/created'], $request->getAppliedProfiles());
        self::assertSame(['https://example.com/profiles/created'], $request->getRequestedProfiles());
    }

    #[Test]
    public function getResourceWhenEmpty(): void
    {
        $request = $this->createRequestWithJsonBody([]);

        $resource = $request->getResource();

        self::assertNull($resource);
    }

    #[Test]
    public function getResource(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [],
            ],
        );

        $resource = $request->getResource();

        self::assertSame([], $resource);
    }

    #[Test]
    public function getResourceTypeWhenEmpty(): void
    {
        $request = $this->createRequestWithJsonBody([]);

        $type = $request->getResourceType();

        self::assertNull($type);
    }

    #[Test]
    public function getResourceType(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'user',
                ],
            ],
        );

        $type = $request->getResourceType();

        self::assertSame('user', $type);
    }

    #[Test]
    public function getResourceIdWhenEmpty(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [],
            ],
        );

        $id = $request->getResourceId();

        self::assertNull($id);
    }

    #[Test]
    public function getResourceId(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'id' => '1',
                ],
            ],
        );

        $id = $request->getResourceId();

        self::assertSame('1', $id);
    }

    #[Test]
    public function getResourceAttributes(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'dog',
                    'id' => '1',
                    'attributes' => [
                        'name' => 'Hot dog',
                    ],
                ],
            ],
        );

        $attributes = $request->getResourceAttributes();

        self::assertSame(
            [
                'name' => 'Hot dog',
            ],
            $attributes,
        );
    }

    #[Test]
    public function getResourceAttribute(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'dog',
                    'id' => '1',
                    'attributes' => [
                        'name' => 'Hot dog',
                    ],
                ],
            ],
        );

        $name = $request->getResourceAttribute('name');

        self::assertSame('Hot dog', $name);
    }

    #[Test]
    public function hasToOneRelationshipWhenTrue(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'dog',
                    'id' => '1',
                    'relationships' => [
                        'owner' => [
                            'data' => ['type' => 'human', 'id' => '1'],
                        ],
                    ],
                ],
            ],
        );

        $hasToOneRelationship = $request->hasToOneRelationship('owner');

        self::assertTrue($hasToOneRelationship);
    }

    #[Test]
    public function hasToOneRelationshipWhenFalse(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'dog',
                    'id' => '1',
                    'relationships' => [],
                ],
            ],
        );

        $hasToOneRelationship = $request->hasToOneRelationship('owner');

        self::assertFalse($hasToOneRelationship);
    }

    #[Test]
    public function getToOneRelationship(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'dog',
                    'id' => '1',
                    'relationships' => [
                        'owner' => [
                            'data' => ['type' => 'human', 'id' => '1'],
                        ],
                    ],
                ],
            ],
        );

        $resourceIdentifier = $request->getToOneRelationship('owner')->getResourceIdentifier();
        $type = $resourceIdentifier !== null ? $resourceIdentifier->getType() : '';
        $id = $resourceIdentifier !== null ? $resourceIdentifier->getId() : '';

        self::assertSame('human', $type);
        self::assertSame('1', $id);
    }

    #[Test]
    public function getDeletingToOneRelationship(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'dog',
                    'id' => '1',
                    'relationships' => [
                        'owner' => [
                            'data' => null,
                        ],
                    ],
                ],
            ],
        );

        $isEmpty = $request->getToOneRelationship('owner')->isEmpty();

        self::assertTrue($isEmpty);
    }

    #[Test]
    public function getToOneRelationshiWhenNotExists(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'dog',
                    'id' => '1',
                    'relationships' => [
                    ],
                ],
            ],
        );

        $this->expectException(RelationshipNotExists::class);

        $request->getToOneRelationship('owner');
    }

    #[Test]
    public function hasToManyRelationshipWhenTrue(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'dog',
                    'id' => '1',
                    'relationships' => [
                        'friends' => [
                            'data' => [
                                ['type' => 'dog', 'id' => '2'],
                                ['type' => 'dog', 'id' => '3'],
                            ],
                        ],
                    ],
                ],
            ],
        );

        $hasRelationship = $request->hasToManyRelationship('friends');

        self::assertTrue($hasRelationship);
    }

    #[Test]
    public function hasToManyRelationshipWhenFalse(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'dog',
                    'id' => '1',
                    'relationships' => [
                    ],
                ],
            ],
        );

        $hasRelationship = $request->hasToManyRelationship('friends');

        self::assertFalse($hasRelationship);
    }

    #[Test]
    public function getToManyRelationship(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'dog',
                    'id' => '1',
                    'relationships' => [
                        'friends' => [
                            'data' => [
                                ['type' => 'dog', 'id' => '2'],
                                ['type' => 'dog', 'id' => '3'],
                            ],
                        ],
                    ],
                ],
            ],
        );

        $resourceIdentifiers = $request->getToManyRelationship('friends')->getResourceIdentifiers();

        self::assertSame('dog', $resourceIdentifiers[0]->getType());
        self::assertSame('2', $resourceIdentifiers[0]->getId());
        self::assertSame('dog', $resourceIdentifiers[1]->getType());
        self::assertSame('3', $resourceIdentifiers[1]->getId());
    }

    #[Test]
    public function getToManyRelationshipWhenNotExists(): void
    {
        $request = $this->createRequestWithJsonBody(
            [
                'data' => [
                    'type' => 'dog',
                    'id' => '1',
                    'relationships' => [
                    ],
                ],
            ],
        );

        $this->expectException(RelationshipNotExists::class);

        $request->getToManyRelationship('friends');
    }

    #[Test]
    public function withQueryParamsInvalidatesParsedJsonApiQueryParams(): void
    {
        $request = $this->createRequestWithQueryParams(
            [
                'fields' => ['book' => 'title,pages'],
                'include' => 'authors',
                'page' => ['offset' => 0, 'limit' => 10],
                'filter' => ['title' => 'Working Effectively with Unit Tests'],
                'sort' => 'title',
                'profile' => 'https://example.com/profiles/last-modified',
            ],
        );

        $request->getIncludedFields('');
        $request->getIncludedRelationships('');
        $request->getPagination();
        $request->getFiltering();
        $request->getSorting();
        $request->getRequiredProfiles();

        $request = $request->withQueryParams(
            [
                'fields' => ['book' => 'isbn'],
                'include' => 'publisher',
                'page' => ['number' => 1, 'size' => 10],
                'filter' => ['title' => 'Building Microservices'],
                'sort' => 'isbn',
                'profile' => 'https://example.com/profiles/created',
            ],
        );

        self::assertSame(['isbn'], $request->getIncludedFields('book'));
        self::assertSame(['publisher'], $request->getIncludedRelationships(''));
        self::assertSame(['number' => 1, 'size' => 10], $request->getPagination());
        self::assertSame(['title' => 'Building Microservices'], $request->getFiltering());
        self::assertSame(['isbn'], $request->getSorting());
        self::assertSame(['https://example.com/profiles/created'], $request->getRequiredProfiles());
    }

    private function createRequest(): JsonApiRequest
    {
        return new JsonApiRequest(new ServerRequest(), new DefaultExceptionFactory(), new JsonDeserializer());
    }

    private function createRequestWithJsonBody(array $body): JsonApiRequest
    {
        $psrRequest = new ServerRequest();
        $psrRequest = $psrRequest->withParsedBody($body);

        return new JsonApiRequest($psrRequest, new DefaultExceptionFactory(), new JsonDeserializer());
    }

    private function createRequestWithHeader(string $headerName, string $headerValue): JsonApiRequest
    {
        $psrRequest = new ServerRequest([], [], null, null, 'php://temp', [$headerName => $headerValue]);

        return new JsonApiRequest($psrRequest, new DefaultExceptionFactory(), new JsonDeserializer());
    }

    private function createRequestWithQueryParams(array $queryParams): JsonApiRequest
    {
        $psrRequest = new ServerRequest();
        $psrRequest = $psrRequest->withQueryParams($queryParams);

        return new JsonApiRequest($psrRequest, new DefaultExceptionFactory(), new JsonDeserializer());
    }
}
