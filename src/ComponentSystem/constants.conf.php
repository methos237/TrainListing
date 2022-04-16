<?php
/**
 * Only store non-secret constants here in plain text.
 * For secrets, add to environmental variables and use
 * define('CONST_NAME', env('CONST_NAME') syntax.
 */

/**
 * The various HTTP request methods
 * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
 */
const REQUEST_METHOD_GET = "GET";
const REQUEST_METHOD_POST = "POST";
const REQUEST_METHOD_HEAD = "HEAD";
const REQUEST_METHOD_PUT = "PUT";
const REQUEST_METHOD_DELETE = "DELETE";
const REQUEST_METHOD_CONNECT = "CONNECT";
const REQUEST_METHOD_OPTIONS = "OPTIONS";
const REQUEST_METHOD_TRACE = "TRACE";
const REQUEST_METHOD_PATCH = "PATCH";