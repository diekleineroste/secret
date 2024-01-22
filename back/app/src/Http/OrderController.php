<?php

namespace Http;

use \Services\DatabaseConnector;

class OrderController extends ApiBaseController
{
    protected \Doctrine\DBAL\Connection $db;

    public function __construct()
    {
        parent::__construct();

        // initiate DB connection
        $this->db = DatabaseConnector::getConnection(DB_NAME);
    }

    public function getOrders(): void
    {
        $userID = AuthController::getUserID();

        $sql = "SELECT * FROM orders WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$userID]);
        $orders = $qry->fetchAllAssociative();

        if (!$orders) $this->message(404, 'Not Found');
        else $this->message(200, null, json_encode($orders));
    }

    public function getOrder(string $orderID): void
    {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$orderID]);
        $orders = $qry->fetchAssociative();

        if (!$orders) $this->message(404, 'Not Found');
        else $this->message(200, null, json_encode($orders));
    }

    public function getOrdersAdmin(): void
    {
        $sql = "SELECT * FROM orders";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([]);
        $orders = $qry->fetchAllAssociative();
        if (!$orders) $this->message(404, 'Not Found');
        else $this->message(200, null, json_encode($orders));
    }

    public function deleteOrder(string $orderID): void
    {
        $userID = AuthController::getUserID();

        $sql = "DELETE * FROM orders WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$orderID, $userID]);

        if (!$qry) $this->message(404, 'Not Found');
        else $this->message(200, 'Order deleted!');
    }

    public function deleteOrderAdmin(string $orderID): void
    {
        $sql = "DELETE * FROM orders WHERE id = ? ";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$orderID]);

        if (!$qry) $this->message(404, 'Not Found');
        else $this->message(200, 'Order deleted!');
    }

    public function updateOrderAdmin(string $orderID): void
    {
        $formErrors = []; // The encountered form errors
        $allowedStatuses = ["ordered", "shipped", "arrived", "returned"];

        $status = isset($_POST['status']) ? $_POST['status'] : '';

        if (trim($status) === "") {
            $formErrors[] = "Vul een nieuwe status in!";
        } else if (!in_array(trim($status), $allowedStatuses)) {
            $formErrors[] = "Vul een geldige status in!";
        }

        if (count($formErrors) === 0) {
            $sql = "UPDATE orders SET status = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $qry = $stmt->executeQuery([$status, $orderID]);

            if (!$qry) $this->message(404, 'Not Found');
            else $this->message(200, 'Order status updated!');
        } else {
            $this->message(400, json_encode($formErrors));
        }
    }

    public function createOrder(): void
    {
        $formErrors = []; // The encountered form errors
        $inputJSON = file_get_contents('php://input');
        $_POST = json_decode($inputJSON, TRUE);

        $street_no = isset($_POST['street_no']) ? $_POST['street_no'] : '';
        $zip = isset($_POST['zip']) ? $_POST['zip'] : '';
        $country = isset($_POST['country']) ? $_POST['country'] : '';
        $city = isset($_POST['city']) ? $_POST['city'] : '';

        $items = isset($_POST['items']) ? $_POST['items'] : '';

        if (count($items) < 1) {
            $formErrors[] = "Winkelwagen kan niet leeg zijn!";
        }

        if (trim($street_no) === "") {
            $formErrors[] = "Vul een straat in!";
        }

        if (trim($zip) === "") {
            $formErrors[] = "Vul een postcode in!";
        }

        if (trim($city) === "") {
            $formErrors[] = "Vul een stad in!";
        }

        if (trim($country) === "") {
            $formErrors[] = "Vul een land in!";
        }

        if (count($formErrors) === 0) {
            $validItems = true;
            $userID = AuthController::getUserID();

            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $sql = "SELECT * FROM orders WHERE artpiece_id = ?";
                $stmt = $this->db->prepare($sql);
                $qry = $stmt->executeQuery([$item["id"]]);
                $order = $qry->fetchAssociative();

                $sql = "SELECT * FROM artpieces WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $qry = $stmt->executeQuery([$item["id"]]);
                $artpiece = $qry->fetchAssociative();

                if (!$artpiece) {
                    $this->message(404, 'Not Found');
                    $validItems = false;
                    return;
                }

                if ($order) {
                    $this->message(400, 'Item already sold');
                    $validItems = false;
                    return;
                }
            };

            if ($validItems) {
                $sql = "INSERT INTO orders_adresses (street_no, zip, city, country) VALUES (?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->executeQuery([$street_no, $zip, $city, $country]);
                $addressID = $this->db->lastInsertId();

                $sql = "INSERT INTO orders (status, user_id, order_adress_id, created_at) VALUES (?,?,?,?)";
                $stmt = $this->db->prepare($sql);
                $stmt->executeQuery(["ordered", $userID, $addressID, date('Y-m-d H:i:s')]);
                $orderID = $this->db->lastInsertId();

                for ($i = 0; $i < count($items); $i++) {
                    $item = $items[$i];
                    $sql = "INSERT INTO order_has_artpiece (order_id, artpiece_id) VALUES (?,?)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->executeQuery([$orderID, $item["id"]]);
                }

                $this->message(201, 'Created');
            } else {
                $this->message(400, "Invalid items in cart!");
            }
        } else {
            $this->message(400, json_encode($formErrors));
        }
    }
}
