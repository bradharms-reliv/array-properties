<?php

namespace Reliv\ArrayProperties\Exception;

/**
 * @author James Jervis - https://github.com/jerv13
 */
class ArrayPropertyException extends \Exception
{
    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param string          $message
     * @param array           $params
     * @param int             $code
     * @param \Exception|null $previous
     */
    public function __construct($message = "", $params = [], $code = 0, \Exception $previous = null)
    {
        $this->properties = $params;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
