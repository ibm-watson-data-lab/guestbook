<?php

namespace Guestbook;

class CommentService
{
    protected $couchdb_handle;
    // Rabbit is optional
    protected $rabbitmq_handle = false;

    public function __construct(\GuzzleHttp\Client $couchdb_handle, \PhpAmqpLib\Connection\AMQPConnection $rabbitmq_handle = null) {
        $this->couchdb_handle = $couchdb_handle;
        $this->rabbitmq_handle = $rabbitmq_handle;
    }

    public function fetch() {
        $comments = [];

        $response = $this->couchdb_handle->request(
            "GET",
            "/comments/_all_docs",
            ['query' => ['include_docs' => 'true']]
        );
        if($response->getStatusCode() == 200) {
            if(false !== $data = json_decode($response->getBody(), true)) {
                foreach($data['rows'] as $row) {
                    $comments[] = $row['doc'];
                }
            }

        }

        return $comments;
    }

    public function add($comment) {
        // add a timestamp field also
        $comment['time'] = time();
        $response = $this->couchdb_handle->request(
            "POST",
            "/comments",
            [
                "headers" => ["Content-Type" => "application/json"],
                "body" => json_encode($comment)
            ]
        );

        if($response && $this->rabbitmq_handle) {
            // also write it to the queue
            $channel = $this->rabbitmq_handle->channel();
			$msg = new \PhpAmqpLib\Message\AMQPMessage(
				json_encode($comment),
				["delivery_mode" => 2] // store this message persistently, as well as just the queue
			);
			$channel->basic_publish($msg, '', 'comments');
        }
        return $response;
    }
}
