<?php
namespace Ratchet\WebSocket;

/**
 * WebSocket Server Interface
 */
interface WsServerInterface {
    /**
     * If any component in a stack supports a WebSocket sub-protocol return each supported in an array
     * @return array
     */
    function getSubProtocols();
}
