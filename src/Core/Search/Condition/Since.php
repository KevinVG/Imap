<?php

/*
 * This file is part of the Imap PHP package.
 * (c) Clivern <hello@clivern.com>
 */

namespace Clivern\Imap\Core\Search\Condition;

use Clivern\Imap\Core\Search\Contract\Condition;

/**
 * Since Class.
 */
class Since implements Condition
{
    /**
     * @var string
     */
    protected $data;

    /**
     * Class Constructor.
     *
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Query String.
     *
     * @return string
     */
    public function __toString()
    {
        return "SINCE \"{$this->data}\"";
    }
}
