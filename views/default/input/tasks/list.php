<?php

$container_guid = (int) get_input('guid');
$container = get_entity($container_guid);
echo $container->title;
