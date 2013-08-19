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
   public $Uses = array('GameModel', 'GenreModel', 'Form');


   /**
    * @var DiscussionModel 
    */
   public $GameModel;



   public function Index($ID = '')
   {


         $Game = $this->GameModel->GetByUrlCode($ID, TRUE);


		/*		 
		 // Load the discussion record
		  $DiscussionID = (is_numeric($DiscussionID) && $DiscussionID > 0) ? $DiscussionID : 0;
		  if (!array_key_exists('Discussion', $this->Data))
			 $this->SetData('Discussion', $this->DiscussionModel->GetID($DiscussionID), TRUE);
		*/
			 

		  if(!isset($ID))
		  {
			 throw new Exception(sprintf(T('%s Not Found'), T('Game')), 404);
		  }


		 $gameid = $Game->gameid;
		$this->SetData('GameData', $Game);


/*
[gameid] => 12071
[platformid] => 10
[gamecatid] => 9991
[zipname] => zzyzzyxx
[gamename] => Zzyzzyxx
[publisherid] => 1
[publisher] => Unknown
[s_publisher] => 
[developerid] => 1
[developer] => Unknown
[datestring] => 1983
[dateinserted] => 2000-01-01
[dateupdated] => 0000-00-00
[description] => Collect gifts for your sweetie and reach her at the top. Known as \"Brix\" <br />
prior to the Cinematronics release.
*/



			$this->SetData('GenreID', $this->genreid = $this->GameData->gamecatid, TRUE);
		  //$this->SetData('Breadcrumbs', GenreModel::GetAncestors($this->GenreID));
      

			$Genre = GenreModel::Genres($Game->gamecatid);









			/*
			  if ($GenreCssClass = GetValue('CssClass', $Genre))
				 Gdn_Theme::Section($GenreCssClass);
		*/
			$this->SetData('GenreData', $Genre);


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
