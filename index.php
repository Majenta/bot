<?php
require_once('core/index.php');
$bot = new bot();
//echo $bot -> get('vk.com') -> body;
$vk = $bot -> loadProvider('vk');
echo $vk -> login('prostocyber@gmail.com', '321rjynfrn67');