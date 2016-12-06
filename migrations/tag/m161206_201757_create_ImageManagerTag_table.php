<?php

use yii\db\Migration;

/**
 * Handles the creation of table `imagemanagertag`.
 */
class m161206_201757_create_ImageManagerTag_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
		//ImageManagerTag: create table
        $this->createTable('ImageManagerTag', [
            'id' => $this->primaryKey(),
			'name' => $this->string(128)->notNull(),
			'created' => $this->datetime()->notNull(),
			'modified' => $this->datetime(),
        ]);
		
		//ImageManagerTag: alter id column
		$this->alterColumn('ImageManagerTag', 'id', 'INT(10) UNSIGNED NOT NULL AUTO_INCREMENT');
		
		//ImageManager_ImageManagerTag: create relation table
		$this->createTable('ImageManager_ImageManagerTag', [
            'ImageManager_id' => $this->integer(),
			'ImageManagerTag_id' => $this->integer(),
			'PRIMARY KEY (ImageManager_id, ImageManagerTag_id)'
        ]);
		
		//ImageManagerTag: alter ImageManager_id
		$this->alterColumn(
			'ImageManager_ImageManagerTag',
			'ImageManager_id',
			'INT(10) UNSIGNED NOT NULL'
		);
		$this->addForeignKey(
			'ImageManager_ImageManagerTag_ibfk_1',
			'ImageManager_ImageManagerTag',
			'ImageManager_id',
			'ImageManager',
			'id',
			'CASCADE',
			'CASCADE'
		);
		
		//ImageManagerTag: alter ImageManagerTag_id
		$this->alterColumn(
			'ImageManager_ImageManagerTag',
			'ImageManagerTag_id',
			'INT(10) UNSIGNED NOT NULL'
		);
		$this->addForeignKey(
			'ImageManager_ImageManagerTag_ibfk_2',
			'ImageManager_ImageManagerTag',
			'ImageManagerTag_id',
			'ImageManagerTag',
			'id',
			'CASCADE',
			'CASCADE'
		);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
		//drop: ImageManager_ImageManagerTag
        $this->dropTable('ImageManager_ImageManagerTag');
		//drop: ImageManagerTag
		$this->dropTable('ImageManagerTag');
    }
}
