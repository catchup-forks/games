<?php if (!defined('APPLICATION')) exit();

function WriteGame($Game, $Alt) {
   $Url = '/game/'.GameModel::Slug($Game, FALSE);
	?>
	<li class="Item GameRow<?php echo $Alt; ?>">
		<div class="ItemContent">
			<?php
			echo '<div>', Anchor($Game->gamename, $Url, 'Title'), '</div>';
			?>
			<div class="Meta">
				<span class="Platform">
				Platform
				<span><?php echo $Game->Platform; ?></span>
				</span>
				<span class="Publisher">
				Publisher
				<span><?php echo $Game->Publisher; ?></span>
				</span>
				<span class="Developer">
				Developer
				<span><?php echo $Game->Developer; ?></span>
				</span>
				<span class="Hits">
				Hits
				<span><?php echo number_format($Game->Hits); ?></span>
				</span>
				<span class="Updated">
				Updated
				<span><?php echo Gdn_Format::Date($Game->DateUpdated); ?></span>
				</span>
			</div>
		</div>
	</li>
	<?php
}
