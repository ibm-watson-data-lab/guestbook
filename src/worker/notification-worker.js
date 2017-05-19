var amqp    = require('amqplib/callback_api');
var atob    = require('atob');
var cfenv   = require('cfenv');
var request = require('request');

if (process.env.VCAP_SERVICES) {
    var appEnv = cfenv.getAppEnv()
    rabbitmq_url = appEnv.getService('guestbook-messages').credentials.uri;
    cert_string = atob(appEnv.getService('guestbook-messages').credentials.ca_certificate_base64);
    ca = new Buffer(cert_string);
    opts = {ca: [ca]};
} else {
	rabbitmq_url = 'amqp://localhost';
    opts = {};
}

amqp.connect(rabbitmq_url, opts, function(err, conn) {
    conn.createChannel(function(err, ch) {
        var q = 'notifications';

        ch.assertQueue(q, {durable: true, noAck: false, arguments: {"x-max-length": 10000}});
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
