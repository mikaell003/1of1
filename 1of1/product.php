<?php require 'inc.php';
$s=$pdo->prepare('SELECT * FROM products WHERE id=?'); $s->execute([(int)($_GET['id']??0)]); $r=$s->fetch();
if(!$r){http_response_code(404);head('Not found');echo '<div class="wrap"><p class="empty">That piece isn\'t here. <a href="index.php">Back to the rack</a></p></div>';foot();exit;}
$inbag=in_array((int)$r['id'],$_SESSION['bag']??[]);
if($_SERVER['REQUEST_METHOD']==='POST'){chk(); if($r['status']==='available'&&!$inbag)$_SESSION['bag'][]=(int)$r['id']; header('Location: bag.php'); exit;}
head($r['title']); ?>
<div class="wrap pd">
 <div class="ph big"><?php if($r['image']): ?><img src="uploads/<?=e($r['image'])?>" alt="<?=e($r['title'])?>"><?php else: ?><div class="noimg"><?=e(mb_substr($r['category'],0,1))?></div><?php endif; ?></div>
 <div class="info">
  <h1><?=e($r['title'])?></h1>
  <p class="price big"><?=kes($r['price'])?></p>
  <dl><dt>Size</dt><dd><?=e($r['size'])?></dd><dt>Condition</dt><dd><?=e($r['item_condition'])?></dd><dt>Category</dt><dd><?=e($r['category'])?></dd></dl>
  <p><?=nl2br(e($r['descr']))?></p>
  <?php if($r['status']!=='available'): ?><p class="note">This piece is <?=$r['status']==='sold'?'gone':'held for another buyer'?>.</p>
  <?php elseif($inbag): ?><a class="btn" href="bag.php">In your bag. Go to checkout</a>
  <?php else: ?><form method="post"><input type="hidden" name="t" value="<?=csrf()?>"><button class="btn">Add to bag</button></form><?php endif; ?>
  <p class="small">Only one available. Pieces in a placed order are held until payment is confirmed.</p>
 </div>
</div>
<?php foot();
