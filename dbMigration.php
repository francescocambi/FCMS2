<?php

//require_once("bootstrap.php");


$colors = new Colors();

/* Initialize connection with old db */
try {
    $oldDbPdo = new PDO("mysql:dbname=framework_new;host=localhost","root","mysql");
    $oldDbPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("\n".$e->getMessage()."\n");
}

try {
    $newDbPdo = new PDO("mysql:dbname=framework_doc;host=localhost","root","mysql");
    $newDbPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $newDbPdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
} catch (PDOException $e) {
    exit("\n".$e->getMessage()."\n");
}

echo "\n";

/* Checks constraints on old db */

//Checks BLOCK.NAME unique constraint
$blockUnique = check_unique_constraint($oldDbPdo, "A.ID, A.NAME, B.NAME, B.ID", "BLOCK A, BLOCK B", "A.ID <> B.ID AND A.NAME = B.NAME");

//Checks PAGE.NAME unique constraint
$pageUnique = check_unique_constraint($oldDbPdo, "A.ID, A.NAME, B.NAME, B.ID", "PAGE A, PAGE B", "A.ID <> B.ID AND A.NAME = B.NAME");

//If all is ok
if (!$blockUnique || !$pageUnique) exit();

echo ">> ALL OK! MIGRATION WILL BEGIN <<\n";

/* Migration begins! All operations are made in one transaction.
 * If something goes wrong script exits and transaction will be rollbacked. */
$newDbPdo->beginTransaction();
//$newDbPdo->exec("SET FOREIGN_KEY_CHECKS=0");

//Table: MENU -> Menu
migrate_table($oldDbPdo, $newDbPdo, "MENU", "Menu", array(
    "ID" => "id",
    "NAME" => "name",
    "DESCRIPTION" => "description"
));

//Table: MENU_ITEM -> MenuItem
migrate_table($oldDbPdo, $newDbPdo, "MENU_ITEM", "MenuItem", array(
    "ID" => "id",
    "LABEL" => "label",
    "URL" => "url",
    "ITEM_ORDER" => "itemOrder",
    "HIDDEN" => "hidden",
    "MENU_ID" => "menu_id",
    "MENU_ITEM_ID" => "parent_id"
));

//Table: SETTINGS -> Setting
migrate_table($oldDbPdo, $newDbPdo, "SETTINGS", "Setting", array(
    "SET_KEY" => "settingKey",
    "SET_VALUE" => "settingValue"
));

//Table: BLOCK -> Block
migrate_table($oldDbPdo, $newDbPdo, "BLOCK", "Block", array(
    "ID" => "id",
    "NAME" => "name",
    "DESCRIPTION" => "description",
    "BG_URL" => "bgurl",
    "BG_RED" => "bgred",
    "BG_GREEN" => "bggreen",
    "BG_BLUE" => "bgblue",
    "BG_OPACITY" => "bgopacity",
    "BG_REPEATX" => "bgrepeatx",
    "BG_REPEATY" => "bgrepeaty",
    "BG_SIZE" => "bgsize",
    "BLOCK_STYLE_CLASSNAME" => "blockStyleClassName"
), array(
    "blockType" => "contentblock"
), array(
    "BLOCK_STYLE_CLASSNAME" => "(SELECT CLASSNAME FROM BLOCK_STYLE_META WHERE ID = BLOCK_STYLE_META_ID)"
));

//Table: BLOCK_CONTENT -> ContentBlock
migrate_table($oldDbPdo, $newDbPdo, "BLOCK_CONTENT", "ContentBlock", array(
    "ID" => "id",
    "CONTENT" => "content"
));

//Table: GROUPS -> AccessGroup
migrate_table($oldDbPdo, $newDbPdo, "GROUPS", "AccessGroup", array(
    "ID" => "id",
    "NAME" => "name",
    "DESCRIPTION" => "description"
));

//Table: LANGUAGES -> Language
migrate_table($oldDbPdo, $newDbPdo, "LANGUAGES", "Language", array(
    "ID" => "id",
    "DESCRIPTION" => "description",
    "CODE" => "code",
    "FLAG_IMG_URL" => "flagImageURL",
    "MENU_ID" => "menu_id"
));

//Table: BLOCK_LANG -> contentblock_language
migrate_table($oldDbPdo, $newDbPdo, "BLOCK_LANG", "contentblock_language", array(
    "BLOCK_ID" => "contentblock_id",
    "LANGUAGE_ID" => "language_id"
));

