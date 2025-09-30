<?php

declare(strict_types=1);

namespace VM\Infrastructure\Http\Constant;

class HttpConstant
{
    public const string HEADER_CONTENT_TYPE = 'Content-Type';
    public const string CONTENT_TYPE_JSON = 'application/json';
    public const string CONTENT_TYPE_HTML = 'text/html';
    public const string CONTENT_TYPE_FORM = 'application/x-www-form-urlencoded';
    public const string CONTENT_TYPE_MULTIPART = 'multipart/form-data';
    public const string METHOD_GET = 'GET';
    public const string METHOD_POST = 'POST';
    public const string METHOD_PUT = 'PUT';
    public const string METHOD_DELETE = 'DELETE';
    public const string METHOD_PATCH = 'PATCH';
    public const string METHOD_OPTIONS = 'OPTIONS';
    public const string METHOD_HEAD = 'HEAD';
    public const int STATUS_OK = 200;
    public const int STATUS_CREATED = 201;
    public const int STATUS_NO_CONTENT = 204;
    public const int STATUS_BAD_REQUEST = 400;
    public const int STATUS_UNAUTHORIZED = 401;
    public const int STATUS_FORBIDDEN = 403;
    public const int STATUS_NOT_FOUND = 404;
    public const int STATUS_METHOD_NOT_ALLOWED = 405;
    public const int STATUS_INTERNAL_SERVER_ERROR = 500;
}
