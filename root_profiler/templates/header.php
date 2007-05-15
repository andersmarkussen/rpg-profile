<?php global $sid, $REQUIRE_LOGIN, $DISPLAY_IMAGES, $DISPLAY_FAQ, $SITE_CSS, $FORUM, $USE_SIDEBAR, $NEW_WINDOW; ?>

<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
  <head>
    <link rel="stylesheet" type="text/css" href="<?php echo $SITE_CSS; ?>" title="Main Stylesheet" />
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=iso-8859-1" />
    <title>RPG Web Profiler :: <?php echo getTitle(); ?></title>
    <?php echo getHead(); ?>
  </head>
  <body>

    <!-- ###### Header ###### -->

    <div id="header">
      <span><a href="<?php echo getUriHome(); ?>">
        <img height="73" src="<?php echo getLogo(); ?>" border="0"></a></span>
      <?php if( !$USE_SIDEBAR || !($sid && loggedIn()) ) { ?>
      <div class="headerLinks">
		<a href="<?php echo getUriHome(); ?>">Home</a>

        <?php if( !$FORUM && !loggedIn() ) { ?>
          | <a href="<?php echo getUriBase(); ?>login.php">Login</a>
        <?php } else if( $sid && loggedIn() ) { ?>
          | <a href="<?php echo getUriBase(); ?>cview.php">Characters</a>
        <?php if( $sid->IsDM() ) { ?>
		  | <a href="<?php echo getUriBase(); ?>campaigns.php">Campaigns</a>
        <?php } ?>
          | <a href="<?php echo getUriBase(); ?>pview.php">Profile</a>
        <?php } ?>
        <?php if( !$REQUIRE_LOGIN || loggedIn() ) { ?>
          | <a href="<?php echo getUriBase(); ?>search.php">Search</a>
        <?php if( $DISPLAY_IMAGES ) { ?>
          | <a href="<?php echo getUriBase(); ?>charimg.php">Images</a>
		<?php } ?>

        <?php } ?>
        <?php if( $DISPLAY_FAQ ) { ?>
        | <a href="<?php echo getUriBase(); ?>faq.php">FAQ</a>
        <?php } ?>
        <?php if( !$FORUM && $sid && loggedIn() ) { ?>
        | <a href="<?php echo getUriBase(); ?>logout.php">Logout</a><br>
        <?php } ?>

      </div>
      <?php } ?>
    </div>


<table id="bodyTable">
<tr style="margin: 0px; padding: 0px;">
<?php if( $sid && $USE_SIDEBAR && loggedIn() ) { ?>
	<td id="quickMenu">

	<a href="<?php echo getUriHome(); ?>">Home</a><br>
	<a href="<?php echo getUriBase(); ?>cview.php">Characters</a><br>

	<?php
	$characters = $sid->GetCharacters();
	if( count( $characters ) > 0 ) {
	  foreach( $characters as $character ) { ?>
      &nbsp;&nbsp;<a class="short" href="view.php?id=<?php echo $character['id']; ?>" <?php if( $NEW_WINDOW ) { ?>target="_blank"<?php } ?>><?php echo $character['name']; ?></a><br/>
	<?php } } ?>

	<?php if( $sid->IsDM() ) { ?>
		<a href="<?php echo getUriBase(); ?>campaigns.php">Campaigns</a><br>

	<?php
	$campaigns = $sid->GetCampaigns();

	if( count( $campaigns ) > 0 ) {
	  foreach( $campaigns as $campaign ) { ?>
      &nbsp;&nbsp;<a class="short" href="view_campaign.php?id=<?php echo $campaign['id']; ?>"><?php echo $campaign['name']; ?></a><br/>

      <?php
      	$camp = new Campaign((int) $campaign['id']);
		$characters = $camp->GetCharacters();

      	if( count( $characters ) > 0 ) { ?>
		  <?php foreach( $characters as $character ) { ?>
		    &nbsp;&nbsp;&nbsp;&nbsp;<a class="short" href="view.php?id=<?php echo $character['id']; ?>" <?php if( $NEW_WINDOW ) { ?>target="_blank"<?php } ?>><?php echo $character['name']; ?></a><br/>
		  <?php } } ?>

	<?php } } ?>
	<?php } ?>
	<a href="<?php echo getUriBase(); ?>pview.php">Profile</a><br>
	<?php if( $DISPLAY_IMAGES ) { ?>
		<a href="<?php echo getUriBase(); ?>charimg.php">Images</a><br>
	<?php } ?>
	<?php if( $DISPLAY_FAQ ) { ?>
		<a href="<?php echo getUriBase(); ?>faq.php">FAQ</a><br>
	<?php } ?>
	<a href="<?php echo getUriBase(); ?>search.php">Search</a><br>
    <?php if( !$FORUM ) { ?>
	<a href="<?php echo getUriBase(); ?>logout.php">Logout</a><br>
	<?php } ?>



	</td>
<?php } ?>

<td id="bodyText">