//Table: PAGE -> Page
migrate_table($oldDbPdo, $newDbPdo, "PAGE", "Page", array(
    "ID" => "id",
    "NAME" => "name",
    "TITLE" => "title",
    "PUBLISHED" => "published",
    "PUBLIC" => "public",
    "LANGUAGE_ID" => "language_id"
));

//Table PAGE_BLOCK -> PageBlock
migrate_table($oldDbPdo, $newDbPdo, "PAGE_BLOCK", "PageBlock", array(
    "VIEWORDER" => "blockOrder",
    "BLOCK_ID" => "block_id",
    "PAGE_ID" => "page_id"
));

//Table URL -> Url
migrate_table($oldDbPdo, $newDbPdo, "URL", "Url", array(
    "URL" => "url",
    "PAGE_ID" => "page_id"
));

//$newDbPdo->exec("SET FOREIGN_KEY_CHECKS=1");
$newDbPdo->commit();

echo $colors->getColoredString(">> MIGRATION SUCCESFULLY COMPLETED! <<", 'white', 'green');
echo "\n";


/* ---------------------------------------- PROCEDURES -------------------------------------- */
/**
 * @param $oldDbPdo PDO Connection PDO with old database
 * @param $newDbPdo PDO Connection PDO with new database
 * @param $oldName string Old Table Name
 * @param $newName string New Table Name
 * @param array $fieldMapping Array with "OLDFIELDNAME" => "newFieldName" items
 * @param array $fieldsValue Array with "newFieldName" => "staticValueForAllRows" items
 * @param array $subqueries Array with "fieldNameInSelectList" => "(subquery sql)"
 * NOTE: fieldNameInSelectList must appear in $fieldMapping array to map subquery result on the destination table
 */
function migrate_table($oldDbPdo, $newDbPdo, $oldName, $newName, array $fieldMapping, array $fieldsValue = null, array $subqueries = null) {
    $colors = new Colors();

    //Prepare query to extract rows from old table
    $selectSql = "SELECT ";
    foreach ($fieldMapping as $oldField => $newField)
        if (!isset($subqueries[$oldField]))
            $selectSql .= $oldField.", ";
    if (!is_null($subqueries)) {
        foreach ($subqueries as $subFieldName => $subquery)
            $selectSql .= $subquery." AS ".$subFieldName.", ";
    }
    $selectSql = substr($selectSql, 0, strlen($selectSql)-2);
    $selectSql .= " FROM ".$oldName;

    //Execute select query
    try {
        $statement = $oldDbPdo->prepare($selectSql);
        $statement->execute();
        $tableRows = $statement->fetchAll();
    } catch (PDOException $e) {
        $newDbPdo->rollBack();
        echo "\nEXCEPTION => ";
        echo $colors->getColoredString($e->getMessage(), 'red','white');
        echo "\n";
        exit($e->getTraceAsString()."\n\n");
    }

    //Prepares insert query to populate new table
    $insertQuery = "INSERT INTO ".$newName." (";
    //Iterates over fields mapping
    foreach ($fieldMapping as $oldField => $newField)
        $insertQuery .= $newField.", ";
    //Iterates over fields values
    if (!is_null($fieldsValue)) {
        foreach ($fieldsValue as $fieldName => $fieldValue)
            $insertQuery .= $fieldName.", ";
    }
    $insertQuery = substr( $insertQuery, 0, strlen($insertQuery)-2 );
    $insertQuery .= ") VALUES ";

    //Creates Values Clause for each row of the old table
    $valuesClauses = array();
    foreach ($tableRows as $row) {
        $s = "(";
        foreach ($fieldMapping as $oldField => $newField) {
            if (is_null($row[$oldField]))
                $s .= "NULL, ";
            else
                $s .= $newDbPdo->quote($row[$oldField]).", ";
        }
        if (!is_null($fieldsValue)) {
            foreach ($fieldsValue as $fieldName => $fieldValue)
                $s .= $newDbPdo->quote($fieldValue).", ";
        }
        $s = substr($s, 0, strlen($s)-2).")";

        array_push($valuesClauses, $s);
    }

    //Executes all prepared queries one by one
    try {
        foreach ($valuesClauses as $valuesClause)
            $newDbPdo->exec($insertQuery.$valuesClause);
    } catch (PDOException $e) {
        $newDbPdo->rollBack();
        echo "\nEXCEPTION => ";
        echo $colors->getColoredString($e->getMessage(), 'red','white');
        echo "\n";
        exit($e->getTraceAsString()."\n\n");
    }

    echo $colors->getColoredString(">> Migration COMPLETED for table ".$newName." <<", 'white', 'green');
    echo "\n";

}

