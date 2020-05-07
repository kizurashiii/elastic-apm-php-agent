<?php

namespace PhilKra\Events;

interface EventFactoryInterface {
    /**
     * Creates a new error.
     *
     * @param \Exception $throwable
     * @param array      $contexts
     *
     * @return Error
     */
    public function newError(\Exception $throwable, array $contexts, Transaction $parent = null);

    /**
     * Creates a new transaction
     *
     * @param $name
     * @param array  $contexts
     *
     * @return Transaction
     */
    public function newTransaction($name, array $contexts, $start = null);

    /**
     * Creates a new Span
     *
     * @param string    $name
     * @param EventBean $parent
     *
     * @return Span
     */
    public function newSpan($name, EventBean $parent);

    /**
     * Creates a new Metricset
     *
     * @link https://www.elastic.co/guide/en/apm/server/7.3/metricset-api.html
     * @link https://github.com/elastic/apm-server/blob/master/docs/spec/metricsets/metricset.json
     *
     * @param array $set, k-v pair ['sys.avg.load' => 89]
     * @param array $tags, Default []
     *
     * @return Metricset
     */
    public function newMetricset(array $set, array $tags = []);

}
