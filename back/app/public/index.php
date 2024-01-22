<?php

require_once('../vendor/autoload.php');
$router = new \Bramus\Router\Router();
$router->setNamespace('\Http');

// add your routes and run!
$router->options('/.*', function () {
    header('Access-Control-Allow-Origin: ' . ALLOW_ORIGIN);
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
});


$router->mount('/api', function () use ($router) {
    $router->mount(
        '/auth',
        function () use ($router) {
            $router->get('/test', "AuthController@test");
            $router->post('/login', "AuthController@login");
            $router->post('/register', "AuthController@register");
            $router->post('/refresh-token', 'AuthController@refreshToken');
        }
    );


    $router->mount('/orders', function () use ($router) {
        $router->before('POST|PATCH|DELETE|GET', '*', "AuthController@verify");
        $router->before('POST|PATCH|DELETE|GET', '/.*', "AuthController@verify");

        $router->get('/', "OrderController@getOrders");
        $router->post('/', "OrderController@createOrders");

        $router->get('/{orderID}', "OrderController@getOrder");
        $router->patch('/{orderID}', "OrderController@updateOrder");
        $router->delete('/{orderID}', "OrderController@deleteOrder");
    });

    $router->mount('/artists', function () use ($router) {
        $router->options('/.*', function () {
            header('Access-Control-Allow-Origin: ' . ALLOW_ORIGIN);
            header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            header('Access-Control-Allow-Credentials: true');
        });
        $router->get('/{artistID}/artpieces', "ArtistController@getArtistsArtpieces");
        $router->get('/popular', "ArtistController@getPopularArtists");
        $router->get('/{artistID}', "ArtistController@getArtist");
        $router->get('/', "ArtistController@getArtists");
    });

    $router->mount('/artpieces', function () use ($router) {
        $router->before('POST|PATCH|DELETE', '*', "AuthController@verify");
        $router->before('POST|PATCH|DELETE', '/.*', "AuthController@verify");

        $router->patch('/{artID}/comments/{commentID}', "CommentController@updateComment");
        $router->delete('/{artID}/comments/{commentID}', "CommentController@deleteComment");

        $router->get('/{artID}/auction', 'AuctionController@getAuction');
        $router->post('/{artID}/auction', 'AuctionController@createAuction');

        $router->get('/{artID}/auction/bids', 'AuctionController@getBids');
        $router->post('/{artID}/auction/bids', 'AuctionController@createBid');

        $router->post('/{artID}/like', "ArtpieceController@likeArtpiece");
        $router->delete('/{artID}/like', "ArtpieceController@unlikeArtpiece");

        $router->get('/{artID}/comments', "CommentController@getComments");
        $router->post('/{artID}/comments', "CommentController@createComments");

        $router->get('/{artID}', "ArtpieceController@getArtpiece");
        $router->patch('/{artID}', "ArtpieceController@updateArtpiece");
        $router->delete('/{artID}', "ArtpieceController@deleteArtpiece");


        $router->get('/', "ArtpieceController@getArtpieces");
        $router->post('/', "ArtpieceController@createArtpieces");
    });

    $router->mount('/admin', function () use ($router) {
        $router->before('POST|PATCH|DELETE|GET', '*', "AuthController@verifyIsAdmin");
        $router->before('POST|PATCH|DELETE|GET', '/.*', "AuthController@verifyIsAdmin");

        $router->mount('/artpieces', function () use ($router) {
            $router->delete('/{artID}/comments/{commentID}', "CommentController@deleteCommentAdmin");

            $router->delete('/{artID}', "ArtpieceController@deleteArtpieceAdmin");
        });


        $router->mount('/orders', function () use ($router) {

            $router->patch('/{orderID}', "OrderController@updateOrderAdmin");
            $router->delete('/{orderID}', "OrderController@deleteOrderAdmin");

            $router->get('/', "OrderController@getOrdersAdmin");
        });
    });
});

$router->run();
