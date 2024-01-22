<?php

namespace Http;

use \Services\DatabaseConnector;

class ArtistController extends ApiBaseController
{
    protected \Doctrine\DBAL\Connection $db;

    public function __construct()
    {
        parent::__construct();

        // initiate DB connection
        $this->db = DatabaseConnector::getConnection(DB_NAME);
    }

    public function getArtists()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : -1;
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        if ($page < 0) return $this->message(400, 'Page parameter is required');

        if (!is_numeric($page)) return $this->message(400, 'Invalid page parameter');

        $pageSize = 20;
        $skip = $page * $pageSize;

        $sql = 'SELECT users.id, users.username, users.email, users.profile_picture, users.bio
        FROM users
        WHERE users.username LIKE ?
        LIMIT ' . $pageSize . ' OFFSET ' . $skip;

        $result = $this->db->executeQuery($sql, ["%" . $search . "%"]);
        $taskRows = $result->fetchAllAssociative();
        $this->message(200, null, json_encode($taskRows));

    }
    public function getPopularArtists()
    {
        $sql = "SELECT users.id, users.username, users.email, users.profile_picture, users.bio, SUM(artpieces.likes) AS total_likes
        FROM users
        LEFT JOIN artpieces ON users.id = artpieces.user_id
        GROUP BY users.id
        ORDER BY total_likes DESC";

        $result = $this->db->executeQuery($sql, []);
        $taskRows = $result->fetchAllAssociative();
        $this->message(200, null, json_encode($taskRows));
    }

    public function getArtistsArtpieces($artistID)
    {
        $sql = "SELECT artpieces.* FROM artpieces WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$artistID]);
        $artpiece = $qry->fetchAllAssociative();

        if (!$artpiece) $this->message(404, 'Not Found');
        else $this->message(200, null, json_encode($artpiece));
    }

    public function getArtist($artistID)
    {
        $sql = "SELECT users.username, users.email, users.profile_picture, users.bio FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$artistID]);
        $artpiece = $qry->fetchAssociative();

        if (!$artpiece) $this->message(404, 'Not Found');
        else $this->message(200, null, json_encode($artpiece));
    }
}
