<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $request, Response $response) {
    $commentService = new \Guestbook\CommentService($this->couchdb['handle']);
    $comments = $commentService->fetch();
    $response = $this->view->render($response, "comments.phtml", ["comments" => $comments]);
    return $response;
});
