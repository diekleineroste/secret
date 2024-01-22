<?php

namespace Http;

use JwtHandeler;
use \Services\DatabaseConnector;

class ArtpieceController extends ApiBaseController
{
    protected \Doctrine\DBAL\Connection $db;

    public function __construct()
    {
        parent::__construct();

        // initiate DB connection
        $this->db = DatabaseConnector::getConnection(DB_NAME);
    }

    public function getArtpieces()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : -1;
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
        $order = isset($_GET['order']) ? $_GET['order'] : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        $minPrice = isset($_GET['minPrice']) ? $_GET['minPrice'] : -1;
        $maxPrice = isset($_GET['maxPrice']) ? $_GET['maxPrice'] : 999999999;

        if ($page < 0) return $this->message(400, 'Page parameter is required');
        if ($minPrice > $maxPrice) return $this->message(400, 'Maximum price has to be higher then minimum!');

        if (!is_numeric($page)) return $this->message(400, 'Invalid page parameter');

        $pageSize = 20;
        $skip = $page * $pageSize;

        if (!in_array(strtoupper($order), ['ASC', 'DESC'])) {
            $order = 'DESC';
        }

        // Construct the SQL query with ORDER BY clause based on the number of comments
        if ($sort === "comments") {
            $sql = 'SELECT artpieces.*, users.username, users.email, users.profile_picture, users.bio, COUNT(comments.id) AS comment_count  FROM artpieces
                    LEFT JOIN users ON artpieces.user_id = users.id 
                    LEFT JOIN comments ON artpieces.id = comments.artpiece_id 
                    WHERE artpieces.category LIKE ?
                    AND artpieces.name LIKE ? 
                    AND artpieces.price < ? AND artpieces.price > ?
                    GROUP BY artpieces.id 
                    ORDER BY comment_count ' . strtoupper($order) . ' 
                    LIMIT ' . $pageSize . ' OFFSET ' . $skip;
        } else if ($sort === "price") {
            $sql = 'SELECT artpieces.*, users.username, users.email, users.profile_picture, users.bio FROM artpieces
                    LEFT JOIN users ON artpieces.user_id = users.id 
                    WHERE artpieces.category LIKE ? 
                    AND artpieces.name LIKE ? 
                    AND artpieces.price < ? AND artpieces.price > ?
                    ORDER BY price ' . strtoupper($order) . ' LIMIT ' . $pageSize . ' OFFSET ' . $skip;
        } else if ($sort === "likes") {
            $sql = 'SELECT artpieces.*, users.username, users.email, users.profile_picture, users.bio FROM artpieces
                    LEFT JOIN users ON artpieces.user_id = users.id
                    WHERE artpieces.category LIKE ? 
                    AND artpieces.name LIKE ? 
                    AND artpieces.price < ? AND artpieces.price > ?
                    ORDER BY likes ' . strtoupper($order) . ' LIMIT ' . $pageSize . ' OFFSET ' . $skip;
        } else {
            $sql = 'SELECT artpieces.*, users.username, users.email, users.profile_picture, users.bio FROM artpieces
                    LEFT JOIN users ON artpieces.user_id = users.id
                    WHERE artpieces.category LIKE ?
                    AND artpieces.name LIKE ?
                    AND artpieces.price < ? AND artpieces.price > ?
                    LIMIT ' . $pageSize . ' OFFSET ' . $skip;
        }

