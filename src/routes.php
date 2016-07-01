<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $request, Response $response) {
    $commentService = new \Guestbook\CommentService($this->couchdb['handle']);
    $comments = $commentService->fetch();
    $response = $this->view->render($response, "comments.phtml", ["comments" => $comments]);
    return $response;
})->setName('home');

$app->map(['GET','POST'], '/add', function (Request $request, Response $response) {

    if($request->isPost()) {
        // process data
        $data = $request->getParsedBody();
        $comment = [];
        $comment['name'] = filter_var($data['name'], FILTER_SANITIZE_STRING);
        $comment['comment'] = filter_var($data['comment'], FILTER_SANITIZE_STRING);

        $commentService = new \Guestbook\CommentService($this->couchdb['handle']);
        $commentService->add($comment);

        return $response->withStatus(302)->withHeader('Location', '/');
    }

    $response = $this->view->render($response, "add-comment.phtml");
    return $response;
})->setName('add-comment');

