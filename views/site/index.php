<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="body-content">
		<h2>Курсы валют!</h2>
		<div class="row">
				<div class="col-md-3 bold">
					ВАЛЮТА
				</div>
				<div class="col-md-3 bold">
					НАИМЕНЬШАЯ
				</div>
				<div class="col-md-6 bold">
					НАИБОЛЬШАЯ
				</div>
			</div>
		
		<?php foreach ($data as $key => $value): ?>
			<div class="row">
				<div class="col-md-3">
					<?=$key;?> / <span class="small gray"><?=$value["to"];?></span>
				</div>
				<div class="col-md-3">
					<?=$value["rialto"]["low"][1]; ?> <span class="small gray">(<?=$value["rialto"]["low"][0]; ?>)</span>
				</div>
				<div class="col-md-6">
					<?=$value["rialto"]["high"][1]; ?> <span class="small gray">(<?=$value["rialto"]["high"][0]; ?>)</span>
				</div>
			</div>
		<?php endforeach; ?>
    </div>
</div>
