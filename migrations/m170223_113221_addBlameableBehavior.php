<?php

use yii\db\Migration;

class m170223_113221_addBlameableBehavior extends Migration
{
    public function up()
    {
        $this->addColumn('ImageManager', 'createdBy', $this->integer(10)->unsigned()->null()->defaultValue(null));
        $this->addColumn('ImageManager', 'modifiedBy', $this->integer(10)->unsigned()->null()->defaultValue(null));
    }

    public function down()
    {
        echo "m170223_113221_addBlameableBehavior cannot be reverted.\n";

        return false;
    }
}
