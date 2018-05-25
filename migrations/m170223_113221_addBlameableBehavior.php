<?php

use yii\db\Migration;

class m170223_113221_addBlameableBehavior extends Migration
{
    public function up()
    {
        $this->addColumn('{{%imagemanager}}', 'createdBy', $this->integer(10)->unsigned()->null()->defaultValue(null));
        $this->addColumn('{{%imagemanager}}', 'modifiedBy', $this->integer(10)->unsigned()->null()->defaultValue(null));
    }

    public function down()
    {
    	$this->dropColumn('{{%imagemanager}}', 'createdBy');
    	$this->dropColumn('{{%imagemanager}}', 'modifiedBy');
    }
}
