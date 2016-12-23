var amqp    = require('amqplib/callback_api');
var atob    = require('atob');
var cfenv   = require('cfenv');

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

var hooks = [];
amqp.connect(rabbitmq_url, opts, function(err, conn) {
    conn.createChannel(function(err, ch) {
        var q = 'comments';

        ch.assertQueue(q, {durable: true, noAck: false});
        console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", q);
        ch.consume(q, function(msg) {
            console.log(msg.content.toString());
            var data = JSON.parse(msg.content);
            var comment = data.comment;

            var q2 = 'notifications';
            ch.assertQueue(q2, {durable: true, noAck: false});
            data.webhooks.forEach(function (url) {
                single_msg = {comment: comment, url: url};
                ch.sendToQueue(q2, new Buffer(JSON.stringify(single_msg)), {persistent: true});
            });
            ch.ack(msg);
        });
    });
});

