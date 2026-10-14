<?php require 'inc.php';
if(isset($_POST['pw'])){ chk(); if(hash_equals(ADMIN_PASSWORD,$_POST['pw']))$_SESSION['adm']=1; header('Location: admin.php'); exit; }
if(isset($_GET['out'])){ unset($_SESSION['adm']); header('Location: admin.php'); exit; }
if(empty($_SESSION['adm'])){ head('Admin'); ?>
 <div class="wrap narrow"><h1>Admin</h1><form method="post" class="checkout"><input type="hidden" name="t" value="<?=csrf()?>"><label>Password<input type="password" name="pw" required></label><button class="btn">Log in</button></form></div>
<?php foot(); exit; }
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){ chk(); $a=$_POST['a']??'';
 if($a==='add'){
  $img=null; $f=$_FILES['image']??null;
  if($f&&$f['error']===UPLOAD_ERR_OK){
   $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name'])]??null;
   if($ext&&$f['size']<=5*1024*1024){ $img=bin2hex(random_bytes(8)).'.'.$ext; move_uploaded_file($f['tmp_name'],__DIR__.'/uploads/'.$img); } else $msg='Image must be JPG, PNG or WebP under 5MB.';
  }
  if(!$msg){ $pdo->prepare('INSERT INTO products(title,category,size,item_condition,price,descr,image) VALUES(?,?,?,?,?,?,?)')
   ->execute([trim($_POST['title']),trim($_POST['category']),trim($_POST['size']),$_POST['cond'],(int)$_POST['price'],trim($_POST['descr']),$img]); $msg='Piece added.'; }
 }
 if($a==='st') $pdo->prepare('UPDATE products SET status=? WHERE id=?')->execute([in_array($_POST['s'],['available','reserved','sold'])?$_POST['s']:'available',(int)$_POST['id']]);
 if($a==='del'){ $s=$pdo->prepare('SELECT image FROM products WHERE id=?'); $s->execute([(int)$_POST['id']]); if($i=$s->fetchColumn()) @unlink(__DIR__.'/uploads/'.basename($i)); $pdo->prepare('DELETE FROM products WHERE id=?')->execute([(int)$_POST['id']]); }
 if($a==='os'){ $m=['paid'=>'sold','cancelled'=>'available','pending'=>'reserved']; $o=$_POST['s']??''; if(isset($m[$o])){
   $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$o,(int)$_POST['id']]);
   $pdo->prepare('UPDATE products SET status=? WHERE id IN(SELECT product_id FROM order_items WHERE order_id=?)')->execute([$m[$o],(int)$_POST['id']]); } }
}
$prods=$pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
$orders=$pdo->query("SELECT o.*,GROUP_CONCAT(p.title SEPARATOR ', ') items FROM orders o LEFT JOIN order_items i ON i.order_id=o.id LEFT JOIN products p ON p.id=i.product_id GROUP BY o.id ORDER BY o.id DESC LIMIT 50")->fetchAll();
head('Admin'); ?>
<div class="wrap admin">
 <p><a href="admin.php?out=1">Log out</a></p><?php if($msg): ?><p class="note"><?=e($msg)?></p><?php endif; ?>
 <h2>Orders</h2>
 <?php foreach($orders as $o): ?><form method="post" class="row"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="a" value="os"><input type="hidden" name="id" value="<?=$o['id']?>">
  <span><b>#<?=$o['id']?></b> <?=e($o['name'])?>, <a href="tel:<?=e($o['phone'])?>"><?=e($o['phone'])?></a><br><small><?=e($o['items'])?> | <?=e($o['zone'])?> <?=e($o['address'])?></small></span>
  <span><?=kes($o['total'])?></span>
  <select name="s" onchange="this.form.submit()"><?php foreach(['pending','paid','cancelled'] as $s): ?><option <?=$o['status']===$s?'selected':''?>><?=$s?></option><?php endforeach; ?></select></form><?php endforeach; ?>
 <h2>Add a piece</h2>
 <form method="post" enctype="multipart/form-data" class="checkout"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="a" value="add">
  <label>Title<input name="title" required></label><label>Category<input name="category" placeholder="Jackets" required></label>
  <label>Size<input name="size" required></label><label>Condition<select name="cond"><option>Like new</option><option>Great</option><option selected>Good</option><option>Worn</option></select></label>
  <label>Price (KES)<input type="number" name="price" min="0" required></label><label>Notes<textarea name="descr" rows="3"></textarea></label>
  <label>Photo<input type="file" name="image" accept="image/*" id="img"></label><img id="prev" alt="" hidden>
  <button class="btn">Add to rack</button></form>
 <h2>Rack</h2>
 <?php foreach($prods as $r): ?><div class="row"><span><?=e($r['title'])?> <small>size <?=e($r['size'])?></small></span><span><?=kes($r['price'])?></span>
  <form method="post"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="a" value="st"><input type="hidden" name="id" value="<?=$r['id']?>">
   <select name="s" onchange="this.form.submit()"><?php foreach(['available','reserved','sold'] as $s): ?><option <?=$r['status']===$s?'selected':''?>><?=$s?></option><?php endforeach; ?></select></form>
  <form method="post" data-confirm="Delete this piece?"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="a" value="del"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="link">Delete</button></form></div><?php endforeach; ?>
</div>
<?php foot();
