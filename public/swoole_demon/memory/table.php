<?php

use Swoole\Table;

$table = new \Swoole\Table(1024);

$table->column('id',Table::TYPE_INT);
$table->column('name',Table::TYPE_STRING,32);
$table->column('age',Table::TYPE_INT);
$table->create();

$table->set('k1',['id'=>1,'name'=>'kk','age'=>18]);
//另一种方案
$table['k2'] = ['id'=>2,'name'=>'kk2','age'=>18];

$table->incr('k1','age',1);
$table->decr('k2','age',1);

$k1 = $table->get('k1');
$k2 = $table->get('k2');

var_dump($k1);

$table->del('k1');

$k1 = $table->get('k1');

var_dump($k1);
var_dump($k2);