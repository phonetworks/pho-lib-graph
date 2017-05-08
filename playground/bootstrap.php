<?php
/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/************************************************************
 * This script shows you how to interact with the Pho Kernel
 *
 * @author Emre Sokullu
 ************************************************************/
function nl(): void
{
    echo PHP_EOL;
}


 // 1. Initiate the autoloaders first.
require(__DIR__."/../vendor/autoload.php");

use Pho\Lib\Graph;

$world = new Graph\Graph();
$google = new Graph\SubGraph($world);
$mark_zuckerberg = new Graph\Node($world); // facebook
$larry_page = new Graph\Node($google); // google
$vincent_cerf = new Graph\Node($google); // google
$yann_lecun = new Graph\Node($world); // facebook
$brad_fitzpatrick = new Graph\Node($google); // google
$ray_kurzweil = new Graph\Node($google); // google

/************************************************************
 * Here is how you can play with it. Fore more information refer 
 * to the README.md in the root folder.
 ************************************************************/

/*
echo "Members of the World Graph:";
nl();
print_r($world->toArray()["members"]);

nl();
nl();

echo "Members of the Google SubGraph:";
nl();
print_r($google->toArray()["members"]);


nl();
exit(0);
*/
