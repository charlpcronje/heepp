<?php
class Connection {
    private $connection = [];

    public function __construct($connection = null) {
        switch($connection) {
            case 'fgx':
                $this->connection = $this->fgx();
                break;
            default:
                $this->connection = $this->fgx();
                break;
        }
    }

    public function getConnection($connection = null) {
        return $this->connection;
    }

    public function getConnections() {
        $connections[] = (object)[
            'name'  =>'fgx',
            'alias' =>'FGX Heepp / Console'];
        return $connections;
    }

    private function fgx() {
        return (object)[
            'host'     => '127.0.0.1',
            'username' => 'root',
            'password' => '',
            'database' => 'fgx'
        ];

        /* return (object)[
            'host'     => 'dedi73.cpt1.host-h.net',
            'username' => 'corefdaavr_1',
            'password' => '0K4N72NEkVFbY1NrBM2i',
            'database' => 'corefdaavr_db1'
        ]; */
    }
}
