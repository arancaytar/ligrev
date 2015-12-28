<?php

namespace Ligrev;

l("Initializing database...", "DBAL");
$db = \Doctrine\DBAL\DriverManager::getConnection(['url' => $config['db']], new \Doctrine\DBAL\Configuration());
$sm = $db->getSchemaManager();
$fromSchema = $sm->createSchema();

// Initialize existing schema database.
$schema = new \Doctrine\DBAL\Schema\Schema();
$tables = [];

// table faq
$tables['faq'] = $schema->createTable("faq");
$tables['faq']->addColumn("id", "integer", ["unsigned" => true, "autoincrement" => true]);
$tables['faq']->addColumn("room", "text");
$tables['faq']->addColumn("keyword", "text");
$tables['faq']->addColumn("author", "text");
$tables['faq']->addColumn("message", "text");
$tables['faq']->setPrimaryKey(["id"]);

// table rss
$tables['rss'] = $schema->createTable("rss");
$tables['rss']->addColumn("url", "string", ["length" => 255]);
$tables['rss']->addColumn("request", "integer", ["notnull" => false]);
$tables['rss']->addColumn("latest", "integer", ["notnull" => false]);
$tables['rss']->setPrimaryKey(["url"]);

// table tell
$tables['tell'] = $schema->createTable("tell");
$tables['tell']->addColumn("id", "integer", ["unsigned" => true, "autoincrement" => true]);
$tables['tell']->addColumn("sender", "text");
$tables['tell']->addColumn("recipient", "text");
$tables['tell']->addColumn("sent", "integer", ["unsigned" => true]);
$tables['tell']->addColumn("private", "boolean");
$tables['tell']->addColumn("message", "text");
$tables['tell']->setPrimaryKey(["id"]);

$comparator = new \Doctrine\DBAL\Schema\Comparator();
$schemaDiff = $comparator->compare($fromSchema, $schema);
$sql = $schemaDiff->toSaveSql($db->getDatabasePlatform());
$total_changes = count($sql);

if ($total_changes > 0) {
  l(sprintf("Schema needs initialization or upgrade, executing %s SQL statement(s) to correct...", $total_changes), "DBAL");
  foreach ($sql as $s) {
    $db->exec($s);
  }
} else {
  l("Schema up to date.", "DBAL");
}
