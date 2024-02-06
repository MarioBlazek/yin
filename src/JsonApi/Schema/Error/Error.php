<?php

declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Schema\Error;

use WoohooLabs\Yin\JsonApi\Schema\Link\ErrorLinks;
use WoohooLabs\Yin\JsonApi\Schema\MetaTrait;

class Error
{
    use MetaTrait;

    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var ErrorLinks|null
     */
    protected $links;

    /**
     * @var string
     */
    protected $status = '';

    /**
     * @var string
     */
    protected $code = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $detail = '';

    /**
     * @var ErrorSource|null
     */
    protected $source;

    public static function create(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLinks(): ?ErrorLinks
    {
        return $this->links;
    }

    /**
     * @return $this
     */
    public function setLinks(ErrorLinks $links): self
    {
        $this->links = $links;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getSource(): ?ErrorSource
    {
        return $this->source;
    }

    public function setSource(ErrorSource $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @internal
     */
    public function transform(): array
    {
        $content = [];

        $this->transformId($content);
        $this->transformMeta($content);
        $this->transformLinks($content);
        $this->transformStatus($content);
        $this->transformCode($content);
        $this->transformTitle($content);
        $this->transformDetail($content);
        $this->transformSource($content);

        return $content;
    }

    protected function transformId(array &$content): void
    {
        if ($this->id !== '') {
            $content['id'] = $this->id;
        }
    }

    protected function transformMeta(array &$content): void
    {
        if (empty($this->meta) === false) {
            $content['meta'] = $this->meta;
        }
    }

    protected function transformLinks(array &$content): void
    {
        if ($this->links !== null) {
            $content['links'] = $this->links->transform();
        }
    }

    protected function transformStatus(array &$content): void
    {
        if ($this->status !== '') {
            $content['status'] = $this->status;
        }
    }

    protected function transformCode(array &$content): void
    {
        if ($this->code !== '') {
            $content['code'] = $this->code;
        }
    }

    protected function transformTitle(array &$content): void
    {
        if ($this->title !== '') {
            $content['title'] = $this->title;
        }
    }

    protected function transformDetail(array &$content): void
    {
        if ($this->detail !== '') {
            $content['detail'] = $this->detail;
        }
    }

    protected function transformSource(array &$content): void
    {
        if ($this->source !== null) {
            $content['source'] = $this->source->transform();
        }
    }
}
