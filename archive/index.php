<?php
/**
 * Created by PhpStorm.
 * User: brwarner
 * Date: 22/02/14
 * Time: 12:20 AM
 */

require_once(dirname(__FILE__) . "/../require/basic.php");
require_once(dirname(__FILE__) . "/../require/rarchive.php");

page_unsecure_head();
?>
<h1>Game Archives</h1>
<p>View data from previous games in a variety of formats</p>
<?php
$archives = get_archives();
foreach($archives as $archive)
{
    echo "<strong>{$archive['name']}</strong><br/>";
    $id = $archive['id'];
    ?>
    <ul>
        <li><a href="players.php?archive=<?php echo $id;?>">Player List</a></li>
        <li><a href="graph.php?archive=<?php echo $id;?>">Graph</a></li>
        <li><a href="tree.php?archive=<?php echo $id;?>">Zombie Tree</a></li>
    </ul>
    <?php
}
page_unsecure_foot();