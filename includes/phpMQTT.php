<?php

namespace Bluerhinos;

/*
 	phpMQTT
	A simple php class to connect/publish/subscribe to an MQTT broker
	
	Licence: MIT License
	Copyright (c) 2010 Blue Rhinos Consulting | Andrew Milsted
*/

class phpMQTT
{
    protected $socket;            /* holds the socket	*/
    protected $msgid = 1;         /* counter for message id */
    public $keepalive = 10;       /* default keepalive timmer */
    public $timesinceping;        /* host unix time, used to detect disconects */
    public $topics = [];          /* used to store currently subscribed topics */
    public $debug = false;        /* should output debug messages */
    public $address;              /* broker address */
    public $port;                 /* broker port */
    public $clientid;             /* client id sent to brocker */
    public $will;                 /* stores the will of the client */
    protected $username;          /* stores username */
    protected $password;          /* stores password */

    public $cafile;
    protected static $known_commands = [
        1 => 'CONNECT',
        2 => 'CONNACK',
        3 => 'PUBLISH',
        4 => 'PUBACK',
        5 => 'PUBREC',
        6 => 'PUBREL',
        7 => 'PUBCOMP',
        8 => 'SUBSCRIBE',
        9 => 'SUBACK',
        10 => 'UNSUBSCRIBE',
        11 => 'UNSUBACK',
        12 => 'PINGREQ',
        13 => 'PINGRESP',
        14 => 'DISCONNECT'
    ];

    public function __construct($address, $port, $clientid, $cafile = null)
    {
        $this->broker($address, $port, $clientid, $cafile);
    }

    public function broker($address, $port, $clientid, $cafile = null): void
    {
        $this->address = $address;
        $this->port = $port;
        $this->clientid = $clientid;
        $this->cafile = $cafile;
    }

    public function connect_auto($clean = true, $will = null, $username = null, $password = null): bool
    {
        while ($this->connect($clean, $will, $username, $password) === false) {
            sleep(10);
        }
        return true;
    }

    public function connect($clean = true, $will = null, $username = null, $password = null): bool
    {
        if ($will) {
            $this->will = $will;
        }
        if ($username) {
            $this->username = $username;
        }
        if ($password) {
            $this->password = $password;
        }

        if ($this->cafile) {
            $socketContext = stream_context_create(
                [
                    'ssl' => [
                        'verify_peer_name' => true,
                        'cafile' => $this->cafile
                    ]
                ]
            );
            $this->socket = stream_socket_client('tls://' . $this->address . ':' . $this->port, $errno, $errstr, 60, STREAM_CLIENT_CONNECT, $socketContext);
        } else {
            $this->socket = stream_socket_client('tcp://' . $this->address . ':' . $this->port, $errno, $errstr, 60, STREAM_CLIENT_CONNECT);
        }

        if (!$this->socket) {
            $this->_errorMessage("stream_socket_create() $errno, $errstr");
            return false;
        }

        stream_set_timeout($this->socket, 5);
        stream_set_blocking($this->socket, 0);

        $i = 0;
        $buffer = '';

        $buffer .= chr(0x00);
        $i++;
        $buffer .= chr(0x04);
        $i++;
        $buffer .= chr(0x4d);
        $i++;
        $buffer .= chr(0x51);
        $i++;
        $buffer .= chr(0x54);
        $i++;
        $buffer .= chr(0x54);
        $i++;
        $buffer .= chr(0x04);
        $i++;

        $var = 0;
        if ($clean) {
            $var += 2;
        }

        if ($this->will !== null) {
            $var += 4;
            $var += ($this->will['qos'] << 3);
            if ($this->will['retain']) {
                $var += 32;
            }
        }

        if ($this->username !== null) {
            $var += 128;
        }
        if ($this->password !== null) {
            $var += 64;
        }

        $buffer .= chr($var);
        $i++;

        $buffer .= chr($this->keepalive >> 8);
        $i++;
        $buffer .= chr($this->keepalive & 0xff);
        $i++;

        $buffer .= $this->strwritestring($this->clientid, $i);

        if ($this->will !== null) {
            $buffer .= $this->strwritestring($this->will['topic'], $i);
            $buffer .= $this->strwritestring($this->will['content'], $i);
        }

        if ($this->username !== null) {
            $buffer .= $this->strwritestring($this->username, $i);
        }
        if ($this->password !== null) {
            $buffer .= $this->strwritestring($this->password, $i);
        }

        $head = chr(0x10);

        while ($i > 0) {
            $encodedByte = $i % 128;
            $i /= 128;
            $i = (int)$i;
            if ($i > 0) {
                $encodedByte |= 128;
            }
            $head .= chr($encodedByte);
        }

        fwrite($this->socket, $head, 2);
        fwrite($this->socket, $buffer);

        $string = $this->read(4);

        if (ord($string[0]) >> 4 === 2 && $string[3] === chr(0)) {
            $this->_debugMessage('Connected to Broker');
        } else {
            $this->_errorMessage(
                sprintf(
                    "Connection failed! (Error: 0x%02x 0x%02x)\n",
                    ord($string[0]),
                    ord($string[3])
                )
            );
            return false;
        }

        $this->timesinceping = time();
        return true;
    }

