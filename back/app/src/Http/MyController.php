<?php

namespace Http;
use \Services\DatabaseConnector;

class MyController extends ApiBaseController
{
    protected \Doctrine\DBAL\Connection $db;

    public function __construct()
    {
        parent::__construct();

        // initiate DB connection
        $this->db = DatabaseConnector::getConnection(DB_NAME);
    }

    public function overview()
    {
        $taskRows = $this->db->fetchAllAssociative('SELECT * FROM products', []);
        echo json_encode(['products' => $taskRows]);
    }
}