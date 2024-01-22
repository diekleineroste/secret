<?php

namespace Http;

use JwtHandeler;
use \Services\DatabaseConnector;
use \Services\SMTPConnector;

class AuthController extends ApiBaseController
{
    protected \Doctrine\DBAL\Connection $db;
    protected \Services\SMTPConnector $smtpConnector;
    protected \Twig\Environment $twig;

    public function __construct()
    {
        parent::__construct();

        // initiate DB connection
        $this->db = DatabaseConnector::getConnection(DB_NAME);
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../resources/templates');
        $this->twig = new \Twig\Environment($loader);
        $function = new \Twig\TwigFunction('url', function ($path) {
            return BASE_PATH . $path;
        });
        $this->twig->addFunction($function);
        $this->smtpConnector = new SMTPConnector($this->twig);
    }

    public function login(): void
    {
        $inputJSON = file_get_contents('php://input');
        $_POST = json_decode($inputJSON, TRUE);

        $username = $_POST['username'] ?? false;
        $password = $_POST['password'] ?? false;

        if (($username !== false) && ($password !== false)) {

            // retrieve $user and $roles from database
            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $this->db->prepare($sql);
            $query = $stmt->executeQuery([$username]);

            $user = $query->fetchAssociative();

            if (($user !== false) && (password_verify($password, $user['password']))) {
                $roles = $user['role'];

                // generate refresh token and send as http-only cookie
                $refreshToken = JwtHandeler::generateJWTToken($user['id'], $user['username'], null, SECRET_KEY, REFRESH_TOKEN_LIFETIME);
                setcookie('refreshToken', $refreshToken, time() + REFRESH_TOKEN_LIFETIME, "", "", false, true);

                // add refresh token to the database
                $stmt = $this->db->prepare('INSERT INTO refresh_tokens (token, user_id) VALUES (?, ?)');
                $stmt->executeStatement([$refreshToken, $user['id']]);

                $user['password'] = null;

                // generate access token and send in HTTP response body
                $jwtToken = JwtHandeler::generateJWTToken($user['id'], $user['username'], $roles, SECRET_KEY, ACCESS_TOKEN_LIFETIME);
                $this->message(200, null, json_encode(['accessToken' => $jwtToken, 'userData' => $user])); // 401 Unauthorized

            } else {
                $this->message(401, 'Invalid credentials'); // 401 Unauthorized
            }
        } else {
            $this->message(400, 'Malformed request.'); // 400 Bad Request
        }
    }

    public function refreshToken(): void
    {
        $refreshToken = $_COOKIE['refreshToken'] ?? false;
        if ($refreshToken) {
            try {
                $decodedPayload = JwtHandeler::validateJWTToken($refreshToken, SECRET_KEY);
                $userId = $decodedPayload->sub;

                // verify & delete refresh token in database
                $stmt = $this->db->prepare('DELETE FROM refresh_tokens WHERE token = ? AND user_id = ?');
                $count = $stmt->executeStatement([$refreshToken, $userId]);

                if ($count > 0) { // the token was still in the table
                    $sql = "SELECT * FROM users WHERE iod = ?";
                    $stmt = $this->db->prepare($sql);
                    $query = $stmt->executeQuery([$userId]);

                    $user = $query->fetchAssociative();
                    $roles = $user['role'];

                    // generate refresh token and send as http-only cookie
                    $refreshToken = JwtHandeler::generateJWTToken($user['id'], $user['username'], null, SECRET_KEY, REFRESH_TOKEN_LIFETIME);
                    setcookie('refreshToken', $refreshToken, time() + REFRESH_TOKEN_LIFETIME, "", "", false, true);

                    // add refresh token to the database
                    $stmt = $this->db->prepare('INSERT INTO refresh_tokens (token, user_id) VALUES (?, ?)');
                    $stmt->executeStatement([$refreshToken, $user['id']]);

                    // generate access token and send in HTTP response body
                    $jwtToken = JwtHandeler::generateJWTToken($user['id'], $user['username'], $roles, SECRET_KEY, ACCESS_TOKEN_LIFETIME);

                    $this->message(200, null, json_encode(['accessToken' => $jwtToken])); // 401 Unauthorized

                    return; // return, won't do 401
                }
            } catch (\Exception $e) {
                // empty, will do 401
            }
        }
        $this->message(401, 'Invalid credentials'); // 401 Unauthorized
    }

