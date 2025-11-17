<?php
namespace core\extension\socket\ratchet\session\serialize;

interface HandlerInterface {
    /**
     * @param array
     * @return string
     */
    function serialize(array $data);

    /**
     * @param string
     * @return array
     */
    function unserialize($raw);
}
