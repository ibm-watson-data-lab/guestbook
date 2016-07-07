var amqp = require('amqplib/callback_api');

var hooks = [];
var nano = require('nano')('http://localhost:5984');
var webhooks_db = nano.use('webhooks');
webhooks_db.view('main', 'all', function (err, hook) {
    hook.rows.forEach(function (row) {
        hooks.push(row.value.url);
    });
    console.log(hooks);
});




amqp.connect('amqp://localhost', function(err, conn) {
  conn.createChannel(function(err, ch) {
    var q = 'comments';

    ch.assertQueue(q, {durable: true});
    console.log(" [*] Waiting for messages in %s. To exit press CTRL+C", q);
    ch.consume(q, function(msg) {
      console.log(msg)
      console.log(" [x] Received %s", msg.content.toString());
    }, {noAck: true});
  });
});
