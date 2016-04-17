<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-ca">
<head>
   <meta name="description" content="">
   <?php $this->RenderAsset('Head'); ?>
</head>
<body id="<?php echo $BodyIdentifier; ?>" class="<?php echo $this->CssClass; ?>">
   <div id="Frame">
   <div class="container">

      <div id="Head">
         <div class="Menu">

         	<!-- WEBSITE TITLE (or logo) -->
            <h1><a class="Title" href="<?php echo Url('/'); ?>"><span><?php echo Gdn_Theme::Logo(); ?></span></a></h1>

            <!-- BEGIN NAVIGATION / MENU -->
            <div id="MenuWrap">
	            <?php
				      $Session = Gdn::Session();
						if ($this->Menu) {
							$this->Menu->AddLink('Dashboard', T('Dashboard'), '/dashboard/settings', array('Garden.Settings.Manage'), array('class' => 'Dashboard'));
							// $this->Menu->AddLink('Dashboard', T('Users'), '/user/browse', array('Garden.Users.Add', 'Garden.Users.Edit', 'Garden.Users.Delete'),  array('class' => 'Users'));
							$this->Menu->AddLink('Activity', T('Activity'), '/activity', FALSE, array('class' => 'Activity'));
							if ($Session->IsValid()) {
								$Name = $Session->User->Name;
								$CountNotifications = $Session->User->CountNotifications;
								if (is_numeric($CountNotifications) && $CountNotifications > 0)
									$Name .= ' <span class="Alert">'.$CountNotifications.'</span>';

	                     if (urlencode($Session->User->Name) == $Session->User->Name)
	                        $ProfileSlug = $Session->User->Name;
	                     else
	                        $ProfileSlug = $Session->UserID.'/'.urlencode($Session->User->Name);
								$this->Menu->AddLink('User', $Name, '/profile/'.$ProfileSlug, array('Garden.SignIn.Allow'), array('class' => 'UserNotifications'));
								$this->Menu->AddLink('SignOut', T('Sign Out'), SignOutUrl(), FALSE, array('class' => 'NonTab SignOut'));
							} else {
								$Attribs = array();
								if (SignInPopup() && strpos(Gdn::Request()->Url(), 'entry') === FALSE)
									$Attribs['class'] = 'SignInPopup';

								$this->Menu->AddLink('Entry', T('Sign In'), SignInUrl($this->SelfUrl), FALSE, array('class' => 'NonTab SignIn'), $Attribs);
							}
							echo $this->Menu->ToString();
						}
					?>

				<!-- BEGIN SEARCH BAR -->
            	<div class="Search"><?php
					$Form = Gdn::Factory('Form');
					$Form->InputPrefix = '';
					echo
						$Form->Open(array('action' => Url('/search'), 'method' => 'get')),
						$Form->TextBox('Search'),
						$Form->Button('Go', array('Name' => '')),
						$Form->Close();
				?></div>
				<!-- END SEARCH BAR -->

            </div>
            <!-- END NAVIGATION / MENU -->

         </div>
      </div>

      <div id="Body">

		 <!-- BEGIN CONTENT BODY -->
         <div id="Content">
         	<?php $this->RenderAsset('Content'); ?>
         </div>
         <!-- END CONTENT BODY -->

         <!-- BEGIN SIDEBAR -->
         <div id="Panel">
         	<?php $this->RenderAsset('Panel'); ?>
         </div>
         <!-- END SIDEBAR -->

      </div>

      <div class="clearall"></div>

      <!-- BEGIN WEBSITE BOTTOM FOOTER -->
      <div id="Foot">
      	 <div id="inner-Foot">
			<?php
				$this->RenderAsset('Foot');
				//echo Wrap(Anchor(T('Powered by Vanilla'), C('Garden.VanillaUrl')), 'div');
			?>

			<!-- COPYRIGHT -->
			Designed by <a href="http://www.ourwebmedia.com">O.W.M Consulting</a> &copy; 2014
		</div>
     </div>
     <!-- END WEBSITE BOTTOM FOOTER -->

   </div>

	<?php $this->FireEvent('AfterBody'); ?>

	<!-- BEGIN GOOGLE WEB FONTS
		 Using https://www.google.com/fonts you can link to a different font. Remember to update the CSS -->
	<!--<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700' rel='stylesheet' type='text/css'>-->
	<!-- END GOOGLE WEB FONTS -->

</body>
</html>