var amqp    = require('amqplib/callback_api');
var request = require('request');

amqp.connect('amqp://localhost', function(err, conn) {
    conn.createChannel(function(err, ch) {
        var q = 'notifications';

        ch.assertQueue(q, {durable: true, noAck: false});
        console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", q);
        ch.consume(q, function(msg) {
			data = JSON.parse(msg.content);
			body = JSON.stringify(data.comment);
			request({
				url: data.url.url,
				method: "post",
				json: body
			}, function (error, response, body) {
				if(!error) {
					console.log(" [x] Complete: %s", msg.content.toString());
					ch.ack(msg);
				}

			});
		});
	});
});
