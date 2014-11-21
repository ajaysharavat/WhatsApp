<?php
$username = "918527853420";                       // Telephone number including the country code without '+' or '00'.
$identity = "%c8%83%c3%af%0d%ac%d0%bf%ed%e0ji%de%f9cf%88%bfic"; // Obtained during registration with this API or using MissVenom (https://github.com/shirioko/MissVenom) to sniff from your phone.
$password = "gWnMP5z6NudNcliKv1oQhw1NKiY";      // A server generated Password you received from WhatsApp. This can NOT be manually created

$nickname = "bpt";                           // This is the username (or nickname) displayed by WhatsApp clients.
$target = 917838828123;                       // Destination telephone number including the country code without '+' or '00'.

//This function only needed to show how eventmanager works.
function onGetProfilePicture($from, $target, $type, $data)
{
    if ($type == "preview") {
        $filename = "preview_" . $target . ".jpg";
    } else {
        $filename = $target . ".jpg";
    }
    $filename = WhatsProt::PICTURES_FOLDER."/" . $filename;
    $fp = @fopen($filename, "w");
    if ($fp) {
        fwrite($fp, $data);
        fclose($fp);
    }
}

//Create the whatsapp object and setup a connection.
$w = new WhatsProt($username, $identity, $nickname, false);



$w->connect();

// Now loginWithPassword function sends Nickname and (Available) Presence
$w->loginWithPassword($password);
$w->sendPresence();


//$groupid = $w->sendGroupsChatCreate('group'.$i,$target);
//savegroup id again account and targets Only one time you can add and send a person to a group.

//$w->sendMessage($groupid, 'Group'.$i.' are created now'); 
  $w->sendPresence();
sleep(2);

//$gjids = array($groupid);
//sleep(5);
//$w->sendGroupsLeave($gjids);
//$response = $w->checkCredentials();
//print_r($response);

//echo "<pre>";
//print_r($w);

//Retrieve large profile picture. Output is in /src/php/pictures/ (you need to bind a function
//to the event onProfilePicture so the script knows what to do.
//$w->eventManager()->bind("onGetProfilePicture", "onGetProfilePicture");
//$w->sendGetProfilePicture($target, true);

//update your profile picture
 //$w->sendSetProfilePicture("demo/bjp.jpg");
 //$txt='BJP Maharashtra';
 //$w->sendStatusUpdate($txt);
//send picture
//$w->sendMessageImage($groupid, "/var/www/html/whatsappgateway/media/image/android.jpg");

//send video
//$w->sendMessageVideo($groupid, 'http://223.130.4.100/whatsappgateway/media/video/imagica.mp4');

//send Audio
//$w->sendMessageAudio($groupid, 'http://www.kozco.com/tech/piano2.wav');

//send Location
//$w->sendLocation($groupid, '4.948568', '52.352957');

// Implemented out queue messages and auto msgidi
//echo $string .= mb_convert_encoding('www.brainpulse.com', 'UTF-8');
//sleep(10);

echo $w->sendMessage($target, ' response testing '); 
/*
$v = new vCard();
$image = file_get_contents('http://223.130.4.48/whatsappgateway/media/image/test.jpeg');


$v->set('data', array(
'first_name' => 'ajay',
'last_name' => 'sharavat',
'cell_tel' => '917838828123',
'photo' => base64_encode($image),
));
*/
//$w->sendVcard($target, 'Ajay Sharavat', $v->show());


if($w->getBrainpulseStatus()=='success')
{
echo "Message Sent";
}
elseif($w->getBrainpulseStatus()=='failed')
{
echo "message failed hai";
}
else
echo "don't know";

//$w->waitForMessageReceipt();

//$w->waitForServer($nodeid);


/**
 * You can create a ProcessNode class (or whatever name you want) that has a process($node) function
 * and pass it through setNewMessageBind, that way everytime the class receives a text message it will run
 * the process function to it.
 */
 
 $w->pollMessages();

$messages = $w->getMessages();

