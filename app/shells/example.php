<?php
/**
 * shell example
 *
 * class: ExampleShell
 * file: example.php
 * called: /PATH/TO/NFORUM/bin/cli example
 *
 * @extends NF_Shell
 */
class ExampleShell extends NF_Shell {

    /**
     * implement function main with parmas $argc, $argv
     */
    public function main($argc, $argv) {

        //use $this->_initKbs init kbs
        $this->_initKbs();

        //call kbs function
        dump(bbs_getwwwguestnumber());
    }
}
