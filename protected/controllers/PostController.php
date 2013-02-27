<?php

class PostController extends Controller {

    public $layout = 'column2';

    /**
     * @var CActiveRecord the currently loaded data model instance.
     */
    private $_model;

    /**
     * Displays a particular model.
     */
    public function actionView() {
        if (!Yii::app()->user->isGuest) {
            $gost_or_user = 'user';
        } else {
            $gost_or_user = 'gost';
        }
        $post = $this->loadModel();
        $comment = $this->newComment($post);

        $type_1 = ObjectRating::TYPE_POST;
        $plus_1 = ObjectRating::PLUS;
        $minus_1 = ObjectRating::MINUS;

        $this->render('view', array(
            'model' => $post,
            'comment' => $comment,
            'gost_or_user' => $gost_or_user,
            'type_1' => $type_1,
            'plus_1' => $plus_1,
            'minus_1' => $minus_1,
                )
        );
    }

    public function actionScrapbook($post_id) {
        $cs = Yii::app()->getClientScript();
        $cs->registerCssFile(Yii::app()->request->baseUrl . '/css/jquery.lightbox-0.5.css');
        $cs->registerScriptFile(Yii::app()->request->baseUrl . '/js/jquery.lightbox-0.5.js');

        $model = Post::model()->with('filetopost', 'filetopost.file')->findByAttributes(array('id' => $post_id));
        $this->render('scrapbook', array('model' => $model));
    }

