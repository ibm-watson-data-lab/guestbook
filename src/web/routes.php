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

        $commentService = new \Guestbook\CommentService($this->couchdb['handle'], $this->rabbitmq);
        $commentService->add($comment);

        return $response->withStatus(302)->withHeader('Location', '/');
    }

    $response = $this->view->render($response, "add-comment.phtml");
    return $response;
})->setName('add-comment');

$app->get('/webhooks', function (Request $request, Response $response) {
    $webhookService = new \Guestbook\WebhookService($this->couchdb['handle']);
    $webhooks = $webhookService->fetch();
    $response = $this->view->render($response, "webhooks.phtml", ["webhooks" => $webhooks]);
    return $response;
})->setName('webhooks');

$app->post('/webhooks', function (Request $request, Response $response) {
    // process data
    $data = $request->getParsedBody();
    $webhook = [];
    $webhook['url'] = filter_var($data['url'], FILTER_VALIDATE_URL);
    if($webhook['url']) {
        $webhookService = new \Guestbook\WebhookService($this->couchdb['handle']);
        $webhookService->add($webhook);
    }

    return $response->withStatus(302)->withHeader('Location', '/webhooks');
});

$app->post('/delete-webhook', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    if($data['id'] && $data['rev']) {
        $webhookService = new \Guestbook\WebhookService($this->couchdb['handle']);
        $webhookService->delete($data);
    }
    return $response->withStatus(302)->withHeader('Location', '/webhooks');
})->setName('delete-webhook');

