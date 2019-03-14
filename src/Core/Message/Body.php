<?php

/*
 * This file is part of the Imap PHP package.
 * (c) Clivern <hello@clivern.com>
 */

namespace Clivern\Imap\Core\Message;

use Clivern\Imap\Core\Connection;

/**
 * Body Class.
 */
class Body
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var int
     */
    protected $message_number;

    /**
     * @var int
     */
    protected $message_uid;

    /**
     * @var int
     */
    protected $encoding;

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @var string
     */
    protected $html = '';

    /**
     * @var string
     */
    protected $plain = '';

    /**
     * Class Constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Config Body.
     *
     * @param int $message_number
     * @param int $message_uid
     *
     * @return Body
     */
    public function config($message_number, $message_uid)
    {
        $this->message_number = $message_number;
        $this->message_uid = $message_uid;

        return $this;
    }

    /**
     * Get Message.
     *
     * @param int $option
     *
     * @return string
     */
    public function getMessage($option = 2)
    {
        $html = $this->getHtml();
        if(!$html) {
            return $this->getPlain();
        }
        return $html;
    }

    /**
     * Get Message.
     *
     * @param int $option
     *
     * @return string
     */
    public function getPlain($option = 2)
    {
        if (!empty($this->plain)) {
            return $this->plain;
        } 
        
        $structure = imap_fetchstructure($this->connection->getStream(), $this->message_number); 

        if (isset($structure->parts) && \is_array($structure->parts) && isset($structure->parts[1])) {
            foreach($structure->parts as $i => $part) {
                $message = imap_fetchbody($this->connection->getStream(), $this->message_number, $i + 1);
                if($part->subtype == "PLAIN") {
                    $this->plain = $message;
                    $this->encoding = $part->encoding; 
                    break;
                } 
            } 

            if (3 === $part->encoding) {
                $this->plain = imap_base64($this->plain);
            } elseif (1 === $part->encoding) {
                $this->plain = imap_8bit($this->plain); 
            } else {
                $this->plain = imap_qprint($this->plain);
            }
        } else {
            $this->plain = imap_body($this->connection->getStream(), $this->message_number, $option);
        }

        return $this->plain;
    }

    /**
     * Get Message.
     *
     * @param int $option
     *
     * @return string
     */
    public function getHtml($option = 2)
    {
        if (!empty($this->html)) {
            return $this->html;
        } 
        
        $structure = imap_fetchstructure($this->connection->getStream(), $this->message_number); 

        if (isset($structure->parts) && \is_array($structure->parts) && isset($structure->parts[1])) {
            foreach($structure->parts as $i => $part) {
                $message = imap_fetchbody($this->connection->getStream(), $this->message_number, $i + 1);
                if($part->subtype == "HTML") {
                    $this->html = $message;
                    $this->encoding = $part->encoding; 
                    break;
                } 
            } 

            if (3 === $part->encoding) {
                $this->html = imap_base64($this->html);
            } elseif (1 === $part->encoding) {
                $this->html = imap_8bit($this->html); 
            } else {
                $this->html = imap_qprint($this->html);
            }
        } else {
            $this->html = imap_body($this->connection->getStream(), $this->message_number, $option);
        }

        return $this->html;
    }

    /**
     * Get Encoding.
     *
     * @return int
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
}