//echo '<pre>'; print_r($messages);
$phone = $username;
if(!empty($messages)) {
         foreach ($messages as $m) {
                // Process inbound messages.
				
				
           if ($m->getTag() == "message") {
           echo $from = $m->getAttribute('from');
           echo $msgid = $m->getAttribute('id'); 
           echo $offline = $m->getAttribute('offline'); 
           echo $time = $m->getAttribute('t'); 
           echo $name = $m->getAttribute('notify'); 
            
		  foreach($m->getChildren() as $child)
            {
			
			if($child->getTag()=='media') {
			echo $url = $child->getAttribute('url');
			echo $mimetype = $child->getAttribute('type');
			echo $caption = $child->getAttribute('caption');
			echo $ip = $child->getAttribute('ip');
			echo $size = $child->getAttribute('size');
			echo $filehash = $child->getAttribute('filehash');
			$type ='media';
			$file='';
			$width='';
			$height='';
			$thumbnail='';
		    OnGetImageBP($phone, $from, $msgid, $type, $time, $name, $size, $url, $file, $mimetype, $filehash, $width, $height, $thumbnail);	
			}
			if($child->getTag()=='body') {
			$type ='text';
			echo $body = $child->getData();
			onMessage($phone, $from, $msgid, $type, $time, $name, $body);
			}				
			}
					
            }
            }
}



 
 
$pn = new ProcessNode($w, $target);
$w->setNewMessageBind($pn);

function onMessageReceivedClient($phone, $from, $msgid, $type, $time){
echo "Received on Client";
}
$w->eventManager()->bind("onMessageReceivedClient", "onMessageReceivedClient");

function onMessageReceivedServer($phone, $from, $msgid, $type){
echo "Received on Server";
}
$w->eventManager()->bind("onMessageReceivedServer", "onMessageReceivedServer");

function onSendMessage($phone, $targets, $id, $node){
echo "On send msg event";
$query = "insert into `sent_messages`(`phone`,`targets`,`mid`,`node`) values ('$phone','$targets','$id','$node')";
mysql_query($query);
}
$w->eventManager()->bind("onSendMessage", "onSendMessage");



function onMessage($mynumber, $from, $id, $type, $time, $name, $body)
{
echo $query = "insert into `reply_messages`(`from_number`,`my_number`,`msg_id`,`type`,`time`,`name`,`body`) values ('$from','$mynumber','$id','$type','$time','$name','$body')";
mysql_query($query);
}
$w->eventManager()->bind("onGetMessage", "onMessage");

function OnGetImageBP($phone, $from, $msgid, $type, $time, $name, $size, $url, $file, $mimetype, $filehash, $width, $height, $thumbnail) {
echo $query = "insert into `reply_messages`(`from_number`,`my_number`,`msg_id`,`type`,`mime_type`,`time`,`name`,`body`) values ('$from','$phone','$msgid','$type','$mimetype','$time','$name','$url')";
mysql_query($query);
}
$w->eventManager()->bind("onGetImage" , "OnGetImageBP");





while (1) {
    $w->pollMessages();
   
}

/**
 * Demo class to show how you can process inbound messages
 */
class ProcessNode
{
    protected $wp = false;
    protected $target = false;

    public function __construct($wp, $groupid)
    {
        $this->wp = $wp;
        $this->target = $groupid;
    }

    /**
     * @param ProtocolNode $node
     */
    public function process($node)
    {
        // Example of process function, you have to guess a number (psss it's 5)
        // If you guess it right you get a gift
        $text = $node->getChild('body');
       echo $text = $text->getData();
        if ($text && ($text == "5" || trim($text) == "5")) {
           $iconfile = "../../tests/Gift.jpg";
           $fp = fopen($iconfile, "r");
           $icon = fread($fp, filesize($iconfile));
            fclose($fp);
          //  $this->wp->sendMessageImage($this->target, "https://mms604.whatsapp.net/d11/26/09/8/5/85a13e7812a5e7ad1f8071319d9d1b43.jpg", "hero.jpg", 84712, $icon);
           // $this->wp->sendMessage($this->target, "Congratulations you guessed the right number!");
        } else {
          //  $this->wp->sendMessage($this->target, "I''m sorry, try again! www.brainpulse.com ");
        }
    }

}
?>
