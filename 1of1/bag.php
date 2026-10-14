<?php require 'inc.php';
$zones=['Pick up in Nairobi CBD'=>0,'Nairobi delivery'=>300,'Countrywide courier'=>600];
$bag=array_values(array_unique($_SESSION['bag']??[])); $done=null; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){ chk();
 if(($_POST['a']??'')==='rm'){ $_SESSION['bag']=array_values(array_diff($bag,[(int)$_POST['id']])); header('Location: bag.php'); exit; }
 if(($_POST['a']??'')==='order' && $bag){
  $name=trim($_POST['name']??''); $phone=preg_replace('/\D/','',$_POST['phone']??''); $zone=$_POST['zone']??''; $addr=trim($_POST['addr']??'');
  if(!$name||strlen($phone)<9||!isset($zones[$zone])) $err='Add your name, a valid phone number and a delivery option.';
  else try{
   $pdo->beginTransaction(); $in=implode(',',array_fill(0,count($bag),'?'));
   $st=$pdo->prepare("SELECT id,price FROM products WHERE id IN($in) AND status='available' FOR UPDATE"); $st->execute($bag); $rows=$st->fetchAll();
   if(count($rows)!==count($bag)){ $pdo->rollBack(); $_SESSION['bag']=array_map('intval',array_column($rows,'id')); $err='Someone just took a piece from your bag. It has been removed. Check and try again.'; }
   else{
    $sub=array_sum(array_column($rows,'price')); $fee=$zones[$zone];
    $pdo->prepare('INSERT INTO orders(name,phone,zone,address,fee,total) VALUES(?,?,?,?,?,?)')->execute([$name,$phone,$zone,$addr,$fee,$sub+$fee]);
    $oid=$pdo->lastInsertId();
    foreach($rows as $r) $pdo->prepare('INSERT INTO order_items(order_id,product_id,price) VALUES(?,?,?)')->execute([$oid,$r['id'],$r['price']]);
    $pdo->prepare("UPDATE products SET status='reserved' WHERE id IN($in)")->execute($bag);
    $pdo->commit(); $_SESSION['bag']=[]; $done=['id'=>$oid,'total'=>$sub+$fee];
   }
  }catch(Throwable $x){ if($pdo->inTransaction())$pdo->rollBack(); $err='Something went wrong. Try again.'; }
 }
}
$bag=array_values(array_unique($_SESSION['bag']??[])); $items=[];
if($bag){ $s=$pdo->prepare('SELECT * FROM products WHERE id IN('.implode(',',array_fill(0,count($bag),'?')).')'); $s->execute($bag); $items=$s->fetchAll(); }
head('Bag'); ?>
<div class="wrap narrow">
<?php if($done): ?>
 <h1>Order #<?=$done['id']?> placed</h1>
 <p>Your pieces are held. Pay <b><?=kes($done['total'])?></b> by M-Pesa to Till <b><?=e(TILL)?></b>, then send us the M-Pesa message on WhatsApp so we can confirm.</p>
 <a class="btn" href="https://wa.me/<?=e(WHATSAPP)?>?text=<?=urlencode('Hi 1OF1, I paid for order #'.$done['id'])?>">Send payment on WhatsApp</a>
<?php elseif(!$items): ?>
 <h1>Your bag is empty</h1><p class="empty"><a href="index.php">Find something one of one</a></p>
<?php else: ?>
 <h1>Your bag</h1><?php if($err): ?><p class="note"><?=e($err)?></p><?php endif; $sub=0; ?>
 <?php foreach($items as $r): $sub+=$r['price']; ?>
  <form method="post" class="row"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="a" value="rm"><input type="hidden" name="id" value="<?=$r['id']?>">
   <span><?=e($r['title'])?> <small>size <?=e($r['size'])?></small></span><span><?=kes($r['price'])?></span><button class="link">Remove</button></form>
 <?php endforeach; ?>
 <form method="post" class="checkout"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="a" value="order">
  <label>Name<input name="name" required></label>
  <label>M-Pesa phone<input name="phone" inputmode="tel" placeholder="07XX XXX XXX" required></label>
  <label>Delivery<select name="zone"><?php foreach($zones as $z=>$f): ?><option value="<?=e($z)?>"><?=e($z)?> (<?=$f?kes($f):'free'?>)</option><?php endforeach; ?></select></label>
  <label>Address or landmark<textarea name="addr" rows="2"></textarea></label>
  <p class="total">Items <?=kes($sub)?>, delivery added at order.</p>
  <button class="btn">Place order</button>
 </form>
<?php endif; ?>
</div>
<?php foot();
