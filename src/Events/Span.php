<?php

namespace PhilKra\Events;

use PhilKra\Helper\Encoding;
use PhilKra\Helper\Timer;
use PhilKra\Traits\Events\Stacktrace;

/**
 *
 * Spans
 *
 * @link https://www.elastic.co/guide/en/apm/server/master/span-api.html
 *
 */
class Span extends TraceableEvent implements \JsonSerializable {
    use Stacktrace;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \PhilKra\Helper\Timer
     */
    private $timer;

    /**
     * @var int
     */
    private $duration = 0;

    /**
     * @var string
     */
    private $action = null;

    /**
     * @var string
     */
    private $type = 'request';

    /**
     * @var string
     */
    private $subtype = 'request';

    /**
     * @var mixed array|null
     */
    private $stacktrace = [];

    /**
     * @param $name
     * @param EventBean $parent
     */
    public function __construct($name, EventBean $parent) {
        parent::__construct([]);
        $this->name  = trim($name);
        $this->timer = new Timer();
        $this->setParent($parent);
    }

    /**
     * Start the Timer
     *
     * @return void
     */
    public function start() {
        $this->timer->start();
    }

    /**
     * Stop the Timer
     *
     * @param integer|null $duration
     *
     * @return void
     */
    public function stop($duration = null) {
        $this->timer->stop();
        $this->duration = $duration ?: round($this->timer->getDurationInMilliseconds(), 3);
    }

    /**
    * Get the Event Name
    *
    * @return string
    */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the Span's Type
     *
     * @param $action
     */
    public function setAction($action) {
        $this->action = trim($action);
    }

    /**
     * Set the Spans' Action
     *
     * @param $type
     */
    public function setType($type) {
        $this->type = trim($type);
    }

    /**
     * Set the Spans' Action
     *
     * @param $subtype
     */
    public function setSubtype($subtype) {
        $this->subtype = trim($subtype);
    }

    /**
     * Set a complimentary Stacktrace for the Span
     *
     * @link https://www.elastic.co/guide/en/apm/server/master/span-api.html
     *
     * @param array $stacktrace
     */
    public function setStacktrace(array $stacktrace) {
        $this->stacktrace = $stacktrace;
    }

    /**
     * Serialize Span Event
     *
     * @link https://www.elastic.co/guide/en/apm/server/master/span-api.html
     *
     * @return array
     */
    public function jsonSerialize() {
        return [
            'span' => [
                'id'             => $this->getId(),
                'transaction_id' => $this->getParentId(),
                'trace_id'       => $this->getTraceId(),
                'parent_id'      => $this->getParentId(),
                'type'           => Encoding::keywordField($this->type),
                'subtype'        => Encoding::keywordField($this->subtype),
                'action'         => Encoding::keywordField($this->action),
                'context'        => $this->getContext(),
                'duration'       => $this->duration,
                'name'           => Encoding::keywordField($this->getName()),
                'stacktrace'     => $this->stacktrace,
                'sync'           => false,
                'timestamp'      => $this->getTimestamp(),
            ]
        ];
    }
}
