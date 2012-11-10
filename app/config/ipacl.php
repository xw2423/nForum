<?php
/**
 * plz set ipacl.on=TRUE in app/config/nforum.php
 */
$config['ipacl']['global'] = array();
$config['ipacl']['wsapi']['sys_checkpwd'] = array(
    array("0.0.0.1", 0, false),
    array("::1", 0, false)
);
$config['ipacl']['wsapi']['u_setpwd'] = array(
    array("0.0.0.1", 0, false),
    array("::1", 0, false)
);
$config['ipacl']['wsapi']['a_autopost'] = array(
    array("0.0.0.1", 0, false),
    array("::1", 0, false)
);
$config['ipacl']['wsapi']['m_automail'] = array(
    array("0.0.0.1", 0, false),
    array("::1", 0, false)
);
$config['ipacl']['wsapi']['sys_test'] = array(
    array("0.0.0.1", 0, false),
    array("::1", 0, false)
);
?>
