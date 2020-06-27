<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\CategorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index');
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if ( ! empty(Yii::$app->request->post('Category'))) 
        {
            $post            = Yii::$app->request->post('Category');
            $model->name     = $post['name'];

            if (empty($parent_id))
                $model->makeRoot();
            else
            {
                $parent = Category::findOne($parent_id);
                $model->appendTo($parent);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionSave()
    {
        $model = new Category();
        $post            = Yii::$app->request->post('Category');
        $node = Category::findOne(['name' => $post['name']]);
        if ($node->isRoot()) {
            $ch = explode(' ', $post['name']);
            $index = $node->children($node['depth'] + 1)->count() + 1;
            $post['name'] = 'Sub ' . $ch[1] . $index;
        } else {
            $index = $node->children($node['depth'])->count() + 1;
            $name = strstr($node->name, '-', true) ?: $node->name;

            $post['name'] = 'Sub ' . $name . '-' . $index;
        }

        $model->name     = $post['name'];
        $parent_id       = $node->id;

        if (empty($parent_id))
            $model->makeRoot();
        else
        {
            $parent = Category::findOne($parent_id);
            $model->appendTo($parent);
        }
        
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
