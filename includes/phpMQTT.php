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