/**
 * @param $pdo
 * @param $selectList string
 * @param $fromClause string
 * @param null $whereClause string
 * @return bool True if there aren't records that violate unique constraint defined on the new table
 */
function check_unique_constraint($pdo, $selectList, $fromClause, $whereClause = null) {

    $colors = new Colors();
    $columns = explode(",",$selectList);
    $fromsplitted = explode(",", $fromClause);
    $fromsplitted = explode(" ", $fromsplitted[0]);
    $table = $fromsplitted[0];
    $constrCol = substr($columns[1], strpos($columns[1] ,".")+1);

    //Prepare sql query
    if (is_null($whereClause))
        $sql = "SELECT ".$selectList." FROM ".$fromClause;
    else
        $sql = "SELECT ".$selectList." FROM ".$fromClause." WHERE ".$whereClause;

    //Execute query
    try {
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();
    } catch (PDOException $e) {
        echo "\nEXCEPTION => ";
        echo $colors->getColoredString($e->getMessage(), 'red','white');
        echo "\n";
        exit($e->getTraceAsString()."\n\n");
    }

    //Analyze results
    if ($statement->rowCount() > 0) {
        //Test failed
        echo $colors->getColoredString(str_pad("CONSTRAINT VIOLATIONS FOUND FOR ".$table.".".$constrCol, 120, "-", STR_PAD_BOTH), 'white', 'red');
        echo "\n";
        echo ">> ".str_pad("ID", 4)."\t".str_pad($constrCol, 40)."\t<-->\t".str_pad($constrCol, 40)."\t".str_pad("ID", 4)." <<\n";
        foreach ($results as $row)
            echo ">> ".str_pad($row[0],4)."\t".str_pad($row[1], 40)."\t<-->\t".str_pad($row[2], 40)."\t".str_pad($row[3], 4)." <<\n";
        echo str_pad("", 120, "-");
        echo "\n";
        return false;
    } else {
        //Test Passed
        echo $colors->getColoredString(">> ".$table." ".$constrCol." CONSTRAINT CHECK PASSED! <<", 'white', 'green');
        echo "\n";
        return true;
    }
}

class Colors {
    private $foreground_colors = array();
    private $background_colors = array();

    public function __construct() {
        // Set up shell colors
        $this->foreground_colors['black'] = '0;30';
        $this->foreground_colors['dark_gray'] = '1;30';
        $this->foreground_colors['blue'] = '0;34';
        $this->foreground_colors['light_blue'] = '1;34';
        $this->foreground_colors['green'] = '0;32';
        $this->foreground_colors['light_green'] = '1;32';
        $this->foreground_colors['cyan'] = '0;36';
        $this->foreground_colors['light_cyan'] = '1;36';
        $this->foreground_colors['red'] = '0;31';
        $this->foreground_colors['light_red'] = '1;31';
        $this->foreground_colors['purple'] = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown'] = '0;33';
        $this->foreground_colors['yellow'] = '1;33';
        $this->foreground_colors['light_gray'] = '0;37';
        $this->foreground_colors['white'] = '1;37';

        $this->background_colors['black'] = '40';
        $this->background_colors['red'] = '41';
        $this->background_colors['green'] = '42';
        $this->background_colors['yellow'] = '43';
        $this->background_colors['blue'] = '44';
        $this->background_colors['magenta'] = '45';
        $this->background_colors['cyan'] = '46';
        $this->background_colors['light_gray'] = '47';
    }

    // Returns colored string
    public function getColoredString($string, $foreground_color = null, $background_color = null) {
        $colored_string = "";

        // Check if given foreground color found
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
        }
        // Check if given background color found
        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
        }

        // Add string and end coloring
        $colored_string .=  $string . "\033[0m";

        return $colored_string;
    }

    // Returns all foreground color names
    public function getForegroundColors() {
        return array_keys($this->foreground_colors);
    }

    // Returns all background color names
    public function getBackgroundColors() {
        return array_keys($this->background_colors);
    }
}