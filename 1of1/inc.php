<?php
session_start();
const DB_HOST='localhost', DB_NAME='oneofone', DB_USER='root', DB_PASS='';
const TILL='000000';              
const WHATSAPP='254700000000';    
const ADMIN_PASSWORD='mike';
$pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,
 [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function kes($n){return 'KES '.number_format((int)$n);}
function csrf(){return $_SESSION['t']??($_SESSION['t']=bin2hex(random_bytes(16)));}
function chk(){if(!hash_equals($_SESSION['t']??'',$_POST['t']??''))die('Bad request');}
function head($t=''){$n=count($_SESSION['bag']??[]);
echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($t?$t.' | 1OF1':'1OF1 - thrift, one of one').'</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,500..900&family=Instrument+Sans:wght@400;600&display=swap" rel="stylesheet"><link rel="stylesheet" href="style.css"></head><body><header class="top"><a class="logo" href="index.php">1<span>of</span>1</a><a class="bagbtn" href="bag.php">Bag ('.$n.')</a></header><main>';}
function foot(){echo '</main><footer>1OF1, Nairobi. Every piece is the only one.</footer><script src="app.js"></script></body></html>';}
function tag($r){
 $img=$r['image']?'<img src="uploads/'.e($r['image']).'" alt="'.e($r['title']).'" loading="lazy">':'<div class="noimg">'.e(mb_substr($r['category'],0,1)).'</div>';
 $st=$r['status']!=='available'?'<b class="stamp">'.($r['status']==='sold'?'Gone':'Held').'</b>':'';
 return '<a class="tag '.$r['status'].'" href="product.php?id='.(int)$r['id'].'"><i class="hole"></i><div class="ph">'.$img.'</div><h3>'.e($r['title']).'</h3><p class="meta">'.e($r['category']).', size '.e($r['size']).'</p><p class="price">'.kes($r['price']).'</p>'.$st.'</a>';}
