<?php if (!defined('APPLICATION')) exit();

include($this->FetchViewLocation('helper_functions'));
?>


   <h1><?php echo $this->Data('Title'); ?></h1>
   <ul class="DataList Games">
      <?php
      if ($this->Data('TotalGames') == 0)
         echo '<li class="Empty">There were no addons matching your search criteria.</li>';


$Alt = '';
foreach ($this->Data('Games')->Result() as $Game)
{
   $Alt = $Alt == ' Alt' ? '' : ' Alt';
   WriteGame($Game, $Alt);
}



if ($this->DeliveryType() == DELIVERY_TYPE_ALL && $this->Data('_Pager'))
{
?>
   </ul>
   <?php
   echo $this->Data('_Pager')->ToString('more');
}
else
{
?></ul><?php
}
