<?php

$output = shell_exec("git fetch --depth=1; git reset --hard origin/master;");

?>

<pre>
<?=$output?>
</pre>