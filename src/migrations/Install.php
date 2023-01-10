<?php

namespace wsydney76\favorites\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Table;
use wsydney76\favorites\records\UserToEntries;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        if (Craft::$app->db->tableExists(UserToEntries::tableName())) {
            return true;
        }

        $this->createTable(UserToEntries::tableName(), [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'entryId' => $this->integer()->notNull(),
            'dateCreated' => $this->date()->notNull(),
            'dateUpdated' => $this->date()->notNull(),
            'uid' => $this->uid()->notNull()
        ]);

        $this->addForeignKey(null,
            UserToEntries::tableName(), 'entryId',
            Table::ELEMENTS, 'id',
            'CASCADE'
        );

        $this->addForeignKey(null,
            UserToEntries::tableName(), 'userId',
            Table::ELEMENTS, 'id',
            'CASCADE'
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->dropTableIfExists(UserToEntries::tableName());

        return true;
    }
}
