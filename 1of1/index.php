<?php require 'inc.php';
$cat=$_GET['cat']??''; $sort=$_GET['sort']??'new';
$o=['new'=>'id DESC','low'=>'price ASC','high'=>'price DESC'][$sort]??'id DESC';
$s=$pdo->prepare('SELECT * FROM products'.($cat!==''?' WHERE category=?':'').' ORDER BY status="sold", '.$o.' LIMIT 80');
$s->execute($cat!==''?[$cat]:[]); $items=$s->fetchAll();
$cats=$pdo->query('SELECT DISTINCT category FROM products ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
head(); ?>
<div class="wrap">
 <section class="hero"><h1>1 of 1.</h1><p>Mtumba picked by hand. Each piece is the only one we have, so when it's gone, it's gone.</p></section>
 <form class="filters" method="get">
  <a class="chip <?=$cat===''?'on':''?>" href="index.php">All</a>
  <?php foreach($cats as $c): ?><a class="chip <?=$cat===$c?'on':''?>" href="?cat=<?=urlencode($c)?>"><?=e($c)?></a><?php endforeach; ?>
  <?php if($cat!==''): ?><input type="hidden" name="cat" value="<?=e($cat)?>"><?php endif; ?>
  <select name="sort" onchange="this.form.submit()" aria-label="Sort">
   <option value="new" <?=$sort==='new'?'selected':''?>>Newest</option>
   <option value="low" <?=$sort==='low'?'selected':''?>>Price, low to high</option>
   <option value="high" <?=$sort==='high'?'selected':''?>>Price, high to low</option></select>
 </form>
 <?php if(!$items): ?><p class="empty">Nothing on the rack yet. New pieces drop after every bale.</p><?php endif; ?>
 <div class="grid"><?php foreach($items as $r) echo tag($r); ?></div>
</div>
<?php foot();