        $result = $this->db->executeQuery($sql, ['%' . $filter . '%', "%" . $search . "%", $maxPrice, $minPrice]);
        $taskRows = $result->fetchAllAssociative();
        $this->message(200, null, json_encode($taskRows));
    }

    public function createArtpieces()
    {
        $formErrors = []; // The encountered form errors
        $allowedCategories = ['pictures', 'sculptures', 'paintings', 'drawings'];
        $allowedtypes = ['buynow', 'auction'];
        //$inputJSON = file_get_contents('php://input');
        //$_POST = json_decode($inputJSON, TRUE);

        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $category = isset($_POST['category']) ? $_POST['category'] : '';
        $widthCm = isset($_POST['width_in_cm']) ? $_POST['width_in_cm'] : '';
        $heightCm = isset($_POST['height_in_cm']) ? $_POST['height_in_cm'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : '';

        $artpiece_image = isset($_FILES['profilePicture']) ? $_FILES['profilePicture'] : '';

        if (trim($name) === "") {
            $formErrors[] = "Vul een naam in!";
        }

        if (trim($description) === "") {
            $formErrors[] = "Vul een beschrijving in!";
        }

        if (trim($widthCm) === "") {
            $formErrors[] = "Vul een breedte in!";
        } else if (!is_numeric($widthCm) || $widthCm < 0) {
            $formErrors[] = "Vul een geldige breedte in!";
        }

        if (trim($heightCm) === "") {
            $formErrors[] = "Vul een hoogte in!";
        } else if (!is_numeric($heightCm) || $heightCm < 0) {
            $formErrors[] = "Vul een geldige hoogte in!";
        }

        if (trim($price) === "") {
            $formErrors[] = "Vul een geldige prijs in!";
        } else if ($price < 0) {
            $formErrors[] = "Vul een geldige prijs in!";
        }

        if (!in_array($type, $allowedtypes)) {
            $formErrors[] = "Vul een geldig type in!";
        }

        if (!in_array($category, $allowedCategories)) {
            $formErrors[] = "Vul een geldige categorie in!";
        }

        if (count($formErrors) === 0) {
            $userID = AuthController::getUserID();

            $sql = "INSERT INTO artpieces (name, description, category, width_in_cm, height_in_cm, price, type, likes, user_id, created_at, auction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->executeQuery([$name, $description, $category, $widthCm, $heightCm, $price, $type, 0, $userID, date('Y-m-d H:i:s'), null]);

            $artpieceID = $this->db->lastInsertId();
            $fileName =  $artpieceID . '.jpg';

            $sql = "INSERT INTO images (url, artpiece_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->executeQuery([$fileName, $artpieceID]);

            if (move_uploaded_file($artpiece_image['tmp_name'], IMG_DIR . '/' . $fileName)) {
                $this->message(201, 'Created');
            } else {
                $this->message(500, 'Internal Server Error');
            }
        } else {
            $this->message(400, json_encode($formErrors));
        }
    }

    public function deleteArtpiece(string $artID): void
    {
        $sql = "DELETE * FROM artpieces WHERE id = ? AND WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$artID]);

        if (!$qry) $this->message(404, 'Not Found');
        else $this->message(200, 'Artpiece deleted!');
    }

    public function deleteArtpieceAdmin($artID): void
    {
        $sql = "DELETE * FROM artpieces WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$artID]);

        if (!$qry) $this->message(404, 'Not Found');
        else $this->message(200, 'Artpiece deleted!');
    }

    public function updateArtpiece(): void
    {
    }

    public function getArtpiece(string $artID): void
    {
        $sql = "SELECT * FROM artpieces WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$artID]);
        $artpiece = $qry->fetchAssociative();

        if (!$artpiece) $this->message(404, 'Not Found');
        else $this->message(200, null, json_encode($artpiece));
    }

    public function likeArtpiece(string $artID): void
    {
        $userID = AuthController::getUserID();

        $sql = "SELECT * FROM artpieces WHERE id =?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$artID]);
        $artpiece = $qry->fetchAssociative();

        if (!$artpiece) {
            $this->message(404, 'Not Found');
            return;
        }

        $sql = "SELECT * FROM likes WHERE user_id =? AND artpiece_id =?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$userID, $artID]);
        $result = $qry->fetchAssociative();

        if ($result) {
            $this->message(400, 'Already liked!');
            return;
        }

        $sql = "INSERT INTO likes (user_id, artpiece_id) VALUES (?,?)";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$userID, $artID]);


        $sql = "UPDATE artpieces SET likes = likes + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$artID]);

        if (!$qry) $this->message(404, 'Not Found');
        else $this->message(200, "Liked!");
    }

    public function unlikeArtpiece(string $artID): void
    {
        $userID = AuthController::getUserID();

        $sql = "SELECT * FROM artpieces WHERE id =?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$artID]);
        $artpiece = $qry->fetchAssociative();

        if (!$artpiece) {
            $this->message(404, 'Not Found');
            return;
        }

        $sql = "SELECT * FROM likes WHERE user_id =? AND artpiece_id =?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$userID, $artID]);
        $result = $qry->fetchAssociative();

        if ($result) {
            $this->message(400, 'Like not found!');
            return;
        }

        $sql = "DELETE * FROM likes (user_id, artpiece_id) VALUES (?,?)";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$userID, $artID]);

        $sql = "UPDATE artpieces SET likes = likes - 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$artID]);

        if (!$qry) $this->message(404, 'Not Found');
        else $this->message(200, "Liked!");
    }
}
