<?php

namespace app\controllers;

use app\models\Sales;
use app\models\SalesSearch;
use Yii;
use yii\web\Controller;
use app\models\Product;
use app\models\User;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Mpdf\Mpdf;
use yii\data\ActiveDataProvider;

class SaleController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'index', 'update', 'delete', 'delete-multiple', 'export-pdf'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $model = new Sales();

        if ($model->load(Yii::$app->request->post())) {
            $model->calculateTotalPrice();
            
            if ($model->save()) {
                // Salvar os produtos na tabela sales_product
                $productIds = Yii::$app->request->post('Sales')['product_id'] ?? [];
                $quantities = Yii::$app->request->post('Sales')['quantity'] ?? [];

                // Itera sobre os produtos e suas quantidades
                foreach ($productIds as $index => $productId) {
                    $quantity = $quantities[$index] ?? 0;
                    
                    if ($productId && $quantity > 0) {
                        // Salva o registro na tabela sales_product
                        Yii::$app->db->createCommand()->insert('sales_products', [
                            'sales_id' => $model->id,
                            'product_id' => $productId,
                            'quantity' => $quantity,
                        ])->execute();
                    }
                }

                return $this->redirect(['index', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'products' => Product::find()->select(['name', 'id'])->indexBy('id')->column(),
            'users' => User::find()->select(['name', 'id'])->indexBy('id')->column(),
        ]);
    }



    public function actionIndex()
    {
        $searchModel = new SalesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $products = \app\models\Product::find()->select('name')->indexBy('id')->column();
        $users = \app\models\User::find()->select('name')->indexBy('id')->column();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'products' => $products,
            'users' => $users,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = Sales::findOne($id);
        
        if (!$model) {
            throw new NotFoundHttpException('A venda solicitada não existe.');
        }

        $currentProducts = Yii::$app->db->createCommand('SELECT * FROM sales_products WHERE sales_id = :sales_id')
            ->bindValue(':sales_id', $model->id)
            ->queryAll();

        $products = Product::find()->select('name')->indexBy('id')->column();
        $users = User::find()->select('name')->indexBy('id')->column();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $productIds = Yii::$app->request->post('Sales')['product_id'] ?? [];
            $quantities = Yii::$app->request->post('Sales')['quantity'] ?? [];

            foreach ($currentProducts as $currentProduct) {
                if (!in_array($currentProduct['product_id'], $productIds)) {
                    Yii::$app->db->createCommand()
                        ->delete('sales_products', ['sales_id' => $model->id, 'product_id' => $currentProduct['product_id']])
                        ->execute();
                }
            }

            foreach ($productIds as $index => $productId) {
                $quantity = $quantities[$index] ?? 0;
                
                if ($productId && $quantity > 0) {
                    $existingProduct = Yii::$app->db->createCommand('SELECT * FROM sales_products WHERE sales_id = :sales_id AND product_id = :product_id')
                        ->bindValue(':sales_id', $model->id)
                        ->bindValue(':product_id', $productId)
                        ->queryOne();

                    if ($existingProduct) {
                        Yii::$app->db->createCommand()
                            ->update('sales_products', ['quantity' => $quantity], ['sales_id' => $model->id, 'product_id' => $productId])
                            ->execute();
                    } else {
                        Yii::$app->db->createCommand()->insert('sales_products', [
                            'sales_id' => $model->id,
                            'product_id' => $productId,
                            'quantity' => $quantity,
                        ])->execute();
                    }
                }
            }

            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'products' => $products,
            'users' => $users,
        ]);
    }


    public function actionDelete($id)
    {
        $model = Sales::findOne($id);
        $model->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteMultiple()
    {
        $ids = Yii::$app->request->post('keylist');
        if ($ids) {
            foreach ($ids as $id) {
                $this->findModel($id)->delete();
            }
            return json_encode(['success' => true]);
        }
        return json_encode(['success' => false]);
    }

    protected function findModel($id)
    {
        if (($model = Sales::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionExportPdf()
    {
        $stylesheet = file_get_contents(Yii::getAlias('@webroot/css/report.css'));

        $ids = Yii::$app->request->get('ids');
        $idArray = explode(',', $ids);

        $dataProvider = new ActiveDataProvider([
            'query' => Sales::find()->where(['id' => $idArray]),
        ]);

        $mpdf = new Mpdf();

        $html = $this->renderPartial('_pdf', [
            'dataProvider' => $dataProvider,
        ]);

        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        $mpdf->Output('vendas_selecionadas.pdf', 'D');
    }
}
