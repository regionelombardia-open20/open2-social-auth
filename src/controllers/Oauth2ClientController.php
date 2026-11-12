<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 * @package   open20\amos\socialauth\controllers\base
 * @author    Aria S.p.A. <protocollo@pec.ariaspa.it>
 * @license   GNU General Public License v3.0 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @link      https://www.ariaspa.it/
 *
 * This file is part of OPEN 2.0.
 *
 * OPEN 2.0 is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPEN 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OPEN 2.0. If not, see <https://www.gnu.org/licenses/>.
 */

namespace open20\amos\socialauth\controllers;

use open20\amos\socialauth\models\search\Oauth2ClientSearch;
use open20\amos\socialauth\Module;
use Yii;
use open20\amos\socialauth\models\Oauth2Client;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use open20\amos\core\controllers\CrudController;
use open20\amos\core\module\BaseAmosModule;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\helpers\Html;
use open20\amos\core\helpers\T;
use yii\helpers\Url;


/**
 * Class Oauth2ClientController
 * Oauth2ClientController implements the CRUD actions for Oauth2Client model.
 *
 * @property \open20\amos\socialauth\models\Oauth2Client $model
 * @property \ $modelSearch
 *
 * @package open20\amos\socialauth\controllers\base
 */
class Oauth2ClientController extends CrudController
{

    /**
     * @var string $layout
     */
    public $layout = 'main';

