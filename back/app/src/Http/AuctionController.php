<?php

namespace Http;

use \Services\DatabaseConnector;

class AuctionController extends ApiBaseController
{
    protected \Doctrine\DBAL\Connection $db;

    public function __construct()
    {
        parent::__construct();

        // initiate DB connection
        $this->db = DatabaseConnector::getConnection(DB_NAME);
    }

    public function getAuction(string $artID)
    {
        //$sql = "SELECT * FROM artpieces WHERE id = ? LEFT JOIN auctions ON artpieces.auction_id = auctions.id";
        $sql = "SELECT auctions.* FROM artpieces JOIN auctions ON artpieces.auction_id = auctions.id JOIN bids ON bids.auction_id = auctions.id WHERE artpieces.id = ? ";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$artID]);
        $auction = $qry->fetchAssociative();

        if (!$auction) $this->message(404, 'Not Found');
        else $this->message(200, null, json_encode($auction));
    }

    public function createAuction(string $artID)
    {
        $formErrors = []; // The encountered form errors
        $inputJSON = file_get_contents('php://input');
        $_POST = json_decode($inputJSON, TRUE);

        $enddate = isset($_POST['content']) ? $_POST['content'] : '';

        if (trim($enddate) === "") {
            $formErrors[] = "Vul een eind datum in!";
        }

        if (count($formErrors) === 0) {
            $userID = AuthController::getUserID();

            $sql = "SELECT * FROM artpieces WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $qry = $stmt->executeQuery([$artID]);
            $artpiece = $qry->fetchAssociative();

            if (!$artpiece) $this->message(404, 'Not Found');
            else {
                $dateObject = new \DateTime($enddate);
                $formattedDate = $dateObject->format('Y-m-d H:i:s');

                $sql = "INSERT INTO auctions (end_date, created_at) VALUES (?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->executeQuery([$formattedDate, date('Y-m-d H:i:s')]);
                $auctionID = $this->db->lastInsertId();

                $sql = "UPDATE artpieces SET auction_id = ? WHERE id = ? AND user_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->executeQuery([$auctionID, $artID, $userID]);


                $this->message(201, 'Created');
            }
        } else {
            $this->message(400, json_encode($formErrors));
        }
    }

    public function createBid(string $artID)
    {
        $formErrors = []; // The encountered form errors
        $inputJSON = file_get_contents('php://input');
        $_POST = json_decode($inputJSON, TRUE);

        $price = isset($_POST['amount']) ? $_POST['amount'] : '';

        if (trim($price) === "") {
            $formErrors[] = "Vul een geldige prijs in!";
        }

        if (count($formErrors) === 0) {
            $userID = AuthController::getUserID();

            $sql = "SELECT * FROM artpieces WHERE id = ? LEFT JOIN auctions ON artpieces.auction_id = auctions.id";
            $stmt = $this->db->prepare($sql);
            $qry = $stmt->executeQuery([$artID]);
            $artpiece = $qry->fetchAssociative();

            if (!$artpiece) $this->message(404, 'Not Found');
            else {
                if ($artpiece['end_date'] < date('Y-m-d H:i:s'))
                    $this->message(400, 'Auction has ended');

                else {
                    $sql = "SELECT * FROM bids WHERE auction_id = ? ORDER BY created_at DESC";
                    $stmt = $this->db->prepare($sql);
                    $qry = $stmt->executeQuery([$artID]);
                    $bid = $qry->fetchAssociative();

                    if ($bid['price'] >= $price)
                        $this->message(400, 'Bid is too low');
                    else {
                        $sql = "INSERT INTO bids (price, created_at, auction_id, user_id) VALUES (?, ?, ?, ?)";
                        $stmt = $this->db->prepare($sql);
                        $stmt->executeQuery([$price, date('Y-m-d H:i:s'), $artpiece['auction_id'], $userID]);

                        $this->message(201, 'Created');
                    }
                }
            }
        } else {
            $this->message(400, json_encode($formErrors));
        }
    }
}
