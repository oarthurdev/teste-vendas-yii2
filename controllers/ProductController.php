<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\ProductSearch;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

class ProductController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'index', 'update', 'delete', 'upload'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $this->actionUpload($model);
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpload($model)
    {
        $uploadPath = Yii::getAlias('@uploads');

        $file = UploadedFile::getInstance($model, 'product_image');
        
        if ($file) {
            $original_name = $file->baseName;  
            $newFileName = Yii::$app->security->generateRandomString() . '.' . $file->extension;

            if ($file->saveAs($uploadPath . '/' . $newFileName)) {
                $model->product_image = $newFileName;
                $model->original_name = $original_name;

                // Salvar o modelo com a imagem
                if (!$model->save(false)) {
                    return json_encode($model->getErrors());
                }
            } else {
                return json_encode(['error' => 'Erro ao salvar a imagem.']);
            }
        }

        return false;
    }

    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = Product::findOne($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = Product::findOne($id);
        $model->delete();

        return $this->redirect(['index']);
    }
}
