<?php

require_once "Net/HL7/Segments/MSH.php";
require_once "Net/HL7/Message.php";
require_once "Net/HL7/Connection.php";
require_once 'PHPUnit/Framework/TestCase.php';
class ConnectionTest extends PHPUnit_Framework_TestCase {

    public function test() {
        $msg  = new Net_HL7_Message();
        $msg->addSegment(new Net_HL7_Segments_MSH());

        $seg1 = new Net_HL7_Segment("PID");

        $seg1->setField(3, "XXX");

        $msg->addSegment($seg1);

        /**
        // If you have fork support, try this...

        $pid = pcntl_fork();

        if (! $pid) {

          // Server process
          set_time_limit(0);

          // Turn on implicit output flushing so we see what we're getting
          // as it comes in.
          ob_implicit_flush();

          if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
            echo "socket_create() failed: reason: " . socket_strerror($sock) . "\n";
          }

          if (($ret = socket_bind($sock, "localhost", 12011)) < 0) {
            echo "socket_bind() failed: reason: " . socket_strerror($ret) . "\n";
          }

          if (($ret = socket_listen($sock, 5)) < 0) {
            echo "socket_listen() failed: reason: " . socket_strerror($ret) . "\n";
          }

          do {
            if (($msgsock = socket_accept($sock)) < 0) {
              echo "socket_accept() failed: reason: " . socket_strerror($msgsock) . "\n";
              break;
            }

            if (false === ($buf = socket_read($msgsock, 8192, PHP_NORMAL_READ))) {
              echo "socket_read() failed: reason: " . socket_strerror($ret) . "\n";
              break 2;
            }

            echo "Incoming: $buf\n";

            $msg = new Net_HL7_Message($buf);

            $ack = new Net_HL7_Messages_ACK($msg);
            socket_write($msgsock, "\013" . $ack->toString() . "\034\015", strlen($ack->toString() + 3));
            echo "Said: " . $ack->toString(1) . "\n";

          } while (true);

          socket_close($msgsock);

          exit;
        }
        */

        $socket = $this->getMock('Net_Socket');

        $socket->expects($this->once())
                ->method('write')
                ->with("\013" . $msg->toString() . "\034\015");

        $socket->expects($this->once())
                ->method('read')
                ->will($this->returnValue("MSH*^~\\&*1\rPID***xxx\r" . "\034\015"));

        $conn = new Net_HL7_Connection($socket);

        $resp = $conn->send($msg);

        $this->assertTrue($resp instanceof Net_HL7_Message);
    }
}