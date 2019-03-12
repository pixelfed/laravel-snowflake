<?php
namespace Pixelfed\Snowflake;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class Snowflake
{
    const TIMESTAMP_LEFT_SHIFT      = 22;
    const DATACENTER_ID_LEFT_SHIFT  = 17;
    const WORKER_ID_LEFT_SHIFT      = 12;
    private $epoch;
    private $lastTimestamp;
    private $datacenterId;
    private $sequence;
    private $workerId;

    public function __construct()
    {
        $this->epoch            = 1549756800000;
        $this->workerId         = 1;
        $this->datacenterId     = 1;
        $this->lastTimestamp    = $this->epoch;
        $this->sequence         = 0;
    }

    /**
     * Generate the 64bit unique id.
     *
     * @return integer
     *
     * @throw Exception
     */
    public function next()
    {
        $timestamp = $this->timestamp();

        if ($timestamp < $this->lastTimestamp) {
            $errorLog = "Couldn't generation snowflake id, os time is backwards. [last timestamp:{$this->lastTimestamp}]";
            Log::error($errorLog);
            throw new Exception($errorLog);
        }

        if ($timestamp === $this->lastTimestamp) {
            $this->sequence = $this->sequence + 1;
            if ($this->sequence > 4095) {
                usleep(1);
                $timestamp      = $this->timestamp();
                $this->sequence = 0;
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = $timestamp;

        return (($timestamp - $this->epoch) << self::TIMESTAMP_LEFT_SHIFT)
        | ($this->datacenterId << self::DATACENTER_ID_LEFT_SHIFT)
        | ($this->workerId << self::WORKER_ID_LEFT_SHIFT)
        | $this->sequence;
    }

    /**
     * Return the now unixtime.
     *
     * @return integer
     */
    protected function timestamp()
    {
        return round(microtime(true) * 1000);
    }
}