    public function register(): void
    {
        $formErrors = []; // The encountered form errors
        $supportedExtensions = ['jpg', 'jpeg', 'png'];
        //$inputJSON = file_get_contents('php://input');
        //$_POST = json_decode($inputJSON, TRUE);

        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $phonenumber = isset($_POST['phonenumber']) ? $_POST['phonenumber'] : '';
        $date_of_birth = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';
        $bio = isset($_POST['bio']) ? $_POST['bio'] : '';

        $street_no = isset($_POST['street_no']) ? $_POST['street_no'] : '';
        $zip = isset($_POST['zip']) ? $_POST['zip'] : '';
        $country = isset($_POST['country']) ? $_POST['country'] : '';
        $city = isset($_POST['city']) ? $_POST['city'] : '';

        $profile_picture = isset($_FILES['profilePicture']) ? $_FILES['profilePicture'] : '';

        if (trim($username) === "") {
            $formErrors[] = "Vul een gebruikersnaam in!";
        }

        if (trim($email) === "") {
            $formErrors[] = "Vul een email adres in!";
        }

        if (trim($password) === "") {
            $formErrors[] = "Vul een wachtwoord in!";
        }

        if (trim($phonenumber) === "") {
            $formErrors[] = "Vul een telefoonnummer in!";
        }

        if (trim($date_of_birth) === "") {
            $formErrors[] = "Vul een geboortedatum in!";
        }

        if (empty($profile_picture)) {
            $formErrors[] = "Selecteer een profielfoto in!";
        } else {
            $fileExtension = pathinfo($profile_picture['name'], PATHINFO_EXTENSION);
            if (!in_array($fileExtension, $supportedExtensions)) {
                $formErrors[] = "Profielfoto moet een van de volgende extensies hebben: " . implode(', ', $supportedExtensions);
            }
        }

        if (trim($bio) === "") {
            $formErrors[] = "Vul een bio in!";
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

        if (!preg_match('/^\+\d{11,15}$/', $phonenumber)) {
            $formErrors[] = "Invalid phone number!";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $formErrors[] = "Invalid email address!";
        }

        $today = new \DateTime();
        $userBirthdate = \DateTime::createFromFormat('Y-m-d', $date_of_birth);

        if (!$userBirthdate || $userBirthdate > $today->sub(new \DateInterval('P13Y'))) {
            $formErrors[] = "Invalid birthdate or user is younger than 13 years old!";
        }

        $sql = "SELECT * FROM users WHERE username =? OR email =?";
        $stmt = $this->db->prepare($sql);
        $qry = $stmt->executeQuery([$username, $email]);
        $result = $qry->fetchAssociative();

        if ($result) {
            $this->message(400, 'User already exists!');
            return;
        }

        if (count($formErrors) === 0) {


            $sql = "INSERT INTO users_adresses (street_no, zip, city, country) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->executeQuery([$street_no, $zip, $city, $country]);
            $addressID = $this->db->lastInsertId();

            $sql = "INSERT INTO users (username,email,password,phonenumber,date_of_birth,profile_picture,bio,role,created_at,users_adresses_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->executeQuery([$username, $email, password_hash($password, PASSWORD_DEFAULT), $phonenumber, $date_of_birth, "noImage.jpg", $bio, 'user', date('Y-m-d H:i:s'), $addressID]);
            $userID = $this->db->lastInsertId();

            $fileName = $userID . '.jpg';

            $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->executeQuery([$fileName, $userID]);

            $basePath = __DIR__ . '/../../public/profilePictures';

            if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $basePath . '/' . $fileName)) {
                $this->smtpConnector->sendMail($email, $username, "signup");

                $this->message(204, 'Created');
            } else {
                $this->message(500, 'Internal Server Error');
            }
        } else {
            $this->message(400, json_encode($formErrors));
        }
    }

    public function verify(): void
    {
        $headers = apache_request_headers();
        if (isset($headers['X-Authorization'])) {
            $jwtToken = str_ireplace('Bearer ', '', $headers['Authorization']);
            try {
                $decodedPayload = JwtHandeler::validateJWTToken($jwtToken, SECRET_KEY);
                $userId = $decodedPayload->sub;
                if ($userId) {
                    $sql = "SELECT * FROM users WHERE id = ?";
                    $stmt = $this->db->prepare($sql);
                    $qry = $stmt->executeQuery([$userId]);
                    $user = $qry->fetchAssociative();

                    if ($user) {
                        return;
                    }
                }
            } catch (\Exception $e) {
                // empty, will do 401
            }
        }
        $this->message(401, 'Invalid credentials'); // 401 Unauthorized
        exit();
    }

    public function verifyIsAdmin(): void
    {
        $headers = apache_request_headers();
        if (isset($headers['X-Authorization'])) {
            $jwtToken = str_ireplace('Bearer ', '', $headers['Authorization']);
            try {
                $decodedPayload = JwtHandeler::validateJWTToken($jwtToken, SECRET_KEY);
                $userId = $decodedPayload->sub;
                $role = $decodedPayload->role;
                if ($userId) {
                    $sql = "SELECT * FROM users WHERE id = ?";
                    $stmt = $this->db->prepare($sql);
                    $qry = $stmt->executeQuery([$userId]);
                    $user = $qry->fetchAssociative();

                    if ($user && $role === 'admin') {
                        return; // no 401
                    }
                }
            } catch (\Exception $e) {
                // empty, will do 401
            }
        }
        $this->message(401, 'Invalid credentials'); // 401 Unauthorized
        exit();
    }

    static public function getUserID(): int
    {
        $headers = apache_request_headers();
        if (isset($headers['X-Authorization'])) {
            $jwtToken = str_ireplace('Bearer ', '', $headers['Authorization']);
            try {
                $decodedPayload = JwtHandeler::validateJWTToken($jwtToken, SECRET_KEY);
                $userId = $decodedPayload->sub;
                return $userId;
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function test(): void
    {
        $this->smtpConnector->sendMail("vinnie@test.com", "Vinnie", "signup");
        $this->message(200, "OK");
    }
}
