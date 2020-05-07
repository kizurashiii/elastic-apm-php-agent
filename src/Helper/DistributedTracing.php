<?php

namespace PhilKra\Helper;

use PhilKra\Exception\InvalidTraceContextHeaderException;

class DistributedTracing {

    /**
     * Supporting Elastic's Traceparent Header until W3C goes GA
     *
     * @link https://www.w3.org/TR/trace-context/#header-name
     */
    const HEADER_NAME = 'ELASTIC-APM-TRACEPARENT';

    /**
     * @link https://www.w3.org/TR/trace-context/#version
     */
    const VERSION = '00';

    /**
     * @var string
     */
    private $traceId;

    /**
     * @var string
     */
    private $parentId;

    /**
     * @var string
     */
    private $traceFlags;

    /**
     * @param $traceId
     * @param $parentId
     * @param $traceFlags
     */
    public function __construct( $traceId,  $parentId,  $traceFlags = '00') {
        $this->traceId = $traceId;
        $this->parentId = $parentId;
        $this->traceFlags = $traceFlags;
    }

    /**
     * @return string
     */
    public function getTraceId() {
        return $this->traceId;
    }

    /**
     * @return string
     */
    public function getParentId() {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function getTraceFlags() {
        return $this->traceFlags;
    }

    /**
     * @param $traceFlags
     */
    public function setTraceFlags($traceFlags) {
        $this->traceFlags = $traceFlags;
    }

    /**
     * Check if the Header Value is valid
     *
     * @link https://www.w3.org/TR/trace-context/#version-format
     *
     * @param $header
     *
     * @return bool
     */
    public static function isValidHeader($header) {
        return preg_match('/^'.self::VERSION.'-[\da-f]{32}-[\da-f]{16}-[\da-f]{2}$/', $header) === 1;
    }

    /**
     * @param $header
     * @return DistributedTracing
     * @throws InvalidTraceContextHeaderException
     */
    public static function createFromHeader($header){
        if (!self::isValidHeader($header)) {
            throw new InvalidTraceContextHeaderException("InvalidTraceContextHeaderException");
        }
        $parsed = explode('-', $header);
        return new self($parsed[1], $parsed[2], $parsed[3]);
    }

    /**
     * Get Distributed Tracing Id
     *
     * @return string
     */
    public function __toString() {
        return sprintf(
            '%s-%s-%s-%s',
            self::VERSION,
            $this->getTraceId(),
            $this->getParentId(),
            $this->getTraceFlags()
        );
    }
}