var amqp    = require('amqplib/callback_api');
var nano    = require('nano')('http://localhost:5984');
var async   = require('async');
var request = require('request');

var hooks = [];
var webhooks_db = nano.use('webhooks');
webhooks_db.list({ include_docs: true }, function (err, hook) {

    hook.rows.forEach(function (row) {
		hooks.push(row.doc.url);
	});

    amqp.connect('amqp://localhost', function(err, conn) {
        conn.createChannel(function(err, ch) {
            var q = 'comments';

            ch.assertQueue(q, {durable: true});
            console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", q);
            ch.consume(q, function(msg) {
                hooks.forEach(function (url) {
                    console.log("Request to " + url);
                    request({
                        url: url,
                        method: "post",
                        json: msg.content.toString()
                    }, function (error, response, body) {
                    });
                });
                ch.ack(msg);

                console.log(" [x] Complete: %s", msg.content.toString());
            });
        });
    });
});


