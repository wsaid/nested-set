<?php

namespace app\models;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;
use yii\helpers\Inflector;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name
 * @property int $tree
 * @property int $lft
 * @property int $rgt
 * @property int $depth
 * @property int $created_at
 * @property int $updated_at
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    public function behaviors() {
        return [
            TimeStampBehavior::className(),
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * {@inheritdoc}
     * @return CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['tree', 'lft', 'rgt', 'depth', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'tree' => 'Tree',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => 'Depth',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

   /**
     * Get parent's ID
     * @return \yii\db\ActiveQuery 
     */
    public function getParentId()
    {
        $parent = $this->parent;
        return $parent ? $parent->id : null;
    }

    /**
     * Get parent's node
     * @return \yii\db\ActiveQuery 
     */
    public function getParent()
    {
        return $this->parents(1)->one();
    }

     public static function getNestedSet($node_id = 0)
    {
        $rows = self::find()->
            select('id, name, depth')->
            orderBy('tree, lft')->
            all();

        $result = '<ul class="menu">';
        $currDepth = 0;

        foreach( $rows as $item )
        {
            if($item['depth'] > $currDepth)
            {
                $result .= '<li><ul>';
            }

            if($item['depth'] < $currDepth)
            {
                $result .= str_repeat("</ul></li>", $currDepth - $item['depth']);
            }

            $id = $item['id'];
            $node = Category::findOne(['id' => $id]);

            $url = '/link';

            foreach($node->parents()->all() as $el) {
                $url .= '/' . Inflector::slug($el['name']);
            }

            $url .= '/' . Inflector::slug($item['name']);

            $result .= "<li style='margin: 5px'><a class='text-black' href='$url'>$item[name]</a> <a class='addnode' href='#' style='color: #778899; text-decoration: none; padding-left: 12px;' data-name='$item[name]'><span class='glyphicon glyphicon-plus'></span>Add subcat</a></li>";
            $currDepth = $item['depth'];
        }

        $result .= "</ul>";

        return $result;
    }

}
