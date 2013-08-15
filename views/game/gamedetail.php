<?php if (!defined('APPLICATION')) exit();
/**
 * Basic Pages - An application for Garden & Vanilla Forums.
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

$Session = Gdn::Session();

$Game = $this->Data('GameData');
$FormatBody = Gdn_Format::To($Game->Body, $Game->Format);
?>
<div id="Game_<?php echo $Game->gameid; ?>" class="Game-<?php echo $Game->UrlCode; ?>">

	<div class="col-lg-3 panel">
		<div class="Photo PhotoWrap PhotoWrapLarge Online Rank-Mod">
			<a href="/profile/picture?userid=18261" class="ChangePicture"><span>Change Picture</span></a><img src="img/p16QUDLQA6S4A.png" class="ProfilePhotoLarge">
		</div>
		<div class="BoxFilter BoxProfileFilter">
		  <ul class="FilterMenu">
			<li class="Active Activity"><a href="/profile/activity/"><span class="Sprite SpActivity"></span> Activity</a></li>
			<li class="Notifications"><a href="/profile/notifications"><span class="Sprite SpNotifications"></span> Notifications</a></li>
			<li class="Discussions"><a href="/profile/discussions/18261/UnderDog"><span class="Sprite SpDiscussions"></span> Discussions<span class="Aside"> <span class="Count">69</span></span></a></li>
			<li class="Comments"><a href="/profile/comments/18261/UnderDog"><span class="Sprite SpComments"></span> Comments<span class="Aside"> <span class="Count">2017</span></span></a></li>
			<li class="Inbox"><a href="/messages/all"><span class="Sprite SpInbox"></span> Inbox</a></li>
			<li><a href="/profile/addons/18261/UnderDog">Addons</a></li>
			<li class="Warnings"><a href="/profile/warnings/18261/UnderDog"><span class="Sprite SpWarn"></span> Warnings</a></li>
		  </ul>
		</div>
		<div id="Badges" class="Box BadgeGrid">
		  <h4>My Badges</h4>
		  <div class="PhotoGrid">
			<a href="/badge/insightful-100" title="100 Insightfuls"><img src="img/insightful-3.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/answer" title="First Answer"><img src="img/answer.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/answer-25" title="25 Answers"><img src="img/answer-3.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/answer-5" title="5 Answers"><img src="img/answer-2.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/answer-100" title="100 Answers"><img src="img/answer-5.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/answer-50" title="50 Answers"><img src="img/answer-4.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/answer-250" title="250 Answers"><img src="img/answer-6.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/lol-25" title="25 LOLs"><img src="img/lol-2.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/awesome-250" title="250 Awesomes"><img src="img/awesome-4.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/anniversary-2" title="Second Anniversary"><img src="img/anniversary-2.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/photogenic" title="Photogenic"><img src="img/user.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/insightful-25" title="25 Insightfuls"><img src="img/insightful-2.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/lol-5" title="5 LOLs"><img src="img/lol-1.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/insightful-5" title="5 Insightfuls"><img src="img/insightful-1.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/awesome-5" title="5 Awesomes"><img src="img/awesome-1.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/awesome-25" title="25 Awesomes"><img src="img/awesome-2.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/awesome-100" title="100 Awesomes"><img src="img/awesome-3.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/like-100" title="100 Likes"><img src="img/like-4.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/comment-1000" title="1000 Comments"><img src="img/comment-5.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/like-50" title="50 Likes"><img src="img/like-3.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/anniversary" title="First Anniversary"><img src="img/anniversary-1.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/like-10" title="10 Likes"><img src="img/like-2.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/name-dropper" title="Name Dropper"><img src="img/address-book.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/like-1" title="First Like"><img src="img/like-1.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/comment-500" title="500 Comments"><img src="img/comment-4.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/comment-100" title="100 Comments"><img src="img/comment-3.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/comment-10" title="10 Comments"><img src="img/comment-2.png" class="ProfilePhoto ProfilePhotoSmall"></a> <a href="/badge/comment" title="First Comment"><img src="img/comment.png" class="ProfilePhoto ProfilePhotoSmall"></a>
		  </div>
		</div>
	</div><!-- /col-lg-3 panel -->


      <div class="col-lg-9 content">

        <div class="ProfileOptions">
		  <div class="Options">
			 <span class="ToggleFlyout OptionsMenu">
				<span class="OptionsTitle" title="<?php echo T('Options'); ?>"><?php echo T('Options'); ?></span>
				<?php echo Sprite('SpFlyoutHandle', 'Arrow'); ?>
				<ul class="Flyout MenuItems" style="display: none;">
				   <?php echo Wrap(Anchor(T('GamersPortal.Settings.EditGame', 'Edit Game'), 'managegames/editgame/' . $Game->gameid, 'EditGame'), 'li'); ?>
				</ul>
			 </span>
		  </div>
		</div>
        <div class="Profile">
          <div class="User" itemscope="" itemtype="http://schema.org/Person">
            <h1 class="H well"><?php echo $Game->gamename; ?></h1>
            <div class="About P">
              <dl class="About">
                <dt class="Name">Gamename</dt>
                <dd class="Name" itemprop="gamename"><?php echo $Game->gamename; ?></dd>
                <dt class="Developer">Developer</dt>
                <dd class="Developer" itemprop="developer"><span class="Developer"><?php echo $Game->gamename; ?></span></dd>
                <dt class="DateAdded">DateAdded</dt>
                <dd class="DateAdded"><?php echo $Game->gamename; ?></dd>
                <dt class="Visits">Hits</dt>
                <dd class="Visits"><?php echo $Game->gamename; ?></dd>
                <dt class="LastUpdated">Last Updated</dt>
                <dd class="LastActive"><?php echo $Game->gamename; ?></dd>
                <dt class="Platforms">Platforms</dt>
                <dd class="Platforms"><?php echo $Game->gamename; ?>, Platform, Platform, Platform</dd>
                <dt class="Publisher">Publisher</dt>
                <dd class="Publisher"><?php echo $Game->gamename; ?></dd>
                <dt class="IP">Register IP</dt>
                <dd class="IP">n/a</dd>
                <dt class="IP">Last IP</dt>
                <dd class="IP">&nbsp;</dd>
                <dt class="Badges">Badges</dt>
                <dd class="Badges">28</dd>
              </dl>
            </div>
            <div class="ReactionsWrap">
              <h2 class="H">Reactions</h2>
              <div class="DataCounts">
				<span class="CountItemWrap"><span class="CountItem"><a href="/profile/reactions/18261/UnderDog?reaction=promote" class="TextColor"> <span class="CountTotal"><span title="5">5</span></span><span class="CountLabel">Videos</span></a></span></span>
				<span class="CountItemWrap"><span class="CountItem"><a href="/profile/reactions/18261/UnderDog?reaction=insightful" class="TextColor"><span class="CountTotal"><span title="114">114</span></span><span class="CountLabel">Reviews</span></a></span></span>
				<span class="CountItemWrap"><span class="CountItem"><a href="/profile/reactions/18261/UnderDog?reaction=awesome" class="TextColor"><span class="CountTotal"><span title="383">383</span></span> <span class="CountLabel">Previews</span></a></span></span>
				<span class="CountItemWrap"><span class="CountItem"><a href="/profile/reactions/18261/UnderDog?reaction=lol" class="TextColor"> <span class="CountTotal"><span title="55">55</span></span> <span class="CountLabel">News</span></a></span></span>
				<span class="CountItemWrap"><span class="CountItem"><a href="/profile/reactions/18261/UnderDog?reaction=spam" class="TextColor"> <span class="CountTotal"><span title="3">3</span></span> <span class="CountLabel">Links</span></a></span></span>
				<span class="CountItemWrap"><span class="CountItem"><a href="/profile/reactions/18261/UnderDog?reaction=abuse" class="TextColor"> <span class="CountTotal"><span title="5">5</span></span> <span class="CountLabel">Comments</span></a></span></span>
			  </div>
            </div>
          </div>

          <div class="DataListWrap">
            <h2 class="H">Activity</h2>
            <form method="post" action="/activity/post/18261?Target=%2Fprofile%2F18261%2FUnderDog" class="Activity">
              <div>
                <input type="hidden" id="Form_TransientKey" name="TransientKey" value="G2ADWVJ43P3Y">
                <input type="hidden" id="Form_SomeRequiredField" name="SomeRequiredField" value="" style="display: none;">
                <div class="ButtonBar">
                  <div class="BarWrap"><span class="ButtonWrap ButtonBarBold" title="Bold, ctrl+B"><span>bold</span></span><span class="ButtonWrap ButtonBarItalic" title="Italic, ctrl+I"><span>italic</span></span><span class="ButtonWrap ButtonBarUnderline ButtonOff" title="Underline, ctrl+U"><span>underline</span></span><span class="ButtonWrap ButtonBarStrike" title="Strike, ctrl+S"><span>strike</span></span><span class="ButtonWrap ButtonBarCode" title="Code, ctrl+O"><span>code</span></span><span class="ButtonWrap ButtonBarImage" title="Image"><span>image</span></span><span class="ButtonWrap ButtonBarUrl" title="Url, ctrl+L"><span>url</span></span><span class="ButtonWrap ButtonBarQuote" title="Quote, ctrl+Q"><span>quote</span></span><span class="ButtonWrap ButtonBarSpoiler ButtonOff" title="Spoiler"><span>spoiler</span></span></div>
                </div>
                <div class="TextBoxWrapper">
                  <div class="TextBoxWrapper">
                    <textarea id="Form_Comment" name="Comment" format="Markdown" class=" TextBox BodyBox" rows="6" cols="100"></textarea>
                    <div class="ButtonBarMarkupHint">You can use <b><a href="http://en.wikipedia.org/wiki/Markdown" target="_new">Markdown</a></b> in your post.</div>
                  </div>
                  <input type="hidden" id="Form_Format" name="Format" value="Markdown">
                </div>
                <input type="submit" id="Form_Share" name="Share" value="Share" class="Button Primary">
              </div>
            </form>
            <ul class="DataList Activities">
              <li id="Activity_184395" class="Item Activity Activity-Badge HasPhoto">
                <div class="Options"><a href="/dashboard/activity/delete/184395/G2ADWVJ43P3Y?Target=profile%2Funderdog" class="Delete">×</a></div>
                <div class="Author Photo"><a href="/badge/insightful-100" class="PhotoWrap"><img src="img/insightful-3.png" class="ProfilePhoto ProfilePhotoMedium"></a></div>
                <div class="ItemContent Activity">
                  <div class="Title"><a href="/profile/18261/You">You</a> earned the <a href="/badge/insightful-100">100 Insightfuls</a> badge.</div>
                  <div class="Excerpt">You received 100 Insightfuls. When you're liked this much, you'll be an MVP in no time!</div>
                  <div class="Meta"> <span class="MItem DateCreated">July 28</span> <span class="MItem AddComment"><a href="/profile/underdog#CommentForm_184395" class="CommentOption">Comment</a></span> </div>
                </div>
                <ul class="DataList ActivityComments">
                  <li id="ActivityComment_867" class="Item ActivityComment ActivityComment HasPhoto">
                    <div class="Author Photo"><a title="BurkeKnight" href="/profile/44842/BurkeKnight" class="Photo PhotoWrap Offline"><img src="img/nEUUFERMAQNGM.png" alt="BurkeKnight" class="ProfilePhoto ProfilePhotoMedium"></a></div>
                    <div class="ItemContent ActivityComment"> <a href="/profile/44842/BurkeKnight" class="Title Name">BurkeKnight</a>
                      <div class="Excerpt">Congrats! :)</div>
                      <div class="Meta"> <span class="DateCreated">
                        <time title="July 30, 2013  6:09AM" datetime="2013-07-30T04:09:35+00:00">July 30</time>
                        </span> <a href="/dashboard/activity/deletecomment?id=867&tk=G2ADWVJ43P3Y&target=profile%2Funderdog" class="DeleteComment">Delete</a> </div>
                    </div>
                  </li>
                  <li id="ActivityComment_870" class="Item ActivityComment ActivityComment HasPhoto">
                    <div class="Author Photo"><a title="UnderDog" href="/profile/18261/UnderDog" class="Photo PhotoWrap Online Rank-Mod"><img src="img/n16QUDLQA6S4A.png" alt="UnderDog" class="ProfilePhoto ProfilePhotoMedium"></a></div>
                    <div class="ItemContent ActivityComment"> <a href="/profile/18261/UnderDog" class="Title Name">UnderDog</a>
                      <div class="Excerpt">thanks man</div>
                      <div class="Meta"> <span class="DateCreated">
                        <time title="August  3, 2013 11:16AM" datetime="2013-08-03T09:16:36+00:00">August 3</time>
                        </span> <a href="/dashboard/activity/deletecomment?id=870&tk=G2ADWVJ43P3Y&target=profile%2Funderdog" class="DeleteComment">Delete</a> </div>
                    </div>
                  </li>
                  <li class="CommentForm"> <a href="/dashboard/activity/comment/184395" class="CommentLink">Write a comment</a>
                    <form method="post" action="/dashboard/activity/comment" class="Hidden">
                      <div>
                        <input type="hidden" id="Form_TransientKey" name="TransientKey" value="G2ADWVJ43P3Y">
                        <input type="hidden" id="Form_SomeRequiredField" name="SomeRequiredField" value="" style="display: none;">
                        <input type="hidden" id="Form_ActivityID" name="ActivityID" value="184395">
                        <input type="hidden" id="Form_Return" name="Return" value="profile/underdog">
                        <div class="TextBoxWrapper">
                          <textarea id="Form_Body" name="Body" rows="6" cols="100" class="TextBox"></textarea>
                        </div>
                        <div class="Buttons">
                          <input type="submit" id="Form_Comment" name="Comment" value="Comment" class="Button Primary">
                        </div>
                      </div>
                    </form>
                  </li>
                </ul>
              </li>
            </ul><!-- /DataList Activities -->
          </div>
        </div>
	</div>
	

</div>

