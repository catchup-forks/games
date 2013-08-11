<?php if (!defined('APPLICATION')) exit();
/**
 * Basic Games - An application for Garden & Vanilla Forums.
 * Copyright (C) 2013  Livid Tech
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * The Games controller.
 *
 * Introduces common methods that child classes can use.
 */
class GamesController extends Gdn_Controller {
   /** @var array List of objects to prep. They will be available as $this->$Name. */
   public $Uses = array('Form', 'GameModel');
   
   /**
    * Include JS, CSS, and modules used by all methods of this controller.
    * Called by dispatcher before controller's requested method.
    */
   public function Initialize() {
      if($this->DeliveryType() == DELIVERY_TYPE_ALL)
         $this->Head = new HeadModule($this);
      $this->AddJsFile('jquery.js');
      $this->AddJsFile('jquery.livequery.js');
      $this->AddJsFile('jquery.form.js');
      $this->AddJsFile('jquery.popup.js');
      $this->AddJsFile('jquery.gardenhandleajaxform.js');
      $this->AddJsFile('global.js');

      $this->AddCssFile('style.css');
      $this->AddCssFile('articles.css');
      
      parent::Initialize();
      $this->AddCssFile('style.css');
      $this->AddCssFile('games.css');
      
      $this->CountCommentsPerPage = C('Vanilla.Comments.PerPage', 30);
		$this->FireEvent('AfterInitialize');
   }



   public function Index($ID = '')
   {

		if ($ID != '')
		{
         $Game = $this->GameModel->GetByUrlCode($ID, TRUE);


            $gameid = $Game->gameid;
			$this->SetData('GameData', $Game);




            //$Description = GetValue('Description', $Game);
            //if ($Description) {
            //   $this->Head->AddTag('meta', array('Name' => 'description', 'content' => Gdn_Format::PlainText($Description, FALSE)));
            //}


            $this->AddCssFile('popup.css');
            $this->AddCssFile('fancyzoom.css');
            $this->AddJsFile('fancyzoom.js');
            $this->AddJsFile('game.js');




            $this->View = 'gamedetail';
				$this->Title($this->Data('gamename'));

            // Set the canonical url.
            $this->CanonicalUrl(Url('/game/'.GameModel::Slug($Addon, FALSE), TRUE));

      }
	  else
	  {

			$this->View = 'browse';
			$this->Browse();
			return;

      }


      
		$this->Render();
   }

	public function Browse($FilterToType = '', $Sort = '', $Page = '') {



		$this->AddJsFile('/js/library/jquery.gardenmorepager.js');
		$this->AddJsFile('browse.js');



      list($Offset, $Limit) = OffsetLimit($Page, Gdn::Config('Garden.Search.PerPage', 10));

         $Title = 'Browse Games';
      $this->SetData('Title', $Title);




		$SortField = 'gamename';
		//   public function GetWhere($Where = FALSE, $OrderFields = '', $OrderDirection = NULL, $Limit = FALSE, $Offset = FALSE)
		$ResultSet = $this->GameModel->GetWhere(FALSE, $SortField, NULL, $Limit, $Offset);







		$this->SetData('Games', $ResultSet);

		$NumResults = $this->GameModel->GetCount(FALSE);

		$this->SetData('TotalGames', $NumResults);
		
		// Build a pager
		$PagerFactory = new Gdn_PagerFactory();
		$Pager = $PagerFactory->GetPager('Pager', $this);
		$Pager->MoreCode = '>>>›';
		$Pager->LessCode = '‹<<<';
		$Pager->ClientID = 'Pager';
		$Pager->Configure(
			$Offset,
			$Limit,
			$NumResults,
			'games/browse/'.$Sort.'/%1$s/?Form/Keywords='.urlencode($Search)
		);
		$this->SetData('_Pager', $Pager);
      
      if ($this->_DeliveryType != DELIVERY_TYPE_ALL)
         $this->SetJson('MoreRow', $Pager->ToString('more'));

			$this->View = 'browse';
		$this->Render();
	}
   
}
