<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 5/14/2015
 * Time: 12:51 PM
 */

    require dirname(__DIR__) . '/vendor/autoload.php';

    $loop   = React\EventLoop\Factory::create();
    $vid = new MyApp\Chat;

    // Listen for the web server to make a ZeroMQ push after an ajax request
    $context = new React\ZMQ\Context($loop);
    $pull = $context->getSocket(ZMQ::SOCKET_PULL);
    $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
    $pull->on('message', array($vid, 'onNewVideo'));



    // Set up our WebSocket server for clients wanting real-time updates
    $webSock = new React\Socket\Server($loop);
    $webSock->listen(8080, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect
    $webServer = new Ratchet\Server\IoServer(
        new Ratchet\Http\HttpServer(
            new Ratchet\WebSocket\WsServer(

                    $vid

            )
        ),
        $webSock
    );



    $loop->run();


?>