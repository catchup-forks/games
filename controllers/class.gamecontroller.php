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
 * The Game controller.
 */
class GameController extends GamesController {
   /** @var array List of objects to prep. They will be available as $this->$Name. */
   public $Uses = array('GameModel', 'Form');


   /**
    * @var DiscussionModel 
    */
   public $GameModel;



   public function Index($ID = '')
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



      
		$this->Render();
   }


}
