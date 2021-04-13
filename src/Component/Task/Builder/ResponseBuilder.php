<?php

namespace App\Component\Task\Builder;

use HttpResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;

class ResponseBuilder
{
    private string $contentType;

    private string $attachmentFilename = 'report';

    public static function create()
    {
        return new self();
    }

    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function build()
    {
        HttpResponse::getContentType()
    }
}