    public function init()
    {
        $this->setModelObj(new Oauth2Client());
        $this->setModelSearch(new Oauth2ClientSearch());

        $this->setAvailableViews([
            'grid' => [
                'name' => 'grid',
                'label' => AmosIcons::show('view-list-alt') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'Table')),
                'url' => '?currentView=grid'
            ],
            /*'list' => [
                'name' => 'list',
                'label' => AmosIcons::show('view-list') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'List')),         
                'url' => '?currentView=list'
            ],
            'icon' => [
                'name' => 'icon',
                'label' => AmosIcons::show('grid') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'Icons')),           
                'url' => '?currentView=icon'
            ],
            'map' => [
                'name' => 'map',
                'label' => AmosIcons::show('map') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'Map')),      
                'url' => '?currentView=map'
            ],
            'calendar' => [
                'name' => 'calendar',
                'intestazione' => '', //codice HTML per l'intestazione che verrà caricato prima del calendario,
                                      //per esempio si può inserire una funzione $model->getHtmlIntestazione() creata ad hoc
                'label' => AmosIcons::show('calendar') . Html::tag('p', BaseAmosModule::tHtml('amoscore', 'Calendari')),                                            
                'url' => '?currentView=calendar'
            ],*/
        ]);

        parent::init();
        $this->setUpLayout();
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            //
                        ],
                        'roles' => ['OAUTH2CLIENT_ADMINISTRATOR']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get']
                ]
            ]
        ]);
    }

    /**
     * @param $layout
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($layout = NULL)
    {
        Url::remember();

        $this->setDataProvider($this->modelSearch->search(Yii::$app->request->getQueryParams()));

        return parent::actionIndex($layout);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $this->model = $this->findModel($id);

        if ($this->model->load(Yii::$app->request->post()) && $this->model->save()) {
            return $this->redirect(['view', 'id' => $this->model->id]);
        } else {
            return $this->render('view', ['model' => $this->model]);
        }
    }

    /**
     * @return string|\yii\web\Response
     * @throws Exception
     */
    public function actionCreate()
    {
        $this->setUpLayout('form');
        $this->model = new Oauth2Client();

        if (empty($this->model->client_secret)) {
            $this->model->client_secret = Yii::$app->security->generateRandomString(32);
        }
        if (empty($this->model->grant_type)) {
            $this->model->grant_type = 'authorization_code';
        }
        if (empty($this->model->scope)) {
            $this->model->scope = 'userinfo.profile';
        }

        if ($this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {
                Yii::$app->getSession()->addFlash('success', BaseAmosModule::t('amoscore', 'Item created'));
                return $this->redirect(['update', 'id' => $this->model->id]);
            } else {
                Yii::$app->getSession()->addFlash('danger', BaseAmosModule::t('amoscore', 'Item not created, check data'));
            }
        }

        return $this->render('create', [
            'model' => $this->model,
            'fid' => NULL,
            'dataField' => NULL,
            'dataEntity' => NULL,
        ]);
    }

    /**
     * @param $fid
     * @param $dataField
     * @return false|string
     */
    public function actionCreateAjax($fid, $dataField)
    {
        $this->setUpLayout('form');
        $this->model = new Oauth2Client();

        if (\Yii::$app->request->isAjax && $this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {
//Yii::$app->getSession()->addFlash('success', BaseAmosModule::t('amoscore', 'Item created'));
                return json_encode($this->model->toArray());
            } else {
//Yii::$app->getSession()->addFlash('danger', BaseAmosModule::t('amoscore', 'Item not created, check data'));
            }
        }

        return $this->renderAjax('_formAjax', [
            'model' => $this->model,
            'fid' => $fid,
            'dataField' => $dataField
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $this->setUpLayout('form');
        $this->model = $this->findModel($id);

        if ($this->model->load(Yii::$app->request->post()) && $this->model->validate()) {
            if ($this->model->save()) {
                Yii::$app->getSession()->addFlash('success', BaseAmosModule::t('amoscore', 'Item updated'));
                return $this->redirect(['update', 'id' => $this->model->id]);
            } else {
                Yii::$app->getSession()->addFlash('danger', BaseAmosModule::t('amoscore', 'Item not updated, check data'));
            }
        }

        return $this->render('update', [
            'model' => $this->model,
            'fid' => NULL,
            'dataField' => NULL,
            'dataEntity' => NULL,
        ]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->model = $this->findModel($id);
        if ($this->model) {
            $this->model->delete();
            if (!$this->model->hasErrors()) {
                Yii::$app->getSession()->addFlash('success', BaseAmosModule::t('amoscore', 'Element deleted successfully.'));
            } else {
                Yii::$app->getSession()->addFlash('danger', BaseAmosModule::t('amoscore', 'You are not authorized to delete this element.'));
            }
        } else {
            Yii::$app->getSession()->addFlash('danger', BaseAmosModule::tHtml('amoscore', 'Element not found.'));
        }
        return $this->redirect(['index']);
    }

    /**
     * Before action
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $titleSection = Module::t('amossocialauth', 'Federazione SSO');

//        $labelLinkAll = '';
//        $urlLinkAll = '';
//        $titleLinkAll = AmosComments::t('amoscomments', 'Visualizza tutti i termini della blacklist');
//        $subTitleSection = Html::tag('p', AmosComments::t('amoscomments', 'Gestisci qui l’attivazione delle funzionalità di moderazione dei commenti'));
        $labelCreate = Module::t('amossocialauth', 'Nuovo');
        $titleCreate = Module::t('amossocialauth', 'Aggiungi un nuovo dominio');
        $urlCreate = '/socialauth/oauth2-client/create';
        $labelSecondAction = Module::t('amossocialauth', 'Info');
        $titleSecondAction = Module::t('amossocialauth', 'Visualizza maggiori informazioni sul funzionamento della federazione SSO');
//        $urlSecondAction = '';
//        $labelManage = \Yii::t('amoscollaborations', 'Gestisci');
//        $titleManage = \Yii::t('amoscollaborations', 'Gestisci proposte di collaborazione');
//        $urlManage = null;

        $this->view->params = [
            'isGuest' => \Yii::$app->user->isGuest,
            'modelLabel' => 'oauth2Client',
            'titleSection' => $titleSection,
//            'subTitleSection' => $subTitleSection,
//            'urlLinkAll' => $urlLinkAll,
//            'labelLinkAll' => $labelLinkAll,
//            'titleLinkAll' => $titleLinkAll,
//            'labelManage' => $labelManage,
//            'titleManage' => $titleManage,
            'labelCreate' => $labelCreate,
            'titleCreate' => $titleCreate,
            'urlCreate' => $urlCreate,
//            'urlManage' => $urlManage,
//            'hideCreate' => true,
            'labelSecondAction' => $labelSecondAction,
//            'titleSecondAction' => $titleSecondAction,
//            'urlSecondAction' => $urlSecondAction,
            'iconSecondAction' => 'info-outline',
            'hideSecondAction' => false,
            'optionsSecondAction' => [
                'class' => 'btn btn-xs btn-default',
                'title' => $titleSecondAction,
                'id' => 'oauth2InfoBtn',
                'data-toggle' => 'modal',
                'data-target' => '#oauth2ModalInfo',
//                'onclick' => 'alert("ok");return false;',
            ],
        ];

        if (!parent::beforeAction($action)) {
            return false;
        }

        // other custom code here

        return true;
    }

}