    public function actionUpdate() {
        $cs = Yii::app()->getClientScript();
        $cs->registerCssFile(Yii::app()->request->baseUrl . '/css/fileuploader.css');
        $cs->registerCssFile(Yii::app()->request->baseUrl . '/css/markitup/sets/markdown/style.css');
        $cs->registerCssFile(Yii::app()->request->baseUrl . '/css/markitup/skins/markitup/style.css');
        $cs->registerScriptFile(Yii::app()->request->baseUrl . '/js/fileuploader.js');
        $cs->registerScriptFile(Yii::app()->request->baseUrl . '/js/markitup/jquery.markitup.js');
        $cs->registerScriptFile(Yii::app()->request->baseUrl . '/js/markitup/sets/markdown/set.js');

        $model = $this->loadModel();
        if (isset($_POST['Post'])) {
            $model->attributes = $_POST['Post'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function actionCreate() {

        $user_id = Yii::app()->user->id;
        $profile = Profile::model()->findByAttributes(array('user_id' => $user_id));
        if (isset($_POST['isept'])) {
            $profile->instruct = 1;
            $profile->save();
        }
        if ($profile->instruct == null) {
            $this->render('instruct');
            die();
        }

        $cs = Yii::app()->getClientScript();
        $cs->registerCssFile(Yii::app()->request->baseUrl . '/css/fileuploader.css');
        $cs->registerCssFile(Yii::app()->request->baseUrl . '/css/markitup/sets/markdown/style.css');
        $cs->registerCssFile(Yii::app()->request->baseUrl . '/css/markitup/skins/markitup/style.css');
        $cs->registerScriptFile(Yii::app()->request->baseUrl . '/js/fileuploader.js');
        $cs->registerScriptFile(Yii::app()->request->baseUrl . '/js/markitup/jquery.markitup.js');
        $cs->registerScriptFile(Yii::app()->request->baseUrl . '/js/markitup/sets/markdown/set.js');
        $model = new Post;

        if (isset($_POST['Post'])) {
            $model->attributes = $_POST['Post'];
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'myForm') {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
            $model->profile_id = $profile->id;
            if ($model->save()) {
                if (isset($_POST['files'])) {
                    $files_ar = $_POST['files'];
                    $model->cover_id = $files_ar[0];

                    $model->save();

                    foreach ($files_ar as $value) {
                        $zsp = new Filetopost();
                        $zsp->post_id = $model->id;
                        $zsp->file_id = $value;
                        $zsp->save();
                    }
                }
                $this->redirect(array('view', 'id' => $model->id));
            }
        }
        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUploadPE() {
        $uf = DIRECTORY_SEPARATOR;
        $basePath = Yii::app()->basePath . "{$uf}..{$uf}uploads{$uf}";
        $basePathDefalt = Yii::app()->basePath . "{$uf}..{$uf}uploads{$uf}oli_";
        $allowedExtensions = array("png", "jpg", "gif");
        $sizeLimit = 10 * 1024 * 1024;
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
        $result = $uploader->handleUpload($basePath);
        if (empty($result['error'])) {
            $file = array(
                'name' => $result['filename'],
                'orig_name' => $result['user_filename'],
                'size' => $result['size'],
                'ext' => $result['ext'],
            );
            $UploadedFiles = new UploadedFiles();
            $UploadedFiles->attributes = $file;
            $UploadedFiles->save();
            $result['file_id'] = $UploadedFiles->id;

            $img = Yii::app()->ih
                    ->load($basePath . $UploadedFiles->name);
            $result['file_url'] = Yii::app()->createAbsoluteUrl('uploads/' . $UploadedFiles->name);

            if ($img->width > 1000) {
                $img->resize(900, 800)//обрезаем изображение для фотогалерии
                        ->save($basePath . 'oli_' . $UploadedFiles->name);
                $result['file_url'] = Yii::app()->createAbsoluteUrl('uploads/oli_' . $UploadedFiles->name);
            } else {
                $source = $basePath . $UploadedFiles->name;
                $dest = $basePath . 'oli_' . $UploadedFiles->name;
                copy($source, $dest);
            }

            if ($img->width > 650) {
                $img->resize(650, 500)//обрезаем изображение для поста
                        ->save($basePath . '/sm_' . $UploadedFiles->name);
                $result['file_url'] = Yii::app()->createAbsoluteUrl('uploads/sm_' . $UploadedFiles->name);
            } else {
                $source = $basePath . $UploadedFiles->name; //делаем копию для маленького изображения
                $dest = $basePath . 'oli_' . $UploadedFiles->name;
                copy($source, $dest);
            }
            $img->crop(220, 220)
                    ->save(Yii::app()->basePath . '/../uploads/thumb_' . $UploadedFiles->name);

            $img->resize(45, 45)
                    ->save(Yii::app()->basePath . '/../uploads/mini_' . $UploadedFiles->name);
            $result['file_url_mini'] = Yii::app()->createAbsoluteUrl('uploads/mini_' . $UploadedFiles->name);
        }
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    public function actionDeletePicterPost() {
        $uf = DIRECTORY_SEPARATOR;
        if (isset($_POST['id'])) {
            $model = UploadedFiles::model()->findByPk($_POST['id']);
            if (!empty($model)) {
                if (file_exists(Yii::app()->basePath . "..{$uf}..{$uf}uploads{$uf}mini_" . $model->name)) {
                    unlink(Yii::app()->basePath . "..{$uf}..{$uf}uploads{$uf}mini_" . $model->name);
                }
                if (file_exists(Yii::app()->basePath . "..{$uf}..{$uf}uploads{$uf}" . $model->name)) {
                    unlink(Yii::app()->basePath . "..{$uf}..{$uf}uploads{$uf}" . $model->name);
                }
                if (file_exists(Yii::app()->basePath . "..{$uf}..{$uf}uploads{$uf}oli_" . $model->name)) {
                    unlink(Yii::app()->basePath . "..{$uf}..{$uf}uploads{$uf}oli_" . $model->name);
                }
                if (file_exists(Yii::app()->basePath . "..{$uf}..{$uf}uploads{$uf}sm_" . $model->name)) {
                    unlink(Yii::app()->basePath . "..{$uf}..{$uf}uploads{$uf}sm_" . $model->name);
                }
                if (file_exists(Yii::app()->basePath . "..{$uf}..{$uf}uploads{$uf}thumb_" . $model->name)) {
                    unlink(Yii::app()->basePath . "}..{$uf}..{$uf}uploads{$uf}thumb_" . $model->name);
                }
                $model->delete();
                echo CJSON::encode(array('status' => 'success'));
                exit();
            }
        }
        exit();
    }

    public function actionDelete() {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = $this->loadModel();
            foreach ($model->filetopost as $filetopost) {
                UploadedFiles::DeleteFiles($filetopost->file);
            }
            $model->delete();
            if (!isset($_GET['ajax']))
                $this->redirect(array('index'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        if (!Yii::app()->user->isGuest) {
            $gost_or_user = 'user';
        } else {
            $gost_or_user = 'gost';
        }

        $type_1 = ObjectRating::TYPE_POST;
        $plus_1 = ObjectRating::PLUS;
        $minus_1 = ObjectRating::MINUS;
        $criteria = new CDbCriteria(array(
                    'condition' => 'status=' . Post::STATUS_PUBLISHED,
                    'order' => 'create_time DESC',
                    'with' => 'commentCount',
                ));
        if (isset($_GET['topic'])) {
            $topic = $_GET['topic'];
        } else {
            $topic = 1;
        }
        $criteria->addSearchCondition('topic', $topic);
        $dataProvider = new CActiveDataProvider('Post', array(
                    'pagination' => array(
                        'pageSize' => 10,
                    ),
                    'criteria' => $criteria,
                ));

        $this->render('index', array(
            'dataProvider' => $dataProvider,
            'gost_or_user' => $gost_or_user,
            'type_1' => $type_1,
            'plus_1' => $plus_1,
            'minus_1' => $minus_1,
                )
        );
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Post('search');
        if (isset($_GET['Post']))
            $model->attributes = $_GET['Post'];
        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function actionSuggestTags() {
        if (isset($_GET['q']) && ($keyword = trim($_GET['q'])) !== '') {
            $tags = Tag::model()->suggestTags($keyword);
            if ($tags !== array())
                echo implode("\n", $tags);
        }
    }

    public function loadModel() {
        if ($this->_model === null) {
            if (isset($_GET['id'])) {
                if (Yii::app()->user->isGuest)
                    $condition = 'status=' . Post::STATUS_PUBLISHED . ' OR status=' . Post::STATUS_ARCHIVED;
                else
                    $condition = '';
                $this->_model = Post::model()->findByPk($_GET['id'], $condition);
            }
            if ($this->_model === null)
                throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $this->_model;
    }

    protected function newComment($post) {
        $comment = new Comment;

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'comment-form') {
            echo CActiveForm::validate($comment);
            Yii::app()->end();
        }

        if (isset($_POST['Comment'])) {
            $profile = Profile::model()->findByAttributes(array('user_id' => Yii::app()->user->id));
            $comment->content = $_POST['Comment']['content'];
            $comment->profile_id = $profile->id;
            $comment->save(false);
            if ($post->addComment($comment)) {
                
            }
        }
        return $comment;
    }

    public function actionAddComment() {
        if (isset($_POST['post_id'])) {
            $gost_or_user = 'user';
            $type = ObjectRating::TYPE_COM;
            $plus = ObjectRating::PLUS;
            $minus = ObjectRating::MINUS;


            $profile = Profile::model()->findByAttributes(array('user_id' => Yii::app()->user->id));
            $new_com = new Comment;
            $new_com->content = $_POST['Comment']['content'];
            $new_com->post_id = $_POST['post_id'];
            $new_com->create_time = time();
            $new_com->profile_id = $profile->id;
            $new_com->save();
            $comment = Comment::model()->findByPk($new_com->id);
            $data = $this->renderPartial('_comments', array(
                'comment' => $comment,
                'gost_or_user' => $gost_or_user,
                'type' => $type,
                'plus' => $plus,
                'minus' => $minus,
                    ), true);
            echo json_encode(array('div' => $data));
        }
    }

}
