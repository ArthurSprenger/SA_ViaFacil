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

    public function read($int = 8192, $nb = false)
    {
        $string = '';
        $togo = $int;

        if ($nb) {
            return fread($this->socket, $togo);
        }

        while (!feof($this->socket) && $togo > 0) {
            $fread = fread($this->socket, $togo);
            $string .= $fread;
            $togo = $int - strlen($string);
        }

        return $string;
    }

    public function subscribe($topics, $qos = 0): void
    {
        $i = 0;
        $buffer = '';
        $id = $this->msgid;
        $buffer .= chr($id >> 8);
        $i++;
        $buffer .= chr($id % 256);
        $i++;

        foreach ($topics as $key => $topic) {
            $buffer .= $this->strwritestring($key, $i);
            $buffer .= chr($topic['qos']);
            $i++;
            $this->topics[$key] = $topic;
        }

        $cmd = 0x82;
        $cmd += ($qos << 1);

        $head = chr($cmd);
        $head .= $this->setmsglength($i);
        fwrite($this->socket, $head, strlen($head));

        $this->_fwrite($buffer);
        $string = $this->read(2);

        $bytes = ord(substr($string, 1, 1));
        $this->read($bytes);
    }

    public function ping(): void
    {
        $head = chr(0xc0);
        $head .= chr(0x00);
        fwrite($this->socket, $head, 2);
        $this->timesinceping = time();
        $this->_debugMessage('ping sent');
    }

    public function disconnect(): void
    {
        $head = ' ';
        $head[0] = chr(0xe0);
        $head[1] = chr(0x00);
        fwrite($this->socket, $head, 2);
    }

    public function close(): void
    {
        $this->disconnect();
        stream_socket_shutdown($this->socket, STREAM_SHUT_WR);
    }

    public function publish($topic, $content, $qos = 0, $retain = false): void
    {
        $i = 0;
        $buffer = '';

        $buffer .= $this->strwritestring($topic, $i);

        if ($qos) {
            $id = $this->msgid++;
            $buffer .= chr($id >> 8);
            $i++;
            $buffer .= chr($id % 256);
            $i++;
        }

        $buffer .= $content;
        $i += strlen($content);

        $head = ' ';
        $cmd = 0x30;
        if ($qos) {
            $cmd += $qos << 1;
        }
        if (empty($retain) === false) {
            ++$cmd;
        }

        $head[0] = chr($cmd);
        $head .= $this->setmsglength($i);

        fwrite($this->socket, $head, strlen($head));
        $this->_fwrite($buffer);
    }

    protected function _fwrite($buffer)
    {
        $buffer_length = strlen($buffer);
        for ($written = 0; $written < $buffer_length; $written += $fwrite) {
            $fwrite = fwrite($this->socket, substr($buffer, $written));
            if ($fwrite === false) {
                return false;
            }
        }
        return $buffer_length;
    }

    public function message($msg)
    {
        $tlen = (ord($msg[0]) << 8) + ord($msg[1]);
        $topic = substr($msg, 2, $tlen);
        $msg = substr($msg, ($tlen + 2));
        $found = false;
        foreach ($this->topics as $key => $top) {
            if (preg_match(
                '/^' . str_replace(
                    '#',
                    '.*',
                    str_replace(
                        '+',
                        "[^\/]*",
                        str_replace(
                            '/',
                            "\/",
                            str_replace(
                                '$',
                                '\$',
                                $key
                            )
                        )
                    )
                ) . '$/',
                $topic
            )) {
                $found = true;

                if (is_callable($top['function'])) {
                    call_user_func($top['function'], $topic, $msg);
                }
            }
        }

        if ($found === false) {
            $this->_debugMessage('msg received but no match in subscriptions');
        }

        return $found;
    }

    public function proc(bool $loop = true)
    {
        if (feof($this->socket)) {
            $this->_debugMessage('eof receive going to reconnect for good measure');
            fclose($this->socket);
            $this->connect_auto(false);
            if (count($this->topics)) {
                $this->subscribe($this->topics);
            }
        }

        $byte = $this->read(1, true);

        if ((string)$byte === '') {
            if ($loop === true) {
                usleep(100000);
            }
        } else {
            $cmd = (int)(ord($byte) / 16);
            $this->_debugMessage(
                sprintf(
                    'Received CMD: %d (%s)',
                    $cmd,
                    isset(static::$known_commands[$cmd]) === true ? static::$known_commands[$cmd] : 'Unknown'
                )
            );

            $multiplier = 1;
            $value = 0;
            do {
                $digit = ord($this->read(1));
                $value += ($digit & 127) * $multiplier;
                $multiplier *= 128;
            } while (($digit & 128) !== 0);

            $this->_debugMessage('Fetching: ' . $value . ' bytes');

            $string = $value > 0 ? $this->read($value) : '';

            if ($cmd) {
                switch ($cmd) {
                    case 3:
                        $return = $this->message($string);
                        if (is_bool($return) === false) {
                            return $return;
                        }
                        break;
                }
            }
        }

        if ($this->timesinceping < (time() - $this->keepalive)) {
            $this->_debugMessage('not had something in a while so ping');
            $this->ping();
        }

        if ($this->timesinceping < (time() - ($this->keepalive * 2))) {
            $this->_debugMessage('not seen a packet in a while, disconnecting/reconnecting');
            fclose($this->socket);
            $this->connect_auto(false);
            if (count($this->topics)) {
                $this->subscribe($this->topics);
            }
        }

        return true;
    }

    protected function setmsglength($len): string
    {
        $string = '';
        do {
            $digit = $len % 128;
            $len >>= 7;
            if ($len > 0) {
                $digit |= 0x80;
            }
            $string .= chr($digit);
        } while ($len > 0);
        return $string;
    }

    protected function strwritestring($str, &$i): string
    {
        $len = strlen($str);
        $msb = $len >> 8;
        $lsb = $len % 256;
        $ret = chr($msb);
        $ret .= chr($lsb);
        $ret .= $str;
        $i += ($len + 2);
        return $ret;
    }

    protected function _debugMessage(string $message): void
    {
        if ($this->debug === true) {
            echo date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
        }
    }

    protected function _errorMessage(string $message): void
    {
        error_log('MQTT Error: ' . $message);
    }
}
