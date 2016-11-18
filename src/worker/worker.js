var amqp    = require('amqplib/callback_api');
var request = require('request');

var hooks = [];
amqp.connect('amqp://localhost', function(err, conn) {
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
                console.log(single_msg);
                ch.sendToQueue(q2, new Buffer(JSON.stringify(single_msg)), {persistent: true});
            });
            ch.ack(msg);
        });
    });
});


