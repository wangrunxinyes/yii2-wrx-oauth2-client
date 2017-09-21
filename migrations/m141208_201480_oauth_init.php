<?php

use yii\db\Migration;
use yii\db\Schema;

class m141208_201480_oauth_init extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        // MySql table options
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        // table blog_catalog
        $this->createTable(
            '{{%oauth_wrx_users}}',
            [
                'id' => Schema::TYPE_PK,
            	'username' => Schema::TYPE_STRING . ' NOT NULL',
            	'photo' => Schema::TYPE_STRING.' NULL DEFAULT NULL',
            	'sex' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 1',
            	'extensions' => Schema::TYPE_TEXT,
            	'open_id' => Schema::TYPE_STRING.' NOT NULL',
            	'client_user_id' => Schema::TYPE_INTEGER,
            	'access_token' => Schema::TYPE_TEXT. ' NOT NULL',
            ],
            $tableOptions
        );
        
        $this->createIndex('client_user_id', '{{%oauth_wrx_users}}', 'client_user_id', true);
        $this->createIndex('open_id', '{{%oauth_wrx_users}}', 'open_id', true);
        
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%oauth_wrx_users}}');
    }
}
