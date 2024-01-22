<?php

namespace Http;

use \Services\DatabaseConnector;

class CommentController extends ApiBaseController
{
    protected \Doctrine\DBAL\Connection $db;

    public function __construct()
    {
        parent::__construct();

        // initiate DB connection
        $this->db = DatabaseConnector::getConnection(DB_NAME);
    }

    public function getComments(string $artID)
    {
        $sql = "SELECT * FROM comments WHERE artpiece_id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$artID]);
        $artpiece = $qry->fetchAllAssociative();

        if (!$artpiece) $this->message(404, 'Not Found');
        else $this->message(200, null, json_encode($artpiece));
    }

    public function deleteComment(string $commentID)
    {
        $userID = AuthController::getUserID();

        $sql = "DELETE * FROM comments WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$commentID, $userID]);

        if (!$qry) $this->message(404, 'Not Found');
        else $this->message(200, 'Comment deleted!');
    }

    public function deleteCommentAdmin(string $commentID)
    {
        $userID = AuthController::getUserID();

        $sql = "DELETE * FROM comments WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$commentID, $userID]);

        if (!$qry) $this->message(404, 'Not Found');
        else $this->message(200, 'Comment deleted!');
    }

    public function updateComment(string $commentID)
    {
        $formErrors = []; // The encountered form errors
        $inputJSON = file_get_contents('php://input');
        $_POST = json_decode($inputJSON, TRUE);

        $content = isset($_POST['content']) ? $_POST['content'] : '';

        if (trim($content) === "") {
            $formErrors[] = "Vul een comment in!";
        }

        if (count($formErrors) === 0) {
            $userID = AuthController::getUserID();

            $sql = "UPDATE comments SET content = ? WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $qry = $stmt->executeQuery([$content, $commentID, $userID]);

            if (!$qry) $this->message(404, 'Not Found');
            else $this->message(200, 'Comment updated!');
        } else {
            $this->message(400, json_encode($formErrors));
        }
    }

    public function createComments(string $artID)
    {
        $formErrors = []; // The encountered form errors
        $inputJSON = file_get_contents('php://input');
        $_POST = json_decode($inputJSON, TRUE);

        $content = isset($_POST['content']) ? $_POST['content'] : '';

        if (trim($content) === "") {
            $formErrors[] = "Vul een comment in!";
        }

        if (count($formErrors) === 0) {
            $userID = AuthController::getUserID();

            $sql = "SELECT * FROM comments WHERE artpiece_id = ?";
            $stmt = $this->db->prepare($sql);
            $qry = $stmt->executeQuery([$artID]);
            $artpiece = $qry->fetchAssociative();

            if (!$artpiece) $this->message(404, 'Not Found');

            else {
                $sql = "INSERT INTO comments (content, likes, artpiece_id, created_at, user_id) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->executeQuery([$content, 0, $artID, date('Y-m-d H:i:s'), $userID]);

                $this->message(201, 'Created');
            }
        } else {
            $this->message(400, json_encode($formErrors));
        }
    }
}
