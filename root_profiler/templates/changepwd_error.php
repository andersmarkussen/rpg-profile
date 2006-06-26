<!--
  changepwd_error.php

  3EProfiler (tm) template file.
  Copyright (C) 2003 Michael J. Eggertson.

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

  **

  Error document for changing the user's profile details.
-->
<?php global $messages; ?>


<h1>Error</h1>

<p>
  Your password wasn't updated because one or more errors occurred. Note the errors below, then return to the <a href="javascript:history.back(1)">previous screen</a> and attempt to fix them. If you get a message saying that your key is no longer valid, that is because you (or someone) has since logged into your profile since your email containing your key was sent out. If this happens, you will need to <a href="resetpwd.php">reset</a> your password again.
</p>
<ul>
<?php foreach( $messages as $msg ) { ?>
  <li><?php echo $msg; ?></li>
<?php } ?>
</ul>